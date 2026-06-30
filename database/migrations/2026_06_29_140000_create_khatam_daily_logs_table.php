<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('khatam_daily_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('khatam_program_id')
                ->constrained('khatam_programs')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->date('log_date');
            $table->unsignedSmallInteger('page_from')->default(1);
            $table->unsignedSmallInteger('page_to');
            $table->unsignedSmallInteger('pages_read');
            $table->unsignedSmallInteger('surah_id_from')->nullable();
            $table->unsignedSmallInteger('verse_from')->nullable();
            $table->unsignedSmallInteger('surah_id_to')->nullable();
            $table->unsignedSmallInteger('verse_to')->nullable();
            $table->timestamps();

            $table->unique(['khatam_program_id', 'log_date']);
            $table->index('log_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('khatam_daily_logs');
    }
};
