<?php
if (!function_exists('admin_profile')) {
    function admin_profile($userId = null) {
        $userId = $userId ?: current_user_id();

        if (!$userId || !table_exists('admin_profiles')) {
            return null;
        }

        try {
            $stmt = db()->prepare("SELECT * FROM admin_profiles WHERE user_id = ? LIMIT 1");
            $stmt->execute([$userId]);
            return $stmt->fetch();
        } catch (Throwable $e) {
            return null;
        }
    }
}

if (!function_exists('admin_role')) {
    function admin_role($userId = null) {
        $profile = admin_profile($userId);
        return $profile['admin_role'] ?? null;
    }
}

if (!function_exists('is_super_admin')) {
    function is_super_admin($userId = null) {
        return admin_role($userId) === 'SUPER_ADMIN';
    }
}
