<?php
$pageTitle = 'Settings';
$pageDescription = 'Manage account preferences, investor access, security and communication settings.';
$pageName = 'investor-settings';

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
                    <i class="fa-solid fa-gear"></i>
                    Investor Workspace
                </div>

                <h1>Settings</h1>

                <p>Manage account preferences, investor access, security and communication settings.</p>
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
                        <div class="icon-box"><i class="fa-solid fa-user-gear"></i></div>
                        <h3>Account Settings</h3>
                        <p>Update account details and investor profile information.</p>
                    </article>
                    <article class="info-card">
                        <div class="icon-box"><i class="fa-solid fa-lock"></i></div>
                        <h3>Security</h3>
                        <p>Prepare password, sessions and login security controls.</p>
                    </article>
                    <article class="info-card">
                        <div class="icon-box"><i class="fa-solid fa-bell"></i></div>
                        <h3>Notifications</h3>
                        <p>Manage future email and platform notification preferences.</p>
                    </article>
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
