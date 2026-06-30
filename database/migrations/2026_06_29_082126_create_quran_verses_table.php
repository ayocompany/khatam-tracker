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
        Schema::create('quran_verses', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->unsignedSmallInteger('surah_id');
            $table->unsignedSmallInteger('verse_number');
            $table->text('text_ar');
            $table->text('text_id')->nullable();
            $table->timestamps();

            $table->foreign('surah_id')
                ->references('id')
                ->on('quran_surahs')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->unique(['surah_id', 'verse_number']);
            $table->index(['surah_id', 'verse_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quran_verses');
    }
};
