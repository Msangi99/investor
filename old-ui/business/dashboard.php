<?php
$pageTitle = 'Business Dashboard';
$pageDescription = 'Business workspace for profile, verification, readiness, documents, opportunities and connections.';
$pageName = 'business-dashboard';
$activeSidebar = 'overview';

require_once __DIR__ . '/../includes/config.php';
require_role('business');

$pdo = db();
$userId = (int) ($_SESSION['user_id'] ?? 0);
$userName = $_SESSION['user_name'] ?? 'Business User';

function biz_table_exists($table) {
    try {
        $stmt = db()->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        return (bool) $stmt->fetch();
    } catch (Throwable $e) {
        return false;
    }
}

function biz_count($table, $where = '', $params = []) {
    try {
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $table) || !biz_table_exists($table)) {
            return 0;
        }
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
    if (biz_table_exists('business_profiles')) {
        $stmt = $pdo->prepare("SELECT * FROM business_profiles WHERE user_id = ? LIMIT 1");
        $stmt->execute([$userId]);
        $profile = $stmt->fetch();
    }
} catch (Throwable $e) {
    $profile = null;
}

$businessId = (int) ($profile['id'] ?? 0);
$businessName = $profile['business_name'] ?? 'Business profile not completed';
$verificationStatus = $profile['verification_status'] ?? 'unverified';
$readinessScore = (int) ($profile['readiness_score'] ?? 0);

$totalDocuments = biz_count('uploads', 'user_id = ?', [$userId]);
$approvedDocuments = biz_count('uploads', "user_id = ? AND upload_status = 'approved'", [$userId]);
$pendingDocuments = biz_count('uploads', "user_id = ? AND upload_status IN ('uploaded','submitted','pending','under_review')", [$userId]);
$totalOpportunities = $businessId ? biz_count('investment_opportunities', 'business_profile_id = ?', [$businessId]) : 0;
$totalConnections = biz_count('partner_connections', 'requester_user_id = ?', [$userId]);
$unreadMessages = biz_count('messages', 'receiver_id = ? AND is_read = 0', [$userId]);

$recentUploads = [];
$recentActivities = [];

