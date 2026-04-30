<?php
$pageTitle = 'Platform Limitations';
$pageDescription = 'Important limitations and responsible use notes for UNIDA Gateway.';
$pageName = 'limitations';
require_once __DIR__ . '/includes/config.php';
include __DIR__ . '/includes/header.php';
$limitations = [];
try {
    if (function_exists('table_exists') && table_exists('platform_limitations')) {
        $limitations = db()->query("SELECT * FROM platform_limitations WHERE is_active = 1 ORDER BY display_order ASC, id ASC")->fetchAll();
    }
} catch (Throwable $e) {}
?>
<main>
<section class="page-hero"><div class="container"><div class="page-kicker"><i class="fa-solid fa-triangle-exclamation"></i> Platform Limitations</div><h1>Important notes before using restricted features.</h1><p>UNIDA Gateway supports coordination and verification, but does not replace independent due diligence.</p></div></section>
<section class="section"><div class="container cards-grid three-columns">
<?php if (!empty($limitations)): foreach ($limitations as $item): ?>
<article class="info-card"><div class="icon-box"><i class="fa-solid fa-circle-info"></i></div><h3><?= e($item['title']); ?></h3><p><?= e($item['description']); ?></p><span class="status-badge <?= $item['severity'] === 'restricted' ? 'status-danger' : ($item['severity'] === 'important' ? 'status-warning' : 'status-open'); ?>"><?= e(ucfirst($item['severity'])); ?></span></article>
<?php endforeach; else: ?>
<article class="info-card"><h3>No limitations listed yet.</h3><p>Records will appear here after installation.</p></article>
<?php endif; ?>
</div></section>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>