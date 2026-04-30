<?php
$pageTitle = 'Create Account';
$pageDescription = 'Create your UNIDA Gateway account with UNIDA TECH LIMITED.';
$pageName = 'register';

require_once __DIR__ . '/includes/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$errors = [];

$allowedRoles = ['business', 'investor', 'stakeholder'];

function go_to_role_dashboard($role) {
    $paths = [
        'business' => 'business/dashboard.php',
        'investor' => 'investor/dashboard.php',
        'stakeholder' => 'stakeholder/dashboard.php',
        'admin' => 'admin/dashboard.php',
    ];

    $path = $paths[$role] ?? 'index.php';

    session_write_close();

    header('Location: ' . BASE_URL . $path);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $organization = trim($_POST['organization'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $role = trim($_POST['role'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

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

                $stmt = $pdo->prepare("
                    INSERT INTO users (
                        full_name,
                        organization,
                        email,
                        phone,
                        role,
                        password_hash,
                        status
                    ) VALUES (
                        :full_name,
                        :organization,
                        :email,
                        :phone,
                        :role,
                        :password_hash,
                        'active'
                    )
                ");

                $stmt->execute([
                    ':full_name' => $fullName,
                    ':organization' => $organization ?: null,
                    ':email' => $email,
                    ':phone' => $phone ?: null,
                    ':role' => $role,
                    ':password_hash' => $passwordHash,
                ]);

                $userId = (int) $pdo->lastInsertId();

                if ($role === 'business') {
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
                            'not_submitted',
                            0
                        )
                    ");

                    $profile->execute([
                        ':user_id' => $userId,
                        ':business_name' => $businessName,
                    ]);
                }

                if ($role === 'investor') {
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
                            'active'
                        )
                    ");

                    $profile->execute([
                        ':user_id' => $userId,
                        ':investor_name' => $investorName,
                    ]);
                }

                if ($role === 'stakeholder') {
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
                            'active'
                        )
                    ");

                    $profile->execute([
                        ':user_id' => $userId,
                        ':organization_name' => $organizationName,
                    ]);
                }

                $pdo->commit();

                session_regenerate_id(true);

                $_SESSION['user_id'] = $userId;
                $_SESSION['user_name'] = $fullName;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_role'] = $role;

                go_to_role_dashboard($role);
            }
        } catch (PDOException $e) {
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
        <form class="auth-card form-grid" method="post" action="">
            <div>
                <div class="page-kicker">
                    <i class="fa-solid fa-user-plus"></i>
                    Create Account
                </div>

                <h1>Register your UNIDA Gateway account.</h1>

                <p class="auth-note">
                    Create an account as a business, investor or ecosystem stakeholder. After registration,
                    you will be directed to your role-based workspace.
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
                        Institution / Government / Bank / Development Partner
                    </option>
                </select>
            </div>

            <div class="form-grid two">
                <div class="form-group">
                    <label>Password</label>
                    <input
                        class="form-control"
                        type="password"
                        name="password"
                        placeholder="Password"
                        required
                    >
                </div>

                <div class="form-group">
                    <label>Confirm Password</label>
                    <input
                        class="form-control"
                        type="password"
                        name="confirm_password"
                        placeholder="Confirm password"
                        required
                    >
                </div>
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