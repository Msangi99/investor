<?php
$pageTitle = 'AI Tools';
$pageDescription = 'Manage AI tools and chatbot settings for UNIDA Gateway.';
$pageName = 'admin-ai-tools';
$activeSidebar = 'settings';

require_once __DIR__ . '/../includes/config.php';
require_role('admin');

$tools = [];
$settings = [];
$logs = [];

try {
    if (function_exists('table_exists') && table_exists('ai_tools')) {
        $tools = db()->query("SELECT * FROM ai_tools ORDER BY display_order ASC, tool_name ASC")->fetchAll();
    }

    if (function_exists('table_exists') && table_exists('ai_settings')) {
        $settings = db()->query("SELECT setting_key, setting_value, setting_group, is_secret FROM ai_settings ORDER BY setting_group, setting_key")->fetchAll();
    }

    if (function_exists('table_exists') && table_exists('ai_tool_logs')) {
        $logs = db()->query("SELECT * FROM ai_tool_logs ORDER BY created_at DESC LIMIT 20")->fetchAll();
    }
} catch (Throwable $e) {}

include __DIR__ . '/../includes/header.php';
?>

<main class="dashboard-page">
    <section class="dashboard-hero admin">
        <div class="container dashboard-hero-grid">
            <div>
                <div class="page-kicker"><i class="fa-solid fa-robot"></i> AI Tools</div>
                <h1>Manage chatbot and AI tools.</h1>
                <p>Use this workspace to review installed AI tools, chatbot settings and usage logs.</p>
            </div>
        </div>
    </section>

    <section class="dashboard-shell">
        <div class="container dashboard-layout">
            <?php include __DIR__ . '/../includes/sidebar.php'; ?>

            <div class="dashboard-content">
                <article class="dashboard-panel">
                    <div class="panel-head">
                        <div><h3>Installed AI Tools</h3><p>Available AI and assistant tools.</p></div>
                        <span class="status-badge status-open"><?= e(count($tools)); ?> tools</span>
                    </div>

                    <div class="dashboard-table-wrap">
                        <table class="dashboard-table">
                            <thead><tr><th>Tool</th><th>Type</th><th>Endpoint</th><th>Status</th></tr></thead>
                            <tbody>
                                <?php foreach ($tools as $tool): ?>
                                    <tr>
                                        <td><?= e($tool['tool_name']); ?><br><small><?= e($tool['description']); ?></small></td>
                                        <td><?= e($tool['tool_type']); ?></td>
                                        <td><?= e($tool['endpoint_url']); ?></td>
                                        <td><?= !empty($tool['is_enabled']) ? 'Enabled' : 'Disabled'; ?></td>
                                    </tr>
                                <?php endforeach; ?>

                                <?php if (empty($tools)): ?>
                                    <tr><td colspan="4">No AI tools found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </article>

                <article class="dashboard-panel">
                    <div class="panel-head">
                        <div><h3>AI Settings</h3><p>Current provider and chatbot settings.</p></div>
                    </div>

                    <div class="pipeline-list">
                        <?php foreach ($settings as $setting): ?>
                            <div>
                                <span><?= e($setting['setting_key']); ?></span>
                                <strong><?= !empty($setting['is_secret']) ? 'Protected' : e($setting['setting_value']); ?></strong>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </article>

                <article class="dashboard-panel">
                    <div class="panel-head">
                        <div><h3>Recent AI Logs</h3><p>Latest chatbot/tool interactions.</p></div>
                        <span class="status-badge status-open"><?= e(count($logs)); ?> logs</span>
                    </div>

                    <div class="activity-list">
                        <?php foreach ($logs as $log): ?>
                            <div>
                                <i class="fa-solid fa-clock-rotate-left"></i>
                                <?= e($log['tool_key']); ?> — <?= e(mb_substr($log['prompt'] ?? '', 0, 80)); ?>
                            </div>
                        <?php endforeach; ?>

                        <?php if (empty($logs)): ?>
                            <div><i class="fa-solid fa-circle-info"></i> No AI logs yet.</div>
                        <?php endif; ?>
                    </div>
                </article>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
