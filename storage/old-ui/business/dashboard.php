<?php
$pageTitle = 'Business Dashboard';
$pageDescription = 'Business workspace for managing business profile, verification, readiness, documents, investment requests and stakeholder connections.';
$pageName = 'business-dashboard';
$activeSidebar = 'overview';

require_once __DIR__ . '/../includes/config.php';
require_role('business');

$pdo = db();

$userId = (int) ($_SESSION['user_id'] ?? 0);
$userName = $_SESSION['user_name'] ?? 'Business User';

function business_count($pdo, $table, $where = '', $params = []) {
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

function business_money_short($amount, $currency = 'TZS') {
    if (!$amount) {
        return $currency . ' 0';
    }

    $amount = (float) $amount;

    if ($amount >= 1000000000) {
        return $currency . ' ' . number_format($amount / 1000000000, 1) . 'B';
    }

    if ($amount >= 1000000) {
        return $currency . ' ' . number_format($amount / 1000000, 1) . 'M';
    }

    if ($amount >= 1000) {
        return $currency . ' ' . number_format($amount / 1000, 1) . 'K';
    }

    return $currency . ' ' . number_format($amount);
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

$businessName = $businessProfile['business_name'] ?? 'Business profile not completed';
$verificationStatus = $businessProfile['verification_status'] ?? 'not_submitted';
$readinessScore = (int) ($businessProfile['readiness_score'] ?? 0);

$fundingNeed = business_money_short(
    $businessProfile['funding_need_amount'] ?? 0,
    $businessProfile['funding_currency'] ?? 'TZS'
);

$totalDocuments = business_count($pdo, 'uploads', 'user_id = ?', [$userId]);
$approvedDocuments = business_count($pdo, 'uploads', "user_id = ? AND upload_status = 'approved'", [$userId]);
$pendingDocuments = business_count($pdo, 'uploads', "user_id = ? AND upload_status IN ('uploaded','under_review')", [$userId]);

$totalOpportunities = $businessProfileId
    ? business_count($pdo, 'investment_opportunities', 'business_profile_id = ?', [$businessProfileId])
    : 0;

$publishedOpportunities = $businessProfileId
    ? business_count($pdo, 'investment_opportunities', "business_profile_id = ? AND status = 'published'", [$businessProfileId])
    : 0;

$totalConnections = business_count($pdo, 'partner_connections', 'requester_user_id = ?', [$userId]);
$pendingConnections = business_count($pdo, 'partner_connections', "requester_user_id = ? AND status = 'pending'", [$userId]);
$unreadMessages = business_count($pdo, 'messages', 'receiver_id = ? AND is_read = 0', [$userId]);

$recentUploads = [];
$recentActivities = [];
$recentOpportunities = [];

try {
    $stmt = $pdo->prepare("
        SELECT original_name, upload_status, created_at
        FROM uploads
        WHERE user_id = ?
        ORDER BY created_at DESC
        LIMIT 4
    ");
    $stmt->execute([$userId]);
    $recentUploads = $stmt->fetchAll();
} catch (Throwable $e) {
    $recentUploads = [];
}

try {
    if ($businessProfileId > 0) {
        $stmt = $pdo->prepare("
            SELECT title, status, verification_status, readiness_score, created_at
            FROM investment_opportunities
            WHERE business_profile_id = ?
            ORDER BY created_at DESC
            LIMIT 4
        ");
        $stmt->execute([$businessProfileId]);
        $recentOpportunities = $stmt->fetchAll();
    }
} catch (Throwable $e) {
    $recentOpportunities = [];
}

try {
    $stmt = $pdo->prepare("
        SELECT action, module, description, created_at
        FROM activity_logs
        WHERE user_id = ?
        ORDER BY created_at DESC
        LIMIT 5
    ");
    $stmt->execute([$userId]);
    $recentActivities = $stmt->fetchAll();
} catch (Throwable $e) {
    $recentActivities = [];
}

$statusLabel = ucfirst(str_replace('_', ' ', $verificationStatus));

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

                <h1>Manage your business readiness and investment profile.</h1>

                <p>
                    Use this workspace to complete your business profile, upload documents, track verification,
                    improve readiness and manage investment opportunities through UNIDA Gateway.
                </p>
            </div>

            <div class="dashboard-profile-card">
                <div class="profile-avatar">
                    <?= e(strtoupper(substr($userName, 0, 2))); ?>
                </div>

                <div>
                    <h3><?= e($userName); ?></h3>
                    <p><?= e($businessName); ?></p>

                    <span class="status-badge <?= $verificationStatus === 'verified' ? 'status-verified' : 'status-progress'; ?>">
                        <i class="fa-solid <?= $verificationStatus === 'verified' ? 'fa-circle-check' : 'fa-spinner'; ?>"></i>
                        <?= e($statusLabel); ?>
                    </span>
                </div>
            </div>
        </div>
    </section>

    <section class="dashboard-shell">
        <div class="container dashboard-layout">
            <?php include __DIR__ . '/../includes/sidebar.php'; ?>

            <div class="dashboard-content">
                <?php if (!$businessProfile): ?>
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
                    <article class="dash-stat">
                        <span class="dash-icon"><i class="fa-solid fa-chart-line"></i></span>
                        <div>
                            <strong><?= e($readinessScore); ?>%</strong>
                            <small>Readiness score</small>
                        </div>
                    </article>

                    <article class="dash-stat">
                        <span class="dash-icon green"><i class="fa-solid fa-file-circle-check"></i></span>
                        <div>
                            <strong><?= e($approvedDocuments); ?> / <?= e($totalDocuments); ?></strong>
                            <small>Approved documents</small>
                        </div>
                    </article>

                    <article class="dash-stat">
                        <span class="dash-icon cyan"><i class="fa-solid fa-briefcase"></i></span>
                        <div>
                            <strong><?= e($totalOpportunities); ?></strong>
                            <small>Investment requests</small>
                        </div>
                    </article>

                    <article class="dash-stat">
                        <span class="dash-icon dark"><i class="fa-solid fa-handshake"></i></span>
                        <div>
                            <strong><?= e($totalConnections); ?></strong>
                            <small>Partner connections</small>
                        </div>
                    </article>
                </div>

                <div class="dashboard-main-grid">
                    <article class="dashboard-panel">
                        <div class="panel-head">
                            <div>
                                <h3>Readiness Progress</h3>
                                <p>Track how prepared your business is for investor or stakeholder review.</p>
                            </div>
                            <span class="status-badge status-progress"><?= e($readinessScore); ?>%</span>
                        </div>

                        <div class="funding-card">
                            <strong><?= e($readinessScore); ?>%</strong>
                            <span>Current readiness score</span>
                            <div class="progress-bar">
                                <span style="width:<?= e(max(0, min(100, $readinessScore))); ?>%;"></span>
                            </div>
                            <small>Improve your score by completing your profile, uploading required documents and defining funding needs clearly.</small>
                        </div>

                        <div style="margin-top:16px;">
                            <a href="<?= e(BASE_URL); ?>business/readiness.php" class="btn btn-soft">
                                <i class="fa-solid fa-arrow-trend-up"></i>
                                Improve Readiness
                            </a>
                        </div>
                    </article>

                    <article class="dashboard-panel">
                        <div class="panel-head">
                            <div>
                                <h3>Funding & Investment Request</h3>
                                <p>Your current funding need and opportunity visibility.</p>
                            </div>
                            <span class="status-badge status-open"><?= e($publishedOpportunities); ?> published</span>
                        </div>

                        <div class="funding-card">
                            <strong><?= e($fundingNeed); ?></strong>
                            <span><?= e($businessProfile['funding_purpose'] ?? 'Funding purpose not added'); ?></span>
                            <div class="progress-bar">
                                <span style="width:<?= e(max(0, min(100, $readinessScore))); ?>%;"></span>
                            </div>
                            <small><?= e($readinessScore); ?>% ready for review based on your current business profile.</small>
                        </div>

                        <div style="margin-top:16px;">
                            <a href="<?= e(BASE_URL); ?>business/opportunities.php" class="btn btn-soft">
                                <i class="fa-solid fa-briefcase"></i>
                                Manage Investment Requests
                            </a>
                        </div>
                    </article>
                </div>

                <div class="dashboard-main-grid">
                    <article class="dashboard-panel">
                        <div class="panel-head">
                            <div>
                                <h3>Document Status</h3>
                                <p>Recent documents submitted for verification and readiness review.</p>
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
                                <div class="task">
                                    <i class="fa-regular fa-circle"></i>
                                    <span>No documents uploaded yet.</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </article>

                    <article class="dashboard-panel">
                        <div class="panel-head">
                            <div>
                                <h3>Investment Opportunities</h3>
                                <p>Recent investment requests linked to your business profile.</p>
                            </div>
                            <span class="status-badge status-open"><?= e($totalOpportunities); ?> total</span>
                        </div>

                        <div class="pipeline-list">
                            <?php if (!empty($recentOpportunities)): ?>
                                <?php foreach ($recentOpportunities as $opportunity): ?>
                                    <div>
                                        <span>
                                            <?= e($opportunity['title']); ?><br>
                                            <small><?= e(str_replace('_', ' ', $opportunity['verification_status'])); ?> · <?= e($opportunity['readiness_score']); ?>% readiness</small>
                                        </span>
                                        <strong><?= e(ucfirst(str_replace('_', ' ', $opportunity['status']))); ?></strong>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div>
                                    <span>No investment requests created yet.</span>
                                    <strong>Start</strong>
                                </div>
                            <?php endif; ?>
                        </div>
                    </article>
                </div>

                <article class="dashboard-panel">
                    <div class="panel-head">
                        <div>
                            <h3>Workspace Summary</h3>
                            <p>Your current UNIDA Gateway business activity.</p>
                        </div>
                        <span class="status-badge status-open"><?= e($unreadMessages); ?> unread messages</span>
                    </div>

                    <div class="activity-list">
                        <?php if (!empty($recentActivities)): ?>
                            <?php foreach ($recentActivities as $activity): ?>
                                <div>
                                    <i class="fa-solid fa-clock-rotate-left"></i>
                                    <?= e($activity['action']); ?>
                                    <?php if (!empty($activity['module'])): ?>
                                        — <?= e($activity['module']); ?>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div>
                                <i class="fa-solid fa-circle-info"></i>
                                No recent activity yet. Start by completing your profile and uploading documents.
                            </div>
                        <?php endif; ?>

                        <?php if ($pendingConnections > 0): ?>
                            <div>
                                <i class="fa-solid fa-handshake"></i>
                                You have <?= e($pendingConnections); ?> pending partner connection request(s).
                            </div>
                        <?php endif; ?>
                    </div>
                </article>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
