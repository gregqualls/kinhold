<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
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

        $family->update(['settings' => $settings]);

        return response()->json([
            'settings' => [
                'id' => $family->id,
                'name' => $family->name,
                'modules' => $settings['modules'] ?? [],
                'preferences' => $settings['preferences'] ?? [],
            ],
        ], 200);
    }
}
