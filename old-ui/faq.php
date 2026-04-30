<?php
$pageTitle = 'FAQ';
$pageDescription = 'Frequently asked questions about UNIDA Gateway.';
$pageName = 'faq';
require_once __DIR__ . '/includes/config.php';
include __DIR__ . '/includes/header.php';
$faqs = [];
try {
    if (function_exists('table_exists') && table_exists('faq_items')) {
        $faqs = db()->query("SELECT * FROM faq_items WHERE is_active = 1 ORDER BY display_order ASC, id ASC")->fetchAll();
    }
} catch (Throwable $e) {}
?>
<main>
<section class="page-hero"><div class="container"><div class="page-kicker"><i class="fa-solid fa-circle-question"></i> FAQ</div><h1>Frequently asked questions.</h1><p>Answers about accounts, verification, dashboards, investors, stakeholders and platform limitations.</p></div></section>
<section class="section"><div class="container legal-page">
<?php if (!empty($faqs)): foreach ($faqs as $faq): ?>
<article class="dashboard-panel legal-panel"><span class="status-badge status-open"><?= e($faq['category']); ?></span><h2><?= e($faq['question']); ?></h2><p><?= e($faq['answer']); ?></p></article>
<?php endforeach; else: ?>
<article class="dashboard-panel legal-panel"><h2>No FAQ records found.</h2><p>FAQ records will appear here after installation.</p></article>
<?php endif; ?>
</div></section>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>