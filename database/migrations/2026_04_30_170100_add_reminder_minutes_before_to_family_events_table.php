<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('family_events', function (Blueprint $table) {
            $table->unsignedSmallInteger('reminder_minutes_before')->nullable()->after('end_time');
        });
    }

    public function down(): void
    {
        Schema::table('family_events', function (Blueprint $table) {
            $table->dropColumn('reminder_minutes_before');
        });
    }
};
