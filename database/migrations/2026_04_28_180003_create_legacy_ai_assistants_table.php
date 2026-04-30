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
CREATE TABLE IF NOT EXISTS `ai_assistants` (
  `id` int(10) UNSIGNED NOT NULL,
  `assistant_key` varchar(80) NOT NULL,
  `assistant_name` varchar(120) NOT NULL,
  `assistant_role` varchar(180) NOT NULL,
  `audience` varchar(80) NOT NULL DEFAULT 'all',
  `description` text DEFAULT NULL,
  `welcome_message` text DEFAULT NULL,
  `welcome_message_sw` text DEFAULT NULL,
  `support_email` varchar(180) DEFAULT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `display_order` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL);
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS `ai_assistants`');
    }
};


