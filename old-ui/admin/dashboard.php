<?php
$pageTitle = 'Admin Dashboard';
$pageDescription = 'Admin control center for users, verifications, roles, opportunities, insights and system activity.';
$pageName = 'admin-dashboard';
$activeSidebar = 'overview';

require_once __DIR__ . '/../includes/config.php';
require_role('admin');

$pdo = db();

function admin_dash_table_exists($table) {
    try {
        $stmt = db()->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        return (bool) $stmt->fetch();
    } catch (Throwable $e) {
        return false;
    }
}

function admin_dash_count($table, $where = '', $params = []) {
    try {
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $table) || !admin_dash_table_exists($table)) {
            return 0;
        }

        $sql = "SELECT COUNT(*) AS total FROM {$table}";
        if ($where !== '') {
            $sql .= " WHERE {$where}";
        }

        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();

        return (int) ($row['total'] ?? 0);
    } catch (Throwable $e) {
        return 0;
    }
}

$userId = (int) ($_SESSION['user_id'] ?? 0);
$userName = $_SESSION['user_name'] ?? 'Admin';

$adminProfile = null;

try {
    if (admin_dash_table_exists('admin_profiles')) {
        $stmt = $pdo->prepare("SELECT * FROM admin_profiles WHERE user_id = ? LIMIT 1");
        $stmt->execute([$userId]);
        $adminProfile = $stmt->fetch();
    }
} catch (Throwable $e) {
    $adminProfile = null;
}

$adminRole = $adminProfile['admin_role'] ?? 'ADMIN';
$permissionGroup = $adminProfile['permission_group'] ?? 'General Admin Access';

$totalUsers = admin_dash_count('users');
$totalAdmins = admin_dash_count('users', "role = 'admin'");
$totalBusinesses = admin_dash_count('business_profiles');
$totalInvestors = admin_dash_count('investor_profiles');
$totalStakeholders = admin_dash_count('stakeholder_profiles');
$totalOpportunities = admin_dash_count('investment_opportunities');
$pendingUploads = admin_dash_count('uploads', "upload_status IN ('uploaded','submitted','pending','under_review')");
$unreadMessages = admin_dash_count('messages', "is_read = 0");

$recentUsers = [];
$recentActivities = [];

