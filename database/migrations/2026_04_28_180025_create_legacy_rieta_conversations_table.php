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
CREATE TABLE IF NOT EXISTS `rieta_conversations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `session_id` varchar(128) NOT NULL,
  `language` varchar(12) NOT NULL DEFAULT 'sw',
  `status` varchar(40) NOT NULL DEFAULT 'open',
  `current_topic` varchar(120) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL);
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS `rieta_conversations`');
    }
};


