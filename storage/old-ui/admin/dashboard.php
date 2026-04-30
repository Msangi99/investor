<?php
$pageTitle = 'Admin Dashboard';
$pageDescription = 'Admin dashboard for managing users, verification, roles, reports, insights and platform settings.';
$pageName = 'admin-dashboard';

require_once __DIR__ . '/../includes/config.php';
require_role('admin');

$pdo = db();

function count_table($pdo, $table, $where = '') {
    try {
        $sql = "SELECT COUNT(*) AS total FROM {$table}";
        if ($where !== '') {
            $sql .= " WHERE {$where}";
        }

        $stmt = $pdo->query($sql);
        $row = $stmt->fetch();

        return (int) ($row['total'] ?? 0);
    } catch (Throwable $e) {
        return 0;
    }
}

$totalUsers = count_table($pdo, 'users');
$totalBusinesses = count_table($pdo, 'business_profiles');
$totalInvestors = count_table($pdo, 'investor_profiles');
$totalStakeholders = count_table($pdo, 'stakeholder_profiles');

$pendingVerifications = count_table($pdo, 'verification_requests', "status IN ('pending','in_review')");
$verifiedProfiles = count_table($pdo, 'verification_requests', "status = 'verified'");
$pendingDocuments = count_table($pdo, 'uploads', "upload_status IN ('uploaded','under_review')");
$totalOpportunities = count_table($pdo, 'investment_opportunities');
$totalInsights = count_table($pdo, 'insights');
$totalMessages = count_table($pdo, 'contact_messages', "status = 'new'");

$recentUsers = [];
$recentVerifications = [];
$recentActivities = [];

