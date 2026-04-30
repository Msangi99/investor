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
CREATE TABLE IF NOT EXISTS `stakeholder_profiles` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `organization_name` varchar(180) NOT NULL,
  `stakeholder_type` enum('government','bank','hub','development_partner','ngo','corporate','academic','other') NOT NULL DEFAULT 'other',
  `focus_areas` text DEFAULT NULL,
  `regions_covered` text DEFAULT NULL,
  `support_services` text DEFAULT NULL,
  `profile_status` enum('pending','active','suspended') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL);
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS `stakeholder_profiles`');
    }
};


