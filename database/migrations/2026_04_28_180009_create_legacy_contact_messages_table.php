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
CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `organization` varchar(180) DEFAULT NULL,
  `email` varchar(180) NOT NULL,
  `phone` varchar(40) DEFAULT NULL,
  `inquiry_type` varchar(120) DEFAULT NULL,
  `message` text NOT NULL,
  `status` enum('new','in_review','responded','closed') NOT NULL DEFAULT 'new',
  `assigned_to` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL);
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS `contact_messages`');
    }
};


