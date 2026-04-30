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
CREATE TABLE IF NOT EXISTS `investor_profiles` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `investor_name` varchar(180) NOT NULL,
  `investor_type` enum('individual','angel','venture_capital','private_equity','corporate','foundation','development_partner','bank','other') NOT NULL DEFAULT 'individual',
  `preferred_sectors` text DEFAULT NULL,
  `preferred_regions` text DEFAULT NULL,
  `ticket_min` decimal(18,2) DEFAULT NULL,
  `ticket_max` decimal(18,2) DEFAULT NULL,
  `currency` varchar(10) NOT NULL DEFAULT 'TZS',
  `investment_stage_interest` text DEFAULT NULL,
  `profile_status` enum('pending','active','suspended') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL);
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS `investor_profiles`');
    }
};


