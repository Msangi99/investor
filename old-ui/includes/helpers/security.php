<?php
if (!function_exists('csrf_token')) {
    function csrf_token() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['_csrf_token'];
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field() {
        return '<input type="hidden" name="_csrf_token" value="' . e(csrf_token()) . '">';
    }
}

if (!function_exists('verify_csrf_token')) {
    function verify_csrf_token() {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            return true;
        }

        $sent = $_POST['_csrf_token'] ?? '';
        $saved = $_SESSION['_csrf_token'] ?? '';

        return is_string($sent) && is_string($saved) && hash_equals($saved, $sent);
    }
}

if (!function_exists('require_valid_csrf')) {
    function require_valid_csrf() {
        if (!verify_csrf_token()) {
            http_response_code(419);
            die('Security token expired. Please refresh the page and try again.');
        }
    }
}
