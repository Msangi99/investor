<?php
$pageTitle = 'Create Account';
$pageDescription = 'Create your UNIDA Gateway account with UNIDA TECH LIMITED.';
$pageName = 'register';

require_once __DIR__ . '/includes/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (is_logged_in()) {
    redirect_by_role($_SESSION['user_role'] ?? '');
}

$errors = [];

$allowedRoles = ['business', 'investor', 'stakeholder'];

function auth_table_exists_local($table) {
    try {
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $table)) {
            return false;
        }

        $stmt = db()->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);

        return (bool) $stmt->fetch();
    } catch (Throwable $e) {
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (function_exists('require_valid_csrf')) {
        require_valid_csrf();
    }

    $fullName = trim($_POST['full_name'] ?? '');
    $organization = trim($_POST['organization'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $role = trim($_POST['role'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    $termsAccepted = isset($_POST['terms_accepted']);
    $dataConsent = isset($_POST['data_consent']);
    $newsletterOptIn = isset($_POST['newsletter_opt_in']) ? 1 : 0;

    if ($fullName === '') {
        $errors[] = 'Full name is required.';
    }

    if ($email === '') {
        $errors[] = 'Email address is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    if (!in_array($role, $allowedRoles, true)) {
        $errors[] = 'Please select a valid account type.';
    }

    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    }

    if ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match.';
    }

    if (!$termsAccepted) {
        $errors[] = 'You must accept the Terms of Use and Privacy Policy.';
    }

    if (!$dataConsent) {
        $errors[] = 'You must consent to data processing for account access and verification.';
    }

    if (empty($errors)) {
        try {
            $pdo = db();

            $check = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
            $check->execute([$email]);

            if ($check->fetch()) {
                $errors[] = 'An account with this email already exists.';
            } else {
                $pdo->beginTransaction();

                $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                $columns = [
                    'full_name',
                    'organization',
                    'email',
                    'phone',
                    'role',
                    'password_hash',
                    'status'
                ];

                $values = [
                    ':full_name' => $fullName,
                    ':organization' => $organization ?: null,
                    ':email' => $email,
                    ':phone' => $phone ?: null,
                    ':role' => $role,
                    ':password_hash' => $passwordHash,
                    ':status' => 'active',
                ];

                $optionalUserFields = [
                    'age_confirmed' => 1,
                    'terms_accepted_at' => date('Y-m-d H:i:s'),
                    'privacy_accepted_at' => date('Y-m-d H:i:s'),
                    'data_consent_at' => date('Y-m-d H:i:s'),
                    'newsletter_opt_in' => $newsletterOptIn,
                ];

                foreach ($optionalUserFields as $field => $value) {
                    try {
                        $col = $pdo->prepare("SHOW COLUMNS FROM users LIKE ?");
                        $col->execute([$field]);

                        if ($col->fetch()) {
                            $columns[] = $field;
                            $values[':' . $field] = $value;
                        }
                    } catch (Throwable $e) {}
                }

                $fieldSql = implode(', ', $columns);
                $placeholderSql = implode(', ', array_keys($values));

                $stmt = $pdo->prepare("
                    INSERT INTO users ({$fieldSql})
                    VALUES ({$placeholderSql})
                ");

                $stmt->execute($values);

                $userId = (int) $pdo->lastInsertId();

                if ($role === 'business' && auth_table_exists_local('business_profiles')) {
                    $businessName = $organization ?: $fullName . ' Business';

                    $profile = $pdo->prepare("
                        INSERT INTO business_profiles (
                            user_id,
                            business_name,
                            verification_status,
                            readiness_score
                        ) VALUES (
                            :user_id,
                            :business_name,
                            'unverified',
                            0
                        )
                    ");

                    $profile->execute([
                        ':user_id' => $userId,
                        ':business_name' => $businessName,
                    ]);
                }

                if ($role === 'investor' && auth_table_exists_local('investor_profiles')) {
                    $investorName = $organization ?: $fullName;

                    $profile = $pdo->prepare("
                        INSERT INTO investor_profiles (
                            user_id,
                            investor_name,
                            investor_type,
                            profile_status
                        ) VALUES (
                            :user_id,
                            :investor_name,
                            'individual',
                            'incomplete'
                        )
                    ");

                    $profile->execute([
                        ':user_id' => $userId,
                        ':investor_name' => $investorName,
                    ]);
                }

                if ($role === 'stakeholder' && auth_table_exists_local('stakeholder_profiles')) {
                    $organizationName = $organization ?: $fullName . ' Organization';

                    $profile = $pdo->prepare("
                        INSERT INTO stakeholder_profiles (
                            user_id,
                            organization_name,
                            stakeholder_type,
                            profile_status
                        ) VALUES (
                            :user_id,
                            :organization_name,
                            'other',
                            'incomplete'
                        )
                    ");

                    $profile->execute([
                        ':user_id' => $userId,
                        ':organization_name' => $organizationName,
                    ]);
                }

                if (auth_table_exists_local('legal_consents')) {
                    $consentInsert = $pdo->prepare("
                        INSERT INTO legal_consents (
                            user_id,
                            consent_type,
                            consent_value,
                            consent_text_version,
                            ip_address,
                            user_agent
                        ) VALUES (
                            :user_id,
                            :consent_type,
                            1,
                            '2026-04',
                            :ip_address,
                            :user_agent
                        )
                    ");

                    $consents = ['terms', 'privacy', 'data_processing'];

                    if ($newsletterOptIn) {
                        $consents[] = 'newsletter';
                    }

                    foreach ($consents as $consentType) {
                        $consentInsert->execute([
                            ':user_id' => $userId,
                            ':consent_type' => $consentType,
                            ':ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
                            ':user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
                        ]);
                    }
                }

                $pdo->commit();

                session_regenerate_id(true);

                $_SESSION['user_id'] = $userId;
                $_SESSION['user_name'] = $fullName;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_role'] = $role;

                if (function_exists('log_activity')) {
                    log_activity($userId, 'register', 'auth', 'User created account.');
                }

                redirect_by_role($role);
            }
        } catch (Throwable $e) {
            if (isset($pdo) && $pdo->inTransaction()) {
                $pdo->rollBack();
            }

            error_log('Registration error: ' . $e->getMessage());
            $errors[] = 'Unable to create account right now. Please try again later.';
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<main class="auth-section">
    <div class="container">
        <form class="auth-card form-grid" method="post" action="" autocomplete="on">
            <?php if (function_exists('csrf_field')): ?>
                <?= csrf_field(); ?>
            <?php endif; ?>

            <div>
                <div class="page-kicker">
                    <i class="fa-solid fa-user-plus"></i>
                    Create Account
                </div>

                <h1>Register your UNIDA Gateway account.</h1>

                <p class="auth-note">
                    Select the correct account type. After registration, the system will direct you to your role-based workspace.
                </p>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="form-alert form-alert-error">
                    <?php foreach ($errors as $error): ?>
                        <p><?= e($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="form-grid two">
                <div class="form-group">
                    <label>Full Name</label>
                    <input
                        class="form-control"
                        type="text"
                        name="full_name"
                        placeholder="Full name"
                        value="<?= e($_POST['full_name'] ?? ''); ?>"
                        autocomplete="name"
                        required
                    >
                </div>

                <div class="form-group">
                    <label>Organization</label>
                    <input
                        class="form-control"
                        type="text"
                        name="organization"
                        placeholder="Organization name"
                        value="<?= e($_POST['organization'] ?? ''); ?>"
                        autocomplete="organization"
                    >
                </div>
            </div>

            <div class="form-grid two">
                <div class="form-group">
                    <label>Email</label>
                    <input
                        class="form-control"
                        type="email"
                        name="email"
                        placeholder="Email address"
                        value="<?= e($_POST['email'] ?? ''); ?>"
                        autocomplete="email"
                        required
                    >
                </div>

                <div class="form-group">
                    <label>Phone</label>
                    <input
                        class="form-control"
                        type="tel"
                        name="phone"
                        placeholder="+255..."
                        value="<?= e($_POST['phone'] ?? ''); ?>"
                        autocomplete="tel"
                    >
                </div>
            </div>

            <div class="form-group">
                <label>Account Type</label>

                <select class="form-control" name="role" required>
                    <option value="">Select account type</option>

                    <option value="business" <?= (($_POST['role'] ?? '') === 'business') ? 'selected' : ''; ?>>
                        Business / SME / Startup
                    </option>

                    <option value="investor" <?= (($_POST['role'] ?? '') === 'investor') ? 'selected' : ''; ?>>
                        Investor
                    </option>

                    <option value="stakeholder" <?= (($_POST['role'] ?? '') === 'stakeholder') ? 'selected' : ''; ?>>
                        Stakeholder / Institution / Government / Bank / Development Partner
                    </option>
                </select>

                <p class="auth-note" style="margin-top:8px;">
                    Admin accounts are created internally by SUPER_ADMIN only.
                </p>
            </div>

            <div class="form-grid two">
                <div class="form-group">
                    <label>Password</label>

                    <div class="password-field">
                        <input
                            class="form-control"
                            id="registerPassword"
                            type="password"
                            name="password"
                            placeholder="Minimum 8 characters"
                            autocomplete="new-password"
                            required
                        >

                        <button
                            class="password-toggle"
                            type="button"
                            data-password-toggle="#registerPassword"
                            aria-label="Show or hide password"
                        >
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label>Confirm Password</label>

                    <div class="password-field">
                        <input
                            class="form-control"
                            id="confirmPassword"
                            type="password"
                            name="confirm_password"
                            placeholder="Confirm password"
                            autocomplete="new-password"
                            required
                        >

                        <button
                            class="password-toggle"
                            type="button"
                            data-password-toggle="#confirmPassword"
                            aria-label="Show or hide password"
                        >
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="form-group legal-consent">
                <label class="checkbox-row">
                    <input type="checkbox" name="terms_accepted" value="1" required>
                    <span>
                        I confirm that I am 18 years or older and I agree to the
                        <a href="<?= e(BASE_URL); ?>terms.php" target="_blank" rel="noopener">Terms of Use</a>
                        and
                        <a href="<?= e(BASE_URL); ?>privacy.php" target="_blank" rel="noopener">Privacy Policy</a>.
                    </span>
                </label>

                <label class="checkbox-row">
                    <input type="checkbox" name="data_consent" value="1" required>
                    <span>
                        I consent to UNIDA Gateway processing my information for account access,
                        profile management, verification, readiness and platform services.
                    </span>
                </label>

                <label class="checkbox-row">
                    <input type="checkbox" name="newsletter_opt_in" value="1" <?= isset($_POST['newsletter_opt_in']) ? 'checked' : ''; ?>>
                    <span>
                        I agree to receive platform updates, opportunities, newsletters and ecosystem insights.
                    </span>
                </label>
            </div>

            <button class="btn btn-primary" type="submit">
                <i class="fa-solid fa-circle-check"></i>
                Create Account
            </button>

            <p class="auth-note">
                Already have an account?
                <a href="<?= e(BASE_URL); ?>login.php" style="color:var(--primary-blue);font-weight:800;">
                    Login here
                </a>
            </p>
        </form>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
