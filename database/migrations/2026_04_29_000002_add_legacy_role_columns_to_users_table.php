<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table): void {
            if (! Schema::hasColumn('users', 'full_name')) {
                $table->string('full_name', 150)->nullable()->after('name');
            }
            if (! Schema::hasColumn('users', 'role')) {
                $table->string('role', 80)->default('business')->after('email');
            }
            if (! Schema::hasColumn('users', 'role_id')) {
                $table->unsignedInteger('role_id')->nullable()->after('role');
            }
            if (! Schema::hasColumn('users', 'status')) {
                $table->string('status', 32)->default('active')->after('role_id');
            }
            if (! Schema::hasColumn('users', 'password_hash')) {
                $table->string('password_hash', 255)->nullable()->after('password');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table): void {
            $drop = [];
            foreach (['full_name', 'role', 'role_id', 'status', 'password_hash'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $drop[] = $column;
                }
            }

            if ($drop !== []) {
                $table->dropColumn($drop);
            }
        });
    }
};
