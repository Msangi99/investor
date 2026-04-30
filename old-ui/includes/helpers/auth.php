<?php
if (!function_exists('login_user_session')) {
    function login_user_session($user) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_regenerate_id(true);

        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];

        try {
            if (table_exists('users')) {
                db_execute("UPDATE users SET last_login_at = NOW() WHERE id = ?", [(int) $user['id']]);
            }
        } catch (Throwable $e) {}

        if (function_exists('log_activity')) {
            log_activity((int) $user['id'], 'login', 'auth', 'User logged in.');
        }
    }
}

if (!function_exists('logout_user_session')) {
    function logout_user_session() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }

        session_destroy();
    }
}
