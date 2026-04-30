<?php
$pageTitle = 'Documents';
$pageDescription = 'Upload, organize and track business documents required for verification and investment readiness.';
$pageName = 'business-documents';

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
                    <i class="fa-solid fa-file-shield"></i>
                    Business Workspace
                </div>

                <h1>Documents</h1>

                <p>Upload, organize and track business documents required for verification and investment readiness.</p>
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
                        <div class="icon-box"><i class="fa-solid fa-cloud-arrow-up"></i></div>
                        <h3>Upload Documents</h3>
                        <p>Submit registration certificate, tax documents, IDs, pitch deck and financial summaries.</p>
                    </article>
                    <article class="info-card">
                        <div class="icon-box"><i class="fa-solid fa-file-circle-check"></i></div>
                        <h3>Document Status</h3>
                        <p>Track uploaded, under review, approved, needs update or rejected document status.</p>
                    </article>
                    <article class="info-card">
                        <div class="icon-box"><i class="fa-solid fa-shield-halved"></i></div>
                        <h3>Secure Records</h3>
                        <p>Keep documents organized for reviewers, investors and authorized stakeholders.</p>
                    </article>
                </div>

                <?php
                $totalUploads = business_safe_count($pdo, 'uploads', 'user_id = ?', [$userId]);
                $approvedUploads = business_safe_count($pdo, 'uploads', "user_id = ? AND upload_status = 'approved'", [$userId]);
                $pendingUploads = business_safe_count($pdo, 'uploads', "user_id = ? AND upload_status IN ('uploaded','under_review')", [$userId]);
                ?>

                <div class="dashboard-stat-grid">
                    <article class="dash-stat"><span class="dash-icon"><i class="fa-solid fa-file"></i></span><div><strong><?= e($totalUploads); ?></strong><small>Total documents</small></div></article>
                    <article class="dash-stat"><span class="dash-icon green"><i class="fa-solid fa-check"></i></span><div><strong><?= e($approvedUploads); ?></strong><small>Approved</small></div></article>
                    <article class="dash-stat"><span class="dash-icon cyan"><i class="fa-solid fa-clock"></i></span><div><strong><?= e($pendingUploads); ?></strong><small>Pending review</small></div></article>
                    <article class="dash-stat"><span class="dash-icon dark"><i class="fa-solid fa-folder"></i></span><div><strong>8</strong><small>Document types</small></div></article>
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
