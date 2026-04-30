<?php
$pageTitle = 'Verification Track';
$pageDescription = 'Track your verification progress on UNIDA Gateway.';
$pageName = 'verification-track';
require_once __DIR__ . '/includes/config.php';
require_login();
$userId = (int) ($_SESSION['user_id'] ?? 0);
$userRole = $_SESSION['user_role'] ?? '';
$track = null;
$steps = [];
try {
    if (function_exists('table_exists') && table_exists('verification_tracks')) {
        $stmt = db()->prepare("SELECT * FROM verification_tracks WHERE user_id = ? LIMIT 1");
        $stmt->execute([$userId]);
        $track = $stmt->fetch();
        if (!$track) {
            $insert = db()->prepare("INSERT INTO verification_tracks (user_id,user_role,current_status,completion_percent,due_date) VALUES (?,?, 'unverified',0,?)");
            $insert->execute([$userId, $userRole, date('Y-m-d H:i:s', strtotime('+7 days'))]);
            $stmt->execute([$userId]);
            $track = $stmt->fetch();
        }
    }
    if (function_exists('table_exists') && table_exists('verification_process_steps')) {
        $steps = db()->query("SELECT * FROM verification_process_steps WHERE is_active = 1 ORDER BY display_order ASC")->fetchAll();
    }
} catch (Throwable $e) {}
include __DIR__ . '/includes/header.php';
$currentStatus = $track['current_status'] ?? 'unverified';
$completion = (int) ($track['completion_percent'] ?? 0);
?>
<main class="dashboard-page">
<section class="dashboard-hero"><div class="container dashboard-hero-grid"><div><div class="page-kicker"><i class="fa-solid fa-route"></i> Verification Track</div><h1>Track your verification progress.</h1><p>Follow your status, next steps, due date and review history.</p></div><div class="dashboard-profile-card"><div class="profile-avatar"><?= e(strtoupper(substr($_SESSION['user_name'] ?? 'US',0,2))); ?></div><div><h3><?= e($_SESSION['user_name'] ?? 'User'); ?></h3><p><?= e(ucfirst($userRole)); ?> Account</p><span class="status-badge <?= function_exists('status_badge_class') ? status_badge_class($currentStatus) : 'status-open'; ?>"><?= e(ucfirst(str_replace('_',' ',$currentStatus))); ?></span></div></div></div></section>
<section class="dashboard-shell"><div class="container dashboard-layout"><?php include __DIR__ . '/includes/sidebar.php'; ?><div class="dashboard-content">
<div class="dashboard-stat-grid"><article class="dash-stat"><span class="dash-icon"><i class="fa-solid fa-chart-line"></i></span><div><strong><?= e($completion); ?>%</strong><small>Completion</small></div></article><article class="dash-stat"><span class="dash-icon green"><i class="fa-solid fa-shield-halved"></i></span><div><strong><?= e(ucfirst(str_replace('_',' ',$currentStatus))); ?></strong><small>Current status</small></div></article><article class="dash-stat"><span class="dash-icon cyan"><i class="fa-solid fa-calendar"></i></span><div><strong><?= e(!empty($track['due_date']) ? date('M d', strtotime($track['due_date'])) : 'N/A'); ?></strong><small>Due date</small></div></article></div>
<article class="dashboard-panel"><div class="panel-head"><div><h3>Verification Process</h3><p>Standard steps used to track verification progress.</p></div><span class="status-badge status-progress">Track</span></div><div class="task-list"><?php foreach ($steps as $step): ?><div class="task"><i class="fa-solid fa-circle-check"></i><span><?= e($step['step_name']); ?> — <?= e($step['description']); ?></span></div><?php endforeach; ?></div></article>
</div></div></section>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>