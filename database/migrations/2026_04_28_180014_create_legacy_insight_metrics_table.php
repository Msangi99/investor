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
CREATE TABLE IF NOT EXISTS `insight_metrics` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `metric_key` varchar(100) NOT NULL,
  `metric_name` varchar(180) NOT NULL,
  `metric_value` decimal(18,2) NOT NULL DEFAULT 0.00,
  `metric_unit` varchar(40) DEFAULT NULL,
  `sector` varchar(120) DEFAULT NULL,
  `region` varchar(120) DEFAULT NULL,
  `period_start` date DEFAULT NULL,
  `period_end` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL);
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS `insight_metrics`');
    }
};


