<?php
$pageTitle = 'Shortlist';
$pageDescription = 'Manage saved opportunities, investor interests, notes and follow-up status.';
$pageName = 'investor-shortlist';

require_once __DIR__ . '/../includes/config.php';
require_role('investor');

$pdo = db();
$userId = (int) ($_SESSION['user_id'] ?? 0);

function investor_safe_count($pdo, $table, $where = '', $params = []) {
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

$investorProfile = null;

try {
    $stmt = $pdo->prepare("
        SELECT *
        FROM investor_profiles
        WHERE user_id = ?
        LIMIT 1
    ");
    $stmt->execute([$userId]);
    $investorProfile = $stmt->fetch();
} catch (Throwable $e) {
    $investorProfile = null;
}

include __DIR__ . '/../includes/header.php';
?>

<main class="dashboard-page">
    <section class="dashboard-hero investor">
        <div class="container dashboard-hero-grid">
            <div>
                <div class="page-kicker">
                    <i class="fa-solid fa-bookmark"></i>
                    Investor Workspace
                </div>

                <h1>Shortlist</h1>

                <p>Manage saved opportunities, investor interests, notes and follow-up status.</p>
            </div>

            <div class="dashboard-profile-card">
                <div class="profile-avatar investor-avatar">
                    <?= e(strtoupper(substr($_SESSION['user_name'] ?? 'IN', 0, 2))); ?>
                </div>

                <div>
                    <h3><?= e($_SESSION['user_name'] ?? 'Investor'); ?></h3>
                    <p><?= e($investorProfile['investor_name'] ?? 'Investor account'); ?></p>

                    <span class="status-badge status-verified">
                        <i class="fa-solid fa-circle-check"></i>
                        Investor workspace
                    </span>
                </div>
            </div>
        </div>
    </section>

    <section class="dashboard-shell">
        <div class="container dashboard-layout">
            <aside class="dashboard-sidebar">
                <a class="<?= $pageName === 'investor-dashboard' ? 'active' : ''; ?>" href="<?= e(BASE_URL); ?>investor/dashboard.php"><i class="fa-solid fa-gauge-high"></i> Overview</a>
                <a class="<?= $pageName === 'investor-profile' ? 'active' : ''; ?>" href="<?= e(BASE_URL); ?>investor/profile.php"><i class="fa-solid fa-user-tie"></i> Investor Profile</a>
                <a class="<?= $pageName === 'investor-discover' ? 'active' : ''; ?>" href="<?= e(BASE_URL); ?>investor/discover.php"><i class="fa-solid fa-magnifying-glass-chart"></i> Discover</a>
                <a class="<?= $pageName === 'investor-verified-businesses' ? 'active' : ''; ?>" href="<?= e(BASE_URL); ?>investor/verified-businesses.php"><i class="fa-solid fa-building-circle-check"></i> Verified Businesses</a>
                <a class="<?= $pageName === 'investor-shortlist' ? 'active' : ''; ?>" href="<?= e(BASE_URL); ?>investor/shortlist.php"><i class="fa-solid fa-bookmark"></i> Shortlist</a>
                <a class="<?= $pageName === 'investor-pipeline' ? 'active' : ''; ?>" href="<?= e(BASE_URL); ?>investor/pipeline.php"><i class="fa-solid fa-route"></i> Pipeline</a>
                <a class="<?= $pageName === 'investor-meetings' ? 'active' : ''; ?>" href="<?= e(BASE_URL); ?>investor/meetings.php"><i class="fa-solid fa-calendar-check"></i> Meetings</a>
                <a class="<?= $pageName === 'investor-insights' ? 'active' : ''; ?>" href="<?= e(BASE_URL); ?>investor/insights.php"><i class="fa-solid fa-chart-pie"></i> Insights</a>
                <a class="<?= $pageName === 'investor-messages' ? 'active' : ''; ?>" href="<?= e(BASE_URL); ?>investor/messages.php"><i class="fa-solid fa-message"></i> Messages</a>
                <a class="<?= $pageName === 'investor-settings' ? 'active' : ''; ?>" href="<?= e(BASE_URL); ?>investor/settings.php"><i class="fa-solid fa-gear"></i> Settings</a>
                <a href="<?= e(BASE_URL); ?>logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
            </aside>

            <div class="dashboard-content">
                <div class="cards-grid three-columns">
                    <article class="info-card">
                        <div class="icon-box"><i class="fa-solid fa-bookmark"></i></div>
                        <h3>Saved Opportunities</h3>
                        <p>Keep promising opportunities in one place.</p>
                    </article>
                    <article class="info-card">
                        <div class="icon-box"><i class="fa-solid fa-note-sticky"></i></div>
                        <h3>Investor Notes</h3>
                        <p>Record internal notes and follow-up observations.</p>
                    </article>
                    <article class="info-card">
                        <div class="icon-box"><i class="fa-solid fa-envelope-open-text"></i></div>
                        <h3>Follow-Up Status</h3>
                        <p>Track interested, contacted, meeting requested and in-review statuses.</p>
                    </article>
                </div>

                <?php
                $saved = investor_safe_count($pdo, 'investor_shortlists', 'investor_user_id = ?', [$userId]);
                $active = investor_safe_count($pdo, 'investor_shortlists', "investor_user_id = ? AND status IN ('interested','contacted','meeting_requested','in_review')", [$userId]);
                ?>

                <div class="dashboard-stat-grid">
                    <article class="dash-stat"><span class="dash-icon"><i class="fa-solid fa-bookmark"></i></span><div><strong><?= e($saved); ?></strong><small>Saved</small></div></article>
                    <article class="dash-stat"><span class="dash-icon green"><i class="fa-solid fa-fire"></i></span><div><strong><?= e($active); ?></strong><small>Active interests</small></div></article>
                    <article class="dash-stat"><span class="dash-icon cyan"><i class="fa-solid fa-calendar-check"></i></span><div><strong>0</strong><small>Meetings</small></div></article>
                    <article class="dash-stat"><span class="dash-icon dark"><i class="fa-solid fa-message"></i></span><div><strong>0</strong><small>Notes</small></div></article>
                </div>

                <article class="dashboard-panel">
                    <div class="panel-head">
                        <div>
                            <h3>Next Actions</h3>
                            <p>This module is ready for backend actions and database-driven listings.</p>
                        </div>

                        <span class="status-badge status-open">Investor</span>
                    </div>

                    <div class="pipeline-list">
                        <div><span>Discover</span><strong>Review opportunities by sector, region, stage and readiness</strong></div>
                        <div><span>Shortlist</span><strong>Save interesting opportunities for follow-up</strong></div>
                        <div><span>Connect</span><strong>Request meetings or additional information</strong></div>
                        <div><span>Track</span><strong>Monitor pipeline, meetings and messages</strong></div>
                    </div>

                    <div style="margin-top:18px;">
                        <a href="<?= e(BASE_URL); ?>investor/dashboard.php" class="btn btn-primary">
                            <i class="fa-solid fa-arrow-left"></i>
                            Back to Investor Overview
                        </a>
                    </div>
                </article>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
