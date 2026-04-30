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
CREATE TABLE IF NOT EXISTS `legal_documents` (
  `id` int(10) UNSIGNED NOT NULL,
  `document_key` varchar(80) NOT NULL,
  `title` varchar(180) NOT NULL,
  `version` varchar(50) NOT NULL DEFAULT '1.0',
  `content` longtext NOT NULL,
  `effective_date` date DEFAULT NULL,
  `status` varchar(40) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL);
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS `legal_documents`');
    }
};


