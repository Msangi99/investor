<?php
$pageTitle = 'Verification';
$pageDescription = 'A hypothetical verification workflow for business profiles, documents, compliance readiness and investor confidence.';
$pageName = 'verification';
require_once __DIR__ . '/includes/config.php';
include __DIR__ . '/includes/header.php';
?>

<main>
    <section class="page-hero">
        <div class="container">
            <div class="page-kicker"><i class="fa-solid fa-shield-halved"></i> Verification</div>
            <h1>Verification creates trust before investment conversations begin.</h1>
            <p>
                This page explains a sample workflow for validating business information, documents and readiness status before stakeholders make decisions.
            </p>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="cards-grid three-columns">
                <article class="info-card">
                    <div class="icon-box"><i class="fa-solid fa-id-card"></i></div>
                    <h3>Identity & Role Check</h3>
                    <p>Confirm user role, organization type and authorized representative details.</p>
                </article>
                <article class="info-card">
                    <div class="icon-box"><i class="fa-solid fa-file-lines"></i></div>
                    <h3>Document Review</h3>
                    <p>Review business registration, tax documents, licenses, pitch decks and financial summaries.</p>
                </article>
                <article class="info-card">
                    <div class="icon-box"><i class="fa-solid fa-chart-line"></i></div>
                    <h3>Readiness Score</h3>
                    <p>Generate a simple readiness status based on profile completion, documents and traction.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="section section-soft">
        <div class="container content-grid">
            <div class="solution-panel">
                <span>Verification Status</span>
                <h2>Simple status labels make the process clear.</h2>
                <p>Each document or profile can move through clear states so users know exactly what action is needed.</p>
                <div class="check-list">
                    <div><i class="fa-solid fa-circle-check"></i> Pending Review</div>
                    <div><i class="fa-solid fa-circle-check"></i> Verified</div>
                    <div><i class="fa-solid fa-circle-check"></i> Needs Update</div>
                    <div><i class="fa-solid fa-circle-check"></i> Rejected with comment</div>
                </div>
            </div>

            <div class="flow-list">
                <article class="flow-block"><div class="flow-number">1</div><div><h3>Submit Profile</h3><p>Business completes profile and uploads required information.</p></div></article>
                <article class="flow-block"><div class="flow-number">2</div><div><h3>Admin Review</h3><p>Authorized reviewer checks documents, consistency and completeness.</p></div></article>
                <article class="flow-block"><div class="flow-number">3</div><div><h3>Status Update</h3><p>The system updates readiness level and sends feedback to the business.</p></div></article>
                <article class="flow-block"><div class="flow-number">4</div><div><h3>Investor Visibility</h3><p>Verified businesses become easier to review and shortlist.</p></div></article>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
