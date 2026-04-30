<?php
if (!function_exists('current_language')) {
    return;
}

$currentLang = current_language();
?>

<div class="language-switch" aria-label="<?= e(t('language', 'Language')); ?>">
    <a class="<?= $currentLang === 'en' ? 'active' : ''; ?>" href="<?= e(language_switch_url('en')); ?>">
        EN
    </a>

    <a class="<?= $currentLang === 'sw' ? 'active' : ''; ?>" href="<?= e(language_switch_url('sw')); ?>">
        SW
    </a>
</div>