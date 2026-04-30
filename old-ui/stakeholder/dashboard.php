<?php
$pageTitle = 'Stakeholder Dashboard';
$pageDescription = 'Stakeholder workspace for organization profile, businesses, recommendations, connections, reports and insights.';
$pageName = 'stakeholder-dashboard';
$activeSidebar = 'overview';

require_once __DIR__ . '/../includes/config.php';
require_role('stakeholder');

$pdo = db();
$userId = (int) ($_SESSION['user_id'] ?? 0);
$userName = $_SESSION['user_name'] ?? 'Stakeholder';

function st_table_exists($table) {
    try {
        $stmt = db()->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        return (bool) $stmt->fetch();
    } catch (Throwable $e) {
        return false;
    }
}

function st_count($table, $where = '', $params = []) {
    try {
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $table) || !st_table_exists($table)) return 0;
        $sql = "SELECT COUNT(*) AS total FROM {$table}";
        if ($where !== '') $sql .= " WHERE {$where}";
        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return (int) ($row['total'] ?? 0);
    } catch (Throwable $e) {
        return 0;
    }
}

$profile = null;
try {
    if (st_table_exists('stakeholder_profiles')) {
        $stmt = $pdo->prepare("SELECT * FROM stakeholder_profiles WHERE user_id = ? LIMIT 1");
        $stmt->execute([$userId]);
        $profile = $stmt->fetch();
    }
} catch (Throwable $e) {}

$profileStatus = $profile['profile_status'] ?? 'incomplete';
$organizationName = $profile['organization_name'] ?? 'Organization profile not completed';
$stakeholderType = $profile['stakeholder_type'] ?? 'Stakeholder';

$totalBusinesses = st_count('business_profiles');
$verifiedBusinesses = st_count('business_profiles', "verification_status = 'verified'");
$recommendations = st_count('stakeholder_recommendations', 'stakeholder_user_id = ?', [$userId]);
$connections = st_count('partner_connections', 'receiver_user_id = ? OR requester_user_id = ?', [$userId, $userId]);
$reports = st_count('reports', 'user_id = ?', [$userId]);
$unreadMessages = st_count('messages', 'receiver_id = ? AND is_read = 0', [$userId]);

$recentBusinesses = [];

try {
    if (st_table_exists('business_profiles')) {
        $recentBusinesses = $pdo->query("
            SELECT business_name, industry, region, verification_status, readiness_score
            FROM business_profiles
            ORDER BY id DESC
            LIMIT 5
        ")->fetchAll();
    }
} catch (Throwable $e) {}

include __DIR__ . '/../includes/header.php';
?>

<main class="dashboard-page">
    <section class="dashboard-hero stakeholder">
        <div class="container dashboard-hero-grid">
            <div>
                <div class="page-kicker"><i class="fa-solid fa-building-columns"></i> Stakeholder Workspace</div>
                <h1>Coordinate support, recommendations and ecosystem connections.</h1>
                <p>Use this workspace to review businesses, send recommendations, manage connections, follow-ups, reports and insights.</p>
            </div>

            <div class="dashboard-profile-card">
                <div class="profile-avatar"><?= e(strtoupper(substr($userName, 0, 2))); ?></div>
                <div>
                    <h3><?= e($organizationName); ?></h3>
                    <p><?= e(ucfirst(str_replace('_', ' ', $stakeholderType))); ?></p>
                    <span class="status-badge <?= in_array($profileStatus, ['active','verified'], true) ? 'status-verified' : 'status-progress'; ?>">
                        <i class="fa-solid <?= in_array($profileStatus, ['active','verified'], true) ? 'fa-circle-check' : 'fa-spinner'; ?>"></i>
                        <?= e(ucfirst(str_replace('_', ' ', $profileStatus))); ?>
                    </span>
                </div>
            </div>
        </div>
    </section>

    <section class="dashboard-shell">
        <div class="container dashboard-layout">
            <?php include __DIR__ . '/../includes/sidebar.php'; ?>

            <div class="dashboard-content">
                <?php if (!$profile): ?>
                    <article class="dashboard-panel">
                        <div class="panel-head">
                            <div>
                                <h3>Complete your organization profile</h3>
                                <p>Add stakeholder category, coverage, support services, focus areas and authorization documents.</p>
                            </div>
                            <span class="status-badge status-progress">Action needed</span>
                        </div>
                        <a class="btn btn-primary" href="<?= e(BASE_URL); ?>stakeholder/profile.php"><i class="fa-solid fa-building-columns"></i> Complete Organization Profile</a>
                    </article>
                <?php endif; ?>

                <div class="dashboard-stat-grid">
                    <article class="dash-stat"><span class="dash-icon"><i class="fa-solid fa-building"></i></span><div><strong><?= e($totalBusinesses); ?></strong><small>Total businesses</small></div></article>
                    <article class="dash-stat"><span class="dash-icon green"><i class="fa-solid fa-circle-check"></i></span><div><strong><?= e($verifiedBusinesses); ?></strong><small>Verified businesses</small></div></article>
                    <article class="dash-stat"><span class="dash-icon cyan"><i class="fa-solid fa-handshake-angle"></i></span><div><strong><?= e($recommendations); ?></strong><small>Recommendations</small></div></article>
                    <article class="dash-stat"><span class="dash-icon dark"><i class="fa-solid fa-file-lines"></i></span><div><strong><?= e($reports); ?></strong><small>Reports</small></div></article>
                </div>

                <div class="dashboard-main-grid">
                    <article class="dashboard-panel">
                        <div class="panel-head">
                            <div>
                                <h3>Businesses to Review</h3>
                                <p>Recent businesses available for stakeholder support and coordination.</p>
                            </div>
                            <span class="status-badge status-open"><?= e($connections); ?> connections</span>
                        </div>

                        <div class="pipeline-list">
                            <?php if (!empty($recentBusinesses)): ?>
                                <?php foreach ($recentBusinesses as $business): ?>
                                    <div>
                                        <span><?= e($business['business_name']); ?><br><small><?= e($business['industry'] ?? 'Industry'); ?> · <?= e($business['region'] ?? 'Region'); ?></small></span>
                                        <strong><?= e((int) ($business['readiness_score'] ?? 0)); ?>%</strong>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div><span>No business profiles found yet.</span><strong>Empty</strong></div>
                            <?php endif; ?>
                        </div>
                    </article>

                    <article class="dashboard-panel">
                        <div class="panel-head">
                            <div>
                                <h3>Stakeholder Actions</h3>
                                <p>Quick actions for coordination and support.</p>
                            </div>
                            <span class="status-badge status-open"><?= e($unreadMessages); ?> messages</span>
                        </div>

                        <div class="cards-grid two-columns">
                            <article class="info-card"><div class="icon-box"><i class="fa-solid fa-building"></i></div><h3>Review Businesses</h3><p>View businesses needing support.</p><a class="btn btn-soft" href="<?= e(BASE_URL); ?>stakeholder/businesses.php">Open</a></article>
                            <article class="info-card"><div class="icon-box"><i class="fa-solid fa-handshake-angle"></i></div><h3>Recommendations</h3><p>Send support or referral recommendations.</p><a class="btn btn-soft" href="<?= e(BASE_URL); ?>stakeholder/recommendations.php">Open</a></article>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>