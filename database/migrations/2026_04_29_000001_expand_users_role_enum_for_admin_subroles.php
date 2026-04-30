<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql' || ! Schema::hasColumn('users', 'role')) {
            return;
        }

        DB::statement("
            ALTER TABLE `users`
            MODIFY COLUMN `role` ENUM(
                'admin',
                'SUPER_ADMIN',
                'VERIFICATION_ADMIN',
                'SUPPORT_ADMIN',
                'FINANCE_ADMIN',
                'CONTENT_ADMIN',
                'PARTNERSHIP_ADMIN',
                'ANALYTICS_ADMIN',
                'business',
                'investor',
                'stakeholder'
            ) NOT NULL DEFAULT 'business'
        ");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql' || ! Schema::hasColumn('users', 'role')) {
            return;
        }

        DB::statement("
            ALTER TABLE `users`
            MODIFY COLUMN `role` ENUM('admin','business','investor','stakeholder')
            NOT NULL DEFAULT 'business'
        ");
    }
};

