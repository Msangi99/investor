-- UNIDA Gateway Auth Roles Patch
-- Run in phpMyAdmin if the installer/files do not create missing columns automatically.

ALTER TABLE users
ADD COLUMN IF NOT EXISTS organization VARCHAR(180) NULL,
ADD COLUMN IF NOT EXISTS phone VARCHAR(60) NULL,
ADD COLUMN IF NOT EXISTS last_login_at DATETIME NULL,
ADD COLUMN IF NOT EXISTS age_confirmed TINYINT(1) NOT NULL DEFAULT 0,
ADD COLUMN IF NOT EXISTS terms_accepted_at DATETIME NULL,
ADD COLUMN IF NOT EXISTS privacy_accepted_at DATETIME NULL,
ADD COLUMN IF NOT EXISTS data_consent_at DATETIME NULL,
ADD COLUMN IF NOT EXISTS newsletter_opt_in TINYINT(1) NOT NULL DEFAULT 0;

CREATE TABLE IF NOT EXISTS legal_consents (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    consent_type ENUM('terms','privacy','data_processing','legal_confirmation','group_authority','verification','newsletter') NOT NULL,
    consent_value TINYINT(1) NOT NULL DEFAULT 1,
    consent_text_version VARCHAR(80) NULL,
    ip_address VARCHAR(64) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_legal_consents_user (user_id),
    INDEX idx_legal_consents_type (consent_type),
    CONSTRAINT fk_legal_consents_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
