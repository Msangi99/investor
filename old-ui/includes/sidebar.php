<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$activeSidebar = $activeSidebar ?? 'overview';
$role = $_SESSION['user_role'] ?? '';

$menus = [
    'admin' => [
        ['key' => 'overview', 'label' => 'Overview', 'icon' => 'fa-solid fa-gauge-high', 'url' => 'admin/dashboard.php'],
        ['key' => 'role-dashboard', 'label' => 'Role Dashboard', 'icon' => 'fa-solid fa-table-columns', 'url' => 'admin/role-dashboard.php'],
        ['key' => 'profile', 'label' => 'Admin Profile', 'icon' => 'fa-solid fa-user-shield', 'url' => 'admin/profile.php'],
        ['key' => 'roles', 'label' => 'Roles & Permissions', 'icon' => 'fa-solid fa-user-lock', 'url' => 'admin/roles.php'],
        ['key' => 'users', 'label' => 'Users', 'icon' => 'fa-solid fa-users', 'url' => 'admin/users.php'],
        ['key' => 'verifications', 'label' => 'Verifications', 'icon' => 'fa-solid fa-file-shield', 'url' => 'admin/verifications.php'],
        ['key' => 'businesses', 'label' => 'Businesses', 'icon' => 'fa-solid fa-building', 'url' => 'admin/businesses.php'],
        ['key' => 'investors', 'label' => 'Investors', 'icon' => 'fa-solid fa-coins', 'url' => 'admin/investors.php'],
        ['key' => 'stakeholders', 'label' => 'Stakeholders', 'icon' => 'fa-solid fa-users-gear', 'url' => 'admin/stakeholders.php'],
        ['key' => 'opportunities', 'label' => 'Opportunities', 'icon' => 'fa-solid fa-briefcase', 'url' => 'admin/opportunities.php'],
        ['key' => 'uploads', 'label' => 'Uploads', 'icon' => 'fa-solid fa-cloud-arrow-up', 'url' => 'admin/uploads.php'],
        ['key' => 'insights', 'label' => 'Insights', 'icon' => 'fa-solid fa-chart-pie', 'url' => 'admin/insights.php'],
        ['key' => 'messages', 'label' => 'Messages', 'icon' => 'fa-solid fa-envelope', 'url' => 'admin/messages.php'],
        ['key' => 'settings', 'label' => 'Settings', 'icon' => 'fa-solid fa-gear', 'url' => 'admin/settings.php'],
    ],

    'business' => [
        ['key' => 'overview', 'label' => 'Overview', 'icon' => 'fa-solid fa-gauge-high', 'url' => 'business/dashboard.php'],
        ['key' => 'profile', 'label' => 'Business Profile', 'icon' => 'fa-solid fa-building', 'url' => 'business/profile.php'],
        ['key' => 'readiness', 'label' => 'Readiness', 'icon' => 'fa-solid fa-chart-line', 'url' => 'business/readiness.php'],
        ['key' => 'documents', 'label' => 'Documents', 'icon' => 'fa-solid fa-file-shield', 'url' => 'business/documents.php'],
        ['key' => 'opportunities', 'label' => 'Opportunities', 'icon' => 'fa-solid fa-briefcase', 'url' => 'business/opportunities.php'],
        ['key' => 'connections', 'label' => 'Connections', 'icon' => 'fa-solid fa-handshake', 'url' => 'business/connections.php'],
        ['key' => 'messages', 'label' => 'Messages', 'icon' => 'fa-solid fa-envelope', 'url' => 'business/messages.php'],
    ],

    'investor' => [
        ['key' => 'overview', 'label' => 'Overview', 'icon' => 'fa-solid fa-gauge-high', 'url' => 'investor/dashboard.php'],
        ['key' => 'profile', 'label' => 'Investor Profile', 'icon' => 'fa-solid fa-user-tie', 'url' => 'investor/profile.php'],
        ['key' => 'discover', 'label' => 'Discover', 'icon' => 'fa-solid fa-magnifying-glass-chart', 'url' => 'investor/discover.php'],
        ['key' => 'shortlist', 'label' => 'Shortlist', 'icon' => 'fa-solid fa-bookmark', 'url' => 'investor/shortlist.php'],
        ['key' => 'meetings', 'label' => 'Meetings', 'icon' => 'fa-solid fa-calendar-check', 'url' => 'investor/meetings.php'],
        ['key' => 'messages', 'label' => 'Messages', 'icon' => 'fa-solid fa-envelope', 'url' => 'investor/messages.php'],
    ],

    'stakeholder' => [
        ['key' => 'overview', 'label' => 'Overview', 'icon' => 'fa-solid fa-gauge-high', 'url' => 'stakeholder/dashboard.php'],
        ['key' => 'profile', 'label' => 'Organization Profile', 'icon' => 'fa-solid fa-building-columns', 'url' => 'stakeholder/profile.php'],
        ['key' => 'businesses', 'label' => 'Review Businesses', 'icon' => 'fa-solid fa-building', 'url' => 'stakeholder/businesses.php'],
        ['key' => 'recommendations', 'label' => 'Recommendations', 'icon' => 'fa-solid fa-handshake-angle', 'url' => 'stakeholder/recommendations.php'],
        ['key' => 'connections', 'label' => 'Connections', 'icon' => 'fa-solid fa-handshake', 'url' => 'stakeholder/connections.php'],
        ['key' => 'reports', 'label' => 'Reports', 'icon' => 'fa-solid fa-file-lines', 'url' => 'stakeholder/reports.php'],
        ['key' => 'messages', 'label' => 'Messages', 'icon' => 'fa-solid fa-envelope', 'url' => 'stakeholder/messages.php'],
    ],
];

$items = $menus[$role] ?? [];
?>

<aside class="dashboard-sidebar">
    <?php foreach ($items as $item): ?>
        <a
            class="<?= $activeSidebar === $item['key'] ? 'active' : ''; ?>"
            href="<?= e(BASE_URL . $item['url']); ?>"
        >
            <i class="<?= e($item['icon']); ?>"></i>
            <?= e($item['label']); ?>
        </a>
    <?php endforeach; ?>

    <a href="<?= e(BASE_URL); ?>logout.php">
        <i class="fa-solid fa-right-from-bracket"></i>
        Logout
    </a>
</aside>
