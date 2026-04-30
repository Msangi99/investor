<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }
        DB::unprepared(<<<'SQL'
CREATE TABLE IF NOT EXISTS `admin_profiles` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `admin_role` enum('SUPER_ADMIN','ADMIN','VERIFICATION_ADMIN','SUPPORT_ADMIN','FINANCE_ADMIN','CONTENT_ADMIN','PARTNERSHIP_ADMIN','ANALYTICS_ADMIN') NOT NULL DEFAULT 'ADMIN',
  `permission_group` varchar(120) DEFAULT NULL,
  `department` varchar(150) DEFAULT NULL,
  `job_title` varchar(150) DEFAULT NULL,
  `phone` varchar(80) DEFAULT NULL,
  `backup_email` varchar(180) DEFAULT NULL,
  `work_email` varchar(180) DEFAULT NULL,
  `work_phone` varchar(60) DEFAULT NULL,
  `alternative_phone` varchar(60) DEFAULT NULL,
  `security_level` enum('standard','high','critical') NOT NULL DEFAULT 'standard',
  `two_factor_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `can_manage_admins` tinyint(1) NOT NULL DEFAULT 0,
  `can_approve_verification` tinyint(1) NOT NULL DEFAULT 0,
  `can_manage_finance` tinyint(1) NOT NULL DEFAULT 0,
  `can_publish_content` tinyint(1) NOT NULL DEFAULT 0,
  `can_view_analytics` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('active','suspended') NOT NULL DEFAULT 'active',
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `updated_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL);
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS `admin_profiles`');
    }
};


