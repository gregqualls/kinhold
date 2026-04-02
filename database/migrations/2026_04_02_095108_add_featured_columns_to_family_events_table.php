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
        Schema::table('family_events', function (Blueprint $table) {
            $table->string('recurrence', 20)->default('none')->after('color');
            $table->string('visibility', 20)->default('visible')->after('recurrence');
            $table->string('featured_scope', 20)->nullable()->after('visibility');
            $table->boolean('is_countdown')->default(false)->after('featured_scope');
            $table->string('icon', 50)->nullable()->after('is_countdown');
            $table->boolean('is_active')->default(true)->after('icon');
        });

        // Migrate data from featured_events into family_events
        if (Schema::hasTable('featured_events')) {
            $featuredEvents = DB::table('featured_events')->get();

            foreach ($featuredEvents as $fe) {
                $startTime = $fe->event_date.' '.($fe->event_time ? substr($fe->event_time, 0, 5).':00' : '00:00:00');

                DB::table('family_events')->insert([
                    'id' => Str::uuid()->toString(),
                    'family_id' => $fe->family_id,
                    'created_by' => $fe->created_by,
                    'title' => $fe->title,
                    'description' => $fe->description,
                    'start_time' => $startTime,
                    'end_time' => null,
                    'all_day' => true,
                    'location' => null,
                    'color' => $fe->color,
                    'recurrence' => $fe->recurrence ?? 'none',
                    'visibility' => 'visible',
                    'featured_scope' => 'family',
                    'is_countdown' => $fe->is_countdown ?? false,
                    'icon' => $fe->icon,
                    'is_active' => $fe->is_active ?? true,
                    'created_at' => $fe->created_at,
                    'updated_at' => $fe->updated_at,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('family_events', function (Blueprint $table) {
            $table->dropColumn([
                'recurrence',
                'visibility',
                'featured_scope',
                'is_countdown',
                'icon',
                'is_active',
            ]);
        });
    }
};
