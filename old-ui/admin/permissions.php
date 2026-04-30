<?php
$pageTitle = 'Permissions & Roles';
$pageDescription = 'Review roles and permissions.';
$pageName = 'admin-permissions';
$activeSidebar = 'roles';
require_once __DIR__ . '/../includes/config.php';
require_role('admin');
$roles = [];
$permissions = [];
try {
    if (function_exists('table_exists') && table_exists('roles')) $roles = db()->query("SELECT * FROM roles ORDER BY role_type DESC, role_name ASC")->fetchAll();
    if (function_exists('table_exists') && table_exists('permissions')) $permissions = db()->query("SELECT * FROM permissions ORDER BY permission_group ASC, permission_label ASC")->fetchAll();
} catch (Throwable $e) {}
include __DIR__ . '/../includes/header.php';
?>
<main class="dashboard-page"><section class="dashboard-hero admin"><div class="container"><div class="page-kicker"><i class="fa-solid fa-user-lock"></i> Permissions & Roles</div><h1>Review roles, permissions and access controls.</h1><p>SUPER_ADMIN has full access. Other roles receive permissions based on responsibilities.</p></div></section><section class="dashboard-shell"><div class="container dashboard-layout"><?php include __DIR__ . '/../includes/sidebar.php'; ?><div class="dashboard-content">
<article class="dashboard-panel"><div class="panel-head"><div><h3>Roles</h3><p>Installed roles.</p></div><span class="status-badge status-open"><?= e(count($roles)); ?> roles</span></div><div class="dashboard-table-wrap"><table class="dashboard-table"><thead><tr><th>Key</th><th>Name</th><th>Type</th><th>Description</th></tr></thead><tbody><?php foreach ($roles as $role): ?><tr><td><?= e($role['role_key']); ?></td><td><?= e($role['role_name']); ?></td><td><?= e($role['role_type']); ?></td><td><?= e($role['description']); ?></td></tr><?php endforeach; ?></tbody></table></div></article>
<article class="dashboard-panel"><div class="panel-head"><div><h3>Permissions</h3><p>Installed permissions.</p></div><span class="status-badge status-open"><?= e(count($permissions)); ?> permissions</span></div><div class="dashboard-table-wrap"><table class="dashboard-table"><thead><tr><th>Group</th><th>Key</th><th>Label</th></tr></thead><tbody><?php foreach ($permissions as $permission): ?><tr><td><?= e($permission['permission_group']); ?></td><td><?= e($permission['permission_key']); ?></td><td><?= e($permission['permission_label']); ?></td></tr><?php endforeach; ?></tbody></table></div></article>
</div></div></section></main>
<?php include __DIR__ . '/../includes/footer.php'; ?>