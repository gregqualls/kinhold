<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->json('notification_preferences')->nullable()->after('email_preferences');
        });

        // Backfill: copy existing email_preferences into the email channel of the
        // unified notification_preferences shape. Keys are stored without the
        // "email_" prefix so the new wants($channel, $key) accessor can address
        // both channels uniformly.
        DB::table('users')
            ->whereNotNull('email_preferences')
            ->orderBy('id')
            ->each(function ($user) {
                $existing = json_decode($user->email_preferences, true) ?: [];
                $emailPrefs = [];
                foreach ($existing as $key => $value) {
                    $stripped = str_starts_with($key, 'email_') ? substr($key, 6) : $key;
                    $emailPrefs[$stripped] = (bool) $value;
                }

                DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'notification_preferences' => json_encode([
                            'email' => $emailPrefs,
                            'push' => [],
                            'quiet_hours' => [
                                'enabled' => false,
                                'start' => '22:00',
                                'end' => '07:00',
                            ],
                            'muted' => false,
                            'dinner_reminder_at' => '15:00',
                        ]),
                    ]);
            });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('notification_preferences');
        });
    }
};
