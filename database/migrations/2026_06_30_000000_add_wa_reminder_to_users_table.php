<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('wa_reminder_enabled')->default(false)->after('reminder_times');
            $table->string('wa_phone', 20)->nullable()->after('wa_reminder_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['wa_reminder_enabled', 'wa_phone']);
        });
    }
};
