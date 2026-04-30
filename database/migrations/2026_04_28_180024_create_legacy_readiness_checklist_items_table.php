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
CREATE TABLE IF NOT EXISTS `readiness_checklist_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `item_key` varchar(100) NOT NULL,
  `item_name` varchar(180) NOT NULL,
  `description` text DEFAULT NULL,
  `weight` tinyint(3) UNSIGNED NOT NULL DEFAULT 10,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL);
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS `readiness_checklist_items`');
    }
};


