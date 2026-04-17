<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tags', function (Blueprint $table) {
            $table->string('scope', 16)->default('task')->after('color');
            $table->index(['family_id', 'scope']);
        });

        // Backfill: any tag that's currently attached to a recipe or restaurant
        // is a food tag. Everything else stays a task tag.
        DB::table('tags')
            ->whereIn('id', function ($q) {
                $q->select('tag_id')->from('recipe_tag');
            })
            ->orWhereIn('id', function ($q) {
                $q->select('tag_id')->from('restaurant_tag');
            })
            ->update(['scope' => 'food']);

        // Also backfill by name for the seeded recipe-friendly tags that
        // may not yet be attached to a recipe (e.g. fresh dev DB).
        DB::table('tags')
            ->whereIn('name', ['Breakfast', 'Lunch', 'Dinner', 'Dessert', 'Snack'])
            ->update(['scope' => 'food']);
    }

    public function down(): void
    {
        Schema::table('tags', function (Blueprint $table) {
            $table->dropIndex(['family_id', 'scope']);
            $table->dropColumn('scope');
        });
    }
};
