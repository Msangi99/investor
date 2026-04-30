<?php
$lang = function_exists('current_language') ? current_language() : ($_SESSION['lang'] ?? 'sw');

$labels = [
    'sw' => [
        'toggle' => 'Uliza Unny',
        'title' => 'Unny',
        'role' => 'Msaidizi wa UNIDA Gateway',
        'welcome' => 'Habari, mimi ni Unny. Naweza kukusaidia kuhusu kujisajili, kuingia, uhakiki, dashboards, fursa na support.',
        'placeholder' => 'Andika swali lako...',
    ],
    'en' => [
        'toggle' => 'Ask Unny',
        'title' => 'Unny',
        'role' => 'UNIDA Gateway Assistant',
        'welcome' => 'Hello, I am Unny. I can help with registration, login, verification, dashboards, opportunities and support.',
        'placeholder' => 'Ask a question...',
    ],
];

$t = $labels[$lang] ?? $labels['sw'];
?>

<div
    class="unida-chatbot"
    id="unidaChatbot"
    aria-live="polite"
    data-lang="<?= e($lang); ?>"
    data-assistant="unny"
>
    <button class="unida-chatbot-toggle" type="button" id="unidaChatbotToggle" aria-expanded="false">
        <i class="fa-solid fa-comments"></i>
        <span><?= e($t['toggle']); ?></span>
    </button>

    <div class="unida-chatbot-panel" id="unidaChatbotPanel">
        <div class="unida-chatbot-head">
            <div>
                <strong id="unidaAssistantName"><?= e($t['title']); ?></strong>
                <small id="unidaAssistantRole"><?= e($t['role']); ?></small>
            </div>

            <button type="button" id="unidaChatbotClose" aria-label="Close chatbot">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="unida-chatbot-messages" id="unidaChatbotMessages">
            <div class="bot-msg">
                <?= e($t['welcome']); ?>
            </div>
        </div>

        <form class="unida-chatbot-form" id="unidaChatbotForm">
            <input type="text" id="unidaChatbotInput" placeholder="<?= e($t['placeholder']); ?>" autocomplete="off">
            <button type="submit" aria-label="Send message">
                <i class="fa-solid fa-paper-plane"></i>
            </button>
        </form>
    </div>
</div>
