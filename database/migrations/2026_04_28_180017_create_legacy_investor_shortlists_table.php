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
CREATE TABLE IF NOT EXISTS `investor_shortlists` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `investor_user_id` int(10) UNSIGNED NOT NULL,
  `opportunity_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('saved','interested','contacted','meeting_requested','in_review','not_interested') NOT NULL DEFAULT 'saved',
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL);
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS `investor_shortlists`');
    }
};


