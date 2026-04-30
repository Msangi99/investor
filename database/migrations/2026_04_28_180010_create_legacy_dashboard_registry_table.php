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
CREATE TABLE IF NOT EXISTS `dashboard_registry` (
  `id` int(10) UNSIGNED NOT NULL,
  `dashboard_key` varchar(120) NOT NULL,
  `dashboard_name` varchar(180) NOT NULL,
  `role_key` varchar(80) NOT NULL,
  `dashboard_url` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `status` varchar(40) NOT NULL DEFAULT 'active',
  `display_order` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL);
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS `dashboard_registry`');
    }
};


