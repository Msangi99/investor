<?php
$pageTitle = 'Home';
$pageDescription = 'UNIDA Gateway is an Investment Ecosystem Platform that connects verified businesses, investors and institutions through readiness, verification, partner coordination and insights.';
$pageName = 'home';

require_once __DIR__ . '/includes/config.php';
include __DIR__ . '/includes/header.php';
?>

<main>
    <section class="hero-section">
        <div class="container hero-grid">
            <div class="hero-content">
                <div class="eyebrow">
                    <i class="fa-solid fa-shield-halved"></i>
                    UNIDA Gateway · Investment Ecosystem Platform
                </div>

                <h1>
                    A digital gateway for
                    <span>verified opportunities, trusted partners and investment-ready businesses.</span>
                </h1>

                <p class="hero-text">
                    UNIDA Gateway helps businesses, investors, institutions and ecosystem stakeholders connect through
                    structured profiles, verification, investment readiness, partner coordination and data-driven insights.
                </p>

                <div class="hero-actions">
                    <a href="<?= e(BASE_URL); ?>register.php" class="btn btn-primary">
                        <i class="fa-solid fa-user-plus"></i>
                        Create Account
                    </a>

                    <a href="<?= e(BASE_URL); ?>login.php" class="btn btn-light">
                        <i class="fa-solid fa-right-to-bracket"></i>
                        Login
                    </a>

                    <a href="<?= e(BASE_URL); ?>ecosystem.php" class="btn btn-soft">
                        <i class="fa-solid fa-diagram-project"></i>
                        Explore Ecosystem
                    </a>
                </div>

                <div class="hero-stats">
                    <div class="stat-card">
                        <strong>UNIDA Invest</strong>
                        <span>Verified opportunities and investor matching</span>
                    </div>

                    <div class="stat-card">
                        <strong>UNIDA Verify</strong>
                        <span>Business, document and stakeholder verification</span>
                    </div>

                    <div class="stat-card">
                        <strong>UNIDA Insights</strong>
                        <span>Updates, reports, data and analytics</span>
                    </div>
                </div>
            </div>

            <div class="hero-visual">
                <div class="platform-card">
                    <div class="platform-top">
                        <div class="window-dots">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>

                        <small>
                            <i class="fa-solid fa-circle-check"></i>
                            Secure Platform Access
                        </small>
                    </div>

                    <div class="metrics-grid">
                        <div class="metric-card">
                            <i class="fa-solid fa-coins"></i>
                            <h3>Invest</h3>
                            <p>Connect the right businesses, investors, groups and scalable projects.</p>
                        </div>

                        <div class="metric-card">
                            <i class="fa-solid fa-file-shield"></i>
                            <h3>Verify</h3>
                            <p>Organize profiles, documents, readiness status and review records.</p>
                        </div>

                        <div class="metric-card">
                            <i class="fa-solid fa-seedling"></i>
                            <h3>Readiness</h3>
                            <p>Help SMEs, startups and youth innovators prepare for opportunities.</p>
                        </div>

                        <div class="metric-card">
                            <i class="fa-solid fa-chart-pie"></i>
                            <h3>Insights</h3>
                            <p>Track sectors, regions, progress, reports, data and analytics.</p>
                        </div>
                    </div>

                    <div class="process-mini-card">
                        <div>
                            <i class="fa-solid fa-user-plus"></i>
                            User creates an account and selects the right account type
                        </div>

                        <div>
                            <i class="fa-solid fa-right-to-bracket"></i>
                            After login, the user is directed to the right workspace
                        </div>

                        <div>
                            <i class="fa-solid fa-file-shield"></i>
                            Business information and documents are submitted securely
                        </div>

                        <div>
                            <i class="fa-solid fa-handshake"></i>
                            Investors and stakeholders connect through approved access
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section" id="modules">
        <div class="container">
            <div class="section-heading">
                <span>Product Modules</span>
                <h2>UNIDA Gateway is built around five connected modules.</h2>
                <p>
                    Each module solves a specific part of the investment ecosystem: access, verification, readiness,
                    stakeholder coordination and decision-support insights.
                </p>
            </div>

            <div class="cards-grid three-columns">
                <article class="info-card">
                    <div class="icon-box"><i class="fa-solid fa-coins"></i></div>
                    <h3>UNIDA Invest</h3>
                    <p>
                        Helps investors discover verified opportunities and helps businesses reach the right people,
                        companies, groups and institutions without unnecessary delays.
                    </p>
                </article>

                <article class="info-card">
                    <div class="icon-box"><i class="fa-solid fa-file-shield"></i></div>
                    <h3>UNIDA Verify</h3>
                    <p>
                        Supports verification of profiles, documents, business information and stakeholder records to
                        improve trust before investment decisions.
                    </p>
                </article>

                <article class="info-card">
                    <div class="icon-box"><i class="fa-solid fa-chart-line"></i></div>
                    <h3>UNIDA Readiness</h3>
                    <p>
                        Helps SMEs, startups and youth innovators understand what is missing and prepare better for
                        funding, partnerships, loans or institutional support.
                    </p>
                </article>

                <article class="info-card">
                    <div class="icon-box"><i class="fa-solid fa-users-gear"></i></div>
                    <h3>UNIDA Partners</h3>
                    <p>
                        Connects government, banks, hubs, development partners, investors and businesses in one
                        coordinated ecosystem with clear follow-up.
                    </p>
                </article>

                <article class="info-card">
                    <div class="icon-box"><i class="fa-solid fa-chart-pie"></i></div>
                    <h3>UNIDA Insights</h3>
                    <p>
                        Provides updates, reports, data, analytics and decision-support visibility across sectors,
                        regions, readiness levels and investment activity.
                    </p>
                </article>

                <article class="info-card">
                    <div class="icon-box"><i class="fa-solid fa-right-to-bracket"></i></div>
                    <h3>Role-Based Access</h3>
                    <p>
                        Users register or login and are directed to the correct workspace: business, investor,
                        stakeholder or administrator.
                    </p>
                </article>
            </div>
        </div>
    </section>

    <section class="section section-soft" id="value">
        <div class="container solution-grid">
            <div class="solution-panel">
                <span>Why It Matters</span>

                <h2>Technology can reduce delays, improve fairness and support better investment decisions.</h2>

                <p>
                    By organizing information, verification and stakeholder coordination in one platform, UNIDA Gateway
                    helps decision makers see opportunities clearly and connect serious businesses with the right support.
                </p>

                <div class="check-list">
                    <div><i class="fa-solid fa-circle-check"></i> Improve verification and trust between ecosystem actors</div>
                    <div><i class="fa-solid fa-circle-check"></i> Support investment readiness for scalable businesses and youth innovators</div>
                    <div><i class="fa-solid fa-circle-check"></i> Connect opportunities to the right investors, companies and institutions</div>
                    <div><i class="fa-solid fa-circle-check"></i> Reduce unnecessary delays, scattered documents and manual follow-ups</div>
                </div>
            </div>

            <div class="cards-grid two-columns">
                <article class="info-card">
                    <div class="icon-box"><i class="fa-solid fa-building-columns"></i></div>
                    <h3>For Government</h3>
                    <p>
                        Better visibility on sectors, regions, business readiness, investment activity and support gaps.
                    </p>
                </article>

                <article class="info-card">
                    <div class="icon-box"><i class="fa-solid fa-store"></i></div>
                    <h3>For Businesses</h3>
                    <p>
                        A guided path to organize profiles, improve readiness and access the right opportunities.
                    </p>
                </article>

                <article class="info-card">
                    <div class="icon-box"><i class="fa-solid fa-coins"></i></div>
                    <h3>For Investors</h3>
                    <p>
                        Faster screening of verified opportunities, clearer pipelines and improved confidence.
                    </p>
                </article>

                <article class="info-card">
                    <div class="icon-box"><i class="fa-solid fa-people-group"></i></div>
                    <h3>For Citizens</h3>
                    <p>
                        Stronger businesses can create jobs, improve services and increase economic participation.
                    </p>
                </article>
            </div>
        </div>
    </section>

    <section class="section" id="process">
        <div class="container">
            <div class="section-heading">
                <span>How It Works</span>
                <h2>A simple journey from account creation to role-based workspace.</h2>
                <p>
                    Dashboards are not public pages. A user must register or login first, then the system directs them
                    to the correct workspace based on role.
                </p>
            </div>

            <div class="steps-grid">
                <article class="step-card">
                    <strong>01</strong>
                    <h3>Create Account</h3>
                    <p>A user registers as a business, investor or ecosystem stakeholder.</p>
                </article>

                <article class="step-card">
                    <strong>02</strong>
                    <h3>Login Securely</h3>
                    <p>The system checks the account and reads the user role from the database.</p>
                </article>

                <article class="step-card">
                    <strong>03</strong>
                    <h3>Open Workspace</h3>
                    <p>The user is redirected to the correct workspace: business, investor or stakeholder.</p>
                </article>

                <article class="step-card">
                    <strong>04</strong>
                    <h3>Coordinate Action</h3>
                    <p>Users manage readiness, verification, opportunities, reports and stakeholder follow-up.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="cta-section">
        <div class="container">
            <div class="cta-box">
                <div>
                    <h2>Start using UNIDA Gateway.</h2>

                    <p>
                        Create an account to access your workspace based on your role, or login if you already have an account.
                    </p>
                </div>

                <div class="cta-actions">
                    <a href="<?= e(BASE_URL); ?>register.php" class="btn btn-white">
                        <i class="fa-solid fa-user-plus"></i>
                        Create Account
                    </a>

                    <a href="<?= e(BASE_URL); ?>login.php" class="btn btn-ghost">
                        <i class="fa-solid fa-right-to-bracket"></i>
                        Login
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
