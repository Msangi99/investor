<?php
$pageTitle = 'Login';
$pageDescription = 'Login to your UNIDA Gateway workspace.';
$pageName = 'login';

require_once __DIR__ . '/includes/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$errors = [];

function go_to_role_dashboard($role) {
    $paths = [
        'admin' => 'admin/dashboard.php',
        'business' => 'business/dashboard.php',
        'investor' => 'investor/dashboard.php',
        'stakeholder' => 'stakeholder/dashboard.php',
    ];

    $path = $paths[$role] ?? 'index.php';

    session_write_close();

    header('Location: ' . BASE_URL . $path);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
            } elseif ($user['status'] !== 'active') {
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

                go_to_role_dashboard($user['role']);
            }
        } catch (PDOException $e) {
            error_log('Login error: ' . $e->getMessage());
            $errors[] = 'Unable to login right now. Please try again later.';
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
                    <i class="fa-solid fa-lock"></i>
                    Secure Access
                </div>

                <h1>Login to your workspace.</h1>

                <p class="auth-note">
                    Access your workspace based on your account type: business, investor, stakeholder or administrator.
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
                    required
                >
            </div>

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