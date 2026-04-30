-- =========================================================
-- UNIDA Gateway Admin Profile and Roles
-- Run manually in phpMyAdmin if installer does not patch DB.
-- =========================================================

CREATE TABLE IF NOT EXISTS admin_profiles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL UNIQUE,

    admin_role ENUM(
        'SUPER_ADMIN',
        'ADMIN',
        'VERIFICATION_ADMIN',
        'SUPPORT_ADMIN',
        'FINANCE_ADMIN',
        'CONTENT_ADMIN',
        'PARTNERSHIP_ADMIN',
        'ANALYTICS_ADMIN'
    ) NOT NULL DEFAULT 'ADMIN',

    permission_group VARCHAR(120) NULL,
    department VARCHAR(150) NULL,
    job_title VARCHAR(150) NULL,

    work_email VARCHAR(180) NULL,
    work_phone VARCHAR(60) NULL,
    alternative_phone VARCHAR(60) NULL,

    security_level ENUM('standard','high','critical') NOT NULL DEFAULT 'standard',
    two_factor_enabled TINYINT(1) NOT NULL DEFAULT 0,
    can_manage_admins TINYINT(1) NOT NULL DEFAULT 0,
    can_approve_verification TINYINT(1) NOT NULL DEFAULT 0,
    can_manage_finance TINYINT(1) NOT NULL DEFAULT 0,
    can_publish_content TINYINT(1) NOT NULL DEFAULT 0,
    can_view_analytics TINYINT(1) NOT NULL DEFAULT 0,

    status ENUM('active','suspended') NOT NULL DEFAULT 'active',

    created_by INT UNSIGNED NULL,
    updated_by INT UNSIGNED NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_admin_profiles_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_admin_profiles_created_by
        FOREIGN KEY (created_by) REFERENCES users(id)
        ON DELETE SET NULL,

    CONSTRAINT fk_admin_profiles_updated_by
        FOREIGN KEY (updated_by) REFERENCES users(id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;