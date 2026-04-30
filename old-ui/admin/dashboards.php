<?php
$pageTitle = 'Dashboard Registry';
$pageDescription = 'Review all role-based dashboards.';
$pageName = 'admin-dashboards';
$activeSidebar = 'role-dashboard';

require_once __DIR__ . '/../includes/config.php';
require_role('admin');

$dashboards = [];

try {
    if (table_exists('dashboard_registry')) {
        $dashboards = db()->query("SELECT * FROM dashboard_registry ORDER BY display_order ASC, dashboard_name ASC")->fetchAll();
    }
} catch (Throwable $e) {}

include __DIR__ . '/../includes/header.php';
?>

<main class="dashboard-page">
    <section class="dashboard-hero admin">
        <div class="container dashboard-hero-grid">
            <div>
                <div class="page-kicker"><i class="fa-solid fa-table-columns"></i> Dashboard Registry</div>
                <h1>All dashboards installed for each role.</h1>
                <p>This registry shows which workspace belongs to each role and where users are routed after login.</p>
            </div>
        </div>
    </section>

    <section class="dashboard-shell">
        <div class="container dashboard-layout">
            <?php include __DIR__ . '/../includes/sidebar.php'; ?>

            <div class="dashboard-content">
                <article class="dashboard-panel">
                    <div class="panel-head"><div><h3>Dashboards</h3><p>Role-based dashboard map.</p></div><span class="status-badge status-open"><?= e(count($dashboards)); ?> dashboards</span></div>
                    <div class="dashboard-table-wrap">
                        <table class="dashboard-table">
                            <thead><tr><th>Name</th><th>Role</th><th>URL</th><th>Description</th><th>Status</th></tr></thead>
                            <tbody>
                                <?php foreach ($dashboards as $dashboard): ?>
                                    <tr>
                                        <td><?= e($dashboard['dashboard_name']); ?></td>
                                        <td><?= e($dashboard['role_key']); ?></td>
                                        <td><a href="<?= e(BASE_URL . $dashboard['dashboard_url']); ?>"><?= e($dashboard['dashboard_url']); ?></a></td>
                                        <td><?= e($dashboard['description']); ?></td>
                                        <td><?= e(ucfirst($dashboard['status'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </article>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
