<?php
$pageTitle = 'Users';
$pageDescription = 'Manage platform users.';
$pageName = 'admin-users';

require_once __DIR__ . '/../includes/config.php';
require_role('admin');

include __DIR__ . '/../includes/header.php';
?>

<main class="dashboard-page">
    <section class="dashboard-hero admin">
        <div class="container">
            <div class="page-kicker">
                <i class="fa-solid fa-users"></i>
                Admin Module
            </div>

            <h1>Users Management</h1>

            <p>
                Manage registered users, roles, account status and access control.
            </p>
        </div>
    </section>

    <section class="dashboard-shell">
        <div class="container">
            <article class="dashboard-panel">
                <div class="panel-head">
                    <div>
                        <h3>Users module is ready for backend actions.</h3>
                        <p>Next step: list users, suspend accounts, change roles and view user profiles.</p>
                    </div>
                </div>

                <a href="<?= e(BASE_URL); ?>admin/dashboard.php" class="btn btn-primary">
                    Back to Admin Dashboard
                </a>
            </article>
        </div>
    </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>