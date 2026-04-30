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
CREATE TABLE IF NOT EXISTS `document_types` (
  `id` int(10) UNSIGNED NOT NULL,
  `type_key` varchar(100) NOT NULL,
  `type_name` varchar(150) NOT NULL,
  `applies_to` enum('business','investor','stakeholder','all') NOT NULL DEFAULT 'business',
  `is_required` tinyint(1) NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL);
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS `document_types`');
    }
};


