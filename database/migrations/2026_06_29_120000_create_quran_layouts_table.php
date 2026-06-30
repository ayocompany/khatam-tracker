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
        Schema::create('quran_layouts', function (Blueprint $table) {
            $table->string('code', 50)->primary();
            $table->string('name', 120);
            $table->unsignedSmallInteger('total_pages');
            $table->unsignedSmallInteger('total_surahs');
            $table->unsignedSmallInteger('total_verses');
            $table->unsignedTinyInteger('lines_per_page');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quran_layouts');
    }
};
