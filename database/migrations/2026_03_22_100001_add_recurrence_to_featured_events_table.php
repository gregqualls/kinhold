<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('featured_events', 'recurrence')) {
            Schema::table('featured_events', function (Blueprint $table) {
                $table->string('recurrence', 20)->default('none')->after('color');
            });
        }
    }

    public function down(): void
    {
        Schema::table('featured_events', function (Blueprint $table) {
            $table->dropColumn('recurrence');
        });
    }
};
