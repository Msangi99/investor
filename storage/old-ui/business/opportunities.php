<?php
$pageTitle = 'Investment Requests';
$pageDescription = 'Create and manage your investment requests, funding needs, opportunity status and investor visibility.';
$pageName = 'business-opportunities';

require_once __DIR__ . '/../includes/config.php';
require_role('business');

$pdo = db();

$userId = (int) ($_SESSION['user_id'] ?? 0);

function business_safe_count($pdo, $table, $where = '', $params = []) {
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

$businessProfile = null;

try {
    $stmt = $pdo->prepare("
        SELECT *
        FROM business_profiles
        WHERE user_id = ?
        LIMIT 1
    ");
    $stmt->execute([$userId]);
    $businessProfile = $stmt->fetch();
} catch (Throwable $e) {
    $businessProfile = null;
}

$businessProfileId = (int) ($businessProfile['id'] ?? 0);

include __DIR__ . '/../includes/header.php';
?>

<main class="dashboard-page">
    <section class="dashboard-hero">
        <div class="container dashboard-hero-grid">
            <div>
                <div class="page-kicker">
                    <i class="fa-solid fa-briefcase"></i>
                    Business Workspace
                </div>

                <h1>Investment Requests</h1>

                <p>Create and manage your investment requests, funding needs, opportunity status and investor visibility.</p>
            </div>

            <div class="dashboard-profile-card">
                <div class="profile-avatar">
                    <?= e(strtoupper(substr($_SESSION['user_name'] ?? 'BU', 0, 2))); ?>
                </div>

                <div>
                    <h3><?= e($_SESSION['user_name'] ?? 'Business User'); ?></h3>
                    <p><?= e($businessProfile['business_name'] ?? 'Business account'); ?></p>

                    <span class="status-badge status-progress">
                        <i class="fa-solid fa-briefcase"></i>
                        Business workspace
                    </span>
                </div>
            </div>
        </div>
    </section>

    <section class="dashboard-shell">
        <div class="container dashboard-layout">
            <aside class="dashboard-sidebar">
                <a class="<?= $pageName === 'business-dashboard' ? 'active' : ''; ?>" href="<?= e(BASE_URL); ?>business/dashboard.php">
                    <i class="fa-solid fa-gauge-high"></i>
                    Overview
                </a>

                <a class="<?= $pageName === 'business-profile' ? 'active' : ''; ?>" href="<?= e(BASE_URL); ?>business/profile.php">
                    <i class="fa-solid fa-building"></i>
                    Business Profile
                </a>

                <a class="<?= $pageName === 'business-documents' ? 'active' : ''; ?>" href="<?= e(BASE_URL); ?>business/documents.php">
                    <i class="fa-solid fa-file-shield"></i>
                    Documents
                </a>

                <a class="<?= $pageName === 'business-readiness' ? 'active' : ''; ?>" href="<?= e(BASE_URL); ?>business/readiness.php">
                    <i class="fa-solid fa-chart-line"></i>
                    Readiness
                </a>

                <a class="<?= $pageName === 'business-opportunities' ? 'active' : ''; ?>" href="<?= e(BASE_URL); ?>business/opportunities.php">
                    <i class="fa-solid fa-briefcase"></i>
                    Investment Requests
                </a>

                <a class="<?= $pageName === 'business-connections' ? 'active' : ''; ?>" href="<?= e(BASE_URL); ?>business/connections.php">
                    <i class="fa-solid fa-handshake"></i>
                    Connections
                </a>

                <a class="<?= $pageName === 'business-insights' ? 'active' : ''; ?>" href="<?= e(BASE_URL); ?>business/insights.php">
                    <i class="fa-solid fa-chart-pie"></i>
                    Insights
                </a>

                <a class="<?= $pageName === 'business-messages' ? 'active' : ''; ?>" href="<?= e(BASE_URL); ?>business/messages.php">
                    <i class="fa-solid fa-message"></i>
                    Messages
                </a>

                <a class="<?= $pageName === 'business-settings' ? 'active' : ''; ?>" href="<?= e(BASE_URL); ?>business/settings.php">
                    <i class="fa-solid fa-gear"></i>
                    Settings
                </a>

                <a href="<?= e(BASE_URL); ?>logout.php">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    Logout
                </a>
            </aside>

            <div class="dashboard-content">
                <div class="cards-grid three-columns">
                    <article class="info-card">
                        <div class="icon-box"><i class="fa-solid fa-lightbulb"></i></div>
                        <h3>Create Opportunity</h3>
                        <p>Publish a structured investment request connected to your business profile.</p>
                    </article>
                    <article class="info-card">
                        <div class="icon-box"><i class="fa-solid fa-route"></i></div>
                        <h3>Pipeline Status</h3>
                        <p>Track draft, published, under review, closed or archived investment requests.</p>
                    </article>
                    <article class="info-card">
                        <div class="icon-box"><i class="fa-solid fa-eye"></i></div>
                        <h3>Investor Visibility</h3>
                        <p>Make your opportunity easier to review by investors and partners after verification.</p>
                    </article>
                </div>

                <?php
                $totalOpportunities = $businessProfileId ? business_safe_count($pdo, 'investment_opportunities', 'business_profile_id = ?', [$businessProfileId]) : 0;
                $publishedOpportunities = $businessProfileId ? business_safe_count($pdo, 'investment_opportunities', "business_profile_id = ? AND status = 'published'", [$businessProfileId]) : 0;
                ?>

                <div class="dashboard-stat-grid">
                    <article class="dash-stat"><span class="dash-icon"><i class="fa-solid fa-briefcase"></i></span><div><strong><?= e($totalOpportunities); ?></strong><small>Total requests</small></div></article>
                    <article class="dash-stat"><span class="dash-icon green"><i class="fa-solid fa-eye"></i></span><div><strong><?= e($publishedOpportunities); ?></strong><small>Published</small></div></article>
                    <article class="dash-stat"><span class="dash-icon cyan"><i class="fa-solid fa-coins"></i></span><div><strong><?= e($businessProfile['funding_currency'] ?? 'TZS'); ?></strong><small>Currency</small></div></article>
                    <article class="dash-stat"><span class="dash-icon dark"><i class="fa-solid fa-chart-line"></i></span><div><strong><?= e($businessProfile['business_stage'] ?? 'mvp'); ?></strong><small>Business stage</small></div></article>
                </div>

                <article class="dashboard-panel">
                    <div class="panel-head">
                        <div>
                            <h3>Next Actions</h3>
                            <p>This module is ready for backend actions and database integration.</p>
                        </div>

                        <span class="status-badge status-open">
                            Business
                        </span>
                    </div>

                    <div class="pipeline-list">
                        <div>
                            <span>Complete profile</span>
                            <strong>Keep business information accurate and updated</strong>
                        </div>

                        <div>
                            <span>Upload documents</span>
                            <strong>Support verification and investment readiness</strong>
                        </div>

                        <div>
                            <span>Track status</span>
                            <strong>Monitor readiness, verification and opportunities</strong>
                        </div>

                        <div>
                            <span>Connect</span>
                            <strong>Reach investors, partners, institutions and support providers</strong>
                        </div>
                    </div>

                    <div style="margin-top:18px;">
                        <a href="<?= e(BASE_URL); ?>business/dashboard.php" class="btn btn-primary">
                            <i class="fa-solid fa-arrow-left"></i>
                            Back to Business Overview
                        </a>
                    </div>
                </article>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
