<?php
$pageTitle = 'Verification Policy';
$pageDescription = 'Verification Policy for UNIDA Gateway by UNIDA TECH LIMITED.';
$pageName = 'verification-policy';

require_once __DIR__ . '/includes/config.php';
include __DIR__ . '/includes/header.php';
?>

<main>
    <section class="page-hero">
        <div class="container">
            <div class="page-kicker"><i class="fa-solid fa-file-shield"></i> Verification Policy</div>
            <h1>Verification Policy for UNIDA Gateway.</h1>
            <p>This policy explains how verification works for businesses, investors, stakeholders, groups, institutions and opportunities.</p>
        </div>
    </section>

    <section class="section">
        <div class="container legal-page">
            <article class="dashboard-panel legal-panel">
                <h2>1. Purpose of Verification</h2>
                <p>Verification helps improve trust, reduce false submissions, support fair access, organize business readiness and protect sensitive opportunity information.</p>
            </article>

            <article class="dashboard-panel legal-panel">
                <h2>2. Verification Statuses</h2>
                <div class="status-grid">
                    <span class="status-badge status-open"><i class="fa-solid fa-circle-info"></i> Unverified</span>
                    <span class="status-badge status-open"><i class="fa-solid fa-upload"></i> Submitted</span>
                    <span class="status-badge status-progress"><i class="fa-solid fa-spinner"></i> Pending</span>
                    <span class="status-badge status-progress"><i class="fa-solid fa-magnifying-glass"></i> Under Review</span>
                    <span class="status-badge status-verified"><i class="fa-solid fa-circle-check"></i> Approved</span>
                    <span class="status-badge status-verified"><i class="fa-solid fa-shield-halved"></i> Verified</span>
                    <span class="status-badge status-warning"><i class="fa-solid fa-pen"></i> Needs Update</span>
                    <span class="status-badge status-danger"><i class="fa-solid fa-ban"></i> Rejected</span>
                    <span class="status-badge status-danger"><i class="fa-solid fa-clock"></i> Expired</span>
                    <span class="status-badge status-danger"><i class="fa-solid fa-lock"></i> Suspended</span>
                </div>
            </article>

            <article class="dashboard-panel legal-panel">
                <h2>3. General Verification Steps</h2>
                <div class="check-list">
                    <div><i class="fa-solid fa-circle-check"></i> Create account and select the correct role.</div>
                    <div><i class="fa-solid fa-circle-check"></i> Complete representative, business, investor or organization profile.</div>
                    <div><i class="fa-solid fa-circle-check"></i> Provide full address and contact details.</div>
                    <div><i class="fa-solid fa-circle-check"></i> Upload required identity, registration or authorization documents.</div>
                    <div><i class="fa-solid fa-circle-check"></i> Accept Terms, Privacy Policy and verification consent.</div>
                    <div><i class="fa-solid fa-circle-check"></i> Submit for review and wait for admin decision.</div>
                </div>
            </article>

            <article class="dashboard-panel legal-panel">
                <h2>4. Required Documents by Category</h2>
                <h3>Business / SME / Startup</h3>
                <p>Business registration certificate, business license where applicable, tax document/TIN, representative ID, proof of address, company profile, pitch deck and financial summary where available.</p>
                <h3>Individual / Sole Proprietor</h3>
                <p>NIDA, passport, voter ID or driving license, TIN where applicable, business license where applicable, proof of address and business description.</p>
                <h3>Group / Community Project / Special Category</h3>
                <p>Group name, leader identity, member list, activity description, proof of address, group registration where available and introduction letter or confirmation from WEO, VEO or another recognized local authority where required.</p>
                <h3>Investor</h3>
                <p>Investor profile, representative ID, organization document where applicable, investment mandate where applicable, preferred sectors, ticket range and investment focus.</p>
                <h3>Stakeholder / Institution / NGO / Government / Hub</h3>
                <p>Organization profile, registration document where applicable, authorization letter, official email, representative ID, office address, focus areas, regions covered and support services.</p>
            </article>

            <article class="dashboard-panel legal-panel">
                <h2>5. Seven-Day Submission Rule</h2>
                <p>Users should complete required verification submissions within the allowed period. If required documents are not completed within 7 days after starting verification, the submission may expire and the user may be required to restart the verification process.</p>
            </article>

            <article class="dashboard-panel legal-panel">
                <h2>6. Access Restrictions</h2>
                <div class="check-list">
                    <div><i class="fa-solid fa-circle-check"></i> Unverified businesses cannot publish investment opportunities.</div>
                    <div><i class="fa-solid fa-circle-check"></i> Pending users may edit profiles and upload documents but may not access restricted features.</div>
                    <div><i class="fa-solid fa-circle-check"></i> Rejected users must correct issues before resubmission.</div>
                    <div><i class="fa-solid fa-circle-check"></i> Verified businesses may publish opportunities and receive investor/stakeholder attention.</div>
                    <div><i class="fa-solid fa-circle-check"></i> Investors and stakeholders may need verification before accessing restricted actions.</div>
                </div>
            </article>

            <article class="dashboard-panel legal-panel">
                <h2>7. Review Decisions and False Information</h2>
                <p>Admin reviewers may approve, reject, request updates, suspend or verify submissions. False documents, misleading claims, impersonation or misuse may lead to rejection, suspension or further action.</p>
                <p><strong>Last updated:</strong> <?= date('F d, Y'); ?></p>
            </article>

            <article class="dashboard-panel legal-panel legal-note">
                <h2>Important Note</h2>
                <p>This is a product-ready draft for UNIDA Gateway. It should be reviewed and confirmed by a qualified legal professional before official public launch.</p>
            </article>
        </div>
    </section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>