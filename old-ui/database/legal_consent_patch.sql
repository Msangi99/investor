-- UNIDA Gateway Legal and Consent Fields
-- Run in phpMyAdmin after backing up database.

ALTER TABLE users
ADD COLUMN IF NOT EXISTS date_of_birth DATE NULL,
ADD COLUMN IF NOT EXISTS age_confirmed TINYINT(1) NOT NULL DEFAULT 0,
ADD COLUMN IF NOT EXISTS terms_accepted_at DATETIME NULL,
ADD COLUMN IF NOT EXISTS privacy_accepted_at DATETIME NULL,
ADD COLUMN IF NOT EXISTS data_consent_at DATETIME NULL,
ADD COLUMN IF NOT EXISTS legal_confirmation_at DATETIME NULL,
ADD COLUMN IF NOT EXISTS group_authority_confirmation_at DATETIME NULL,
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