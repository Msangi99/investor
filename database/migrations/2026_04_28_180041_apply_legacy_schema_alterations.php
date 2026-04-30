<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Intentionally no-op.
        // Legacy table definitions are created in earlier migrations.
        // The raw SQL dump alterations contain duplicate/invalid clauses for this environment.
    }

    public function down(): void
    {
        // No-op.
    }
};
