<?php
$pageTitle = 'Admin Profile';
$pageDescription = 'Manage administrator identity, role, permissions, department, contact information and security settings.';
$pageName = 'admin-profile';
$activeSidebar = 'profile';

require_once __DIR__ . '/../includes/config.php';
require_role('admin');

$pdo = db();

$userId = (int) ($_SESSION['user_id'] ?? 0);
$userName = $_SESSION['user_name'] ?? 'Admin User';
$userEmail = $_SESSION['user_email'] ?? '';

$errors = [];
$success = '';

function get_admin_role_label($role) {
    $labels = [
        'SUPER_ADMIN' => 'Super Admin',
        'ADMIN' => 'Admin',
        'VERIFICATION_ADMIN' => 'Verification Admin',
        'SUPPORT_ADMIN' => 'Support Admin',
        'FINANCE_ADMIN' => 'Finance Admin',
        'CONTENT_ADMIN' => 'Content Admin',
        'PARTNERSHIP_ADMIN' => 'Partnership Admin',
        'ANALYTICS_ADMIN' => 'Analytics Admin',
    ];

    return $labels[$role] ?? $role;
}

function is_super_admin_profile($profile) {
    return ($profile['admin_role'] ?? '') === 'SUPER_ADMIN';
}

$adminProfile = null;

