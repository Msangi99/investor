UNIDA Gateway Official Chatbot Name: Unny

I chose "Unny" because:
- It feels friendly and short
- It is close to UNIDA branding
- It works better as a product assistant name than "Habibi"
- It can serve both Swahili and English users

Install:
1. Upload all files to project root.
2. Open:
   https://investoraccess.unidatechs.com/install_unida_unny_chatbot.php
3. Confirm OK.
4. Delete:
   install_unida_unny_chatbot.php

Footer must include:
<?php
$chatbotWidget = __DIR__ . '/components/chatbot-widget.php';
if (file_exists($chatbotWidget)) {
    include $chatbotWidget;
}
?>

<link rel="stylesheet" href="<?= e(asset_url('css/chatbot.css')); ?>">

<script>
    window.UNIDA_BASE_URL = "<?= e(BASE_URL); ?>";
    window.UNIDA_LANG = "<?= function_exists('current_language') ? e(current_language()) : 'sw'; ?>";
</script>

<script src="<?= e(asset_url('js/chatbot.js')); ?>"></script>
<script src="<?= e(asset_url('js/main.js')); ?>"></script>
