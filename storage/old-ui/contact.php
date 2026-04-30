<?php
$pageTitle = 'Contact';
$pageDescription = 'Contact UNIDA TECH LIMITED for platform support, partnership discussion or institutional collaboration.';
$pageName = 'contact';

require_once __DIR__ . '/includes/config.php';
include __DIR__ . '/includes/header.php';
?>

<main>
    <section class="page-hero">
        <div class="container">
            <div class="page-kicker">
                <i class="fa-solid fa-paper-plane"></i>
                Contact
            </div>

            <h1>Contact UNIDA for support, partnership or institutional discussion.</h1>

            <p>
                Use this page to reach UNIDA TECH LIMITED for platform support, stakeholder collaboration,
                investment ecosystem inquiries or partnership opportunities.
            </p>
        </div>
    </section>

    <section class="section">
        <div class="container contact-grid">
            <div class="info-card">
                <div class="icon-box">
                    <i class="fa-solid fa-headset"></i>
                </div>

                <h3>UNIDA TECH LIMITED</h3>

                <p>
                    We support businesses, investors, institutions and ecosystem stakeholders using the
                    Investment Ecosystem Gateway.
                </p>

                <ul class="feature-list" style="margin-top:16px;">
                    <li>
                        <i class="fa-solid fa-envelope"></i>
                        <a href="mailto:<?= e(COMPANY_EMAIL); ?>">
                            <?= e(COMPANY_EMAIL); ?>
                        </a>
                    </li>

                    <li>
                        <i class="fa-solid fa-phone"></i>
                        <a href="tel:+255762494775">
                            <?= e(COMPANY_PHONE); ?>
                        </a>
                    </li>

                    <li>
                        <i class="fa-solid fa-globe"></i>
                        <a href="<?= e(COMPANY_URL); ?>" target="_blank" rel="noopener">
                            <?= e(COMPANY_NAME); ?>
                        </a>
                    </li>

                    <li>
                        <i class="fa-solid fa-location-dot"></i>
                        Tanzania
                    </li>
                </ul>

                <div style="margin-top:22px;">
                    <a href="<?= e(BASE_URL); ?>register.php" class="btn btn-primary">
                        <i class="fa-solid fa-user-plus"></i>
                        Create Account
                    </a>
                </div>
            </div>

            <form class="auth-card form-grid" method="post" action="">
                <div>
                    <h1>Send Message</h1>
                    <p class="auth-note">
                        Send your inquiry to UNIDA TECH LIMITED. Our team will review your message and respond accordingly.
                    </p>
                </div>

                <div class="form-grid two">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input
                            class="form-control"
                            type="text"
                            name="name"
                            placeholder="Your full name"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label>Organization</label>
                        <input
                            class="form-control"
                            type="text"
                            name="organization"
                            placeholder="Organization name"
                        >
                    </div>
                </div>

                <div class="form-grid two">
                    <div class="form-group">
                        <label>Email</label>
                        <input
                            class="form-control"
                            type="email"
                            name="email"
                            placeholder="you@example.com"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label>Phone</label>
                        <input
                            class="form-control"
                            type="tel"
                            name="phone"
                            placeholder="+255..."
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label>Inquiry Type</label>
                    <select class="form-control" name="interest" required>
                        <option value="">Select inquiry type</option>
                        <option value="Platform Support">Platform Support</option>
                        <option value="Partnership Discussion">Partnership Discussion</option>
                        <option value="Government or Institutional Collaboration">Government or Institutional Collaboration</option>
                        <option value="Investor Access">Investor Access</option>
                        <option value="Business Account Support">Business Account Support</option>
                        <option value="Verification Support">Verification Support</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Message</label>
                    <textarea
                        class="form-control"
                        name="message"
                        placeholder="Write your message..."
                        required
                    ></textarea>
                </div>

                <button class="btn btn-primary" type="submit">
                    <i class="fa-solid fa-paper-plane"></i>
                    Send Message
                </button>
            </form>
        </div>
    </section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>