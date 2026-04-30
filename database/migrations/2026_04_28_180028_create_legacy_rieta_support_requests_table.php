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
CREATE TABLE IF NOT EXISTS `rieta_support_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `conversation_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `full_name` varchar(180) NOT NULL,
  `email` varchar(180) NOT NULL,
  `phone` varchar(80) NOT NULL,
  `subject` varchar(220) DEFAULT NULL,
  `concern` text NOT NULL,
  `language` varchar(12) NOT NULL DEFAULT 'sw',
  `status` varchar(40) NOT NULL DEFAULT 'new',
  `forwarded_to` varchar(180) NOT NULL DEFAULT 'support@unidagateway.co.tz',
  `mail_sent` tinyint(1) NOT NULL DEFAULT 0,
  `mail_error` text DEFAULT NULL,
  `ip_address` varchar(64) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL);
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS `rieta_support_requests`');
    }
};


