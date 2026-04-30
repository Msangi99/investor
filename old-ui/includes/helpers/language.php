<?php
/**
 * UNIDA Gateway Language Helper
 * Supports English and Kiswahili.
 */

if (!function_exists('available_languages')) {
    function available_languages() {
        return [
            'en' => 'English',
            'sw' => 'Kiswahili',
        ];
    }
}

if (!function_exists('current_language')) {
    function current_language() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $allowed = array_keys(available_languages());

        if (isset($_GET['lang']) && in_array($_GET['lang'], $allowed, true)) {
            $_SESSION['lang'] = $_GET['lang'];

            if (!headers_sent()) {
                $cleanUrl = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
                header('Location: ' . $cleanUrl);
                exit;
            }
        }

        return $_SESSION['lang'] ?? 'en';
    }
}

if (!function_exists('language_switch_url')) {
    function language_switch_url($lang) {
        $path = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
        return $path . '?lang=' . urlencode($lang);
    }
}

if (!function_exists('t')) {
    function t($key, $fallback = '') {
        $lang = current_language();

        $dict = [
            'en' => [
                'home' => 'Home',
                'ecosystem' => 'Ecosystem',
                'verification' => 'Verification',
                'about' => 'About',
                'contact' => 'Contact',
                'login' => 'Login',
                'create_account' => 'Create Account',
                'my_workspace' => 'My Workspace',
                'logout' => 'Logout',
                'language' => 'Language',
                'platform_line' => 'UNIDA Gateway: verification, readiness and stakeholder coordination',
                'built_by' => 'Built in Tanzania by',
                'footer_summary' => 'UNIDA Gateway supports verified business access, investment readiness, stakeholder coordination and data-driven ecosystem insights for Tanzania and beyond.',
                'platform' => 'Platform',
                'legal_trust' => 'Legal & Trust',
                'opportunities' => 'Opportunities',
                'terms' => 'Terms of Use',
                'privacy' => 'Privacy Policy',
                'verification_policy' => 'Verification Policy',
                'all_rights' => 'All rights reserved.',
                'designed_by' => 'Designed and developed by',
            ],
            'sw' => [
                'home' => 'Mwanzo',
                'ecosystem' => 'Mfumo',
                'verification' => 'Uhakiki',
                'about' => 'Kuhusu',
                'contact' => 'Mawasiliano',
                'login' => 'Ingia',
                'create_account' => 'Fungua Akaunti',
                'my_workspace' => 'Dashboard Yangu',
                'logout' => 'Toka',
                'language' => 'Lugha',
                'platform_line' => 'UNIDA Gateway: uhakiki, readiness na uratibu wa wadau',
                'built_by' => 'Imejengwa Tanzania na',
                'footer_summary' => 'UNIDA Gateway inasaidia upatikanaji wa biashara zilizothibitishwa, investment readiness, uratibu wa wadau na taarifa za mfumo kwa Tanzania na zaidi.',
                'platform' => 'Jukwaa',
                'legal_trust' => 'Sheria & Uaminifu',
                'opportunities' => 'Fursa',
                'terms' => 'Masharti ya Matumizi',
                'privacy' => 'Sera ya Faragha',
                'verification_policy' => 'Sera ya Uhakiki',
                'all_rights' => 'Haki zote zimehifadhiwa.',
                'designed_by' => 'Imebuniwa na kutengenezwa na',
            ],
        ];

        return $dict[$lang][$key] ?? $fallback ?: $key;
    }
}

if (!function_exists('choose_text')) {
    function choose_text($en, $sw) {
        return current_language() === 'sw' ? $sw : $en;
    }
}