<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // PostgreSQL: drop & recreate check constraint
        // SQLite: can't alter constraints, skip (SQLite uses 'text' check via migration)
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE khatam_programs DROP CONSTRAINT IF EXISTS khatam_programs_target_type_check');
            DB::statement("ALTER TABLE khatam_programs ADD CONSTRAINT khatam_programs_target_type_check CHECK (target_type IN ('date_bound', 'daily_pages', 'daily_verses'))");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE khatam_programs DROP CONSTRAINT IF EXISTS khatam_programs_target_type_check');
            DB::statement("ALTER TABLE khatam_programs ADD CONSTRAINT khatam_programs_target_type_check CHECK (target_type IN ('date_bound', 'daily_pages'))");
        }
    }
};
