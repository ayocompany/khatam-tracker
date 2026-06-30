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
        Schema::create('quran_surahs', function (Blueprint $table) {
            $table->unsignedSmallInteger('id')->primary();
            $table->string('name_ar', 150);
            $table->string('name_id', 150);
            $table->unsignedSmallInteger('total_verses');
            $table->enum('type', ['meccan', 'medinan']);
            $table->timestamps();

            $table->index('name_id');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quran_surahs');
    }
};
