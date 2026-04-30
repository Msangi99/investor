<?php
$pageTitle = 'Messages & Inquiries';
$pageDescription = 'Manage contact messages, support inquiries, partnership requests and institutional communication.';
$pageName = 'admin-messages';

require_once __DIR__ . '/../includes/config.php';
require_role('admin');

$pdo = db();

function safe_count($pdo, $table, $where = '') {
    try {
        $sql = "SELECT COUNT(*) AS total FROM {$table}";
        if ($where !== '') {
            $sql .= " WHERE {$where}";
        }
        $stmt = $pdo->query($sql);
        $row = $stmt->fetch();
        return (int) ($row['total'] ?? 0);
    } catch (Throwable $e) {
        return 0;
    }
}

include __DIR__ . '/../includes/header.php';
?>

<main class="dashboard-page">
    <section class="dashboard-hero admin">
        <div class="container dashboard-hero-grid">
            <div>
                <div class="page-kicker">
                    <i class="fa-solid fa-envelope"></i>
                    Admin Module
                </div>

                <h1>Messages & Inquiries</h1>

                <p>Manage contact messages, support inquiries, partnership requests and institutional communication.</p>
            </div>

            <div class="dashboard-profile-card">
                <div class="profile-avatar admin-avatar">
                    <?= e(strtoupper(substr($_SESSION['user_name'] ?? 'AD', 0, 2))); ?>
                </div>
                <div>
                    <h3><?= e($_SESSION['user_name'] ?? 'System Admin'); ?></h3>
                    <p>Administrative workspace</p>
                    <span class="status-badge status-verified">
                        <i class="fa-solid fa-shield"></i>
                        Full access
                    </span>
                </div>
            </div>
        </div>
    </section>

    <section class="dashboard-shell">
        <div class="container dashboard-layout">
            <aside class="dashboard-sidebar">
                <a href="<?= e(BASE_URL); ?>admin/dashboard.php"><i class="fa-solid fa-gauge-high"></i> Overview</a>
                <a href="<?= e(BASE_URL); ?>admin/users.php"><i class="fa-solid fa-users"></i> Users</a>
                <a href="<?= e(BASE_URL); ?>admin/roles.php"><i class="fa-solid fa-user-lock"></i> Roles & Permissions</a>
                <a class="<?= $pageName === 'admin-verifications' ? 'active' : ''; ?>" href="<?= e(BASE_URL); ?>admin/verifications.php"><i class="fa-solid fa-file-shield"></i> Verifications</a>
                <a class="<?= $pageName === 'admin-businesses' ? 'active' : ''; ?>" href="<?= e(BASE_URL); ?>admin/businesses.php"><i class="fa-solid fa-building"></i> Businesses</a>
                <a class="<?= $pageName === 'admin-investors' ? 'active' : ''; ?>" href="<?= e(BASE_URL); ?>admin/investors.php"><i class="fa-solid fa-coins"></i> Investors</a>
                <a class="<?= $pageName === 'admin-stakeholders' ? 'active' : ''; ?>" href="<?= e(BASE_URL); ?>admin/stakeholders.php"><i class="fa-solid fa-users-gear"></i> Stakeholders</a>
                <a class="<?= $pageName === 'admin-opportunities' ? 'active' : ''; ?>" href="<?= e(BASE_URL); ?>admin/opportunities.php"><i class="fa-solid fa-briefcase"></i> UNIDA Invest</a>
                <a class="<?= $pageName === 'admin-uploads' ? 'active' : ''; ?>" href="<?= e(BASE_URL); ?>admin/uploads.php"><i class="fa-solid fa-cloud-arrow-up"></i> Uploads</a>
                <a class="<?= $pageName === 'admin-insights' ? 'active' : ''; ?>" href="<?= e(BASE_URL); ?>admin/insights.php"><i class="fa-solid fa-chart-pie"></i> UNIDA Insights</a>
                <a class="<?= $pageName === 'admin-messages' ? 'active' : ''; ?>" href="<?= e(BASE_URL); ?>admin/messages.php"><i class="fa-solid fa-envelope"></i> Messages</a>
                <a class="<?= $pageName === 'admin-settings' ? 'active' : ''; ?>" href="<?= e(BASE_URL); ?>admin/settings.php"><i class="fa-solid fa-gear"></i> Settings</a>
                <a href="<?= e(BASE_URL); ?>logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
            </aside>

            <div class="dashboard-content">
                <div class="cards-grid three-columns">
                    <article class="info-card">
                        <div class="icon-box"><i class="fa-solid fa-inbox"></i></div>
                        <h3>New Messages</h3>
                        <p>Review incoming platform support and partnership inquiries.</p>
                    </article>
                    <article class="info-card">
                        <div class="icon-box"><i class="fa-solid fa-reply"></i></div>
                        <h3>Responses</h3>
                        <p>Track responded, in-review and closed messages.</p>
                    </article>
                    <article class="info-card">
                        <div class="icon-box"><i class="fa-solid fa-user-check"></i></div>
                        <h3>Assignments</h3>
                        <p>Assign messages to admin, support, operations or partnership teams.</p>
                    </article>
                </div>

                <article class="dashboard-panel">
                    <div class="panel-head">
                        <div>
                            <h3>Next Backend Actions</h3>
                            <p>This module is connected to admin access and ready for table listing, filtering, approvals and CRUD actions.</p>
                        </div>

                        <span class="status-badge status-open">Ready</span>
                    </div>

                    <div class="pipeline-list">
                        <div>
                            <span>View records</span>
                            <strong>List database items with search and filters</strong>
                        </div>
                        <div>
                            <span>Manage status</span>
                            <strong>Approve, reject, suspend, publish or archive</strong>
                        </div>
                        <div>
                            <span>Audit trail</span>
                            <strong>Record admin activity in activity_logs</strong>
                        </div>
                        <div>
                            <span>Reports</span>
                            <strong>Connect module metrics to UNIDA Insights</strong>
                        </div>
                    </div>

                    <div style="margin-top:18px;">
                        <a href="<?= e(BASE_URL); ?>admin/dashboard.php" class="btn btn-primary">
                            <i class="fa-solid fa-arrow-left"></i>
                            Back to Admin Dashboard
                        </a>
                    </div>
                </article>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
