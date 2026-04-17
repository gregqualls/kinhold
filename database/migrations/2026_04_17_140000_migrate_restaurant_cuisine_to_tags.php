<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('restaurants', 'cuisine')) {
            return;
        }

        // Backfill: for every restaurant with a cuisine, convert the value
        // to food-scoped tag(s) per family that has the restaurant linked.
        $restaurants = DB::table('restaurants')
            ->whereNotNull('cuisine')
            ->where('cuisine', '!=', '')
            ->get(['id', 'cuisine']);

        foreach ($restaurants as $restaurant) {
            $cuisines = array_values(array_filter(array_map(
                'trim',
                preg_split('/[,;]+/', (string) $restaurant->cuisine) ?: []
            )));

            if (empty($cuisines)) {
                continue;
            }

            // Walk every family linked to this restaurant via the pivot.
            $familyIds = DB::table('family_restaurants')
                ->where('restaurant_id', $restaurant->id)
                ->pluck('family_id');

            foreach ($familyIds as $familyId) {
                foreach ($cuisines as $name) {
                    $existing = DB::table('tags')
                        ->where('family_id', $familyId)
                        ->where('name', $name)
                        ->where('scope', 'food')
                        ->first();

                    $tagId = $existing->id ?? null;
                    if (! $tagId) {
                        $tagId = (string) Str::uuid();
                        DB::table('tags')->insert([
                            'id' => $tagId,
                            'family_id' => $familyId,
                            'name' => $name,
                            'color' => null,
                            'scope' => 'food',
                            'sort_order' => (DB::table('tags')->where('family_id', $familyId)->max('sort_order') ?? 0) + 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }

                    // Attach if not already attached.
                    $alreadyAttached = DB::table('restaurant_tag')
                        ->where('restaurant_id', $restaurant->id)
                        ->where('tag_id', $tagId)
                        ->exists();

                    if (! $alreadyAttached) {
                        DB::table('restaurant_tag')->insert([
                            'id' => (string) Str::uuid(),
                            'restaurant_id' => $restaurant->id,
                            'tag_id' => $tagId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }

        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn('cuisine');
        });
    }

    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->string('cuisine', 100)->nullable()->after('menu_url');
        });
        // Tag data is not reverted — safe to leave attached.
    }
};
