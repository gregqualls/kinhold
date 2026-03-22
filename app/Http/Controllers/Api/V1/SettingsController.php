<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Get app settings for the current family.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $family = $request->user()->currentFamily()->firstOrFail();

        return response()->json([
            'settings' => [
                'id' => $family->id,
                'name' => $family->name,
                'modules' => $family->settings['modules'] ?? [
                    'tasks' => true,
                    'vault' => true,
                    'calendar' => true,
                    'chat' => true,
                    'points' => true,
                    'badges' => true,
                ],
                'preferences' => $family->settings['preferences'] ?? [],
                'leaderboard_period' => $family->settings['leaderboard_period'] ?? 'weekly',
                'kudos_cost_enabled' => $family->settings['kudos_cost_enabled'] ?? false,
                'default_points_low' => $family->settings['default_points_low'] ?? 5,
                'default_points_medium' => $family->settings['default_points_medium'] ?? 10,
                'default_points_high' => $family->settings['default_points_high'] ?? 20,
            ],
        ], 200);
    }

    /**
     * Update app settings (parent only).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();
        $family = $user->currentFamily()->firstOrFail();

        // Authorize: user must be parent
        $this->authorize('update', $family);

        $validated = $request->validate([
            'modules' => 'nullable|array',
            'modules.tasks' => 'nullable|boolean',
            'modules.vault' => 'nullable|boolean',
            'modules.calendar' => 'nullable|boolean',
            'modules.chat' => 'nullable|boolean',
            'modules.points' => 'nullable|boolean',
            'modules.badges' => 'nullable|boolean',
            'preferences' => 'nullable|array',
            'leaderboard_period' => 'nullable|string|in:daily,weekly,monthly',
            'kudos_cost_enabled' => 'nullable|boolean',
            'default_points_low' => 'nullable|integer|min:0|max:1000',
            'default_points_medium' => 'nullable|integer|min:0|max:1000',
            'default_points_high' => 'nullable|integer|min:0|max:1000',
        ]);

        $settings = $family->settings ?? [];

        if ($request->filled('modules')) {
            $settings['modules'] = array_merge(
                $settings['modules'] ?? [],
                $validated['modules']
            );
        }

        if ($request->filled('preferences')) {
            $settings['preferences'] = array_merge(
                $settings['preferences'] ?? [],
                $validated['preferences']
            );
        }

        if ($request->filled('leaderboard_period')) {
            $settings['leaderboard_period'] = $validated['leaderboard_period'];
        }

        if ($request->has('kudos_cost_enabled')) {
            $settings['kudos_cost_enabled'] = (bool) $validated['kudos_cost_enabled'];
        }

        // Update default points per priority
        foreach (['default_points_low', 'default_points_medium', 'default_points_high'] as $key) {
            if ($request->has($key)) {
                $settings[$key] = (int) $validated[$key];
            }
        }

        $family->update(['settings' => $settings]);

        return response()->json([
            'settings' => [
                'id' => $family->id,
                'name' => $family->name,
                'modules' => $settings['modules'] ?? [],
                'preferences' => $settings['preferences'] ?? [],
                'kudos_cost_enabled' => $settings['kudos_cost_enabled'] ?? false,
                'default_points_low' => $settings['default_points_low'] ?? 5,
                'default_points_medium' => $settings['default_points_medium'] ?? 10,
                'default_points_high' => $settings['default_points_high'] ?? 20,
            ],
        ], 200);
    }

    /**
     * Get the current user's email notification preferences.
     */
    public function emailPreferences(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'email_preferences' => $user->email_preferences ?? User::defaultEmailPreferences(),
        ], 200);
    }

    /**
     * Update the current user's email notification preferences.
     */
    public function updateEmailPreferences(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'email_task_completed' => 'required|boolean',
            'email_task_assigned' => 'required|boolean',
            'email_weekly_digest' => 'required|boolean',
            'email_family_invite' => 'required|boolean',
        ]);

        $user->update([
            'email_preferences' => $validated,
        ]);

        return response()->json([
            'email_preferences' => $user->email_preferences,
            'message' => 'Email preferences updated successfully.',
        ], 200);
    }
}
