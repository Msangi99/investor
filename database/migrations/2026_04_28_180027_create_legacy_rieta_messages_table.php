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
CREATE TABLE IF NOT EXISTS `rieta_messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `conversation_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `sender` varchar(40) NOT NULL DEFAULT 'user',
  `language` varchar(12) NOT NULL DEFAULT 'sw',
  `message` mediumtext NOT NULL,
  `intent` varchar(120) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL);
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS `rieta_messages`');
    }
};


