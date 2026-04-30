UNIDA Gateway Roles Fix + AI Chatbot Tools Pack

This fixes:
- FAILED Seed data Unknown column role_type
- FAILED Seed data Unknown column permission_group

It installs:
- AI chatbot tables
- AI settings/tools/logs
- knowledge base table
- chatbot API
- chatbot widget component
- chatbot CSS/JS
- admin AI tools page
- admin chatbot logs page

Install:
1. Upload all files to your project root.
2. Open:
   https://investoraccess.unidatechs.com/install_unida_ai_chatbot_tools.php
3. Confirm OK.
4. Delete:
   install_unida_ai_chatbot_tools.php

To show chatbot on every page:
In includes/footer.php, add before the main.js script or before </body>:

<?php include __DIR__ . '/components/chatbot-widget.php'; ?>
<link rel="stylesheet" href="<?= e(asset_url('css/chatbot.css')); ?>">
<script>
window.UNIDA_BASE_URL = "<?= e(BASE_URL); ?>";
</script>
<script src="<?= e(asset_url('js/chatbot.js')); ?>"></script>
