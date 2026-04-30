<?php
/**
 * UNIDA Gateway Permission Helpers
 */

if (!function_exists('permission_table_exists')) {
    function permission_table_exists($table) {
        try {
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $table)) return false;
            $stmt = db()->prepare("SHOW TABLES LIKE ?");
            $stmt->execute([$table]);
            return (bool) $stmt->fetch();
        } catch (Throwable $e) {
            return false;
        }
    }
}

if (!function_exists('role_has_permission')) {
    function role_has_permission($roleKey, $permissionKey, $userId = null) {
        if ($roleKey === 'SUPER_ADMIN') return true;

        if (!permission_table_exists('roles') || !permission_table_exists('permissions') || !permission_table_exists('role_permissions')) {
            return false;
        }

        try {
            $stmt = db()->prepare("
                SELECT p.id
                FROM roles r
                INNER JOIN role_permissions rp ON rp.role_id = r.id
                INNER JOIN permissions p ON p.id = rp.permission_id
                WHERE r.role_key = ?
                AND p.permission_key = ?
                LIMIT 1
            ");
            $stmt->execute([$roleKey, $permissionKey]);
            return (bool) $stmt->fetch();
        } catch (Throwable $e) {
            return false;
        }
    }
}

if (!function_exists('current_admin_role_key')) {
    function current_admin_role_key() {
        if (($_SESSION['user_role'] ?? '') !== 'admin') {
            return $_SESSION['user_role'] ?? '';
        }

        try {
            if (!permission_table_exists('admin_profiles')) return 'ADMIN';
            $stmt = db()->prepare("SELECT admin_role FROM admin_profiles WHERE user_id = ? LIMIT 1");
            $stmt->execute([(int) ($_SESSION['user_id'] ?? 0)]);
            $row = $stmt->fetch();
            return $row['admin_role'] ?? 'ADMIN';
        } catch (Throwable $e) {
            return 'ADMIN';
        }
    }
}

if (!function_exists('current_user_can')) {
    function current_user_can($permissionKey) {
        return role_has_permission(current_admin_role_key(), $permissionKey, (int) ($_SESSION['user_id'] ?? 0));
    }
}
