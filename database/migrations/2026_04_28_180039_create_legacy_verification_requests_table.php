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
CREATE TABLE IF NOT EXISTS `verification_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `business_profile_id` int(10) UNSIGNED DEFAULT NULL,
  `request_type` enum('business_profile','document','investor_profile','stakeholder_profile','opportunity') NOT NULL DEFAULT 'business_profile',
  `status` enum('pending','in_review','verified','needs_update','rejected') NOT NULL DEFAULT 'pending',
  `readiness_score_before` tinyint(3) UNSIGNED DEFAULT NULL,
  `readiness_score_after` tinyint(3) UNSIGNED DEFAULT NULL,
  `submitted_at` datetime NOT NULL DEFAULT current_timestamp(),
  `reviewed_by` int(10) UNSIGNED DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `reviewer_comment` text DEFAULT NULL,
  `internal_note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL);
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS `verification_requests`');
    }
};


