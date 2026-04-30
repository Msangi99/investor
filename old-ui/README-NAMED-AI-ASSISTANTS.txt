UNIDA Gateway Named AI Assistants Pack

Assistants:
- Unice: Public platform assistant
- Lieta: Insights, readiness and coordination assistant

Install:
1. Upload files to project root.
2. Open:
   https://investoraccess.unidatechs.com/install_unida_named_ai_assistants.php
3. Confirm OK.
4. Delete:
   install_unida_named_ai_assistants.php

Footer integration:
Before </body>, include:

<?php include __DIR__ . '/components/chatbot-widget.php'; ?>
<link rel="stylesheet" href="<?= e(asset_url('css/chatbot.css')); ?>">
<script>
window.UNIDA_BASE_URL = "<?= e(BASE_URL); ?>";
</script>
<script src="<?= e(asset_url('js/chatbot.js')); ?>"></script>
<script src="<?= e(asset_url('js/main.js')); ?>"></script>

Test:
- admin/ai-assistants.php
- api/chatbot.php