try {
    $stmt = $pdo->query("
        SELECT id, full_name, email, role, status, created_at
        FROM users
        ORDER BY created_at DESC
        LIMIT 5
    ");
    $recentUsers = $stmt->fetchAll();
} catch (Throwable $e) {
    $recentUsers = [];
}

try {
    $stmt = $pdo->query("
        SELECT 
            vr.id,
            vr.request_type,
            vr.status,
            vr.submitted_at,
            u.full_name,
            u.email
        FROM verification_requests vr
        LEFT JOIN users u ON u.id = vr.user_id
        ORDER BY vr.submitted_at DESC
        LIMIT 5
    ");
    $recentVerifications = $stmt->fetchAll();
} catch (Throwable $e) {
    $recentVerifications = [];
}

try {
    $stmt = $pdo->query("
        SELECT action, module, description, created_at
        FROM activity_logs
        ORDER BY created_at DESC
        LIMIT 5
    ");
    $recentActivities = $stmt->fetchAll();
} catch (Throwable $e) {
    $recentActivities = [];
}

include __DIR__ . '/../includes/header.php';
?>

<main class="dashboard-page">
    <section class="dashboard-hero admin">
        <div class="container dashboard-hero-grid">
            <div>
                <div class="page-kicker">
                    <i class="fa-solid fa-user-shield"></i>
                    Admin Workspace
                </div>

                <h1>Manage UNIDA Gateway users, verification, insights and ecosystem activity.</h1>

                <p>
                    This workspace gives administrators visibility and control over users, roles, businesses,
                    investors, documents, verification requests, opportunities, reports and platform settings.
                </p>
            </div>

            <div class="dashboard-profile-card">
                <div class="profile-avatar admin-avatar">
                    <?= e(strtoupper(substr($_SESSION['user_name'] ?? 'AD', 0, 2))); ?>
                </div>

                <div>
                    <h3><?= e($_SESSION['user_name'] ?? 'System Admin'); ?></h3>
                    <p>Administrative workspace</p>

                    <span class="status-badge status-verified">
                        <i class="fa-solid fa-shield"></i>
                        Full access
                    </span>
                </div>
            </div>
        </div>
    </section>

    <section class="dashboard-shell">
        <div class="container dashboard-layout">
            <aside class="dashboard-sidebar">
                <a class="active" href="<?= e(BASE_URL); ?>admin/dashboard.php">
                    <i class="fa-solid fa-gauge-high"></i>
                    Overview
                </a>

                <a href="<?= e(BASE_URL); ?>admin/users.php">
                    <i class="fa-solid fa-users"></i>
                    Users
                </a>

                <a href="<?= e(BASE_URL); ?>admin/roles.php">
                    <i class="fa-solid fa-user-lock"></i>
                    Roles & Permissions
                </a>

                <a href="<?= e(BASE_URL); ?>admin/verifications.php">
                    <i class="fa-solid fa-file-shield"></i>
                    Verifications
                </a>

                <a href="<?= e(BASE_URL); ?>admin/businesses.php">
                    <i class="fa-solid fa-building"></i>
                    Businesses
                </a>

                <a href="<?= e(BASE_URL); ?>admin/investors.php">
                    <i class="fa-solid fa-coins"></i>
                    Investors
                </a>

                <a href="<?= e(BASE_URL); ?>admin/stakeholders.php">
                    <i class="fa-solid fa-users-gear"></i>
                    Stakeholders
                </a>

                <a href="<?= e(BASE_URL); ?>admin/opportunities.php">
                    <i class="fa-solid fa-briefcase"></i>
                    UNIDA Invest
                </a>

                <a href="<?= e(BASE_URL); ?>admin/insights.php">
                    <i class="fa-solid fa-chart-pie"></i>
                    UNIDA Insights
                </a>

                <a href="<?= e(BASE_URL); ?>admin/uploads.php">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                    Uploads
                </a>

                <a href="<?= e(BASE_URL); ?>admin/messages.php">
                    <i class="fa-solid fa-envelope"></i>
                    Messages
                </a>

                <a href="<?= e(BASE_URL); ?>admin/settings.php">
                    <i class="fa-solid fa-gear"></i>
                    Settings
                </a>

                <a href="<?= e(BASE_URL); ?>logout.php">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    Logout
                </a>
            </aside>

            <div class="dashboard-content">
                <div class="dashboard-stat-grid">
                    <article class="dash-stat">
                        <span class="dash-icon">
                            <i class="fa-solid fa-users"></i>
                        </span>
                        <div>
                            <strong><?= e($totalUsers); ?></strong>
                            <small>Total users</small>
                        </div>
                    </article>

                    <article class="dash-stat">
                        <span class="dash-icon green">
                            <i class="fa-solid fa-file-circle-check"></i>
                        </span>
                        <div>
                            <strong><?= e($pendingVerifications); ?></strong>
                            <small>Pending verifications</small>
                        </div>
                    </article>

                    <article class="dash-stat">
                        <span class="dash-icon cyan">
                            <i class="fa-solid fa-building"></i>
                        </span>
                        <div>
                            <strong><?= e($totalBusinesses); ?></strong>
                            <small>Business profiles</small>
                        </div>
                    </article>

                    <article class="dash-stat">
                        <span class="dash-icon dark">
                            <i class="fa-solid fa-coins"></i>
                        </span>
                        <div>
                            <strong><?= e($totalInvestors); ?></strong>
                            <small>Investor profiles</small>
                        </div>
                    </article>
                </div>

                <div class="dashboard-stat-grid">
                    <article class="dash-stat">
                        <span class="dash-icon">
                            <i class="fa-solid fa-users-gear"></i>
                        </span>
                        <div>
                            <strong><?= e($totalStakeholders); ?></strong>
                            <small>Stakeholders</small>
                        </div>
                    </article>

                    <article class="dash-stat">
                        <span class="dash-icon green">
                            <i class="fa-solid fa-shield-halved"></i>
                        </span>
                        <div>
                            <strong><?= e($verifiedProfiles); ?></strong>
                            <small>Verified records</small>
                        </div>
                    </article>

                    <article class="dash-stat">
                        <span class="dash-icon cyan">
                            <i class="fa-solid fa-briefcase"></i>
                        </span>
                        <div>
                            <strong><?= e($totalOpportunities); ?></strong>
                            <small>Opportunities</small>
                        </div>
                    </article>

                    <article class="dash-stat">
                        <span class="dash-icon dark">
                            <i class="fa-solid fa-chart-pie"></i>
                        </span>
                        <div>
                            <strong><?= e($totalInsights); ?></strong>
                            <small>Insights & reports</small>
                        </div>
                    </article>
                </div>

                <div class="dashboard-main-grid">
                    <article class="dashboard-panel">
                        <div class="panel-head">
                            <div>
                                <h3>Admin Modules</h3>
                                <p>Core areas for managing UNIDA Gateway.</p>
                            </div>

                            <span class="status-badge status-open">
                                Active
                            </span>
                        </div>

                        <div class="pipeline-list">
                            <div>
                                <span>UNIDA Invest</span>
                                <strong>Opportunities, shortlists and investor pipeline</strong>
                            </div>

                            <div>
                                <span>UNIDA Verify</span>
                                <strong>Documents, verification requests and review status</strong>
                            </div>

                            <div>
                                <span>UNIDA Readiness</span>
                                <strong>Readiness checklist, score and business preparation</strong>
                            </div>

                            <div>
                                <span>UNIDA Partners</span>
                                <strong>Stakeholder coordination and partner connections</strong>
                            </div>

                            <div>
                                <span>UNIDA Insights</span>
                                <strong>Updates, reports, analytics and decision-support data</strong>
                            </div>
                        </div>
                    </article>

                    <article class="dashboard-panel">
                        <div class="panel-head">
                            <div>
                                <h3>Verification Queue</h3>
                                <p>Recent verification requests from users.</p>
                            </div>

                            <span class="status-badge status-progress">
                                <?= e($pendingVerifications); ?> pending
                            </span>
                        </div>

                        <div class="task-list">
                            <?php if (!empty($recentVerifications)): ?>
                                <?php foreach ($recentVerifications as $request): ?>
                                    <div class="task">
                                        <i class="fa-regular fa-circle"></i>
                                        <span>
                                            <?= e($request['full_name'] ?? 'Unknown user'); ?>
                                            —
                                            <?= e($request['request_type']); ?>
                                            —
                                            <?= e($request['status']); ?>
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="task done">
                                    <i class="fa-solid fa-circle-info"></i>
                                    <span>No verification requests yet.</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </article>
                </div>

                <div class="dashboard-main-grid">
                    <article class="dashboard-panel">
                        <div class="panel-head">
                            <div>
                                <h3>Recent Users</h3>
                                <p>Latest registered platform accounts.</p>
                            </div>

                            <span class="status-badge status-open">
                                <?= e($totalUsers); ?> users
                            </span>
                        </div>

                        <div class="pipeline-list">
                            <?php if (!empty($recentUsers)): ?>
                                <?php foreach ($recentUsers as $user): ?>
                                    <div>
                                        <span>
                                            <?= e($user['full_name']); ?>
                                            <br>
                                            <small><?= e($user['email']); ?></small>
                                        </span>

                                        <strong>
                                            <?= e(ucfirst($user['role'])); ?>
                                            ·
                                            <?= e($user['status']); ?>
                                        </strong>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div>
                                    <span>No users found.</span>
                                    <strong>Empty</strong>
                                </div>
                            <?php endif; ?>
                        </div>
                    </article>

                    <article class="dashboard-panel">
                        <div class="panel-head">
                            <div>
                                <h3>System Overview</h3>
                                <p>Important system areas to monitor.</p>
                            </div>

                            <span class="status-badge status-verified">
                                Live
                            </span>
                        </div>

                        <div class="pipeline-list">
                            <div>
                                <span>Pending document reviews</span>
                                <strong><?= e($pendingDocuments); ?></strong>
                            </div>

                            <div>
                                <span>New contact messages</span>
                                <strong><?= e($totalMessages); ?></strong>
                            </div>

                            <div>
                                <span>Published opportunities</span>
                                <strong><?= e($totalOpportunities); ?></strong>
                            </div>

                            <div>
                                <span>Insights and reports</span>
                                <strong><?= e($totalInsights); ?></strong>
                            </div>
                        </div>
                    </article>
                </div>

                <article class="dashboard-panel">
                    <div class="panel-head">
                        <div>
                            <h3>Recent Activity</h3>
                            <p>Latest system activities and audit events.</p>
                        </div>

                        <span class="status-badge status-open">
                            Audit Trail
                        </span>
                    </div>

                    <div class="activity-list">
                        <?php if (!empty($recentActivities)): ?>
                            <?php foreach ($recentActivities as $activity): ?>
                                <div>
                                    <i class="fa-solid fa-clock-rotate-left"></i>
                                    <?= e($activity['action']); ?>
                                    <?php if (!empty($activity['module'])): ?>
                                        —
                                        <?= e($activity['module']); ?>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div>
                                <i class="fa-solid fa-circle-info"></i>
                                No activity logs yet.
                            </div>
                        <?php endif; ?>
                    </div>
                </article>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>