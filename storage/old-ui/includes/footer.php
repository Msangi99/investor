<footer class="site-footer">
    <div class="container footer-grid">
        <div class="footer-about">
            <a href="<?= e(COMPANY_URL); ?>" target="_blank" rel="noopener" class="footer-brand">
                <span class="brand-mark small">
                    <span class="drop drop-blue"></span>
                    <span class="drop drop-green"></span>
                </span>

                <span>
                    <strong><?= e(APP_NAME); ?></strong>
                    <small><?= e(APP_SUBTITLE); ?></small>
                </span>
            </a>

            <p>
                UNIDA Gateway helps businesses, investors and institutions work smarter through verification,
                readiness tracking, stakeholder coordination and data-driven ecosystem insights.
            </p>
        </div>

        <div>
            <h4>Platform</h4>
            <a href="<?= e(BASE_URL); ?>ecosystem.php">Ecosystem Flow</a>
            <a href="<?= e(BASE_URL); ?>verification.php">UNIDA Verify</a>
            <a href="<?= e(BASE_URL); ?>opportunities.php">UNIDA Invest</a>
            <a href="<?= e(BASE_URL); ?>register.php">Create Account</a>
        </div>

        <div>
            <h4>Modules</h4>
            <a href="<?= e(BASE_URL); ?>opportunities.php">UNIDA Invest</a>
            <a href="<?= e(BASE_URL); ?>verification.php">UNIDA Verify</a>
            <a href="<?= e(BASE_URL); ?>ecosystem.php#readiness">UNIDA Readiness</a>
            <a href="<?= e(BASE_URL); ?>ecosystem.php#partners">UNIDA Partners</a>
            <a href="<?= e(BASE_URL); ?>ecosystem.php#insights">UNIDA Insights</a>
        </div>

        <div>
            <h4>Contact</h4>
            <a href="<?= e(COMPANY_URL); ?>" target="_blank" rel="noopener">
                <?= e(COMPANY_NAME); ?>
            </a>

            <a href="mailto:<?= e(COMPANY_EMAIL); ?>">
                <?= e(COMPANY_EMAIL); ?>
            </a>

            <a href="tel:+255762494775">
                <?= e(COMPANY_PHONE); ?>
            </a>

            <a href="<?= e(COMPANY_URL); ?>" target="_blank" rel="noopener">
                About UNIDA
            </a>
        </div>
    </div>

    <div class="container footer-bottom">
        <span>
            © <?= date('Y'); ?> <?= e(COMPANY_NAME); ?>. All rights reserved.
        </span>

        <span>
            Designed and developed by
            <a href="<?= e(COMPANY_URL); ?>" target="_blank" rel="noopener">
                <?= e(COMPANY_NAME); ?>
            </a>
        </span>
    </div>
</footer>

<script src="<?= e(asset_url('js/main.js')); ?>"></script>
</body>
</html>
