<?php
/**
 * UNIDA Gateway
 * Investment Ecosystem Platform
 * includes/config.php
 */

date_default_timezone_set('Africa/Dar_es_Salaam');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
 * IMPORTANT: replace DB_PASS with your new secure database password.
 */
define('DB_HOST', 'localhost');
define('DB_NAME', 'eunicetz_unida');
define('DB_USER', 'eunicetz_unidainvest');
define('DB_PASS', 'MNJDddQ7E1aA1!');
define('DB_CHARSET', 'utf8mb4');

/**
 * BASE URL
 */
if (!function_exists('detect_base_url')) {
    function detect_base_url() {
        $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/');
        $dir = trim(dirname($scriptName), '/');

        if ($dir === '.' || $dir === '') {
            return '/';
        }

        $parts = explode('/', $dir);
        $removeFolders = ['admin', 'business', 'investor', 'stakeholder', 'dashboards'];

        while (!empty($parts)) {
            $last = end($parts);

            if (in_array($last, $removeFolders, true)) {
                array_pop($parts);
                continue;
            }

            break;
        }

        return empty($parts) ? '/' : '/' . implode('/', $parts) . '/';
    }
}

define('BASE_URL', detect_base_url());

/**
 * PUBLIC NAVIGATION LINKS
 */
$navLinks = [
    ['label' => 'Home', 'url' => BASE_URL . 'index.php', 'key' => 'home'],
    ['label' => 'Ecosystem', 'url' => BASE_URL . 'ecosystem.php', 'key' => 'ecosystem'],
    ['label' => 'Verification', 'url' => BASE_URL . 'verification.php', 'key' => 'verification'],
    ['label' => 'Opportunities', 'url' => BASE_URL . 'opportunities.php', 'key' => 'opportunities'],
    ['label' => 'About', 'url' => BASE_URL . 'about.php', 'key' => 'about'],
    ['label' => 'Contact', 'url' => BASE_URL . 'contact.php', 'key' => 'contact'],
];

/**
 * DATABASE CONNECTION
 */
if (!function_exists('db')) {
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
}

/**
 * BASIC DATABASE UTILITIES
 */
if (!function_exists('table_exists')) {
    function table_exists($table) {
        try {
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $table)) {
                return false;
            }

            $stmt = db()->prepare("SHOW TABLES LIKE ?");
            $stmt->execute([$table]);

            return (bool) $stmt->fetch();
        } catch (Throwable $e) {
            return false;
        }
    }
}

/**
 * AUTH HELPERS
 */
if (!function_exists('is_logged_in')) {
    function is_logged_in() {
        return isset($_SESSION['user_id']);
    }
}

if (!function_exists('current_user_id')) {
    function current_user_id() {
        return (int) ($_SESSION['user_id'] ?? 0);
    }
}

if (!function_exists('current_user_role')) {
    function current_user_role() {
        return $_SESSION['user_role'] ?? '';
    }
}

if (!function_exists('current_user_name')) {
    function current_user_name() {
        return $_SESSION['user_name'] ?? '';
    }
}

if (!function_exists('dashboard_url_by_role')) {
    function dashboard_url_by_role($role) {
        if ($role === 'admin') {
            if (file_exists(__DIR__ . '/../admin/role-dashboard.php')) {
                return BASE_URL . 'admin/role-dashboard.php';
            }

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
}

if (!function_exists('redirect_by_role')) {
    function redirect_by_role($role) {
        if ($role === 'admin') {
            if (file_exists(__DIR__ . '/../admin/role-dashboard.php')) {
                redirect('admin/role-dashboard.php');
            }

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
}

if (!function_exists('require_login')) {
    function require_login() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            redirect('login.php');
        }
    }
}

if (!function_exists('require_role')) {
    function require_role($role) {
        require_login();

        if (($_SESSION['user_role'] ?? '') !== $role) {
            redirect('login.php');
        }

        if ($role === 'business' && business_page_requires_approval() && !business_is_approved(current_user_id())) {
            redirect('business/dashboard.php');
        }
    }
}

if (!function_exists('business_page_requires_approval')) {
    function business_page_requires_approval() {
        $path = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
        $file = basename($path);

        if (!str_contains($path, '/business/')) {
            return false;
        }

        $allowedBeforeApproval = [
            'dashboard.php',
            'profile.php',
            'documents.php',
            'readiness.php',
        ];

        return !in_array($file, $allowedBeforeApproval, true);
    }
}

if (!function_exists('business_is_approved')) {
    function business_is_approved($userId) {
        $userId = (int) $userId;
        if ($userId <= 0 || !table_exists('business_profiles')) {
            return false;
        }

        try {
            $stmt = db()->prepare("SELECT verification_status FROM business_profiles WHERE user_id = ? LIMIT 1");
            $stmt->execute([$userId]);
            $verificationStatus = (string) ($stmt->fetchColumn() ?: '');
            if ($verificationStatus !== 'verified') {
                return false;
            }

            if (!table_exists('uploads')) {
                return false;
            }

            $stmt = db()->prepare("SELECT COUNT(*) FROM uploads WHERE user_id = ? AND related_type = 'business' AND upload_status = 'approved'");
            $stmt->execute([$userId]);

            return (int) $stmt->fetchColumn() > 0;
        } catch (Throwable $e) {
            return false;
        }
    }
}

/**
 * GENERAL HELPERS
 */
if (!function_exists('e')) {
    function e($value) {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('asset_url')) {
    function asset_url($path) {
        return BASE_URL . 'assets/' . ltrim($path, '/');
    }
}

if (!function_exists('page_title')) {
    function page_title($pageTitle = '') {
        return $pageTitle ? $pageTitle . ' | ' . APP_NAME : APP_NAME . ' | ' . APP_SUBTITLE;
    }
}

if (!function_exists('redirect')) {
    function redirect($path) {
        $url = BASE_URL . ltrim($path, '/');

        if (headers_sent()) {
            echo '<script>window.location.href="' . e($url) . '";</script>';
            exit;
        }

        header('Location: ' . $url);
        exit;
    }
}

if (!function_exists('is_post_request')) {
    function is_post_request() {
        return ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST';
    }
}

if (!function_exists('active_class')) {
    function active_class($pageName, $key) {
        return $pageName === $key ? 'active' : '';
    }
}

/**
 * OPTIONAL BOOTSTRAP
 */
$bootstrapFile = __DIR__ . '/bootstrap.php';

if (file_exists($bootstrapFile)) {
    require_once $bootstrapFile;
}
?>