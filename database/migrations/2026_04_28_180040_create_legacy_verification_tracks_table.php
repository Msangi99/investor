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
CREATE TABLE IF NOT EXISTS `verification_tracks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `user_role` varchar(40) NOT NULL,
  `current_status` varchar(80) NOT NULL DEFAULT 'unverified',
  `completion_percent` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `due_date` datetime DEFAULT NULL,
  `submitted_at` datetime DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `rejected_at` datetime DEFAULT NULL,
  `expired_at` datetime DEFAULT NULL,
  `assigned_admin_id` int(10) UNSIGNED DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `user_notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL);
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS `verification_tracks`');
    }
};


