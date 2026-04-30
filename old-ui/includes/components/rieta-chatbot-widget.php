<?php
$lang = function_exists('current_language') ? current_language() : ($_SESSION['lang'] ?? 'en');

$text = [
    'en' => [
        'toggle' => 'Ask Unny',
        'name' => 'Unny',
        'role' => 'English Assistant',
        'welcome' => 'Hello, I am Unny. Choose an area below or type your question.',
        'placeholder' => 'Type your question or concern...',
        'human_title' => 'Forward to human agent',
        'full_name' => 'Full name',
        'email' => 'Email',
        'phone' => 'Phone number',
        'concern' => 'Describe your concern',
        'submit_support' => 'Send to Support',
        'topic_register' => 'Create account',
        'topic_login' => 'Login / Dashboard',
        'topic_verify' => 'Verification',
        'topic_business' => 'Business readiness',
        'topic_investor' => 'Investor access',
        'topic_stakeholder' => 'Stakeholder support',
        'topic_human' => 'Talk to human agent',
    ],
    'sw' => [
        'toggle' => 'Uliza Rieta',
        'name' => 'Rieta',
        'role' => 'Msaidizi wa Kiswahili',
        'welcome' => 'Habari, mimi ni Rieta. Chagua eneo unalohitaji msaada au andika swali lako.',
        'placeholder' => 'Andika swali au concern...',
        'human_title' => 'Tuma kwa human agent',
        'full_name' => 'Jina kamili',
        'email' => 'Email',
        'phone' => 'Namba ya simu',
        'concern' => 'Eleza concern yako',
        'submit_support' => 'Tuma kwa Support',
        'topic_register' => 'Kujisajili',
        'topic_login' => 'Login / Dashboard',
        'topic_verify' => 'Uhakiki',
        'topic_business' => 'Business readiness',
        'topic_investor' => 'Mwekezaji / Fursa',
        'topic_stakeholder' => 'Msaada wa mdau',
        'topic_human' => 'Ongea na mtu',
    ],
];

$t = $text[$lang] ?? $text['en'];
$assistantKey = $lang === 'sw' ? 'rieta' : 'unny';
?>

<div class="rieta-chatbot" id="rietaChatbot" data-lang="<?= e($lang); ?>" data-assistant="<?= e($assistantKey); ?>">
    <button class="rieta-toggle" type="button" id="rietaToggle" aria-expanded="false">
        <i class="fa-solid fa-headset"></i>
        <span><?= e($t['toggle']); ?></span>
    </button>

    <section class="rieta-panel" id="rietaPanel" aria-label="<?= e($t['name']); ?> chatbot">
        <header class="rieta-head">
            <div>
                <strong><?= e($t['name']); ?></strong>
                <small><?= e($t['role']); ?></small>
            </div>

            <button type="button" id="rietaClose" aria-label="Close">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </header>

        <div class="rieta-body" id="rietaBody">
            <div class="rieta-msg bot"><?= e($t['welcome']); ?></div>

            <div class="rieta-topics" id="rietaTopics">
                <button type="button" data-topic="registration"><?= e($t['topic_register']); ?></button>
                <button type="button" data-topic="login_dashboard"><?= e($t['topic_login']); ?></button>
                <button type="button" data-topic="verification"><?= e($t['topic_verify']); ?></button>
                <button type="button" data-topic="business_readiness"><?= e($t['topic_business']); ?></button>
                <button type="button" data-topic="investor_access"><?= e($t['topic_investor']); ?></button>
                <button type="button" data-topic="stakeholder"><?= e($t['topic_stakeholder']); ?></button>
                <button type="button" data-topic="human_agent"><?= e($t['topic_human']); ?></button>
            </div>
        </div>

        <form class="rieta-form" id="rietaForm">
            <input type="text" id="rietaInput" placeholder="<?= e($t['placeholder']); ?>" autocomplete="off">
            <button type="submit" aria-label="Send">
                <i class="fa-solid fa-paper-plane"></i>
            </button>
        </form>

        <form class="rieta-support-form" id="rietaSupportForm">
            <strong><?= e($t['human_title']); ?></strong>
            <input type="text" name="full_name" placeholder="<?= e($t['full_name']); ?>" required>
            <input type="email" name="email" placeholder="<?= e($t['email']); ?>" required>
            <input type="tel" name="phone" placeholder="<?= e($t['phone']); ?>" required>
            <textarea name="concern" placeholder="<?= e($t['concern']); ?>" required></textarea>
            <button type="submit">
                <i class="fa-solid fa-envelope"></i>
                <?= e($t['submit_support']); ?>
            </button>
        </form>
    </section>
</div>