try {
    if (biz_table_exists('uploads')) {
        $stmt = $pdo->prepare("SELECT original_name, upload_status, created_at FROM uploads WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
        $stmt->execute([$userId]);
        $recentUploads = $stmt->fetchAll();
    }
} catch (Throwable $e) {}

try {
    if (biz_table_exists('activity_logs')) {
        $stmt = $pdo->prepare("SELECT action, module, description, created_at FROM activity_logs WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
        $stmt->execute([$userId]);
        $recentActivities = $stmt->fetchAll();
    }
} catch (Throwable $e) {}

include __DIR__ . '/../includes/header.php';
?>

<main class="dashboard-page">
    <section class="dashboard-hero">
        <div class="container dashboard-hero-grid">
            <div>
                <div class="page-kicker"><i class="fa-solid fa-briefcase"></i> Business Workspace</div>
                <h1>Manage your business readiness and verification journey.</h1>
                <p>Complete your profile, upload required documents, improve readiness and publish opportunities after approval.</p>
            </div>

            <div class="dashboard-profile-card">
                <div class="profile-avatar"><?= e(strtoupper(substr($userName, 0, 2))); ?></div>
                <div>
                    <h3><?= e($userName); ?></h3>
                    <p><?= e($businessName); ?></p>
                    <span class="status-badge <?= $verificationStatus === 'verified' ? 'status-verified' : 'status-progress'; ?>">
                        <i class="fa-solid <?= $verificationStatus === 'verified' ? 'fa-circle-check' : 'fa-spinner'; ?>"></i>
                        <?= e(ucfirst(str_replace('_', ' ', $verificationStatus))); ?>
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
                                <h3>Complete your business profile</h3>
                                <p>Your account is active, but your business profile is not completed yet.</p>
                            </div>
                            <span class="status-badge status-progress">Action needed</span>
                        </div>
                        <a href="<?= e(BASE_URL); ?>business/profile.php" class="btn btn-primary">
                            <i class="fa-solid fa-building"></i>
                            Complete Business Profile
                        </a>
                    </article>
                <?php endif; ?>

                <div class="dashboard-stat-grid">
                    <article class="dash-stat"><span class="dash-icon"><i class="fa-solid fa-chart-line"></i></span><div><strong><?= e($readinessScore); ?>%</strong><small>Readiness score</small></div></article>
                    <article class="dash-stat"><span class="dash-icon green"><i class="fa-solid fa-file-circle-check"></i></span><div><strong><?= e($approvedDocuments); ?> / <?= e($totalDocuments); ?></strong><small>Approved documents</small></div></article>
                    <article class="dash-stat"><span class="dash-icon cyan"><i class="fa-solid fa-briefcase"></i></span><div><strong><?= e($totalOpportunities); ?></strong><small>Investment requests</small></div></article>
                    <article class="dash-stat"><span class="dash-icon dark"><i class="fa-solid fa-message"></i></span><div><strong><?= e($unreadMessages); ?></strong><small>Unread messages</small></div></article>
                </div>

                <div class="dashboard-main-grid">
                    <article class="dashboard-panel">
                        <div class="panel-head">
                            <div>
                                <h3>Readiness Progress</h3>
                                <p>Track how prepared your business is for review.</p>
                            </div>
                            <span class="status-badge status-progress"><?= e($readinessScore); ?>%</span>
                        </div>

                        <div class="funding-card">
                            <strong><?= e($readinessScore); ?>%</strong>
                            <span>Current readiness score</span>
                            <div class="progress-bar"><span style="width:<?= e(max(0, min(100, $readinessScore))); ?>%;"></span></div>
                            <small>Improve your score by completing profile, documents and funding/support details.</small>
                        </div>
                    </article>

                    <article class="dashboard-panel">
                        <div class="panel-head">
                            <div>
                                <h3>Document Status</h3>
                                <p>Recent uploaded documents and verification statuses.</p>
                            </div>
                            <span class="status-badge status-progress"><?= e($pendingDocuments); ?> pending</span>
                        </div>

                        <div class="task-list">
                            <?php if (!empty($recentUploads)): ?>
                                <?php foreach ($recentUploads as $upload): ?>
                                    <div class="task <?= $upload['upload_status'] === 'approved' ? 'done' : ''; ?>">
                                        <i class="fa-solid <?= $upload['upload_status'] === 'approved' ? 'fa-circle-check' : 'fa-file'; ?>"></i>
                                        <span><?= e($upload['original_name']); ?> — <?= e(str_replace('_', ' ', $upload['upload_status'])); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="task"><i class="fa-regular fa-circle"></i><span>No documents uploaded yet.</span></div>
                            <?php endif; ?>
                        </div>
                    </article>
                </div>

                <article class="dashboard-panel">
                    <div class="panel-head">
                        <div>
                            <h3>Next Actions</h3>
                            <p>Recommended steps for your business account.</p>
                        </div>
                        <span class="status-badge status-open"><?= e($totalConnections); ?> connections</span>
                    </div>

                    <div class="cards-grid three-columns">
                        <article class="info-card"><div class="icon-box"><i class="fa-solid fa-building"></i></div><h3>Profile</h3><p>Update business information, address, industry and stage.</p><a class="btn btn-soft" href="<?= e(BASE_URL); ?>business/profile.php">Open</a></article>
                        <article class="info-card"><div class="icon-box"><i class="fa-solid fa-file-shield"></i></div><h3>Documents</h3><p>Upload required documents for verification.</p><a class="btn btn-soft" href="<?= e(BASE_URL); ?>business/documents.php">Open</a></article>
                        <article class="info-card"><div class="icon-box"><i class="fa-solid fa-briefcase"></i></div><h3>Opportunities</h3><p>Create investment or support requests after verification.</p><a class="btn btn-soft" href="<?= e(BASE_URL); ?>business/opportunities.php">Open</a></article>
                    </div>
                </article>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>