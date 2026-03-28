<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('google_avatar')->nullable()->after('avatar_color');
        });

        // Backfill: copy current avatar to google_avatar for users who signed in via Google
        DB::table('users')
            ->whereNotNull('google_id')
            ->whereNotNull('avatar')
            ->where('avatar', 'like', 'https://lh3.googleusercontent.com%')
            ->update(['google_avatar' => DB::raw('avatar')]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('google_avatar');
        });
    }
};
