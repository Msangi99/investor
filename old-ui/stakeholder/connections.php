<?php
$pageTitle = 'Partner Connections';
$pageDescription = 'Manage collaboration records, referrals, accepted connections and coordination actions.';
$pageName = 'stakeholder-connections';

require_once __DIR__ . '/../includes/config.php';
require_role('stakeholder');

$pdo = db();
$userId = (int) ($_SESSION['user_id'] ?? 0);

function stakeholder_safe_count($pdo, $table, $where = '', $params = []) {
    try {
        $sql = "SELECT COUNT(*) AS total FROM {$table}";
        if ($where !== '') {
            $sql .= " WHERE {$where}";
        }
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return (int) ($row['total'] ?? 0);
    } catch (Throwable $e) {
        return 0;
    }
}

$stakeholderProfile = null;

try {
    $stmt = $pdo->prepare("
        SELECT *
        FROM stakeholder_profiles
        WHERE user_id = ?
        LIMIT 1
    ");
    $stmt->execute([$userId]);
    $stakeholderProfile = $stmt->fetch();
} catch (Throwable $e) {
    $stakeholderProfile = null;
}

include __DIR__ . '/../includes/header.php';
?>

<main class="dashboard-page">
    <section class="dashboard-hero">
        <div class="container dashboard-hero-grid">
            <div>
                <div class="page-kicker">
                    <i class="fa-solid fa-handshake"></i>
                    Stakeholder Workspace
                </div>

                <h1>Partner Connections</h1>

                <p>Manage collaboration records, referrals, accepted connections and coordination actions.</p>
            </div>

            <div class="dashboard-profile-card">
                <div class="profile-avatar">
                    <?= e(strtoupper(substr($_SESSION['user_name'] ?? 'ST', 0, 2))); ?>
                </div>

                <div>
                    <h3><?= e($_SESSION['user_name'] ?? 'Stakeholder'); ?></h3>
                    <p><?= e($stakeholderProfile['organization_name'] ?? 'Stakeholder account'); ?></p>

                    <span class="status-badge status-verified">
                        <i class="fa-solid fa-circle-check"></i>
                        Stakeholder workspace
                    </span>
                </div>
            </div>
        </div>
    </section>

    <section class="dashboard-shell">
        <div class="container dashboard-layout">
            <aside class="dashboard-sidebar">
                <a class="<?= $pageName === 'stakeholder-dashboard' ? 'active' : ''; ?>" href="<?= e(BASE_URL); ?>stakeholder/dashboard.php"><i class="fa-solid fa-gauge-high"></i> Overview</a>
                <a class="<?= $pageName === 'stakeholder-profile' ? 'active' : ''; ?>" href="<?= e(BASE_URL); ?>stakeholder/profile.php"><i class="fa-solid fa-building-columns"></i> Organization Profile</a>
                <a class="<?= $pageName === 'stakeholder-businesses' ? 'active' : ''; ?>" href="<?= e(BASE_URL); ?>stakeholder/businesses.php"><i class="fa-solid fa-building"></i> Businesses</a>
                <a class="<?= $pageName === 'stakeholder-recommendations' ? 'active' : ''; ?>" href="<?= e(BASE_URL); ?>stakeholder/recommendations.php"><i class="fa-solid fa-handshake-angle"></i> Recommendations</a>
                <a class="<?= $pageName === 'stakeholder-connections' ? 'active' : ''; ?>" href="<?= e(BASE_URL); ?>stakeholder/connections.php"><i class="fa-solid fa-handshake"></i> Partner Connections</a>
                <a class="<?= $pageName === 'stakeholder-follow-ups' ? 'active' : ''; ?>" href="<?= e(BASE_URL); ?>stakeholder/follow-ups.php"><i class="fa-solid fa-calendar-check"></i> Follow-ups</a>
                <a class="<?= $pageName === 'stakeholder-insights' ? 'active' : ''; ?>" href="<?= e(BASE_URL); ?>stakeholder/insights.php"><i class="fa-solid fa-chart-pie"></i> Insights</a>
                <a class="<?= $pageName === 'stakeholder-reports' ? 'active' : ''; ?>" href="<?= e(BASE_URL); ?>stakeholder/reports.php"><i class="fa-solid fa-file-lines"></i> Reports</a>
                <a class="<?= $pageName === 'stakeholder-messages' ? 'active' : ''; ?>" href="<?= e(BASE_URL); ?>stakeholder/messages.php"><i class="fa-solid fa-message"></i> Messages</a>
                <a class="<?= $pageName === 'stakeholder-settings' ? 'active' : ''; ?>" href="<?= e(BASE_URL); ?>stakeholder/settings.php"><i class="fa-solid fa-gear"></i> Settings</a>
                <a href="<?= e(BASE_URL); ?>logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
            </aside>

            <div class="dashboard-content">
                <div class="cards-grid three-columns">
                    <article class="info-card">
                        <div class="icon-box"><i class="fa-solid fa-handshake"></i></div>
                        <h3>Connections</h3>
                        <p>Track relationships between businesses, investors, institutions and support partners.</p>
                    </article>
                    <article class="info-card">
                        <div class="icon-box"><i class="fa-solid fa-route"></i></div>
                        <h3>Coordination Status</h3>
                        <p>Monitor pending, accepted, in-progress, completed and closed connections.</p>
                    </article>
                    <article class="info-card">
                        <div class="icon-box"><i class="fa-solid fa-message"></i></div>
                        <h3>Collaboration Notes</h3>
                        <p>Keep messages and follow-up activity organized.</p>
                    </article>
                </div>

                <?php
                $connections = stakeholder_safe_count($pdo, 'partner_connections', 'requester_user_id = ? OR receiver_user_id = ?', [$userId, $userId]);
                $pending = stakeholder_safe_count($pdo, 'partner_connections', "(requester_user_id = ? OR receiver_user_id = ?) AND status = 'pending'", [$userId, $userId]);
                $completed = stakeholder_safe_count($pdo, 'partner_connections', "(requester_user_id = ? OR receiver_user_id = ?) AND status = 'completed'", [$userId, $userId]);
                ?>

                <div class="dashboard-stat-grid">
                    <article class="dash-stat"><span class="dash-icon"><i class="fa-solid fa-handshake"></i></span><div><strong><?= e($connections); ?></strong><small>Total connections</small></div></article>
                    <article class="dash-stat"><span class="dash-icon green"><i class="fa-solid fa-clock"></i></span><div><strong><?= e($pending); ?></strong><small>Pending</small></div></article>
                    <article class="dash-stat"><span class="dash-icon cyan"><i class="fa-solid fa-circle-check"></i></span><div><strong><?= e($completed); ?></strong><small>Completed</small></div></article>
                    <article class="dash-stat"><span class="dash-icon dark"><i class="fa-solid fa-message"></i></span><div><strong>Notes</strong><small>Coordination</small></div></article>
                </div>

                <article class="dashboard-panel">
                    <div class="panel-head">
                        <div>
                            <h3>Next Actions</h3>
                            <p>This module is ready for backend actions and database-driven listings.</p>
                        </div>

                        <span class="status-badge status-open">Stakeholder</span>
                    </div>

                    <div class="pipeline-list">
                        <div><span>Review</span><strong>Check business readiness and ecosystem activity</strong></div>
                        <div><span>Recommend</span><strong>Connect businesses to the right support channels</strong></div>
                        <div><span>Coordinate</span><strong>Track referrals, meetings, notes and follow-ups</strong></div>
                        <div><span>Report</span><strong>Use insights and reports for better decision making</strong></div>
                    </div>

                    <div style="margin-top:18px;">
                        <a href="<?= e(BASE_URL); ?>stakeholder/dashboard.php" class="btn btn-primary">
                            <i class="fa-solid fa-arrow-left"></i>
                            Back to Stakeholder Overview
                        </a>
                    </div>
                </article>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
