<?php
$pageTitle = 'Workspaces';
$pageDescription = 'UNIDA Gateway role-based workspaces for businesses, investors, stakeholders and administrators.';
$pageName = 'dashboards';

require_once __DIR__ . '/includes/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id']) && !empty($_SESSION['user_role'])) {
    redirect_by_role($_SESSION['user_role']);
}

include __DIR__ . '/includes/header.php';
?>

<main>
    <section class="page-hero">
        <div class="container">
            <div class="page-kicker">
                <i class="fa-solid fa-gauge-high"></i>
                Role-Based Workspaces
            </div>

            <h1>Access the right workspace based on your role.</h1>

            <p>
                UNIDA Gateway provides separate dashboards for businesses, investors, stakeholders and administrators.
                Create an account or login to continue.
            </p>

            <div class="hero-actions" style="margin-top:22px;">
                <a href="<?= e(BASE_URL); ?>login.php" class="btn btn-primary">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    Login
                </a>

                <a href="<?= e(BASE_URL); ?>register.php" class="btn btn-light">
                    <i class="fa-solid fa-user-plus"></i>
                    Create Account
                </a>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container cards-grid four-columns">
            <article class="info-card">
                <div class="icon-box"><i class="fa-solid fa-building"></i></div>
                <h3>Business Workspace</h3>
                <p>Manage profile, documents, readiness, verification and investment requests.</p>
            </article>

            <article class="info-card">
                <div class="icon-box"><i class="fa-solid fa-coins"></i></div>
                <h3>Investor Workspace</h3>
                <p>Discover verified opportunities, shortlist businesses and request meetings.</p>
            </article>

            <article class="info-card">
                <div class="icon-box"><i class="fa-solid fa-building-columns"></i></div>
                <h3>Stakeholder Workspace</h3>
                <p>Coordinate support, recommendations, connections, follow-ups and reports.</p>
            </article>

            <article class="info-card">
                <div class="icon-box"><i class="fa-solid fa-user-shield"></i></div>
                <h3>Admin Workspace</h3>
                <p>Manage users, roles, verification, opportunities, insights and system activity.</p>
            </article>
        </div>
    </section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>