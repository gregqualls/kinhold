<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('featured_events', function (Blueprint $table) {
            $table->boolean('is_countdown')->default(false)->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('featured_events', function (Blueprint $table) {
            $table->dropColumn('is_countdown');
        });
    }
};