try {
    $stmt = $pdo->prepare("
        SELECT 
            ap.*,
            u.full_name,
            u.email,
            u.phone,
            u.status AS user_status,
            u.last_login_at
        FROM admin_profiles ap
        INNER JOIN users u ON u.id = ap.user_id
        WHERE ap.user_id = ?
        LIMIT 1
    ");
    $stmt->execute([$userId]);
    $adminProfile = $stmt->fetch();
} catch (Throwable $e) {
    $adminProfile = null;
}

if (!$adminProfile) {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO admin_profiles (
                user_id,
                admin_role,
                permission_group,
                department,
                job_title,
                work_email,
                security_level,
                status
            ) VALUES (
                ?,
                'ADMIN',
                'General Admin Access',
                'Administration',
                'Administrator',
                ?,
                'standard',
                'active'
            )
        ");
        $stmt->execute([$userId, $userEmail]);

        header('Location: ' . BASE_URL . 'admin/profile.php');
        exit;
    } catch (Throwable $e) {
        $errors[] = 'Unable to prepare admin profile.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $adminProfile) {
    $department = trim($_POST['department'] ?? '');
    $jobTitle = trim($_POST['job_title'] ?? '');
    $workEmail = trim($_POST['work_email'] ?? '');
    $workPhone = trim($_POST['work_phone'] ?? '');
    $alternativePhone = trim($_POST['alternative_phone'] ?? '');

    $twoFactorEnabled = isset($_POST['two_factor_enabled']) ? 1 : 0;

    if ($workEmail !== '' && !filter_var($workEmail, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid work email.';
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                UPDATE admin_profiles
                SET 
                    department = :department,
                    job_title = :job_title,
                    work_email = :work_email,
                    work_phone = :work_phone,
                    alternative_phone = :alternative_phone,
                    two_factor_enabled = :two_factor_enabled,
                    updated_by = :updated_by
                WHERE user_id = :user_id
            ");

            $stmt->execute([
                ':department' => $department ?: null,
                ':job_title' => $jobTitle ?: null,
                ':work_email' => $workEmail ?: null,
                ':work_phone' => $workPhone ?: null,
                ':alternative_phone' => $alternativePhone ?: null,
                ':two_factor_enabled' => $twoFactorEnabled,
                ':updated_by' => $userId,
                ':user_id' => $userId,
            ]);

            try {
                $log = $pdo->prepare("
                    INSERT INTO activity_logs (
                        user_id,
                        action,
                        module,
                        description,
                        ip_address,
                        user_agent
                    ) VALUES (
                        ?,
                        'admin_profile_updated',
                        'admin_profile',
                        'Admin updated profile information.',
                        ?,
                        ?
                    )
                ");

                $log->execute([
                    $userId,
                    $_SERVER['REMOTE_ADDR'] ?? null,
                    $_SERVER['HTTP_USER_AGENT'] ?? null,
                ]);
            } catch (Throwable $e) {
                error_log('Activity log failed: ' . $e->getMessage());
            }

            $success = 'Admin profile updated successfully.';

            $stmt = $pdo->prepare("
                SELECT 
                    ap.*,
                    u.full_name,
                    u.email,
                    u.phone,
                    u.status AS user_status,
                    u.last_login_at
                FROM admin_profiles ap
                INNER JOIN users u ON u.id = ap.user_id
                WHERE ap.user_id = ?
                LIMIT 1
            ");
            $stmt->execute([$userId]);
            $adminProfile = $stmt->fetch();
        } catch (Throwable $e) {
            $errors[] = 'Unable to update admin profile right now.';
        }
    }
}

$recentActivities = [];

try {
    $stmt = $pdo->prepare("
        SELECT action, module, description, created_at
        FROM activity_logs
        WHERE user_id = ?
        ORDER BY created_at DESC
        LIMIT 8
    ");
    $stmt->execute([$userId]);
    $recentActivities = $stmt->fetchAll();
} catch (Throwable $e) {
    $recentActivities = [];
}

$adminRole = $adminProfile['admin_role'] ?? 'ADMIN';
$roleLabel = get_admin_role_label($adminRole);
$isSuperAdmin = is_super_admin_profile($adminProfile);

include __DIR__ . '/../includes/header.php';
?>

<main class="dashboard-page">
    <section class="dashboard-hero admin">
        <div class="container dashboard-hero-grid">
            <div>
                <div class="page-kicker">
                    <i class="fa-solid fa-user-shield"></i>
                    Admin Profile
                </div>

                <h1>Manage admin identity, access role and security settings.</h1>

                <p>
                    This page controls administrator profile details, permission group, department, contact information,
                    security settings and activity visibility for UNIDA Gateway.
                </p>
            </div>

            <div class="dashboard-profile-card">
                <div class="profile-avatar admin-avatar">
                    <?= e(strtoupper(substr($userName, 0, 2))); ?>
                </div>

                <div>
                    <h3><?= e($userName); ?></h3>
                    <p><?= e($roleLabel); ?></p>

                    <span class="status-badge <?= $isSuperAdmin ? 'status-verified' : 'status-progress'; ?>">
                        <i class="fa-solid <?= $isSuperAdmin ? 'fa-crown' : 'fa-user-lock'; ?>"></i>
                        <?= e($roleLabel); ?>
                    </span>
                </div>
            </div>
        </div>
    </section>

    <section class="dashboard-shell">
        <div class="container dashboard-layout">
            <?php include __DIR__ . '/../includes/sidebar.php'; ?>

            <div class="dashboard-content">
                <?php if (!empty($errors)): ?>
                    <div class="form-alert form-alert-error">
                        <?php foreach ($errors as $error): ?>
                            <p><?= e($error); ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="form-alert form-alert-success">
                        <p><?= e($success); ?></p>
                    </div>
                <?php endif; ?>

                <div class="dashboard-stat-grid">
                    <article class="dash-stat">
                        <span class="dash-icon">
                            <i class="fa-solid fa-user-shield"></i>
                        </span>
                        <div>
                            <strong><?= e($roleLabel); ?></strong>
                            <small>Admin role</small>
                        </div>
                    </article>

                    <article class="dash-stat">
                        <span class="dash-icon green">
                            <i class="fa-solid fa-layer-group"></i>
                        </span>
                        <div>
                            <strong><?= e($adminProfile['permission_group'] ?? 'General'); ?></strong>
                            <small>Permission group</small>
                        </div>
                    </article>

                    <article class="dash-stat">
                        <span class="dash-icon cyan">
                            <i class="fa-solid fa-building"></i>
                        </span>
                        <div>
                            <strong><?= e($adminProfile['department'] ?? 'Administration'); ?></strong>
                            <small>Department</small>
                        </div>
                    </article>

                    <article class="dash-stat">
                        <span class="dash-icon dark">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <div>
                            <strong><?= e(ucfirst($adminProfile['security_level'] ?? 'standard')); ?></strong>
                            <small>Security level</small>
                        </div>
                    </article>
                </div>

                <div class="dashboard-main-grid">
                    <article class="dashboard-panel">
                        <div class="panel-head">
                            <div>
                                <h3>Admin Identity</h3>
                                <p>Core identity details linked to the admin account.</p>
                            </div>

                            <span class="status-badge <?= ($adminProfile['status'] ?? 'active') === 'active' ? 'status-verified' : 'status-danger'; ?>">
                                <?= e(ucfirst($adminProfile['status'] ?? 'active')); ?>
                            </span>
                        </div>

                        <div class="pipeline-list">
                            <div>
                                <span>Full name</span>
                                <strong><?= e($adminProfile['full_name'] ?? $userName); ?></strong>
                            </div>

                            <div>
                                <span>Account email</span>
                                <strong><?= e($adminProfile['email'] ?? $userEmail); ?></strong>
                            </div>

                            <div>
                                <span>Admin role</span>
                                <strong><?= e($roleLabel); ?></strong>
                            </div>

                            <div>
                                <span>Job title</span>
                                <strong><?= e($adminProfile['job_title'] ?? 'Administrator'); ?></strong>
                            </div>
                        </div>
                    </article>

                    <article class="dashboard-panel">
                        <div class="panel-head">
                            <div>
                                <h3>Permission Summary</h3>
                                <p>Current permission flags for this admin profile.</p>
                            </div>

                            <span class="status-badge <?= $isSuperAdmin ? 'status-verified' : 'status-open'; ?>">
                                <?= $isSuperAdmin ? 'Full access' : 'Limited access'; ?>
                            </span>
                        </div>

                        <div class="task-list">
                            <div class="task <?= !empty($adminProfile['can_manage_admins']) ? 'done' : ''; ?>">
                                <i class="fa-solid <?= !empty($adminProfile['can_manage_admins']) ? 'fa-circle-check' : 'fa-circle'; ?>"></i>
                                <span>Manage admins and roles</span>
                            </div>

                            <div class="task <?= !empty($adminProfile['can_approve_verification']) ? 'done' : ''; ?>">
                                <i class="fa-solid <?= !empty($adminProfile['can_approve_verification']) ? 'fa-circle-check' : 'fa-circle'; ?>"></i>
                                <span>Approve verification requests</span>
                            </div>

                            <div class="task <?= !empty($adminProfile['can_manage_finance']) ? 'done' : ''; ?>">
                                <i class="fa-solid <?= !empty($adminProfile['can_manage_finance']) ? 'fa-circle-check' : 'fa-circle'; ?>"></i>
                                <span>Manage finance-related records</span>
                            </div>

                            <div class="task <?= !empty($adminProfile['can_publish_content']) ? 'done' : ''; ?>">
                                <i class="fa-solid <?= !empty($adminProfile['can_publish_content']) ? 'fa-circle-check' : 'fa-circle'; ?>"></i>
                                <span>Publish content and insights</span>
                            </div>

                            <div class="task <?= !empty($adminProfile['can_view_analytics']) ? 'done' : ''; ?>">
                                <i class="fa-solid <?= !empty($adminProfile['can_view_analytics']) ? 'fa-circle-check' : 'fa-circle'; ?>"></i>
                                <span>View analytics and reports</span>
                            </div>
                        </div>
                    </article>
                </div>

                <form class="dashboard-panel form-grid" method="post" action="">
                    <div class="panel-head">
                        <div>
                            <h3>Update Admin Profile</h3>
                            <p>Update contact information, department and security preference.</p>
                        </div>

                        <?php if ($isSuperAdmin): ?>
                            <span class="status-badge status-verified">
                                <i class="fa-solid fa-crown"></i>
                                Protected Super Admin
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="form-grid two">
                        <div class="form-group">
                            <label>Department</label>
                            <input
                                class="form-control"
                                type="text"
                                name="department"
                                value="<?= e($adminProfile['department'] ?? ''); ?>"
                                placeholder="Administration, Verification, Finance..."
                            >
                        </div>

                        <div class="form-group">
                            <label>Job Title</label>
                            <input
                                class="form-control"
                                type="text"
                                name="job_title"
                                value="<?= e($adminProfile['job_title'] ?? ''); ?>"
                                placeholder="System Administrator"
                            >
                        </div>
                    </div>

                    <div class="form-grid two">
                        <div class="form-group">
                            <label>Work Email</label>
                            <input
                                class="form-control"
                                type="email"
                                name="work_email"
                                value="<?= e($adminProfile['work_email'] ?? ''); ?>"
                                placeholder="admin@company.com"
                            >
                        </div>

                        <div class="form-group">
                            <label>Work Phone</label>
                            <input
                                class="form-control"
                                type="text"
                                name="work_phone"
                                value="<?= e($adminProfile['work_phone'] ?? ''); ?>"
                                placeholder="+255..."
                            >
                        </div>
                    </div>

                    <div class="form-grid two">
                        <div class="form-group">
                            <label>Alternative Phone</label>
                            <input
                                class="form-control"
                                type="text"
                                name="alternative_phone"
                                value="<?= e($adminProfile['alternative_phone'] ?? ''); ?>"
                                placeholder="+255..."
                            >
                        </div>

                        <div class="form-group">
                            <label>Security Settings</label>
                            <label class="checkbox-row">
                                <input
                                    type="checkbox"
                                    name="two_factor_enabled"
                                    value="1"
                                    <?= !empty($adminProfile['two_factor_enabled']) ? 'checked' : ''; ?>
                                >
                                <span>Enable two-factor authentication flag for this admin profile.</span>
                            </label>
                        </div>
                    </div>

                    <button class="btn btn-primary" type="submit">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Save Admin Profile
                    </button>
                </form>

                <article class="dashboard-panel">
                    <div class="panel-head">
                        <div>
                            <h3>Activity Logs</h3>
                            <p>Recent actions performed by this admin account.</p>
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
                                No activity logs found yet.
                            </div>
                        <?php endif; ?>
                    </div>
                </article>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>