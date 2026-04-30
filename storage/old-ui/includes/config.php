<?php
/**
 * UNIDA Gateway
 * Investment Ecosystem Platform
 * includes/config.php
 */

date_default_timezone_set('Africa/Dar_es_Salaam');

/**
 * APP DETAILS
 */
define('APP_NAME', 'UNIDA Gateway');
define('APP_SUBTITLE', 'Investment Ecosystem Platform');
define('COMPANY_NAME', 'UNIDA TECH LIMITED');
define('COMPANY_TAGLINE', 'Smart. Secure. Connected.');
define('COMPANY_URL', 'https://unidatechs.com');
define('COMPANY_EMAIL', 'support@unidatechs.com');
define('COMPANY_PHONE', '0762 494 775');

/**
 * DATABASE CONFIG
 * Badilisha password yako kwenye hosting panel kisha weka password mpya hapa.
 */
define('DB_HOST', 'localhost');
define('DB_NAME', 'eunicetz_unida');
define('DB_USER', 'eunicetz_unidainvest');
define('DB_PASS', 'MNJDddQ7E1aA1!');
define('DB_CHARSET', 'utf8mb4');

/**
 * BASE URL
 * Inafanya kazi hata ukiwa:
 * /
 * /business/dashboard.php
 * /investor/dashboard.php
 * /admin/dashboard.php
 * /stakeholder/dashboard.php
 * /subfolder/business/dashboard.php
 */
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
$basePath = rtrim($scriptDir, '/');

if (preg_match('#/(admin|investor|business|stakeholder)$#', $basePath)) {
    $basePath = dirname($basePath);
}

define('BASE_URL', ($basePath === '' || $basePath === '/' || $basePath === '.') ? '/' : $basePath . '/');

/**
 * PUBLIC NAVIGATION LINKS
 */
$navLinks = [
    ['label' => 'Home', 'url' => BASE_URL . 'index.php', 'key' => 'home'],
    ['label' => 'Ecosystem', 'url' => BASE_URL . 'ecosystem.php', 'key' => 'ecosystem'],
    ['label' => 'Verification', 'url' => BASE_URL . 'verification.php', 'key' => 'verification'],
    ['label' => 'About', 'url' => BASE_URL . 'about.php', 'key' => 'about'],
    ['label' => 'Contact', 'url' => BASE_URL . 'contact.php', 'key' => 'contact'],
];

/**
 * DATABASE CONNECTION
 */
function db() {
    static $pdo = null;

    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            error_log('Database connection failed: ' . $e->getMessage());
            die('System connection error. Please try again later.');
        }
    }

    return $pdo;
}

/**
 * AUTH HELPERS
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function dashboard_url_by_role($role) {
    if ($role === 'admin') {
        return BASE_URL . 'admin/dashboard.php';
    }

    if ($role === 'business') {
        return BASE_URL . 'business/dashboard.php';
    }

    if ($role === 'investor') {
        return BASE_URL . 'investor/dashboard.php';
    }

    if ($role === 'stakeholder') {
        return BASE_URL . 'stakeholder/dashboard.php';
    }

    return BASE_URL . 'login.php';
}

function redirect_by_role($role) {
    if ($role === 'admin') {
        redirect('admin/dashboard.php');
    }

    if ($role === 'business') {
        redirect('business/dashboard.php');
    }

    if ($role === 'investor') {
        redirect('investor/dashboard.php');
    }

    if ($role === 'stakeholder') {
        redirect('stakeholder/dashboard.php');
    }

    redirect('index.php');
}

function require_login() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['user_id'])) {
        redirect('login.php');
    }
}

function require_role($role) {
    require_login();

    if (($_SESSION['user_role'] ?? '') !== $role) {
        redirect('login.php');
    }
}

/**
 * HELPERS
 */
function e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function asset_url($path) {
    return BASE_URL . 'assets/' . ltrim($path, '/');
}

function page_title($pageTitle = '') {
    return $pageTitle ? $pageTitle . ' | ' . APP_NAME : APP_NAME . ' | ' . APP_SUBTITLE;
}

function redirect($path) {
    header('Location: ' . BASE_URL . ltrim($path, '/'));
    exit;
}

function is_post_request() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}
?>