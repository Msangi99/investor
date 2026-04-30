<?php
/**
 * UNIDA Gateway Safe Reset Super Admin Login
 *
 * Upload to project root:
 * https://investoraccess.unidatechs.com/reset_superadmin_login_safe.php
 *
 * This version continues even if users table already exists.
 * Delete after success.
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$root = __DIR__;
$results = [];

function add_result($item, $ok, $message = '') {
    global $results;
    $results[] = [$item, $ok, $message];
}

function safe_exec($pdo, $label, $sql) {
    try {
        $pdo->exec($sql);
        add_result($label, true, 'done');
        return true;
    } catch (Throwable $e) {
        $msg = $e->getMessage();

        if (strpos($msg, 'already exists') !== false || strpos($msg, 'Duplicate') !== false) {
            add_result($label, true, 'already exists / skipped');
            return true;
        }

        add_result($label, false, $msg);
        return false;
    }
}

function table_exists_safe($pdo, $table) {
    try {
        $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        return (bool) $stmt->fetch();
    } catch (Throwable $e) {
        try {
            $pdo->query("SELECT 1 FROM `$table` LIMIT 1");
            return true;
        } catch (Throwable $e2) {
            return false;
        }
    }
}

function column_exists_safe($pdo, $table, $column) {
    try {
        $stmt = $pdo->prepare("SHOW COLUMNS FROM `$table` LIKE ?");
        $stmt->execute([$column]);
        return (bool) $stmt->fetch();
    } catch (Throwable $e) {
        return false;
    }
}

function add_column_safe($pdo, $table, $column, $sql) {
    if (column_exists_safe($pdo, $table, $column)) {
        add_result("$table.$column", true, 'already exists');
        return true;
    }

    try {
        $pdo->exec($sql);
        add_result("$table.$column", true, 'added');
        return true;
    } catch (Throwable $e) {
        $msg = $e->getMessage();

        if (strpos($msg, 'Duplicate column') !== false) {
            add_result("$table.$column", true, 'already exists / skipped');
            return true;
        }

        add_result("$table.$column", false, $msg);
        return false;
    }
}

try {
    if (!file_exists($root . '/includes/config.php')) {
        throw new Exception('includes/config.php not found. Upload this installer to the same folder as index.php.');
    }

    require_once $root . '/includes/config.php';
    add_result('includes/config.php', true, 'loaded');
} catch (Throwable $e) {
    add_result('includes/config.php', false, $e->getMessage());
    goto render_page;
}

try {
    $pdo = db();
    add_result('Database connection', true, 'connected');

    /**
     * Always use CREATE TABLE IF NOT EXISTS and continue on 1050.
     */
    safe_exec($pdo, 'users table', "
        CREATE TABLE IF NOT EXISTS users (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            full_name VARCHAR(180) NOT NULL,
            email VARCHAR(180) NOT NULL UNIQUE,
            phone VARCHAR(80) NULL,
            role VARCHAR(40) NOT NULL DEFAULT 'business',
            password_hash VARCHAR(255) NOT NULL,
            status VARCHAR(40) NOT NULL DEFAULT 'active',
            email_verified_at DATETIME NULL,
            last_login_at DATETIME NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    /**
     * Ensure important users columns exist.
     */
    add_column_safe($pdo, 'users', 'full_name', "ALTER TABLE users ADD COLUMN full_name VARCHAR(180) NOT NULL DEFAULT 'User' AFTER id");
    add_column_safe($pdo, 'users', 'email', "ALTER TABLE users ADD COLUMN email VARCHAR(180) NOT NULL AFTER full_name");
    add_column_safe($pdo, 'users', 'phone', "ALTER TABLE users ADD COLUMN phone VARCHAR(80) NULL AFTER email");
    add_column_safe($pdo, 'users', 'role', "ALTER TABLE users ADD COLUMN role VARCHAR(40) NOT NULL DEFAULT 'business' AFTER phone");
    add_column_safe($pdo, 'users', 'password_hash', "ALTER TABLE users ADD COLUMN password_hash VARCHAR(255) NOT NULL AFTER role");
    add_column_safe($pdo, 'users', 'status', "ALTER TABLE users ADD COLUMN status VARCHAR(40) NOT NULL DEFAULT 'active' AFTER password_hash");
    add_column_safe($pdo, 'users', 'email_verified_at', "ALTER TABLE users ADD COLUMN email_verified_at DATETIME NULL AFTER status");
    add_column_safe($pdo, 'users', 'last_login_at', "ALTER TABLE users ADD COLUMN last_login_at DATETIME NULL AFTER email_verified_at");
    add_column_safe($pdo, 'users', 'created_at', "ALTER TABLE users ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
    add_column_safe($pdo, 'users', 'updated_at', "ALTER TABLE users ADD COLUMN updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP");

    /**
     * Unique email index: skip if impossible because duplicate exists. We clean duplicate admin email below.
     */
    try {
        $pdo->exec("ALTER TABLE users ADD UNIQUE KEY unique_users_email (email)");
        add_result('users.email unique index', true, 'added');
    } catch (Throwable $e) {
        add_result('users.email unique index', true, 'already exists or skipped');
    }

    safe_exec($pdo, 'roles table', "
        CREATE TABLE IF NOT EXISTS roles (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            role_key VARCHAR(80) NOT NULL UNIQUE,
            role_name VARCHAR(160) NOT NULL,
            role_type VARCHAR(40) NOT NULL DEFAULT 'public_user',
            description TEXT NULL,
            is_system TINYINT(1) NOT NULL DEFAULT 1,
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    add_column_safe($pdo, 'roles', 'role_type', "ALTER TABLE roles ADD COLUMN role_type VARCHAR(40) NOT NULL DEFAULT 'public_user' AFTER role_name");
    add_column_safe($pdo, 'roles', 'description', "ALTER TABLE roles ADD COLUMN description TEXT NULL AFTER role_type");
    add_column_safe($pdo, 'roles', 'is_system', "ALTER TABLE roles ADD COLUMN is_system TINYINT(1) NOT NULL DEFAULT 1 AFTER description");
    add_column_safe($pdo, 'roles', 'is_active', "ALTER TABLE roles ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1 AFTER is_system");

    safe_exec($pdo, 'admin_profiles table', "
        CREATE TABLE IF NOT EXISTS admin_profiles (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id INT UNSIGNED NOT NULL UNIQUE,
            admin_role VARCHAR(80) NOT NULL DEFAULT 'ADMIN',
            permission_group VARCHAR(120) NOT NULL DEFAULT 'GENERAL_ADMIN',
            department VARCHAR(160) NULL,
            job_title VARCHAR(160) NULL,
            phone VARCHAR(80) NULL,
            backup_email VARCHAR(180) NULL,
            two_factor_enabled TINYINT(1) NOT NULL DEFAULT 0,
            last_activity_at DATETIME NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    add_column_safe($pdo, 'admin_profiles', 'admin_role', "ALTER TABLE admin_profiles ADD COLUMN admin_role VARCHAR(80) NOT NULL DEFAULT 'ADMIN' AFTER user_id");
    add_column_safe($pdo, 'admin_profiles', 'permission_group', "ALTER TABLE admin_profiles ADD COLUMN permission_group VARCHAR(120) NOT NULL DEFAULT 'GENERAL_ADMIN' AFTER admin_role");
    add_column_safe($pdo, 'admin_profiles', 'department', "ALTER TABLE admin_profiles ADD COLUMN department VARCHAR(160) NULL AFTER permission_group");
    add_column_safe($pdo, 'admin_profiles', 'job_title', "ALTER TABLE admin_profiles ADD COLUMN job_title VARCHAR(160) NULL AFTER department");
    add_column_safe($pdo, 'admin_profiles', 'phone', "ALTER TABLE admin_profiles ADD COLUMN phone VARCHAR(80) NULL AFTER job_title");
    add_column_safe($pdo, 'admin_profiles', 'backup_email', "ALTER TABLE admin_profiles ADD COLUMN backup_email VARCHAR(180) NULL AFTER phone");

    /**
     * Seed roles.
     */
    safe_exec($pdo, 'roles seed', "
        INSERT INTO roles (role_key, role_name, role_type, description, is_system, is_active)
        VALUES
        ('SUPER_ADMIN', 'Super Admin', 'admin', 'Full system owner with all permissions.', 1, 1),
        ('ADMIN', 'Admin', 'admin', 'General administrator.', 1, 1),
        ('VERIFICATION_ADMIN', 'Verification Admin', 'admin', 'Verification administrator.', 1, 1),
        ('SUPPORT_ADMIN', 'Support Admin', 'admin', 'Support administrator.', 1, 1),
        ('FINANCE_ADMIN', 'Finance Admin', 'admin', 'Finance administrator.', 1, 1),
        ('CONTENT_ADMIN', 'Content Admin', 'admin', 'Content administrator.', 1, 1),
        ('PARTNERSHIP_ADMIN', 'Partnership Admin', 'admin', 'Partnership administrator.', 1, 1),
        ('ANALYTICS_ADMIN', 'Analytics Admin', 'admin', 'Analytics administrator.', 1, 1)
        ON DUPLICATE KEY UPDATE
            role_name=VALUES(role_name),
            role_type=VALUES(role_type),
            description=VALUES(description),
            is_active=1
    ");

    /**
     * Force reset super admin user.
     */
    $email = 'admin@unidatechs.com';
    $password = 'pass123456Mama';
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    /**
     * Remove duplicate rows for admin email.
     */
    try {
        $dupStmt = $pdo->prepare("SELECT id FROM users WHERE LOWER(email) = LOWER(?) ORDER BY id ASC");
        $dupStmt->execute([$email]);
        $duplicates = $dupStmt->fetchAll();

        if (count($duplicates) > 1) {
            $keepId = (int) $duplicates[0]['id'];

            for ($i = 1; $i < count($duplicates); $i++) {
                $deleteId = (int) $duplicates[$i]['id'];
                $pdo->prepare("DELETE FROM admin_profiles WHERE user_id = ?")->execute([$deleteId]);
                $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$deleteId]);
            }

            add_result('duplicate admin email cleanup', true, 'kept user id ' . $keepId . ', removed ' . (count($duplicates) - 1));
        } else {
            add_result('duplicate admin email cleanup', true, 'no duplicate detected');
        }
    } catch (Throwable $e) {
        add_result('duplicate admin email cleanup', false, $e->getMessage());
    }

    $stmt = $pdo->prepare("SELECT id FROM users WHERE LOWER(email) = LOWER(?) LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        $userId = (int) $user['id'];

        $update = $pdo->prepare("
            UPDATE users
            SET
                full_name = :full_name,
                email = :email,
                phone = :phone,
                role = 'admin',
                password_hash = :password_hash,
                status = 'active',
                email_verified_at = COALESCE(email_verified_at, NOW()),
                updated_at = NOW()
            WHERE id = :id
        ");

        $update->execute([
            ':full_name' => 'UNIDA Super Admin',
            ':email' => $email,
            ':phone' => '0762494775',
            ':password_hash' => $passwordHash,
            ':id' => $userId
        ]);

        add_result('super admin user', true, 'updated user id ' . $userId);
    } else {
        $insert = $pdo->prepare("
            INSERT INTO users (
                full_name,
                email,
                phone,
                role,
                password_hash,
                status,
                email_verified_at,
                created_at
            ) VALUES (
                :full_name,
                :email,
                :phone,
                'admin',
                :password_hash,
                'active',
                NOW(),
                NOW()
            )
        ");

        $insert->execute([
            ':full_name' => 'UNIDA Super Admin',
            ':email' => $email,
            ':phone' => '0762494775',
            ':password_hash' => $passwordHash
        ]);

        $userId = (int) $pdo->lastInsertId();
        add_result('super admin user', true, 'created user id ' . $userId);
    }

    /**
     * Verify password immediately.
     */
    $verifyStmt = $pdo->prepare("SELECT id, email, role, status, password_hash FROM users WHERE LOWER(email) = LOWER(?) LIMIT 1");
    $verifyStmt->execute([$email]);
    $verifyUser = $verifyStmt->fetch();

    if ($verifyUser && password_verify($password, $verifyUser['password_hash'])) {
        add_result('password_verify test', true, 'hash matches pass123456Mama');
    } else {
        add_result('password_verify test', false, 'hash does not match. Make sure password_hash column length is at least 255.');
    }

    /**
     * Set SUPER_ADMIN profile.
     */
    $profileStmt = $pdo->prepare("
        INSERT INTO admin_profiles (
            user_id,
            admin_role,
            permission_group,
            department,
            job_title,
            phone,
            backup_email,
            two_factor_enabled,
            last_activity_at
        ) VALUES (
            :user_id,
            'SUPER_ADMIN',
            'SYSTEM_OWNER',
            'System Administration',
            'Super Administrator',
            '0762494775',
            :backup_email,
            0,
            NOW()
        )
        ON DUPLICATE KEY UPDATE
            admin_role = 'SUPER_ADMIN',
            permission_group = 'SYSTEM_OWNER',
            department = 'System Administration',
            job_title = 'Super Administrator',
            phone = '0762494775',
            backup_email = VALUES(backup_email),
            last_activity_at = NOW()
    ");

    $profileStmt->execute([
        ':user_id' => (int) $verifyUser['id'],
        ':backup_email' => $email
    ]);

    add_result('admin profile', true, 'set to SUPER_ADMIN');

    add_result(
        'final account',
        true,
        'email=' . $verifyUser['email'] . ', users.role=' . $verifyUser['role'] . ', status=' . $verifyUser['status']
    );

} catch (Throwable $e) {
    add_result('installer error', false, $e->getMessage());
}

render_page:
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Safe Reset Super Admin Login</title>
    <style>
        body{font-family:Arial,sans-serif;background:#f2f6f9;color:#1d2939;padding:30px}
        .box{max-width:1080px;margin:auto;background:#fff;border-radius:18px;padding:24px;box-shadow:0 18px 44px rgba(8,59,122,.12)}
        h1{color:#083B7A}
        .ok{color:#0E7C6B;font-weight:800}
        .fail{color:#991b1b;font-weight:800}
        li{padding:8px 0;border-bottom:1px solid #eef2f6}
        code{background:#eef7fb;padding:3px 6px;border-radius:6px}
        .note{background:#f8fcff;border-left:5px solid #0A5DB7;padding:14px;border-radius:12px;margin-top:18px}
        .warn{background:#fff5f5;border-left:5px solid #dc2626;padding:14px;border-radius:12px;margin-top:18px}
    </style>
</head>
<body>
<div class="box">
    <h1>Safe Reset Super Admin Login</h1>

    <ul>
        <?php foreach ($results as $row): ?>
            <li>
                <span class="<?= $row[1] ? 'ok' : 'fail'; ?>">
                    <?= $row[1] ? 'OK' : 'FAILED'; ?>
                </span>
                <code><?= htmlspecialchars($row[0], ENT_QUOTES, 'UTF-8'); ?></code>
                <?= htmlspecialchars($row[2], ENT_QUOTES, 'UTF-8'); ?>
            </li>
        <?php endforeach; ?>
    </ul>

    <div class="note">
        <strong>Login now:</strong><br>
        Email: <code>admin@unidatechs.com</code><br>
        Password: <code>pass123456Mama</code><br><br>
        Then open: <code>/admin/dashboard.php</code>
    </div>

    <div class="warn">
        <strong>Important:</strong> Delete <code>reset_superadmin_login_safe.php</code> immediately after success.
    </div>
</div>
</body>
</html>
