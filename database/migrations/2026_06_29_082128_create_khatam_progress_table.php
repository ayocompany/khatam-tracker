<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('khatam_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('khatam_program_id')
                ->constrained('khatam_programs')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->unsignedSmallInteger('last_surah_id');
            $table->unsignedSmallInteger('last_verse_number');
            $table->unsignedSmallInteger('current_page_in_home_mushaf');
            $table->timestamps();

            $table->foreign('last_surah_id')
                ->references('id')
                ->on('quran_surahs')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->unique('khatam_program_id');
            $table->index(['last_surah_id', 'last_verse_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('khatam_progress');
    }
};
