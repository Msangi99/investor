<?php
/**
 * UNIDA Gateway
 * includes/sidebar.php
 *
 * Usage before include:
 * $activeSidebar = 'overview';
 * include __DIR__ . '/../includes/sidebar.php';
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$currentRole = $_SESSION['user_role'] ?? '';
$activeSidebar = $activeSidebar ?? 'overview';

$sidebarMenus = [
    'admin' => [
        ['key' => 'overview', 'label' => 'Overview', 'icon' => 'fa-solid fa-gauge-high', 'url' => 'admin/dashboard.php'],
        ['key' => 'users', 'label' => 'Users', 'icon' => 'fa-solid fa-users', 'url' => 'admin/users.php'],
        ['key' => 'roles', 'label' => 'Roles & Permissions', 'icon' => 'fa-solid fa-user-lock', 'url' => 'admin/roles.php'],
        ['key' => 'verifications', 'label' => 'Verifications', 'icon' => 'fa-solid fa-file-shield', 'url' => 'admin/verifications.php'],
        ['key' => 'businesses', 'label' => 'Businesses', 'icon' => 'fa-solid fa-building', 'url' => 'admin/businesses.php'],
        ['key' => 'investors', 'label' => 'Investors', 'icon' => 'fa-solid fa-coins', 'url' => 'admin/investors.php'],
        ['key' => 'stakeholders', 'label' => 'Stakeholders', 'icon' => 'fa-solid fa-users-gear', 'url' => 'admin/stakeholders.php'],
        ['key' => 'opportunities', 'label' => 'UNIDA Invest', 'icon' => 'fa-solid fa-briefcase', 'url' => 'admin/opportunities.php'],
        ['key' => 'uploads', 'label' => 'Uploads', 'icon' => 'fa-solid fa-cloud-arrow-up', 'url' => 'admin/uploads.php'],
        ['key' => 'insights', 'label' => 'UNIDA Insights', 'icon' => 'fa-solid fa-chart-pie', 'url' => 'admin/insights.php'],
        ['key' => 'messages', 'label' => 'Messages', 'icon' => 'fa-solid fa-envelope', 'url' => 'admin/messages.php'],
        ['key' => 'settings', 'label' => 'Settings', 'icon' => 'fa-solid fa-gear', 'url' => 'admin/settings.php'],
    ],

    'business' => [
        ['key' => 'overview', 'label' => 'Overview', 'icon' => 'fa-solid fa-gauge-high', 'url' => 'business/dashboard.php'],
        ['key' => 'profile', 'label' => 'Business Profile', 'icon' => 'fa-solid fa-building', 'url' => 'business/profile.php'],
        ['key' => 'documents', 'label' => 'Documents', 'icon' => 'fa-solid fa-file-shield', 'url' => 'business/documents.php'],
        ['key' => 'readiness', 'label' => 'Readiness', 'icon' => 'fa-solid fa-chart-line', 'url' => 'business/readiness.php'],
        ['key' => 'opportunities', 'label' => 'Investment Requests', 'icon' => 'fa-solid fa-briefcase', 'url' => 'business/opportunities.php'],
        ['key' => 'connections', 'label' => 'Connections', 'icon' => 'fa-solid fa-handshake', 'url' => 'business/connections.php'],
        ['key' => 'insights', 'label' => 'Insights', 'icon' => 'fa-solid fa-chart-pie', 'url' => 'business/insights.php'],
        ['key' => 'messages', 'label' => 'Messages', 'icon' => 'fa-solid fa-message', 'url' => 'business/messages.php'],
        ['key' => 'settings', 'label' => 'Settings', 'icon' => 'fa-solid fa-gear', 'url' => 'business/settings.php'],
    ],

    'investor' => [
        ['key' => 'overview', 'label' => 'Overview', 'icon' => 'fa-solid fa-gauge-high', 'url' => 'investor/dashboard.php'],
        ['key' => 'profile', 'label' => 'Investor Profile', 'icon' => 'fa-solid fa-user-tie', 'url' => 'investor/profile.php'],
        ['key' => 'discover', 'label' => 'Discover', 'icon' => 'fa-solid fa-magnifying-glass-chart', 'url' => 'investor/discover.php'],
        ['key' => 'verified-businesses', 'label' => 'Verified Businesses', 'icon' => 'fa-solid fa-building-circle-check', 'url' => 'investor/verified-businesses.php'],
        ['key' => 'shortlist', 'label' => 'Shortlist', 'icon' => 'fa-solid fa-bookmark', 'url' => 'investor/shortlist.php'],
        ['key' => 'pipeline', 'label' => 'Pipeline', 'icon' => 'fa-solid fa-route', 'url' => 'investor/pipeline.php'],
        ['key' => 'meetings', 'label' => 'Meetings', 'icon' => 'fa-solid fa-calendar-check', 'url' => 'investor/meetings.php'],
        ['key' => 'insights', 'label' => 'Insights', 'icon' => 'fa-solid fa-chart-pie', 'url' => 'investor/insights.php'],
        ['key' => 'messages', 'label' => 'Messages', 'icon' => 'fa-solid fa-message', 'url' => 'investor/messages.php'],
        ['key' => 'settings', 'label' => 'Settings', 'icon' => 'fa-solid fa-gear', 'url' => 'investor/settings.php'],
    ],

    'stakeholder' => [
        ['key' => 'overview', 'label' => 'Overview', 'icon' => 'fa-solid fa-gauge-high', 'url' => 'stakeholder/dashboard.php'],
        ['key' => 'profile', 'label' => 'Organization Profile', 'icon' => 'fa-solid fa-building-columns', 'url' => 'stakeholder/profile.php'],
        ['key' => 'businesses', 'label' => 'Businesses', 'icon' => 'fa-solid fa-building', 'url' => 'stakeholder/businesses.php'],
        ['key' => 'recommendations', 'label' => 'Recommendations', 'icon' => 'fa-solid fa-handshake-angle', 'url' => 'stakeholder/recommendations.php'],
        ['key' => 'connections', 'label' => 'Partner Connections', 'icon' => 'fa-solid fa-handshake', 'url' => 'stakeholder/connections.php'],
        ['key' => 'follow-ups', 'label' => 'Follow-ups', 'icon' => 'fa-solid fa-calendar-check', 'url' => 'stakeholder/follow-ups.php'],
        ['key' => 'insights', 'label' => 'Insights', 'icon' => 'fa-solid fa-chart-pie', 'url' => 'stakeholder/insights.php'],
        ['key' => 'reports', 'label' => 'Reports', 'icon' => 'fa-solid fa-file-lines', 'url' => 'stakeholder/reports.php'],
        ['key' => 'messages', 'label' => 'Messages', 'icon' => 'fa-solid fa-message', 'url' => 'stakeholder/messages.php'],
        ['key' => 'settings', 'label' => 'Settings', 'icon' => 'fa-solid fa-gear', 'url' => 'stakeholder/settings.php'],
    ],
];

$menuItems = $sidebarMenus[$currentRole] ?? [];
?>

<aside class="dashboard-sidebar">
    <div class="sidebar-head">
        <div class="sidebar-logo">
            <span class="sidebar-dot"></span>
        </div>

        <div>
            <strong><?= e(APP_NAME); ?></strong>
            <small><?= e(ucfirst($currentRole ?: 'Workspace')); ?></small>
        </div>
    </div>

    <nav class="sidebar-nav" aria-label="Dashboard navigation">
        <?php foreach ($menuItems as $item): ?>
            <a
                class="<?= $activeSidebar === $item['key'] ? 'active' : ''; ?>"
                href="<?= e(BASE_URL . $item['url']); ?>"
            >
                <i class="<?= e($item['icon']); ?>"></i>
                <span><?= e($item['label']); ?></span>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="sidebar-footer">
        <a href="<?= e(BASE_URL); ?>index.php">
            <i class="fa-solid fa-house"></i>
            <span>Public Site</span>
        </a>

        <a class="logout-link" href="<?= e(BASE_URL); ?>logout.php">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span>Logout</span>
        </a>
    </div>
</aside>
