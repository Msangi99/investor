<?php
$pageTitle = 'Opportunities';
$pageDescription = 'Discover verified investment-ready businesses, scalable projects and sector opportunities through UNIDA Gateway.';
$pageName = 'opportunities';

require_once __DIR__ . '/includes/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['user_id']);
$userRole = $_SESSION['user_role'] ?? '';

$totalOpportunities = 0;
$verifiedOpportunities = 0;
$publishedOpportunities = 0;
$sectors = [];
$featuredOpportunities = [];

try {
    $pdo = db();

    $stmt = $pdo->query("
        SELECT COUNT(*) AS total
        FROM investment_opportunities
        WHERE status IN ('published', 'under_review')
    ");
    $totalOpportunities = (int) ($stmt->fetch()['total'] ?? 0);

    $stmt = $pdo->query("
        SELECT COUNT(*) AS total
        FROM investment_opportunities
        WHERE verification_status = 'verified'
        AND status IN ('published', 'under_review')
    ");
    $verifiedOpportunities = (int) ($stmt->fetch()['total'] ?? 0);

    $stmt = $pdo->query("
        SELECT COUNT(*) AS total
        FROM investment_opportunities
        WHERE status = 'published'
    ");
    $publishedOpportunities = (int) ($stmt->fetch()['total'] ?? 0);

    $stmt = $pdo->query("
        SELECT sector, COUNT(*) AS total
        FROM investment_opportunities
        WHERE status IN ('published', 'under_review')
        GROUP BY sector
        ORDER BY total DESC
        LIMIT 6
    ");
    $sectors = $stmt->fetchAll();

    $stmt = $pdo->query("
        SELECT 
            io.title,
            io.summary,
            io.sector,
            io.region,
            io.stage,
            io.funding_type,
            io.funding_amount,
            io.currency,
            io.readiness_score,
            io.verification_status,
            bp.business_name
        FROM investment_opportunities io
        LEFT JOIN business_profiles bp ON bp.id = io.business_profile_id
        WHERE io.status = 'published'
        ORDER BY 
            CASE WHEN io.verification_status = 'verified' THEN 0 ELSE 1 END,
            io.readiness_score DESC,
            io.created_at DESC
        LIMIT 3
    ");
    $featuredOpportunities = $stmt->fetchAll();
} catch (Throwable $e) {
    $totalOpportunities = 0;
    $verifiedOpportunities = 0;
    $publishedOpportunities = 0;
    $sectors = [];
    $featuredOpportunities = [];
}

function opportunity_money_short($amount, $currency = 'TZS') {
    if (!$amount) {
        return 'Amount not specified';
    }

    $amount = (float) $amount;

    if ($amount >= 1000000000) {
        return $currency . ' ' . number_format($amount / 1000000000, 1) . 'B';
    }

    if ($amount >= 1000000) {
        return $currency . ' ' . number_format($amount / 1000000, 1) . 'M';
    }

    if ($amount >= 1000) {
        return $currency . ' ' . number_format($amount / 1000, 1) . 'K';
    }

    return $currency . ' ' . number_format($amount);
}

include __DIR__ . '/includes/header.php';
?>

<main>
    <section class="page-hero">
        <div class="container">
            <div class="page-kicker">
                <i class="fa-solid fa-briefcase"></i>
                UNIDA Invest
            </div>

            <h1>Discover verified opportunities, investment-ready businesses and scalable projects.</h1>

            <p>
                UNIDA Invest helps investors, institutions and ecosystem stakeholders discover organized,
                verified and investment-ready businesses across Tanzania and beyond.
            </p>

            <div class="hero-actions" style="margin-top:22px;">
                <?php if ($isLoggedIn): ?>
                    <a href="<?= e(dashboard_url_by_role($userRole)); ?>" class="btn btn-primary">
                        <i class="fa-solid fa-gauge-high"></i>
                        Go to My Workspace
                    </a>
                <?php else: ?>
                    <a href="<?= e(BASE_URL); ?>register.php" class="btn btn-primary">
                        <i class="fa-solid fa-user-plus"></i>
                        Create Account
                    </a>

                    <a href="<?= e(BASE_URL); ?>login.php" class="btn btn-light">
                        <i class="fa-solid fa-right-to-bracket"></i>
                        Login
                    </a>
                <?php endif; ?>

                <a href="<?= e(BASE_URL); ?>verification.php" class="btn btn-soft">
                    <i class="fa-solid fa-shield-halved"></i>
                    View Verification Process
                </a>
            </div>
        </div>
    </section>

    <section class="section section-soft">
        <div class="container">
            <div class="dashboard-stat-grid">
                <article class="dash-stat">
                    <span class="dash-icon">
                        <i class="fa-solid fa-briefcase"></i>
                    </span>

                    <div>
                        <strong><?= e($totalOpportunities); ?></strong>
                        <small>Total opportunities</small>
                    </div>
                </article>

                <article class="dash-stat">
                    <span class="dash-icon green">
                        <i class="fa-solid fa-circle-check"></i>
                    </span>

                    <div>
                        <strong><?= e($verifiedOpportunities); ?></strong>
                        <small>Verified opportunities</small>
                    </div>
                </article>

                <article class="dash-stat">
                    <span class="dash-icon cyan">
                        <i class="fa-solid fa-eye"></i>
                    </span>

                    <div>
                        <strong><?= e($publishedOpportunities); ?></strong>
                        <small>Published opportunities</small>
                    </div>
                </article>

                <article class="dash-stat">
                    <span class="dash-icon dark">
                        <i class="fa-solid fa-lock"></i>
                    </span>

                    <div>
                        <strong>Role-Based</strong>
                        <small>Access control</small>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container content-grid">
            <div class="solution-panel">
                <span>Opportunity Access</span>

                <h2>Only verified and approved users can access full opportunity details.</h2>

                <p>
                    UNIDA Gateway protects sensitive business information through verification, role-based access,
                    readiness scoring and admin approval. Public visitors can view the ecosystem direction, while
                    verified users access deeper details through their workspace.
                </p>

                <div class="check-list">
                    <div>
                        <i class="fa-solid fa-circle-check"></i>
                        Businesses must complete profile and verification before publishing opportunities.
                    </div>

                    <div>
                        <i class="fa-solid fa-circle-check"></i>
                        Investors must complete investor profile before viewing restricted opportunity details.
                    </div>

                    <div>
                        <i class="fa-solid fa-circle-check"></i>
                        Stakeholders must complete organization profile before sending recommendations.
                    </div>

                    <div>
                        <i class="fa-solid fa-circle-check"></i>
                        Admin reviewers approve, reject or request updates before full visibility.
                    </div>
                </div>
            </div>

            <div class="cards-grid two-columns">
                <article class="info-card">
                    <div class="icon-box">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>

                    <h3>Verified Records</h3>

                    <p>
                        Every opportunity can be connected to verification status, document review, readiness score and
                        admin decision.
                    </p>
                </article>

                <article class="info-card">
                    <div class="icon-box">
                        <i class="fa-solid fa-scale-balanced"></i>
                    </div>

                    <h3>Fair Access</h3>

                    <p>
                        Structured verification helps reduce bias, unnecessary delays and informal gatekeeping in
                        opportunity discovery.
                    </p>
                </article>

                <article class="info-card">
                    <div class="icon-box">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>

                    <h3>Readiness Score</h3>

                    <p>
                        Investors and stakeholders can review readiness indicators before deeper engagement or support.
                    </p>
                </article>

                <article class="info-card">
                    <div class="icon-box">
                        <i class="fa-solid fa-user-lock"></i>
                    </div>

                    <h3>Protected Details</h3>

                    <p>
                        Sensitive documents, contacts and financial details remain restricted to approved users only.
                    </p>
                </article>
            </div>
        </div>
    </section>

    <section class="section section-soft">
        <div class="container">
            <div class="section-heading">
                <span>Discovery Filters</span>

                <h2>Opportunities can be discovered by sector, region, stage, funding type and readiness.</h2>

                <p>
                    UNIDA Gateway is designed to help investors and stakeholders find the right businesses,
                    projects and groups using structured filters and verification badges.
                </p>
            </div>

            <div class="cards-grid three-columns">
                <article class="info-card">
                    <div class="icon-box">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>

                    <h3>Industry / Sector</h3>

                    <p>
                        Agriculture, health technology, energy, cooling, education, fintech, manufacturing, logistics,
                        tourism, ICT, climate and other sectors.
                    </p>
                </article>

                <article class="info-card">
                    <div class="icon-box">
                        <i class="fa-solid fa-location-dot"></i>
                    </div>

                    <h3>Region / Location</h3>

                    <p>
                        Filter by country, region, city, district, ward, street or operational coverage.
                    </p>
                </article>

                <article class="info-card">
                    <div class="icon-box">
                        <i class="fa-solid fa-seedling"></i>
                    </div>

                    <h3>Business Stage</h3>

                    <p>
                        Idea, prototype, MVP, early revenue, growth and scale-stage opportunities.
                    </p>
                </article>

                <article class="info-card">
                    <div class="icon-box">
                        <i class="fa-solid fa-coins"></i>
                    </div>

                    <h3>Funding Type</h3>

                    <p>
                        Equity, debt, grant, asset finance, partnership, technical support or blended support.
                    </p>
                </article>

                <article class="info-card">
                    <div class="icon-box">
                        <i class="fa-solid fa-chart-simple"></i>
                    </div>

                    <h3>Readiness Level</h3>

                    <p>
                        Match opportunities by readiness score, document status, traction and profile completion.
                    </p>
                </article>

                <article class="info-card">
                    <div class="icon-box">
                        <i class="fa-solid fa-badge-check"></i>
                    </div>

                    <h3>Status Badges</h3>

                    <p>
                        Verified, approved, pending, submitted, under review, needs update, rejected and expired.
                    </p>
                </article>
            </div>
        </div>
    </section>

    <section class="section" id="sectors">
        <div class="container">
            <div class="section-heading">
                <span>Opportunity Sectors</span>

                <h2>Explore sectors supported by UNIDA Gateway.</h2>

                <p>
                    The platform can organize investment-ready businesses, scalable projects and special groups
                    across multiple sectors.
                </p>
            </div>

            <div class="cards-grid three-columns">
                <?php if (!empty($sectors)): ?>
                    <?php foreach ($sectors as $sector): ?>
                        <article class="info-card">
                            <div class="icon-box">
                                <i class="fa-solid fa-chart-pie"></i>
                            </div>

                            <h3><?= e($sector['sector'] ?: 'Unspecified Sector'); ?></h3>

                            <p>
                                <?= e($sector['total']); ?> opportunity record(s) currently organized under this sector.
                            </p>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <article class="info-card">
                        <div class="icon-box">
                            <i class="fa-solid fa-leaf"></i>
                        </div>

                        <h3>Agribusiness</h3>

                        <p>
                            Farming, processing, aggregation, market access, food systems and cold chain opportunities.
                        </p>
                    </article>

                    <article class="info-card">
                        <div class="icon-box">
                            <i class="fa-solid fa-heart-pulse"></i>
                        </div>

                        <h3>Health Technology</h3>

                        <p>
                            Digital health, diagnostics, telemedicine, access to care and health service innovation.
                        </p>
                    </article>

                    <article class="info-card">
                        <div class="icon-box">
                            <i class="fa-solid fa-solar-panel"></i>
                        </div>

                        <h3>Energy & Cooling</h3>

                        <p>
                            Clean energy, smart cooling, refrigeration, cold rooms and energy-aware infrastructure.
                        </p>
                    </article>

                    <article class="info-card">
                        <div class="icon-box">
                            <i class="fa-solid fa-graduation-cap"></i>
                        </div>

                        <h3>Education</h3>

                        <p>
                            Learning platforms, skills development, scholarships, training and digital education tools.
                        </p>
                    </article>

                    <article class="info-card">
                        <div class="icon-box">
                            <i class="fa-solid fa-mobile-screen"></i>
                        </div>

                        <h3>Fintech & Digital Services</h3>

                        <p>
                            Payments, wallets, business tools, financial access and digital service platforms.
                        </p>
                    </article>

                    <article class="info-card">
                        <div class="icon-box">
                            <i class="fa-solid fa-truck-fast"></i>
                        </div>

                        <h3>Logistics & Market Access</h3>

                        <p>
                            Distribution, transport, marketplace systems, fulfillment and supply chain support.
                        </p>
                    </article>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="section section-soft" id="featured">
        <div class="container">
            <div class="section-heading">
                <span>Featured Opportunities</span>

                <h2>Published opportunities appear here after verification and readiness review.</h2>

                <p>
                    Public visitors see limited information only. Approved investors and stakeholders can access
                    deeper opportunity information after login and verification.
                </p>
            </div>

            <div class="cards-grid three-columns">
                <?php if (!empty($featuredOpportunities)): ?>
                    <?php foreach ($featuredOpportunities as $opportunity): ?>
                        <article class="info-card">
                            <div class="icon-box">
                                <i class="fa-solid fa-briefcase"></i>
                            </div>

                            <h3><?= e($opportunity['title']); ?></h3>

                            <p>
                                <?= e($opportunity['summary']); ?>
                            </p>

                            <div class="opportunity-meta" style="margin-top:14px;">
                                <span>
                                    <i class="fa-solid fa-building"></i>
                                    <?= e($opportunity['business_name'] ?? 'Business'); ?>
                                </span>

                                <span>
                                    <i class="fa-solid fa-location-dot"></i>
                                    <?= e($opportunity['region'] ?: 'Tanzania'); ?>
                                </span>

                                <span>
                                    <i class="fa-solid fa-seedling"></i>
                                    <?= e(ucfirst(str_replace('_', ' ', $opportunity['stage']))); ?>
                                </span>

                                <span>
                                    <i class="fa-solid fa-chart-line"></i>
                                    <?= e((int) $opportunity['readiness_score']); ?>% readiness
                                </span>
                            </div>

                            <div style="margin-top:14px;">
                                <strong>
                                    <?= e(opportunity_money_short($opportunity['funding_amount'], $opportunity['currency'] ?: 'TZS')); ?>
                                </strong>
                            </div>

                            <div style="margin-top:16px;">
                                <span class="status-badge <?= $opportunity['verification_status'] === 'verified' ? 'status-verified' : 'status-progress'; ?>">
                                    <i class="fa-solid <?= $opportunity['verification_status'] === 'verified' ? 'fa-circle-check' : 'fa-spinner'; ?>"></i>
                                    <?= e(ucfirst(str_replace('_', ' ', $opportunity['verification_status']))); ?>
                                </span>
                            </div>

                            <div style="margin-top:16px;">
                                <?php if ($isLoggedIn && in_array($userRole, ['investor', 'stakeholder', 'admin'], true)): ?>
                                    <a href="<?= e(dashboard_url_by_role($userRole)); ?>" class="btn btn-soft">
                                        <i class="fa-solid fa-eye"></i>
                                        View in Workspace
                                    </a>
                                <?php else: ?>
                                    <a href="<?= e(BASE_URL); ?>login.php" class="btn btn-soft">
                                        <i class="fa-solid fa-lock"></i>
                                        Login for Details
                                    </a>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <article class="info-card">
                        <div class="icon-box">
                            <i class="fa-solid fa-lock"></i>
                        </div>

                        <h3>Verified Listings Coming Soon</h3>

                        <p>
                            Published opportunities will appear here after businesses complete profile, document
                            submission, readiness review and verification.
                        </p>
                    </article>

                    <article class="info-card">
                        <div class="icon-box">
                            <i class="fa-solid fa-user-plus"></i>
                        </div>

                        <h3>Business Onboarding</h3>

                        <p>
                            Businesses can create an account, complete readiness, upload documents and submit for
                            verification.
                        </p>
                    </article>

                    <article class="info-card">
                        <div class="icon-box">
                            <i class="fa-solid fa-coins"></i>
                        </div>

                        <h3>Investor Access</h3>

                        <p>
                            Investors can complete their profile and access verified opportunities based on approval.
                        </p>
                    </article>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="section" id="verification-conditions">
        <div class="container content-grid">
            <div class="solution-panel">
                <span>Verification Conditions</span>

                <h2>Opportunities become visible after required conditions are completed.</h2>

                <p>
                    UNIDA Gateway supports a structured verification process so investors and stakeholders can make
                    better decisions while businesses receive fair and organized visibility.
                </p>

                <div class="check-list">
                    <div>
                        <i class="fa-solid fa-circle-check"></i>
                        Complete business or organization profile
                    </div>

                    <div>
                        <i class="fa-solid fa-circle-check"></i>
                        Submit required identity and registration documents
                    </div>

                    <div>
                        <i class="fa-solid fa-circle-check"></i>
                        Accept Terms of Use, Privacy Policy and verification consent
                    </div>

                    <div>
                        <i class="fa-solid fa-circle-check"></i>
                        Receive admin review decision: approved, needs update or rejected
                    </div>

                    <div>
                        <i class="fa-solid fa-circle-check"></i>
                        Maintain active verification status to publish or access restricted details
                    </div>
                </div>
            </div>

            <div class="cards-grid two-columns">
                <article class="info-card">
                    <div class="icon-box">
                        <i class="fa-solid fa-id-card"></i>
                    </div>

                    <h3>Identity Documents</h3>

                    <p>
                        NIDA, passport, voter ID, driving license, representative ID or official institutional
                        authorization may be required.
                    </p>
                </article>

                <article class="info-card">
                    <div class="icon-box">
                        <i class="fa-solid fa-file-signature"></i>
                    </div>

                    <h3>Business Documents</h3>

                    <p>
                        Registration certificate, business license, TIN, company profile, pitch deck or financial
                        summary may be required depending on user category.
                    </p>
                </article>

                <article class="info-card">
                    <div class="icon-box">
                        <i class="fa-solid fa-clock"></i>
                    </div>

                    <h3>Submission Timeline</h3>

                    <p>
                        Users should complete required verification within the allowed period. Expired submissions may
                        require restart or resubmission.
                    </p>
                </article>

                <article class="info-card">
                    <div class="icon-box">
                        <i class="fa-solid fa-ban"></i>
                    </div>

                    <h3>Access Restriction</h3>

                    <p>
                        Unverified users cannot access sensitive opportunities, full business documents or private
                        connection actions.
                    </p>
                </article>
            </div>
        </div>
    </section>

    <section class="cta-section">
        <div class="container">
            <div class="cta-box">
                <div>
                    <h2>Join UNIDA Gateway and access the right workspace.</h2>

                    <p>
                        Create an account as a business, investor or stakeholder and complete your verification process
                        to access the right tools.
                    </p>
                </div>

                <div class="cta-actions">
                    <?php if ($isLoggedIn): ?>
                        <a href="<?= e(dashboard_url_by_role($userRole)); ?>" class="btn btn-white">
                            <i class="fa-solid fa-gauge-high"></i>
                            My Workspace
                        </a>
                    <?php else: ?>
                        <a href="<?= e(BASE_URL); ?>register.php" class="btn btn-white">
                            <i class="fa-solid fa-user-plus"></i>
                            Create Account
                        </a>

                        <a href="<?= e(BASE_URL); ?>login.php" class="btn btn-ghost">
                            <i class="fa-solid fa-right-to-bracket"></i>
                            Login
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>