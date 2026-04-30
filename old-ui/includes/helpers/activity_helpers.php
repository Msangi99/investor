<?php
if (!function_exists('log_activity')) {
    function log_activity($userId, $action, $module = null, $description = null) {
        try {
            if (!table_exists('activity_logs')) {
                return false;
            }

            $stmt = db()->prepare("
                INSERT INTO activity_logs (user_id, action, module, description, ip_address, user_agent)
                VALUES (?, ?, ?, ?, ?, ?)
            ");

            return $stmt->execute([
                $userId,
                $action,
                $module,
                $description,
                $_SERVER['REMOTE_ADDR'] ?? null,
                $_SERVER['HTTP_USER_AGENT'] ?? null,
            ]);
        } catch (Throwable $e) {
            return false;
        }
    }
}