try {
    if (admin_dash_table_exists('users')) {
        $recentUsers = $pdo->query("
            SELECT id, full_name, email, role, status, created_at
            FROM users
            ORDER BY id DESC
            LIMIT 6
        ")->fetchAll();
    }
} catch (Throwable $e) {
    $recentUsers = [];
}

try {
    if (admin_dash_table_exists('activity_logs')) {
        $recentActivities = $pdo->query("
            SELECT action, module, description, created_at
            FROM activity_logs
            ORDER BY created_at DESC
            LIMIT 6
        ")->fetchAll();
    }
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
                    Admin Control Center
                </div>

                <h1>Manage users, verification, roles, opportunities and ecosystem activity.</h1>

                <p>
                    This workspace helps administrators manage UNIDA Gateway users, verification workflows,
                    permissions, dashboards, insights and platform activity.
                </p>
            </div>

            <div class="dashboard-profile-card">
                <div class="profile-avatar admin-avatar">
                    <?= e(strtoupper(substr($userName, 0, 2))); ?>
                </div>

                <div>
                    <h3><?= e($userName); ?></h3>
                    <p><?= e(str_replace('_', ' ', $adminRole)); ?></p>

                    <span class="status-badge <?= $adminRole === 'SUPER_ADMIN' ? 'status-verified' : 'status-open'; ?>">
                        <i class="fa-solid <?= $adminRole === 'SUPER_ADMIN' ? 'fa-crown' : 'fa-user-lock'; ?>"></i>
                        <?= e($permissionGroup); ?>
                    </span>
                </div>
            </div>
        </div>
    </section>

    <section class="dashboard-shell">
        <div class="container dashboard-layout">
            <?php include __DIR__ . '/../includes/sidebar.php'; ?>

            <div class="dashboard-content">
                <div class="dashboard-stat-grid">
                    <article class="dash-stat"><span class="dash-icon"><i class="fa-solid fa-users"></i></span><div><strong><?= e($totalUsers); ?></strong><small>Total users</small></div></article>
                    <article class="dash-stat"><span class="dash-icon green"><i class="fa-solid fa-building"></i></span><div><strong><?= e($totalBusinesses); ?></strong><small>Businesses</small></div></article>
                    <article class="dash-stat"><span class="dash-icon cyan"><i class="fa-solid fa-coins"></i></span><div><strong><?= e($totalInvestors); ?></strong><small>Investors</small></div></article>
                    <article class="dash-stat"><span class="dash-icon dark"><i class="fa-solid fa-users-gear"></i></span><div><strong><?= e($totalStakeholders); ?></strong><small>Stakeholders</small></div></article>
                </div>

                <div class="dashboard-main-grid">
                    <article class="dashboard-panel">
                        <div class="panel-head">
                            <div>
                                <h3>Platform Overview</h3>
                                <p>Core system records and pending activity.</p>
                            </div>
                            <span class="status-badge status-open">System</span>
                        </div>

                        <div class="pipeline-list">
                            <div><span>Admin accounts</span><strong><?= e($totalAdmins); ?></strong></div>
                            <div><span>Investment opportunities</span><strong><?= e($totalOpportunities); ?></strong></div>
                            <div><span>Pending document reviews</span><strong><?= e($pendingUploads); ?></strong></div>
                            <div><span>Unread messages</span><strong><?= e($unreadMessages); ?></strong></div>
                        </div>
                    </article>

                    <article class="dashboard-panel">
                        <div class="panel-head">
                            <div>
                                <h3>Admin Role & Permissions</h3>
                                <p>Your current admin profile and access group.</p>
                            </div>
                            <span class="status-badge <?= $adminRole === 'SUPER_ADMIN' ? 'status-verified' : 'status-progress'; ?>">
                                <?= e($adminRole); ?>
                            </span>
                        </div>

                        <div class="task-list">
                            <div class="task <?= !empty($adminProfile['can_manage_admins']) || $adminRole === 'SUPER_ADMIN' ? 'done' : ''; ?>">
                                <i class="fa-solid <?= !empty($adminProfile['can_manage_admins']) || $adminRole === 'SUPER_ADMIN' ? 'fa-circle-check' : 'fa-circle'; ?>"></i>
                                <span>Manage admins and roles</span>
                            </div>

                            <div class="task <?= !empty($adminProfile['can_approve_verification']) || $adminRole === 'SUPER_ADMIN' ? 'done' : ''; ?>">
                                <i class="fa-solid <?= !empty($adminProfile['can_approve_verification']) || $adminRole === 'SUPER_ADMIN' ? 'fa-circle-check' : 'fa-circle'; ?>"></i>
                                <span>Approve verification requests</span>
                            </div>

                            <div class="task <?= !empty($adminProfile['can_view_analytics']) || $adminRole === 'SUPER_ADMIN' ? 'done' : ''; ?>">
                                <i class="fa-solid <?= !empty($adminProfile['can_view_analytics']) || $adminRole === 'SUPER_ADMIN' ? 'fa-circle-check' : 'fa-circle'; ?>"></i>
                                <span>View analytics and reports</span>
                            </div>
                        </div>

                        <div style="margin-top:16px;">
                            <a href="<?= e(BASE_URL); ?>admin/profile.php" class="btn btn-soft">
                                <i class="fa-solid fa-user-shield"></i>
                                Admin Profile
                            </a>
                            <a href="<?= e(BASE_URL); ?>admin/roles.php" class="btn btn-soft">
                                <i class="fa-solid fa-user-lock"></i>
                                Roles
                            </a>
                        </div>
                    </article>
                </div>

                <div class="dashboard-main-grid">
                    <article class="dashboard-panel">
                        <div class="panel-head">
                            <div>
                                <h3>Recent Users</h3>
                                <p>Latest accounts created on the platform.</p>
                            </div>
                            <span class="status-badge status-open">Users</span>
                        </div>

                        <div class="pipeline-list">
                            <?php if (!empty($recentUsers)): ?>
                                <?php foreach ($recentUsers as $user): ?>
                                    <div>
                                        <span><?= e($user['full_name']); ?><br><small><?= e($user['email']); ?></small></span>
                                        <strong><?= e(ucfirst($user['role'])); ?></strong>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div><span>No users found yet.</span><strong>Empty</strong></div>
                            <?php endif; ?>
                        </div>
                    </article>

                    <article class="dashboard-panel">
                        <div class="panel-head">
                            <div>
                                <h3>Activity Logs</h3>
                                <p>Recent platform activity and audit trail.</p>
                            </div>
                            <span class="status-badge status-progress">Audit</span>
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
                                <div><i class="fa-solid fa-circle-info"></i> No activity logs found yet.</div>
                            <?php endif; ?>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>