<?php
$pageTitle = 'Investor Dashboard';
$pageDescription = 'Investor workspace for verified opportunities, preferences, shortlist, pipeline and meetings.';
$pageName = 'investor-dashboard';
$activeSidebar = 'overview';

require_once __DIR__ . '/../includes/config.php';
require_role('investor');

$pdo = db();
$userId = (int) ($_SESSION['user_id'] ?? 0);
$userName = $_SESSION['user_name'] ?? 'Investor';

function inv_table_exists($table) {
    try {
        $stmt = db()->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        return (bool) $stmt->fetch();
    } catch (Throwable $e) {
        return false;
    }
}

function inv_count($table, $where = '', $params = []) {
    try {
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $table) || !inv_table_exists($table)) return 0;
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
    if (inv_table_exists('investor_profiles')) {
        $stmt = $pdo->prepare("SELECT * FROM investor_profiles WHERE user_id = ? LIMIT 1");
        $stmt->execute([$userId]);
        $profile = $stmt->fetch();
    }
} catch (Throwable $e) {}

$profileStatus = $profile['profile_status'] ?? 'incomplete';
$investorName = $profile['investor_name'] ?? $userName;
$investorType = $profile['investor_type'] ?? 'Investor';

$verifiedOpportunities = inv_count('investment_opportunities', "status = 'published' AND verification_status = 'verified'");
$totalOpportunities = inv_count('investment_opportunities', "status = 'published'");
$shortlist = inv_count('investor_shortlists', 'investor_user_id = ?', [$userId]);
$meetings = inv_count('meetings', 'user_id = ?', [$userId]);
$unreadMessages = inv_count('messages', 'receiver_id = ? AND is_read = 0', [$userId]);

$featuredOpportunities = [];

try {
    if (inv_table_exists('investment_opportunities')) {
        $featuredOpportunities = $pdo->query("
            SELECT title, sector, region, stage, readiness_score, verification_status
            FROM investment_opportunities
            WHERE status = 'published'
            ORDER BY readiness_score DESC, created_at DESC
            LIMIT 5
        ")->fetchAll();
    }
} catch (Throwable $e) {}

include __DIR__ . '/../includes/header.php';
?>

<main class="dashboard-page">
    <section class="dashboard-hero investor">
        <div class="container dashboard-hero-grid">
            <div>
                <div class="page-kicker"><i class="fa-solid fa-coins"></i> Investor Workspace</div>
                <h1>Discover verified opportunities and manage your investment pipeline.</h1>
                <p>Use your workspace to review verified businesses, shortlist opportunities, request meetings and track your pipeline.</p>
            </div>

            <div class="dashboard-profile-card">
                <div class="profile-avatar investor-avatar"><?= e(strtoupper(substr($userName, 0, 2))); ?></div>
                <div>
                    <h3><?= e($investorName); ?></h3>
                    <p><?= e(ucfirst(str_replace('_', ' ', $investorType))); ?></p>
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
                                <h3>Complete your investor profile</h3>
                                <p>Add investor type, preferences, ticket size, mandate and verification documents.</p>
                            </div>
                            <span class="status-badge status-progress">Action needed</span>
                        </div>
                        <a class="btn btn-primary" href="<?= e(BASE_URL); ?>investor/profile.php"><i class="fa-solid fa-user-tie"></i> Complete Investor Profile</a>
                    </article>
                <?php endif; ?>

                <div class="dashboard-stat-grid">
                    <article class="dash-stat"><span class="dash-icon"><i class="fa-solid fa-briefcase"></i></span><div><strong><?= e($totalOpportunities); ?></strong><small>Published opportunities</small></div></article>
                    <article class="dash-stat"><span class="dash-icon green"><i class="fa-solid fa-circle-check"></i></span><div><strong><?= e($verifiedOpportunities); ?></strong><small>Verified opportunities</small></div></article>
                    <article class="dash-stat"><span class="dash-icon cyan"><i class="fa-solid fa-bookmark"></i></span><div><strong><?= e($shortlist); ?></strong><small>Shortlisted</small></div></article>
                    <article class="dash-stat"><span class="dash-icon dark"><i class="fa-solid fa-calendar-check"></i></span><div><strong><?= e($meetings); ?></strong><small>Meetings</small></div></article>
                </div>

                <div class="dashboard-main-grid">
                    <article class="dashboard-panel">
                        <div class="panel-head">
                            <div>
                                <h3>Featured Opportunities</h3>
                                <p>Published opportunities based on readiness and verification.</p>
                            </div>
                            <span class="status-badge status-open"><?= e($unreadMessages); ?> messages</span>
                        </div>

                        <div class="pipeline-list">
                            <?php if (!empty($featuredOpportunities)): ?>
                                <?php foreach ($featuredOpportunities as $opportunity): ?>
                                    <div>
                                        <span><?= e($opportunity['title']); ?><br><small><?= e($opportunity['sector']); ?> · <?= e($opportunity['region']); ?></small></span>
                                        <strong><?= e((int) $opportunity['readiness_score']); ?>%</strong>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div><span>No published opportunities yet.</span><strong>Empty</strong></div>
                            <?php endif; ?>
                        </div>
                    </article>

                    <article class="dashboard-panel">
                        <div class="panel-head">
                            <div>
                                <h3>Investor Actions</h3>
                                <p>Quick actions for investor workflow.</p>
                            </div>
                            <span class="status-badge status-progress">Access</span>
                        </div>

                        <div class="cards-grid two-columns">
                            <article class="info-card"><div class="icon-box"><i class="fa-solid fa-magnifying-glass-chart"></i></div><h3>Discover</h3><p>Find verified opportunities.</p><a class="btn btn-soft" href="<?= e(BASE_URL); ?>investor/discover.php">Open</a></article>
                            <article class="info-card"><div class="icon-box"><i class="fa-solid fa-bookmark"></i></div><h3>Shortlist</h3><p>Save opportunities for review.</p><a class="btn btn-soft" href="<?= e(BASE_URL); ?>investor/shortlist.php">Open</a></article>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>