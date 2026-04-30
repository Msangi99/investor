-- =========================================================
-- UNIDA Gateway Core Helpers Patch
-- Run in phpMyAdmin after database backup.
-- =========================================================

CREATE TABLE IF NOT EXISTS system_settings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(120) NOT NULL UNIQUE,
    setting_value TEXT NULL,
    setting_group VARCHAR(80) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS system_notifications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NULL,
    title VARCHAR(180) NOT NULL,
    message TEXT NULL,
    notification_type VARCHAR(80) DEFAULT 'system',
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_notifications_user (user_id),
    INDEX idx_notifications_read (is_read),
    CONSTRAINT fk_notifications_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE activity_logs
ADD COLUMN IF NOT EXISTS record_type VARCHAR(80) NULL,
ADD COLUMN IF NOT EXISTS record_id BIGINT UNSIGNED NULL;

INSERT INTO system_settings (setting_key, setting_value, setting_group)
VALUES
('verification_due_days', '7', 'verification'),
('max_upload_mb', '25', 'uploads'),
('platform_status', 'active', 'system')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);