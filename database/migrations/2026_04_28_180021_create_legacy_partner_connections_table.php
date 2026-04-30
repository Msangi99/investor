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
CREATE TABLE IF NOT EXISTS `partner_connections` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `requester_user_id` int(10) UNSIGNED NOT NULL,
  `receiver_user_id` int(10) UNSIGNED DEFAULT NULL,
  `opportunity_id` bigint(20) UNSIGNED DEFAULT NULL,
  `connection_type` enum('investment','bank_finance','government_support','mentorship','partnership','verification_support','other') NOT NULL DEFAULT 'partnership',
  `subject` varchar(220) NOT NULL,
  `message` text DEFAULT NULL,
  `status` enum('pending','accepted','declined','in_progress','completed','closed') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL);
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS `partner_connections`');
    }
};


