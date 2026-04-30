<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($pageTitle)) {
    $pageTitle = APP_NAME;
}

if (!isset($pageDescription)) {
    $pageDescription = 'UNIDA Gateway is an investment ecosystem platform for verification, readiness, stakeholder coordination and data-driven investment access.';
}

if (!isset($pageName)) {
    $pageName = '';
}

$isLoggedIn = isset($_SESSION['user_id']);
$userRole = $_SESSION['user_role'] ?? '';
$dashboardUrl = $isLoggedIn ? dashboard_url_by_role($userRole) : BASE_URL . 'login.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= e(page_title($pageTitle)); ?></title>

    <meta name="description" content="<?= e($pageDescription); ?>">
    <meta name="theme-color" content="#0A5DB7">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    >

    <link rel="stylesheet" href="<?= e(asset_url('css/main.css')); ?>">
    <link rel="stylesheet" href="<?= e(asset_url('css/dashboard.css')); ?>">
    <link rel="stylesheet" href="<?= e(asset_url('css/legal.css')); ?>">
</head>

<body data-page="<?= e($pageName); ?>">

    <div class="topbar">
        <div class="container topbar-inner">
            <span>
                <i class="fa-solid fa-shield-halved"></i>
                UNIDA Gateway: verification, readiness and stakeholder coordination
            </span>

            <span>
                <i class="fa-solid fa-location-dot"></i>
                Built in Tanzania by <?= e(COMPANY_NAME); ?>
            </span>
        </div>
    </div>

    <header class="site-header" id="siteHeader">
        <div class="container nav-wrap">

            <a href="<?= e(BASE_URL); ?>index.php" class="brand" aria-label="<?= e(APP_NAME); ?>">
                <span class="brand-mark">
                    <span class="drop drop-blue"></span>
                    <span class="drop drop-green"></span>
                </span>

                <span class="brand-text">
                    <strong>UNIDA Gateway</strong>
                    <small><?= e(APP_SUBTITLE); ?></small>
                </span>
            </a>

            <nav class="desktop-nav" aria-label="Primary navigation">
                <?php foreach ($navLinks as $link): ?>
                    <a
                        class="<?= $pageName === $link['key'] ? 'active' : ''; ?>"
                        href="<?= e($link['url']); ?>"
                    >
                        <?= e($link['label']); ?>
                    </a>
                <?php endforeach; ?>

                <a
                    class="<?= in_array($pageName, ['privacy', 'terms', 'verification-policy'], true) ? 'active' : ''; ?>"
                    href="<?= e(BASE_URL); ?>verification-policy.php"
                >
                    Policies
                </a>
            </nav>

            <div class="nav-actions">
<?php
$langSwitch = __DIR__ . '/components/language-switch.php';

if (file_exists($langSwitch)) {
    include $langSwitch;
}
?>
                <?php if ($isLoggedIn): ?>
                    <a class="btn btn-soft hide-sm" href="<?= e($dashboardUrl); ?>">
                        <i class="fa-solid fa-gauge-high"></i>
                        My Workspace
                    </a>

                    <a class="btn btn-primary" href="<?= e(BASE_URL); ?>logout.php">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        Logout
                    </a>
                <?php else: ?>
                    <a class="btn btn-soft hide-sm" href="<?= e(BASE_URL); ?>login.php">
                        <i class="fa-solid fa-right-to-bracket"></i>
                        Login
                    </a>

                    <a class="btn btn-primary" href="<?= e(BASE_URL); ?>register.php">
                        <i class="fa-solid fa-user-plus"></i>
                        Create Account
                    </a>
                <?php endif; ?>

                <button
                    class="menu-toggle"
                    type="button"
                    aria-label="Open menu"
                    aria-expanded="false"
                    aria-controls="mobileNav"
                >
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>
        </div>

        <nav class="mobile-nav" id="mobileNav" aria-label="Mobile navigation">
            <?php foreach ($navLinks as $link): ?>
                <a
                    class="<?= $pageName === $link['key'] ? 'active' : ''; ?>"
                    href="<?= e($link['url']); ?>"
                >
                    <?= e($link['label']); ?>
                </a>
            <?php endforeach; ?>

            <a
                class="<?= $pageName === 'verification-policy' ? 'active' : ''; ?>"
                href="<?= e(BASE_URL); ?>verification-policy.php"
            >
                Verification Policy
            </a>

            <a
                class="<?= $pageName === 'terms' ? 'active' : ''; ?>"
                href="<?= e(BASE_URL); ?>terms.php"
            >
                Terms of Use
            </a>

            <a
                class="<?= $pageName === 'privacy' ? 'active' : ''; ?>"
                href="<?= e(BASE_URL); ?>privacy.php"
            >
                Privacy Policy
            </a>

            <?php if ($isLoggedIn): ?>
                <a href="<?= e($dashboardUrl); ?>">
                    <i class="fa-solid fa-gauge-high"></i>
                    My Workspace
                </a>

                <a href="<?= e(BASE_URL); ?>logout.php">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    Logout
                </a>
            <?php else: ?>
                <a href="<?= e(BASE_URL); ?>login.php">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    Login
                </a>

                <a href="<?= e(BASE_URL); ?>register.php">
                    <i class="fa-solid fa-user-plus"></i>
                    Create Account
                </a>
            <?php endif; ?>
        </nav>
    </header>
