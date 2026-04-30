<?php
/**
 * UNIDA Gateway Super Admin Creator / Hash Generator
 *
 * SECURITY:
 * 1. Upload this file to /tools/create_super_admin.php
 * 2. Edit the configuration below.
 * 3. Set SETUP_ENABLED to true.
 * 4. Open it once in browser or run via CLI.
 * 5. Delete this file immediately after success.
 */

require_once __DIR__ . '/../includes/config.php';

/**
 * CHANGE THESE VALUES BEFORE RUNNING.
 */
const SETUP_ENABLED = false;

$superAdmin = [
    'full_name' => 'Super Admin',
    'email' => 'admin@example.com',
    'phone' => '0762 494 775',
    'password' => 'CHANGE_THIS_PASSWORD_NOW',
];

if (!SETUP_ENABLED) {
    http_response_code(403);
    die('Setup disabled. Edit tools/create_super_admin.php and set SETUP_ENABLED to true, then delete this file after use.');
}

if ($superAdmin['password'] === 'CHANGE_THIS_PASSWORD_NOW' || strlen($superAdmin['password']) < 10) {
    die('Please set a strong password with at least 10 characters.');
}

try {
    $pdo = db();
    $hash = password_hash($superAdmin['password'], PASSWORD_DEFAULT);

    $pdo->beginTransaction();

    $check = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    $check->execute([$superAdmin['email']]);
    $existing = $check->fetch();

    if ($existing) {
        $userId = (int) $existing['id'];

        $update = $pdo->prepare("
            UPDATE users
            SET 
                full_name = :full_name,
                phone = :phone,
                role = 'admin',
                password_hash = :password_hash,
                status = 'active'
            WHERE id = :id
        ");

        $update->execute([
            ':full_name' => $superAdmin['full_name'],
            ':phone' => $superAdmin['phone'],
            ':password_hash' => $hash,
            ':id' => $userId,
        ]);
    } else {
        $insert = $pdo->prepare("
            INSERT INTO users (
                full_name,
                organization,
                email,
                phone,
                role,
                password_hash,
                status
            ) VALUES (
                :full_name,
                :organization,
                :email,
                :phone,
                'admin',
                :password_hash,
                'active'
            )
        ");

        $insert->execute([
            ':full_name' => $superAdmin['full_name'],
            ':organization' => COMPANY_NAME,
            ':email' => $superAdmin['email'],
            ':phone' => $superAdmin['phone'],
            ':password_hash' => $hash,
        ]);

        $userId = (int) $pdo->lastInsertId();
    }

    $profile = $pdo->prepare("
        INSERT INTO admin_profiles (
            user_id,
            admin_role,
            permission_group,
            department,
            job_title,
            work_email,
            work_phone,
            security_level,
            can_manage_admins,
            can_approve_verification,
            can_manage_finance,
            can_publish_content,
            can_view_analytics,
            status
        ) VALUES (
            :user_id,
            'SUPER_ADMIN',
            'Full System Access',
            'Executive / System Administration',
            'Super Administrator',
            :work_email,
            :work_phone,
            'critical',
            1,
            1,
            1,
            1,
            1,
            'active'
        )
        ON DUPLICATE KEY UPDATE
            admin_role = 'SUPER_ADMIN',
            permission_group = 'Full System Access',
            department = 'Executive / System Administration',
            job_title = 'Super Administrator',
            work_email = VALUES(work_email),
            work_phone = VALUES(work_phone),
            security_level = 'critical',
            can_manage_admins = 1,
            can_approve_verification = 1,
            can_manage_finance = 1,
            can_publish_content = 1,
            can_view_analytics = 1,
            status = 'active'
    ");

    $profile->execute([
        ':user_id' => $userId,
        ':work_email' => $superAdmin['email'],
        ':work_phone' => $superAdmin['phone'],
    ]);

    $pdo->commit();

    echo '<pre>';
    echo "SUPER_ADMIN created/updated successfully.\n\n";
    echo "Email: " . htmlspecialchars($superAdmin['email'], ENT_QUOTES, 'UTF-8') . "\n";
    echo "Password hash:\n" . htmlspecialchars($hash, ENT_QUOTES, 'UTF-8') . "\n\n";
    echo "IMPORTANT: Delete tools/create_super_admin.php now.\n";
    echo '</pre>';
} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(500);
    echo 'Failed: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}
