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
CREATE TABLE IF NOT EXISTS `investment_opportunities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_profile_id` int(10) UNSIGNED NOT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `title` varchar(220) NOT NULL,
  `sector` varchar(120) DEFAULT NULL,
  `region` varchar(120) DEFAULT NULL,
  `summary` text NOT NULL,
  `funding_amount` decimal(18,2) DEFAULT NULL,
  `currency` varchar(10) NOT NULL DEFAULT 'TZS',
  `funding_type` enum('equity','debt','grant','partnership','asset_finance','other') NOT NULL DEFAULT 'partnership',
  `stage` enum('idea','prototype','mvp','early_revenue','growth','scale') NOT NULL DEFAULT 'mvp',
  `readiness_score` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `verification_status` enum('pending','verified','needs_update','rejected') NOT NULL DEFAULT 'pending',
  `status` enum('draft','published','under_review','closed','archived') NOT NULL DEFAULT 'draft',
  `published_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL);
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS `investment_opportunities`');
    }
};


