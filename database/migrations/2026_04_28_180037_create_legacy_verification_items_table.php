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
CREATE TABLE IF NOT EXISTS `verification_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `verification_request_id` bigint(20) UNSIGNED NOT NULL,
  `upload_id` bigint(20) UNSIGNED DEFAULT NULL,
  `item_name` varchar(150) NOT NULL,
  `status` enum('pending','approved','needs_update','rejected') NOT NULL DEFAULT 'pending',
  `comment` text DEFAULT NULL,
  `reviewed_by` int(10) UNSIGNED DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL);
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS `verification_items`');
    }
};


