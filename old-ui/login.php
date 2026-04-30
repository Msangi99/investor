<?php
$pageTitle = 'Login';
$pageDescription = 'Login to your UNIDA Gateway workspace.';
$pageName = 'login';

require_once __DIR__ . '/includes/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (is_logged_in()) {
    redirect_by_role($_SESSION['user_role'] ?? '');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (function_exists('require_valid_csrf')) {
        require_valid_csrf();
    }

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '') {
        $errors[] = 'Email address is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    if ($password === '') {
        $errors[] = 'Password is required.';
    }

    if (empty($errors)) {
        try {
            $pdo = db();

            $stmt = $pdo->prepare("
                SELECT 
                    id,
                    full_name,
                    email,
                    role,
                    password_hash,
                    status
                FROM users
                WHERE email = :email
                LIMIT 1
            ");

            $stmt->execute([
                ':email' => $email
            ]);

            $user = $stmt->fetch();

            if (!$user || !password_verify($password, $user['password_hash'])) {
                $errors[] = 'Invalid email or password.';
            } elseif (($user['status'] ?? '') !== 'active') {
                $errors[] = 'Your account is not active. Please contact support.';
            } else {
                session_regenerate_id(true);

                $_SESSION['user_id'] = (int) $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];

                try {
                    $updateLogin = $pdo->prepare("
                        UPDATE users
                        SET last_login_at = NOW()
                        WHERE id = ?
                    ");
                    $updateLogin->execute([(int) $user['id']]);
                } catch (Throwable $e) {
                    error_log('Last login update failed: ' . $e->getMessage());
                }

                if (function_exists('log_activity')) {
                    log_activity((int) $user['id'], 'login', 'auth', 'User logged in.');
                }

                redirect_by_role($user['role']);
            }
        } catch (Throwable $e) {
            error_log('Login error: ' . $e->getMessage());
            $errors[] = 'Unable to login right now. Please try again later.';
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
                    <i class="fa-solid fa-lock"></i>
                    Secure Access
                </div>

                <h1>Login to your workspace.</h1>

                <p class="auth-note">
                    Access your workspace based on your account role and permissions.
                </p>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="form-alert form-alert-error">
                    <?php foreach ($errors as $error): ?>
                        <p><?= e($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label>Email Address</label>
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
                <label>Password</label>

                <div class="password-field">
                    <input
                        class="form-control"
                        id="loginPassword"
                        type="password"
                        name="password"
                        placeholder="Password"
                        autocomplete="current-password"
                        required
                    >

                    <button
                        class="password-toggle"
                        type="button"
                        data-password-toggle="#loginPassword"
                        aria-label="Show or hide password"
                    >
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>
            </div>

            <button class="btn btn-primary" type="submit">
                <i class="fa-solid fa-right-to-bracket"></i>
                Login
            </button>

            <p class="auth-note">
                No account yet?
                <a href="<?= e(BASE_URL); ?>register.php" style="color:var(--primary-blue);font-weight:800;">
                    Create account
                </a>
            </p>
        </form>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
