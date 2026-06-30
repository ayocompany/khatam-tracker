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
        Schema::table('users', function (Blueprint $table) {
            $table->string('quran_layout_code', 50)
                ->nullable()
                ->after('phone_verified_at');

            $table->index('quran_layout_code');
        });

        // Seed default layouts first, then set default value & FK
        \Artisan::call('db:seed', ['--class' => 'Database\Seeders\QuranLayoutSeeder', '--force' => true]);

        Schema::table('users', function (Blueprint $table) {
            // Set existing rows to default layout
            \Illuminate\Support\Facades\DB::table('users')
                ->whereNull('quran_layout_code')
                ->update(['quran_layout_code' => 'madinah_15']);

            $table->string('quran_layout_code', 50)
                ->default('madinah_15')
                ->change();

            $table->foreign('quran_layout_code')
                ->references('code')
                ->on('quran_layouts')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['quran_layout_code']);
            $table->dropIndex(['quran_layout_code']);
            $table->dropColumn('quran_layout_code');
        });
    }
};
