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
CREATE TABLE IF NOT EXISTS `business_readiness_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_profile_id` int(10) UNSIGNED NOT NULL,
  `checklist_item_id` int(10) UNSIGNED NOT NULL,
  `status` enum('not_started','completed','needs_update') NOT NULL DEFAULT 'not_started',
  `score_awarded` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `note` text DEFAULT NULL,
  `updated_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL);
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS `business_readiness_items`');
    }
};


