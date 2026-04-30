<?php
$pageTitle = 'Messages';
$pageDescription = 'Manage messages, support communication, partner coordination and business follow-up conversations.';
$pageName = 'stakeholder-messages';

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
                    <i class="fa-solid fa-message"></i>
                    Stakeholder Workspace
                </div>

                <h1>Messages</h1>

                <p>Manage messages, support communication, partner coordination and business follow-up conversations.</p>
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
                        <div class="icon-box"><i class="fa-solid fa-inbox"></i></div>
                        <h3>Inbox</h3>
                        <p>Receive messages from businesses, admins, investors and partners.</p>
                    </article>
                    <article class="info-card">
                        <div class="icon-box"><i class="fa-solid fa-reply"></i></div>
                        <h3>Replies</h3>
                        <p>Respond to referrals, support requests and coordination messages.</p>
                    </article>
                    <article class="info-card">
                        <div class="icon-box"><i class="fa-solid fa-bell"></i></div>
                        <h3>Notifications</h3>
                        <p>Track platform updates, verification and connection activity.</p>
                    </article>
                </div>

                <?php
                $messages = stakeholder_safe_count($pdo, 'messages', 'receiver_id = ?', [$userId]);
                $unread = stakeholder_safe_count($pdo, 'messages', 'receiver_id = ? AND is_read = 0', [$userId]);
                ?>

                <div class="dashboard-stat-grid">
                    <article class="dash-stat"><span class="dash-icon"><i class="fa-solid fa-envelope"></i></span><div><strong><?= e($messages); ?></strong><small>Total messages</small></div></article>
                    <article class="dash-stat"><span class="dash-icon green"><i class="fa-solid fa-envelope-open"></i></span><div><strong><?= e($unread); ?></strong><small>Unread</small></div></article>
                    <article class="dash-stat"><span class="dash-icon cyan"><i class="fa-solid fa-bell"></i></span><div><strong>0</strong><small>Notifications</small></div></article>
                    <article class="dash-stat"><span class="dash-icon dark"><i class="fa-solid fa-headset"></i></span><div><strong>Support</strong><small>Available</small></div></article>
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
