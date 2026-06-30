<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('page_mappings', function (Blueprint $table) {
            $table->index(['layout_type', 'page_number', 'verse_id'], 'idx_page_mappings_lookup');
        });

        Schema::table('khatam_daily_logs', function (Blueprint $table) {
            $table->index('khatam_program_id', 'idx_daily_logs_program');
        });

        Schema::table('khatam_progress', function (Blueprint $table) {
            $table->index('khatam_program_id', 'idx_progress_program');
        });
    }

    public function down(): void
    {
        Schema::table('page_mappings', function (Blueprint $table) {
            $table->dropIndex('idx_page_mappings_lookup');
        });

        Schema::table('khatam_daily_logs', function (Blueprint $table) {
            $table->dropIndex('idx_daily_logs_program');
        });

        Schema::table('khatam_progress', function (Blueprint $table) {
            $table->dropIndex('idx_progress_program');
        });
    }
};
