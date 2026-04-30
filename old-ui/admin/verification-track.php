<?php
$pageTitle = 'Verification Tracking';
$pageDescription = 'Admin real-time verification process tracking.';
$pageName = 'admin-verification-track';
$activeSidebar = 'verifications';

require_once __DIR__ . '/../includes/config.php';
require_role('admin');

$tracks = [];

try {
    if (table_exists('verification_tracks')) {
        $tracks = db()->query("
            SELECT vt.*, u.full_name, u.email
            FROM verification_tracks vt
            INNER JOIN users u ON u.id = vt.user_id
            ORDER BY vt.updated_at DESC, vt.created_at DESC
            LIMIT 100
        ")->fetchAll();
    }
} catch (Throwable $e) {}

include __DIR__ . '/../includes/header.php';
?>

<main class="dashboard-page">
    <section class="dashboard-hero admin">
        <div class="container dashboard-hero-grid">
            <div>
                <div class="page-kicker"><i class="fa-solid fa-route"></i> Verification Tracking</div>
                <h1>Track real-time verification process for all users.</h1>
                <p>Review user status, completion percentage, due date, assigned admin and current verification stage.</p>
            </div>
        </div>
    </section>

    <section class="dashboard-shell">
        <div class="container dashboard-layout">
            <?php include __DIR__ . '/../includes/sidebar.php'; ?>

            <div class="dashboard-content">
                <article class="dashboard-panel">
                    <div class="panel-head"><div><h3>Verification Tracks</h3><p>Latest verification records.</p></div><span class="status-badge status-open"><?= e(count($tracks)); ?> tracks</span></div>
                    <div class="dashboard-table-wrap">
                        <table class="dashboard-table">
                            <thead><tr><th>User</th><th>Role</th><th>Status</th><th>Completion</th><th>Due Date</th><th>Updated</th></tr></thead>
                            <tbody>
                                <?php foreach ($tracks as $track): ?>
                                    <tr>
                                        <td><?= e($track['full_name']); ?><br><small><?= e($track['email']); ?></small></td>
                                        <td><?= e($track['user_role']); ?></td>
                                        <td><span class="status-badge <?= e(status_badge_class($track['current_status'])); ?>"><?= e(ucfirst(str_replace('_', ' ', $track['current_status']))); ?></span></td>
                                        <td><?= e((int) $track['completion_percent']); ?>%</td>
                                        <td><?= e($track['due_date'] ?: 'N/A'); ?></td>
                                        <td><?= e($track['updated_at'] ?: $track['created_at']); ?></td>
                                    </tr>
                                <?php endforeach; ?>

                                <?php if (empty($tracks)): ?>
                                    <tr><td colspan="6">No verification tracking records yet.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </article>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
