<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Truncate sessions since we're changing the column type
        DB::table('sessions')->truncate();

        Schema::table('sessions', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('sessions', function (Blueprint $table) {
            $table->uuid('user_id')->nullable()->index()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('sessions', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('sessions', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->index()->after('id');
        });
    }
};
