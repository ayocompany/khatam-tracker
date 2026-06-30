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
        Schema::create('page_mappings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('verse_id');
            $table->string('layout_type', 50);
            $table->unsignedSmallInteger('page_number');
            $table->timestamps();

            $table->foreign('verse_id')
                ->references('id')
                ->on('quran_verses')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->unique(['verse_id', 'layout_type']);
            $table->index(['layout_type', 'page_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_mappings');
    }
};
