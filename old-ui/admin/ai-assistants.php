<?php
$pageTitle = 'AI Assistants';
$pageDescription = 'Manage named AI assistants for UNIDA Gateway.';
$pageName = 'admin-ai-assistants';
$activeSidebar = 'settings';

require_once __DIR__ . '/../includes/config.php';
require_role('admin');

$assistants = [];
$logs = [];

try {
    if (function_exists('table_exists') && table_exists('ai_assistants')) {
        $assistants = db()->query("SELECT * FROM ai_assistants ORDER BY display_order ASC, assistant_name ASC")->fetchAll();
    }

    if (function_exists('table_exists') && table_exists('ai_tool_logs')) {
        $logs = db()->query("SELECT * FROM ai_tool_logs ORDER BY created_at DESC LIMIT 30")->fetchAll();
    }
} catch (Throwable $e) {}

include __DIR__ . '/../includes/header.php';
?>

<main class="dashboard-page">
    <section class="dashboard-hero admin">
        <div class="container dashboard-hero-grid">
            <div>
                <div class="page-kicker"><i class="fa-solid fa-robot"></i> AI Assistants</div>
                <h1>Manage Unice and Lieta.</h1>
                <p>Unice supports public users. Lieta supports readiness, insights, analytics and coordination.</p>
            </div>
        </div>
    </section>

    <section class="dashboard-shell">
        <div class="container dashboard-layout">
            <?php include __DIR__ . '/../includes/sidebar.php'; ?>

            <div class="dashboard-content">
                <div class="cards-grid two-columns">
                    <?php foreach ($assistants as $assistant): ?>
                        <article class="info-card">
                            <div class="icon-box"><i class="fa-solid fa-robot"></i></div>
                            <h3><?= e($assistant['assistant_name']); ?></h3>
                            <p><strong><?= e($assistant['assistant_role']); ?></strong></p>
                            <p><?= e($assistant['description']); ?></p>
                            <span class="status-badge <?= !empty($assistant['is_enabled']) ? 'status-verified' : 'status-danger'; ?>">
                                <?= !empty($assistant['is_enabled']) ? 'Enabled' : 'Disabled'; ?>
                            </span>
                        </article>
                    <?php endforeach; ?>

                    <?php if (empty($assistants)): ?>
                        <article class="info-card">
                            <h3>No assistants found.</h3>
                            <p>Run the named AI assistants installer.</p>
                        </article>
                    <?php endif; ?>
                </div>

                <article class="dashboard-panel">
                    <div class="panel-head">
                        <div>
                            <h3>Recent Assistant Logs</h3>
                            <p>Latest chatbot interactions by assistant.</p>
                        </div>
                        <span class="status-badge status-open"><?= e(count($logs)); ?> logs</span>
                    </div>

                    <div class="activity-list">
                        <?php foreach ($logs as $log): ?>
                            <div>
                                <i class="fa-solid fa-clock-rotate-left"></i>
                                <?= e(ucfirst($log['assistant_key'] ?? $log['tool_key'])); ?>:
                                <?= e(mb_substr($log['prompt'] ?? '', 0, 90)); ?>
                            </div>
                        <?php endforeach; ?>

                        <?php if (empty($logs)): ?>
                            <div><i class="fa-solid fa-circle-info"></i> No assistant logs yet.</div>
                        <?php endif; ?>
                    </div>
                </article>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
