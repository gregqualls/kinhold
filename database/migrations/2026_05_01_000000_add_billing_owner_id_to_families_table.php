<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('families', function (Blueprint $table) {
            $table->uuid('billing_owner_id')->nullable()->after('settings');
            $table->foreign('billing_owner_id')
                ->references('id')->on('users')
                ->nullOnDelete();
            $table->index('billing_owner_id');
        });

        // Backfill: pick the oldest parent user in each family as the
        // billing owner. Done in PHP so the same logic works on PostgreSQL
        // (hosted) and SQLite (self-hosted/CI).
        DB::table('families')->orderBy('id')->chunk(100, function ($families) {
            foreach ($families as $family) {
                $owner = DB::table('users')
                    ->where('family_id', $family->id)
                    ->where('family_role', 'parent')
                    ->orderBy('created_at')
                    ->value('id');

                if ($owner !== null) {
                    DB::table('families')
                        ->where('id', $family->id)
                        ->update(['billing_owner_id' => $owner]);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('families', function (Blueprint $table) {
            $table->dropForeign(['billing_owner_id']);
            $table->dropIndex(['billing_owner_id']);
            $table->dropColumn('billing_owner_id');
        });
    }
};
