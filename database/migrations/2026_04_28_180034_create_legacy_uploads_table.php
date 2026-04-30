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
CREATE TABLE IF NOT EXISTS `uploads` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `related_type` enum('business','investor','stakeholder','opportunity','verification','insight') NOT NULL DEFAULT 'business',
  `related_id` int(10) UNSIGNED DEFAULT NULL,
  `document_type_id` int(10) UNSIGNED DEFAULT NULL,
  `original_name` varchar(255) NOT NULL,
  `stored_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_ext` varchar(20) DEFAULT NULL,
  `mime_type` varchar(120) DEFAULT NULL,
  `file_size` bigint(20) UNSIGNED DEFAULT NULL,
  `upload_status` enum('uploaded','under_review','approved','needs_update','rejected') NOT NULL DEFAULT 'uploaded',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL);
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS `uploads`');
    }
};


