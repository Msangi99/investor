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
CREATE TABLE IF NOT EXISTS `business_profiles` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `business_name` varchar(180) NOT NULL,
  `registration_number` varchar(100) DEFAULT NULL,
  `tax_identification_number` varchar(100) DEFAULT NULL,
  `sector` varchar(120) DEFAULT NULL,
  `business_stage` enum('idea','prototype','mvp','early_revenue','growth','scale') NOT NULL DEFAULT 'mvp',
  `region` varchar(120) DEFAULT NULL,
  `district` varchar(120) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `problem_statement` text DEFAULT NULL,
  `solution_summary` text DEFAULT NULL,
  `target_market` text DEFAULT NULL,
  `traction_summary` text DEFAULT NULL,
  `funding_need_amount` decimal(18,2) DEFAULT NULL,
  `funding_currency` varchar(10) NOT NULL DEFAULT 'TZS',
  `funding_purpose` text DEFAULT NULL,
  `jobs_current` int(10) UNSIGNED DEFAULT 0,
  `jobs_potential` int(10) UNSIGNED DEFAULT 0,
  `readiness_score` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `verification_status` enum('not_submitted','pending','verified','needs_update','rejected') NOT NULL DEFAULT 'not_submitted',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL);
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS `business_profiles`');
    }
};


