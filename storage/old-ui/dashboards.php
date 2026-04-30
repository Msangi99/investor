<?php
$pageTitle = 'Dashboards';
$pageDescription = 'Sample dashboard structure for admin, investor, business and stakeholder users.';
$pageName = 'dashboards';
require_once __DIR__ . '/includes/config.php';
include __DIR__ . '/includes/header.php';
?>

<main>
    <section class="page-hero">
        <div class="container">
            <div class="page-kicker"><i class="fa-solid fa-gauge-high"></i> Dashboards</div>
            <h1>Different dashboards for different ecosystem roles.</h1>
            <p>
                The platform can separate access and features based on user role, making the system easier to manage and safer to scale.
            </p>
        </div>
    </section>

    <section class="section">
        <div class="container dashboard-grid">
            <article class="dashboard-card">
                <div class="icon-box"><i class="fa-solid fa-user-shield"></i></div>
                <h3>Admin Dashboard</h3>
                <p>Manage users, verification, roles, reports, platform settings and stakeholder approvals.</p>
                <ul class="feature-list">
                    <li><i class="fa-solid fa-check"></i> User management</li>
                    <li><i class="fa-solid fa-check"></i> Document review</li>
                    <li><i class="fa-solid fa-check"></i> Reports and audit logs</li>
                </ul>
            </article>

            <article class="dashboard-card">
                <div class="icon-box"><i class="fa-solid fa-coins"></i></div>
                <h3>Investor Dashboard</h3>
                <p>View verified businesses, opportunities, sector filters, notes and pipeline status.</p>
                <ul class="feature-list">
                    <li><i class="fa-solid fa-check"></i> Opportunity discovery</li>
                    <li><i class="fa-solid fa-check"></i> Shortlist and notes</li>
                    <li><i class="fa-solid fa-check"></i> Meeting requests</li>
                </ul>
            </article>

            <article class="dashboard-card">
                <div class="icon-box"><i class="fa-solid fa-briefcase"></i></div>
                <h3>Business Dashboard</h3>
                <p>Build company profile, upload documents, track readiness and manage investment requests.</p>
                <ul class="feature-list">
                    <li><i class="fa-solid fa-check"></i> Profile completion</li>
                    <li><i class="fa-solid fa-check"></i> Document vault</li>
                    <li><i class="fa-solid fa-check"></i> Readiness status</li>
                </ul>
            </article>
        </div>
    </section>

    <section class="section section-soft">
        <div class="container">
            <div class="section-heading">
                <span>Sample Metrics</span>
                <h2>Dashboards can turn scattered information into useful insights.</h2>
                <p>These are example metrics for demonstration only.</p>
            </div>

            <div class="cards-grid three-columns">
                <article class="info-card"><div class="icon-box"><i class="fa-solid fa-building-circle-check"></i></div><h3>125</h3><p>Sample businesses in ecosystem pipeline</p></article>
                <article class="info-card"><div class="icon-box"><i class="fa-solid fa-file-circle-check"></i></div><h3>78%</h3><p>Sample document completion rate</p></article>
                <article class="info-card"><div class="icon-box"><i class="fa-solid fa-chart-line"></i></div><h3>12</h3><p>Sample sectors with investment activity</p></article>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
