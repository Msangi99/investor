<?php
$pageTitle = 'Chatbot';
$pageDescription = 'Chatbot conversations and support assistant logs.';
$pageName = 'admin-chatbot';
$activeSidebar = 'messages';

require_once __DIR__ . '/../includes/config.php';
require_role('admin');

$conversations = [];
$messages = [];

try {
    if (function_exists('table_exists') && table_exists('chatbot_conversations')) {
        $conversations = db()->query("SELECT * FROM chatbot_conversations ORDER BY updated_at DESC, created_at DESC LIMIT 50")->fetchAll();
    }

    if (function_exists('table_exists') && table_exists('chatbot_messages')) {
        $messages = db()->query("SELECT cm.*, cc.session_id FROM chatbot_messages cm INNER JOIN chatbot_conversations cc ON cc.id = cm.conversation_id ORDER BY cm.created_at DESC LIMIT 80")->fetchAll();
    }
} catch (Throwable $e) {}

include __DIR__ . '/../includes/header.php';
?>

<main class="dashboard-page">
    <section class="dashboard-hero admin">
        <div class="container dashboard-hero-grid">
            <div>
                <div class="page-kicker"><i class="fa-solid fa-comments"></i> Chatbot</div>
                <h1>Review chatbot conversations.</h1>
                <p>Monitor user questions, assistant replies and support needs.</p>
            </div>
        </div>
    </section>

    <section class="dashboard-shell">
        <div class="container dashboard-layout">
            <?php include __DIR__ . '/../includes/sidebar.php'; ?>

            <div class="dashboard-content">
                <div class="dashboard-stat-grid">
                    <article class="dash-stat"><span class="dash-icon"><i class="fa-solid fa-comments"></i></span><div><strong><?= e(count($conversations)); ?></strong><small>Conversations</small></div></article>
                    <article class="dash-stat"><span class="dash-icon green"><i class="fa-solid fa-message"></i></span><div><strong><?= e(count($messages)); ?></strong><small>Recent messages</small></div></article>
                </div>

                <article class="dashboard-panel">
                    <div class="panel-head">
                        <div><h3>Recent Messages</h3><p>Latest chatbot conversation messages.</p></div>
                    </div>

                    <div class="activity-list">
                        <?php foreach ($messages as $message): ?>
                            <div>
                                <i class="fa-solid <?= $message['sender'] === 'assistant' ? 'fa-robot' : 'fa-user'; ?>"></i>
                                <?= e(strtoupper($message['sender'])); ?>:
                                <?= e(mb_substr($message['message'], 0, 140)); ?>
                            </div>
                        <?php endforeach; ?>

                        <?php if (empty($messages)): ?>
                            <div><i class="fa-solid fa-circle-info"></i> No chatbot messages yet.</div>
                        <?php endif; ?>
                    </div>
                </article>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
