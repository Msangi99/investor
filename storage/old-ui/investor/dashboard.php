<?php
$pageTitle = 'Investor Dashboard';
$pageDescription = 'Investor workspace for discovering verified opportunities, managing shortlists, pipeline, meetings and investment interests.';
$pageName = 'investor-dashboard';

require_once __DIR__ . '/../includes/config.php';
require_role('investor');

$pdo = db();

$userId = (int) ($_SESSION['user_id'] ?? 0);
$userName = $_SESSION['user_name'] ?? 'Investor';

function investor_count($pdo, $table, $where = '', $params = []) {
    try {
        $sql = "SELECT COUNT(*) AS total FROM {$table}";
        if ($where !== '') {
            $sql .= " WHERE {$where}";
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();

        return (int) ($row['total'] ?? 0);
    } catch (Throwable $e) {
        return 0;
    }
}

function investor_money_short($amount, $currency = 'TZS') {
    if (!$amount) {
        return $currency . ' 0';
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

$investorProfile = null;

try {
    $stmt = $pdo->prepare("
        SELECT *
        FROM investor_profiles
        WHERE user_id = ?
        LIMIT 1
    ");
    $stmt->execute([$userId]);
    $investorProfile = $stmt->fetch();
} catch (Throwable $e) {
    $investorProfile = null;
}

$investorName = $investorProfile['investor_name'] ?? $userName;
$investorType = $investorProfile['investor_type'] ?? 'individual';
$profileStatus = $investorProfile['profile_status'] ?? 'active';

$totalBusinesses = investor_count($pdo, 'business_profiles');
$verifiedBusinesses = investor_count($pdo, 'business_profiles', "verification_status = 'verified'");
$totalOpportunities = investor_count($pdo, 'investment_opportunities', "status IN ('published','under_review')");
$verifiedOpportunities = investor_count($pdo, 'investment_opportunities', "verification_status = 'verified' AND status IN ('published','under_review')");
$totalShortlists = investor_count($pdo, 'investor_shortlists', 'investor_user_id = ?', [$userId]);
$interestedShortlists = investor_count($pdo, 'investor_shortlists', "investor_user_id = ? AND status IN ('interested','contacted','meeting_requested','in_review')", [$userId]);
$totalConnections = investor_count($pdo, 'partner_connections', 'requester_user_id = ? OR receiver_user_id = ?', [$userId, $userId]);
$unreadMessages = investor_count($pdo, 'messages', 'receiver_id = ? AND is_read = 0', [$userId]);

$featuredOpportunity = null;
$recentShortlists = [];
$sectorPipeline = [];

try {
    $stmt = $pdo->query("
        SELECT 
            io.id,
            io.title,
            io.summary,
            io.sector,
            io.region,
            io.stage,
            io.funding_amount,
            io.currency,
            io.readiness_score,
            io.verification_status,
            bp.business_name
        FROM investment_opportunities io
        LEFT JOIN business_profiles bp ON bp.id = io.business_profile_id
        WHERE io.status IN ('published','under_review')
        ORDER BY 
            CASE WHEN io.verification_status = 'verified' THEN 0 ELSE 1 END,
            io.readiness_score DESC,
            io.created_at DESC
        LIMIT 1
    ");
    $featuredOpportunity = $stmt->fetch();
} catch (Throwable $e) {
    $featuredOpportunity = null;
}

try {
    $stmt = $pdo->prepare("
        SELECT 
            s.status,
            s.created_at,
            io.title,
            io.sector,
            io.region,
            io.readiness_score,
            io.verification_status
        FROM investor_shortlists s
        LEFT JOIN investment_opportunities io ON io.id = s.opportunity_id
        WHERE s.investor_user_id = ?
        ORDER BY s.created_at DESC
        LIMIT 5
    ");
    $stmt->execute([$userId]);
    $recentShortlists = $stmt->fetchAll();
} catch (Throwable $e) {
    $recentShortlists = [];
}

try {
    $stmt = $pdo->query("
        SELECT sector, COUNT(*) AS total
        FROM investment_opportunities
        WHERE status IN ('published','under_review')
        GROUP BY sector
        ORDER BY total DESC
        LIMIT 5
    ");
    $sectorPipeline = $stmt->fetchAll();
} catch (Throwable $e) {
    $sectorPipeline = [];
}

include __DIR__ . '/../includes/header.php';
?>

<main class="dashboard-page">
    <section class="dashboard-hero investor">
        <div class="container dashboard-hero-grid">
            <div>
                <div class="page-kicker">
                    <i class="fa-solid fa-coins"></i>
                    Investor Workspace
                </div>

                <h1>Discover verified opportunities and manage your investment pipeline.</h1>

                <p>
                    Use this workspace to review verified businesses, discover investment opportunities,
                    build shortlists, request follow-ups and track engagement through UNIDA Gateway.
                </p>
            </div>

            <div class="dashboard-profile-card">
                <div class="profile-avatar investor-avatar">
                    <?= e(strtoupper(substr($investorName, 0, 2))); ?>
                </div>

                <div>
                    <h3><?= e($investorName); ?></h3>
                    <p><?= e(ucfirst(str_replace('_', ' ', $investorType))); ?> account</p>

                    <span class="status-badge <?= $profileStatus === 'active' ? 'status-verified' : 'status-progress'; ?>">
                        <i class="fa-solid <?= $profileStatus === 'active' ? 'fa-circle-check' : 'fa-spinner'; ?>"></i>
                        <?= e(ucfirst($profileStatus)); ?>
                    </span>
                </div>
            </div>
        </div>
    </section>

    <section class="dashboard-shell">
        <div class="container dashboard-layout">
            <aside class="dashboard-sidebar">
                <a class="active" href="<?= e(BASE_URL); ?>investor/dashboard.php">
                    <i class="fa-solid fa-gauge-high"></i>
                    Overview
                </a>

                <a href="<?= e(BASE_URL); ?>investor/profile.php">
                    <i class="fa-solid fa-user-tie"></i>
                    Investor Profile
                </a>

                <a href="<?= e(BASE_URL); ?>investor/discover.php">
                    <i class="fa-solid fa-magnifying-glass-chart"></i>
                    Discover
                </a>

                <a href="<?= e(BASE_URL); ?>investor/verified-businesses.php">
                    <i class="fa-solid fa-building-circle-check"></i>
                    Verified Businesses
                </a>

                <a href="<?= e(BASE_URL); ?>investor/shortlist.php">
                    <i class="fa-solid fa-bookmark"></i>
                    Shortlist
                </a>

                <a href="<?= e(BASE_URL); ?>investor/pipeline.php">
                    <i class="fa-solid fa-route"></i>
                    Pipeline
                </a>

                <a href="<?= e(BASE_URL); ?>investor/meetings.php">
                    <i class="fa-solid fa-calendar-check"></i>
                    Meetings
                </a>

                <a href="<?= e(BASE_URL); ?>investor/insights.php">
                    <i class="fa-solid fa-chart-pie"></i>
                    Insights
                </a>

                <a href="<?= e(BASE_URL); ?>investor/messages.php">
                    <i class="fa-solid fa-message"></i>
                    Messages
                </a>

                <a href="<?= e(BASE_URL); ?>investor/settings.php">
                    <i class="fa-solid fa-gear"></i>
                    Settings
                </a>

                <a href="<?= e(BASE_URL); ?>logout.php">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    Logout
                </a>
            </aside>

            <div class="dashboard-content">
                <?php if (!$investorProfile): ?>
                    <article class="dashboard-panel">
                        <div class="panel-head">
                            <div>
                                <h3>Complete your investor profile</h3>
                                <p>
                                    Your account is active, but your investor profile is not completed yet.
                                    Add your investor type, preferred sectors, regions and ticket range.
                                </p>
                            </div>

                            <span class="status-badge status-progress">
                                Action needed
                            </span>
                        </div>

                        <a href="<?= e(BASE_URL); ?>investor/profile.php" class="btn btn-primary">
                            <i class="fa-solid fa-user-tie"></i>
                            Complete Investor Profile
                        </a>
                    </article>
                <?php endif; ?>

                <div class="dashboard-stat-grid">
                    <article class="dash-stat">
                        <span class="dash-icon">
                            <i class="fa-solid fa-building"></i>
                        </span>
                        <div>
                            <strong><?= e($totalBusinesses); ?></strong>
                            <small>Businesses listed</small>
                        </div>
                    </article>

                    <article class="dash-stat">
                        <span class="dash-icon green">
                            <i class="fa-solid fa-circle-check"></i>
                        </span>
                        <div>
                            <strong><?= e($verifiedBusinesses); ?></strong>
                            <small>Verified profiles</small>
                        </div>
                    </article>

                    <article class="dash-stat">
                        <span class="dash-icon cyan">
                            <i class="fa-solid fa-briefcase"></i>
                        </span>
                        <div>
                            <strong><?= e($verifiedOpportunities); ?></strong>
                            <small>Verified opportunities</small>
                        </div>
                    </article>

                    <article class="dash-stat">
                        <span class="dash-icon dark">
                            <i class="fa-solid fa-bookmark"></i>
                        </span>
                        <div>
                            <strong><?= e($totalShortlists); ?></strong>
                            <small>Shortlisted</small>
                        </div>
                    </article>
                </div>

                <div class="dashboard-main-grid">
                    <article class="dashboard-panel">
                        <div class="panel-head">
                            <div>
                                <h3>Opportunity Pipeline</h3>
                                <p>Published opportunities grouped by sector.</p>
                            </div>

                            <span class="status-badge status-open">
                                <?= e($totalOpportunities); ?> active
                            </span>
                        </div>

                        <div class="pipeline-list">
                            <?php if (!empty($sectorPipeline)): ?>
                                <?php foreach ($sectorPipeline as $sector): ?>
                                    <div>
                                        <span><?= e($sector['sector'] ?: 'Unspecified sector'); ?></span>
                                        <strong><?= e($sector['total']); ?> opportunities</strong>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div>
                                    <span>No published opportunities yet.</span>
                                    <strong>Empty</strong>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div style="margin-top:16px;">
                            <a href="<?= e(BASE_URL); ?>investor/discover.php" class="btn btn-soft">
                                <i class="fa-solid fa-magnifying-glass-chart"></i>
                                Discover Opportunities
                            </a>
                        </div>
                    </article>

                    <article class="dashboard-panel">
                        <div class="panel-head">
                            <div>
                                <h3>Featured Opportunity</h3>
                                <p>Top opportunity based on verification and readiness.</p>
                            </div>

                            <span class="status-badge <?= ($featuredOpportunity['verification_status'] ?? '') === 'verified' ? 'status-verified' : 'status-progress'; ?>">
                                <?= e(ucfirst($featuredOpportunity['verification_status'] ?? 'Pending')); ?>
                            </span>
                        </div>

                        <?php if ($featuredOpportunity): ?>
                            <div class="opportunity-card">
                                <h4><?= e($featuredOpportunity['title']); ?></h4>
                                <p><?= e($featuredOpportunity['summary']); ?></p>

                                <div class="opportunity-meta">
                                    <span><i class="fa-solid fa-building"></i> <?= e($featuredOpportunity['business_name'] ?? 'Business'); ?></span>
                                    <span><i class="fa-solid fa-location-dot"></i> <?= e($featuredOpportunity['region'] ?? 'Tanzania'); ?></span>
                                    <span><i class="fa-solid fa-seedling"></i> <?= e(ucfirst(str_replace('_', ' ', $featuredOpportunity['stage']))); ?></span>
                                    <span><i class="fa-solid fa-chart-line"></i> <?= e($featuredOpportunity['readiness_score']); ?>% readiness</span>
                                </div>

                                <div style="margin-top:16px;">
                                    <strong><?= e(investor_money_short($featuredOpportunity['funding_amount'], $featuredOpportunity['currency'])); ?></strong>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="opportunity-card">
                                <h4>No featured opportunity yet.</h4>
                                <p>Verified opportunities will appear here once businesses publish investment requests.</p>
                            </div>
                        <?php endif; ?>
                    </article>
                </div>

                <div class="dashboard-main-grid">
                    <article class="dashboard-panel">
                        <div class="panel-head">
                            <div>
                                <h3>Recent Shortlist Activity</h3>
                                <p>Your latest saved or reviewed opportunities.</p>
                            </div>

                            <span class="status-badge status-open">
                                <?= e($interestedShortlists); ?> active
                            </span>
                        </div>

                        <div class="pipeline-list">
                            <?php if (!empty($recentShortlists)): ?>
                                <?php foreach ($recentShortlists as $item): ?>
                                    <div>
                                        <span>
                                            <?= e($item['title'] ?? 'Opportunity'); ?>
                                            <br>
                                            <small>
                                                <?= e($item['sector'] ?? 'Sector'); ?>
                                                ·
                                                <?= e($item['readiness_score'] ?? 0); ?>% readiness
                                            </small>
                                        </span>

                                        <strong><?= e(ucfirst(str_replace('_', ' ', $item['status']))); ?></strong>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div>
                                    <span>No shortlist activity yet.</span>
                                    <strong>Start</strong>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div style="margin-top:16px;">
                            <a href="<?= e(BASE_URL); ?>investor/shortlist.php" class="btn btn-soft">
                                <i class="fa-solid fa-bookmark"></i>
                                View Shortlist
                            </a>
                        </div>
                    </article>

                    <article class="dashboard-panel">
                        <div class="panel-head">
                            <div>
                                <h3>Workspace Summary</h3>
                                <p>Your investor activity and communication status.</p>
                            </div>

                            <span class="status-badge status-open">
                                <?= e($unreadMessages); ?> unread
                            </span>
                        </div>

                        <div class="pipeline-list">
                            <div>
                                <span>Partner connections</span>
                                <strong><?= e($totalConnections); ?></strong>
                            </div>

                            <div>
                                <span>Shortlisted opportunities</span>
                                <strong><?= e($totalShortlists); ?></strong>
                            </div>

                            <div>
                                <span>Active investor interests</span>
                                <strong><?= e($interestedShortlists); ?></strong>
                            </div>

                            <div>
                                <span>Unread messages</span>
                                <strong><?= e($unreadMessages); ?></strong>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
