<?php
$pageTitle = 'Stakeholder Dashboard';
$pageDescription = 'Stakeholder workspace for coordination, recommendations, business support, follow-ups and ecosystem insights.';
$pageName = 'stakeholder-dashboard';

require_once __DIR__ . '/../includes/config.php';
require_role('stakeholder');

$pdo = db();

$userId = (int) ($_SESSION['user_id'] ?? 0);
$userName = $_SESSION['user_name'] ?? 'Stakeholder';

function stakeholder_count($pdo, $table, $where = '', $params = []) {
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

$organizationName = $stakeholderProfile['organization_name'] ?? $userName;
$stakeholderType = $stakeholderProfile['stakeholder_type'] ?? 'other';
$profileStatus = $stakeholderProfile['profile_status'] ?? 'active';

$totalBusinesses = stakeholder_count($pdo, 'business_profiles');
$verifiedBusinesses = stakeholder_count($pdo, 'business_profiles', "verification_status = 'verified'");
$pendingBusinesses = stakeholder_count($pdo, 'business_profiles', "verification_status IN ('pending','needs_update','not_submitted')");
$totalOpportunities = stakeholder_count($pdo, 'investment_opportunities', "status IN ('published','under_review')");
$totalConnections = stakeholder_count($pdo, 'partner_connections', 'requester_user_id = ? OR receiver_user_id = ?', [$userId, $userId]);
$pendingConnections = stakeholder_count($pdo, 'partner_connections', "(requester_user_id = ? OR receiver_user_id = ?) AND status = 'pending'", [$userId, $userId]);
$unreadMessages = stakeholder_count($pdo, 'messages', 'receiver_id = ? AND is_read = 0', [$userId]);
$publishedInsights = stakeholder_count($pdo, 'insights', "status = 'published' AND visibility IN ('public','logged_in','stakeholders')");

$sectorActivity = [];
$recentConnections = [];
$recentInsights = [];

try {
    $stmt = $pdo->query("
        SELECT sector, COUNT(*) AS total
        FROM business_profiles
        GROUP BY sector
        ORDER BY total DESC
        LIMIT 5
    ");
    $sectorActivity = $stmt->fetchAll();
} catch (Throwable $e) {
    $sectorActivity = [];
}

try {
    $stmt = $pdo->prepare("
        SELECT 
            pc.subject,
            pc.connection_type,
            pc.status,
            pc.created_at,
            u.full_name AS requester_name
        FROM partner_connections pc
        LEFT JOIN users u ON u.id = pc.requester_user_id
        WHERE pc.requester_user_id = ? OR pc.receiver_user_id = ?
        ORDER BY pc.created_at DESC
        LIMIT 5
    ");
    $stmt->execute([$userId, $userId]);
    $recentConnections = $stmt->fetchAll();
} catch (Throwable $e) {
    $recentConnections = [];
}

try {
    $stmt = $pdo->query("
        SELECT title, insight_type, sector, region, published_at
        FROM insights
        WHERE status = 'published' AND visibility IN ('public','logged_in','stakeholders')
        ORDER BY published_at DESC, created_at DESC
        LIMIT 5
    ");
    $recentInsights = $stmt->fetchAll();
} catch (Throwable $e) {
    $recentInsights = [];
}

include __DIR__ . '/../includes/header.php';
?>

<main class="dashboard-page">
    <section class="dashboard-hero">
        <div class="container dashboard-hero-grid">
            <div>
                <div class="page-kicker">
                    <i class="fa-solid fa-users-gear"></i>
                    Stakeholder Workspace
                </div>

                <h1>Coordinate support, recommendations and ecosystem follow-up.</h1>

                <p>
                    Use this workspace to review business readiness, coordinate referrals, connect businesses with the
                    right support channels, monitor ecosystem activity and access UNIDA Insights.
                </p>
            </div>

            <div class="dashboard-profile-card">
                <div class="profile-avatar">
                    <?= e(strtoupper(substr($organizationName, 0, 2))); ?>
                </div>

                <div>
                    <h3><?= e($organizationName); ?></h3>
                    <p><?= e(ucfirst(str_replace('_', ' ', $stakeholderType))); ?> account</p>

                    <span class="status-badge <?= $profileStatus === 'active' ? 'status-verified' : 'status-progress'; ?>">
                        <i class="fa-solid <?= $profileStatus === 'active' ? 'fa-circle-check' : 'fa-spinner'; ?>"></i>
                        <?= e(ucfirst($profileStatus)); ?>
                    </span>
                </div>
            </div>
        </div>
    </section>

    <section class="dashboard-shell">
        <div class="container dashboard-layout">
            <aside class="dashboard-sidebar">
                <a class="active" href="<?= e(BASE_URL); ?>stakeholder/dashboard.php">
                    <i class="fa-solid fa-gauge-high"></i>
                    Overview
                </a>

                <a href="<?= e(BASE_URL); ?>stakeholder/profile.php">
                    <i class="fa-solid fa-building-columns"></i>
                    Organization Profile
                </a>

                <a href="<?= e(BASE_URL); ?>stakeholder/businesses.php">
                    <i class="fa-solid fa-building"></i>
                    Businesses
                </a>

                <a href="<?= e(BASE_URL); ?>stakeholder/recommendations.php">
                    <i class="fa-solid fa-handshake-angle"></i>
                    Recommendations
                </a>

                <a href="<?= e(BASE_URL); ?>stakeholder/connections.php">
                    <i class="fa-solid fa-handshake"></i>
                    Partner Connections
                </a>

                <a href="<?= e(BASE_URL); ?>stakeholder/follow-ups.php">
                    <i class="fa-solid fa-calendar-check"></i>
                    Follow-ups
                </a>

                <a href="<?= e(BASE_URL); ?>stakeholder/insights.php">
                    <i class="fa-solid fa-chart-pie"></i>
                    Insights
                </a>

                <a href="<?= e(BASE_URL); ?>stakeholder/reports.php">
                    <i class="fa-solid fa-file-lines"></i>
                    Reports
                </a>

                <a href="<?= e(BASE_URL); ?>stakeholder/messages.php">
                    <i class="fa-solid fa-message"></i>
                    Messages
                </a>

                <a href="<?= e(BASE_URL); ?>stakeholder/settings.php">
                    <i class="fa-solid fa-gear"></i>
                    Settings
                </a>

                <a href="<?= e(BASE_URL); ?>logout.php">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    Logout
                </a>
            </aside>

            <div class="dashboard-content">
                <?php if (!$stakeholderProfile): ?>
                    <article class="dashboard-panel">
                        <div class="panel-head">
                            <div>
                                <h3>Complete your organization profile</h3>
                                <p>
                                    Your account is active, but your stakeholder profile is not completed yet.
                                    Add your organization type, focus areas, regions covered and support services.
                                </p>
                            </div>

                            <span class="status-badge status-progress">
                                Action needed
                            </span>
                        </div>

                        <a href="<?= e(BASE_URL); ?>stakeholder/profile.php" class="btn btn-primary">
                            <i class="fa-solid fa-building-columns"></i>
                            Complete Organization Profile
                        </a>
                    </article>
                <?php endif; ?>

                <div class="dashboard-stat-grid">
                    <article class="dash-stat">
                        <span class="dash-icon">
                            <i class="fa-solid fa-building"></i>
                        </span>
                        <div>
                            <strong><?= e($totalBusinesses); ?></strong>
                            <small>Businesses in ecosystem</small>
                        </div>
                    </article>

                    <article class="dash-stat">
                        <span class="dash-icon green">
                            <i class="fa-solid fa-circle-check"></i>
                        </span>
                        <div>
                            <strong><?= e($verifiedBusinesses); ?></strong>
                            <small>Verified businesses</small>
                        </div>
                    </article>

                    <article class="dash-stat">
                        <span class="dash-icon cyan">
                            <i class="fa-solid fa-handshake"></i>
                        </span>
                        <div>
                            <strong><?= e($totalConnections); ?></strong>
                            <small>Partner connections</small>
                        </div>
                    </article>

                    <article class="dash-stat">
                        <span class="dash-icon dark">
                            <i class="fa-solid fa-chart-pie"></i>
                        </span>
                        <div>
                            <strong><?= e($publishedInsights); ?></strong>
                            <small>Available insights</small>
                        </div>
                    </article>
                </div>

                <div class="dashboard-main-grid">
                    <article class="dashboard-panel">
                        <div class="panel-head">
                            <div>
                                <h3>Business Review Queue</h3>
                                <p>Businesses that may need support, verification or readiness follow-up.</p>
                            </div>

                            <span class="status-badge status-progress">
                                <?= e($pendingBusinesses); ?> pending
                            </span>
                        </div>

                        <div class="pipeline-list">
                            <?php if (!empty($sectorActivity)): ?>
                                <?php foreach ($sectorActivity as $sector): ?>
                                    <div>
                                        <span><?= e($sector['sector'] ?: 'Unspecified sector'); ?></span>
                                        <strong><?= e($sector['total']); ?> businesses</strong>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div>
                                    <span>No business activity yet.</span>
                                    <strong>Empty</strong>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div style="margin-top:16px;">
                            <a href="<?= e(BASE_URL); ?>stakeholder/businesses.php" class="btn btn-soft">
                                <i class="fa-solid fa-building"></i>
                                Review Businesses
                            </a>
                        </div>
                    </article>

                    <article class="dashboard-panel">
                        <div class="panel-head">
                            <div>
                                <h3>Coordination Summary</h3>
                                <p>Track referrals, recommendations and collaboration actions.</p>
                            </div>

                            <span class="status-badge status-open">
                                <?= e($pendingConnections); ?> pending
                            </span>
                        </div>

                        <div class="pipeline-list">
                            <div>
                                <span>Active opportunities</span>
                                <strong><?= e($totalOpportunities); ?></strong>
                            </div>

                            <div>
                                <span>Partner connections</span>
                                <strong><?= e($totalConnections); ?></strong>
                            </div>

                            <div>
                                <span>Unread messages</span>
                                <strong><?= e($unreadMessages); ?></strong>
                            </div>

                            <div>
                                <span>Pending referrals</span>
                                <strong><?= e($pendingConnections); ?></strong>
                            </div>
                        </div>
                    </article>
                </div>

                <div class="dashboard-main-grid">
                    <article class="dashboard-panel">
                        <div class="panel-head">
                            <div>
                                <h3>Recent Partner Connections</h3>
                                <p>Latest coordination records connected to your account.</p>
                            </div>

                            <span class="status-badge status-open">
                                Coordination
                            </span>
                        </div>

                        <div class="task-list">
                            <?php if (!empty($recentConnections)): ?>
                                <?php foreach ($recentConnections as $connection): ?>
                                    <div class="task <?= $connection['status'] === 'completed' ? 'done' : ''; ?>">
                                        <i class="fa-solid <?= $connection['status'] === 'completed' ? 'fa-circle-check' : 'fa-handshake'; ?>"></i>
                                        <span>
                                            <?= e($connection['subject']); ?>
                                            —
                                            <?= e(str_replace('_', ' ', $connection['status'])); ?>
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="task">
                                    <i class="fa-regular fa-circle"></i>
                                    <span>No partner connections yet.</span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div style="margin-top:16px;">
                            <a href="<?= e(BASE_URL); ?>stakeholder/connections.php" class="btn btn-soft">
                                <i class="fa-solid fa-handshake"></i>
                                Manage Connections
                            </a>
                        </div>
                    </article>

                    <article class="dashboard-panel">
                        <div class="panel-head">
                            <div>
                                <h3>UNIDA Insights</h3>
                                <p>Latest ecosystem updates, reports and analytics available to stakeholders.</p>
                            </div>

                            <span class="status-badge status-progress">
                                Reports
                            </span>
                        </div>

                        <div class="pipeline-list">
                            <?php if (!empty($recentInsights)): ?>
                                <?php foreach ($recentInsights as $insight): ?>
                                    <div>
                                        <span>
                                            <?= e($insight['title']); ?>
                                            <br>
                                            <small>
                                                <?= e(str_replace('_', ' ', $insight['insight_type'])); ?>
                                                <?php if (!empty($insight['sector'])): ?>
                                                    · <?= e($insight['sector']); ?>
                                                <?php endif; ?>
                                            </small>
                                        </span>

                                        <strong><?= e($insight['region'] ?: 'General'); ?></strong>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div>
                                    <span>No stakeholder insights published yet.</span>
                                    <strong>Coming soon</strong>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div style="margin-top:16px;">
                            <a href="<?= e(BASE_URL); ?>stakeholder/insights.php" class="btn btn-soft">
                                <i class="fa-solid fa-chart-pie"></i>
                                View Insights
                            </a>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
