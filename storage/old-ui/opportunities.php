<?php
$pageTitle = 'Opportunities';
$pageDescription = 'Sample opportunity page for the UNIDA Investment Ecosystem.';
$pageName = 'opportunities';
require_once __DIR__ . '/includes/config.php';
include __DIR__ . '/includes/header.php';
?>
<main>
    <section class="page-hero">
        <div class="container">
            <div class="page-kicker"><i class="fa-solid fa-briefcase"></i> Opportunities</div>
            <h1>Sample investment opportunities can be listed here.</h1>
            <p>This page can later show verified businesses, sector filters, regions, funding needs and readiness status.</p>
        </div>
    </section>
    <section class="section">
        <div class="container cards-grid three-columns">
            <article class="info-card"><div class="icon-box"><i class="fa-solid fa-leaf"></i></div><h3>Agribusiness</h3><p>Sample verified opportunities in farming, processing and distribution.</p></article>
            <article class="info-card"><div class="icon-box"><i class="fa-solid fa-heart-pulse"></i></div><h3>Health Technology</h3><p>Sample digital health and service access opportunities.</p></article>
            <article class="info-card"><div class="icon-box"><i class="fa-solid fa-solar-panel"></i></div><h3>Energy & Cooling</h3><p>Sample clean energy, smart cooling and infrastructure opportunities.</p></article>
        </div>
    </section>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>
