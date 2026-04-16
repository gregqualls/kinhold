<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Family;
use App\Models\User;
use App\Services\AccountDeletionService;
use App\Services\AgentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SettingsController extends Controller
{
    /**
     * Get app settings for the current family.
     */
    public function index(Request $request): JsonResponse
    {
        $family = $request->user()->currentFamily()->firstOrFail();
        $settings = $family->settings ?? [];

        return response()->json([
            'settings' => [
                'id' => $family->id,
                'name' => $family->name,
                'modules' => $settings['modules'] ?? [
                    'tasks' => true,
                    'vault' => true,
                    'calendar' => true,
                    'chat' => true,
                    'points' => true,
                    'badges' => true,
                ],
                'module_access' => $family->getAllModuleAccess(),
                'preferences' => $settings['preferences'] ?? [],
                'leaderboard_period' => $settings['leaderboard_period'] ?? 'weekly',
                'kudos_cost_enabled' => $settings['kudos_cost_enabled'] ?? false,
                'default_points_low' => $settings['default_points_low'] ?? 5,
                'default_points_medium' => $settings['default_points_medium'] ?? 10,
                'default_points_high' => $settings['default_points_high'] ?? 20,
                'ai_mode' => $settings['ai_mode'] ?? 'kinhold',
                'ai_provider' => $settings['ai_provider'] ?? 'anthropic',
                'ai_model' => $settings['ai_model'] ?? '',
                'ai_api_key_masked' => $this->maskApiKey($settings),
                'ai_has_key' => ! empty($settings['ai_api_key']),
                'ai_providers' => AgentService::availableProviders(),
                'task_assignment' => $settings['task_assignment'] ?? [
                    'mode' => 'all',
                    'users' => [],
                ],
                'children_can_change_avatar' => $settings['children_can_change_avatar'] ?? true,
                'week_start_day' => $settings['week_start_day'] ?? 'monday',
                'meal_slots' => $settings['meal_slots'] ?? ['breakfast', 'lunch', 'dinner', 'snack'],
                'services' => [
                    'google_oauth' => ! empty(config('services.google.client_id')),
                    'google_calendar' => ! empty(config('kinhold.google.client_id')),
                    'ai_platform_key' => ! empty(config('kinhold.chatbot.api_key')),
                    'ai_family_key' => ! empty($settings['ai_api_key']),
                    'mail' => ! empty(config('mail.mailers.'.config('mail.default').'.host'))
                        || config('mail.default') === 'log',
                ],
            ],
        ], 200);
    }

    /**
     * Update app settings (parent only).
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
            'module_access' => 'nullable|array',
            'module_access.*' => 'nullable|array',
            'module_access.*.mode' => 'required_with:module_access.*|string|in:all,off,roles,users',
            'module_access.*.roles' => 'nullable|array',
            'module_access.*.roles.*' => 'string|in:parent,child',
            'module_access.*.users' => 'nullable|array',
            'module_access.*.users.*' => 'string|uuid',
            'preferences' => 'nullable|array',
            'leaderboard_period' => 'nullable|string|in:daily,weekly,monthly',
            'kudos_cost_enabled' => 'nullable|boolean',
            'default_points_low' => 'nullable|integer|min:0|max:1000',
            'default_points_medium' => 'nullable|integer|min:0|max:1000',
            'default_points_high' => 'nullable|integer|min:0|max:1000',
            'ai_mode' => 'nullable|string|in:kinhold,byok',
            'ai_provider' => 'nullable|string|in:anthropic,openai,google',
            'ai_api_key' => 'nullable|string|max:500',
            'ai_model' => 'nullable|string|max:100',
            'task_assignment' => 'nullable|array',
            'task_assignment.mode' => 'nullable|string|in:all,parents_only,users',
            'task_assignment.users' => 'nullable|array',
            'task_assignment.users.*' => 'uuid|exists:users,id',
            'children_can_change_avatar' => 'nullable|boolean',
            'week_start_day' => 'nullable|string|in:sunday,monday',
            'meal_slots' => 'nullable|array|min:1',
            'meal_slots.*' => 'string|in:breakfast,lunch,dinner,snack',
        ]);

        $settings = $family->settings ?? [];

        if ($request->filled('modules')) {
            $settings['modules'] = array_merge(
                $settings['modules'] ?? [],
                $validated['modules']
            );
        }

        // Granular module_access — replaces legacy boolean modules when present
        if ($request->filled('module_access')) {
            $allowed = Family::MODULES;
            $existing = $settings['module_access'] ?? [];

            foreach ($validated['module_access'] as $module => $rule) {
                if (! in_array($module, $allowed, true)) {
                    continue;
                }
                $existing[$module] = $rule;

                // Keep the legacy modules key in sync for backward compat
                $settings['modules'] = $settings['modules'] ?? [];
                $settings['modules'][$module] = ($rule['mode'] ?? 'all') !== 'off';
            }

            $settings['module_access'] = $existing;
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

        // AI provider settings
        if ($request->has('ai_mode')) {
            $settings['ai_mode'] = $validated['ai_mode'];
        }

        if ($request->has('ai_provider')) {
            $settings['ai_provider'] = $validated['ai_provider'];
        }

        if ($request->has('ai_model')) {
            $settings['ai_model'] = $validated['ai_model'] ?? '';
        }

        // Task assignment permissions
        if ($request->has('task_assignment')) {
            $settings['task_assignment'] = [
                'mode' => $validated['task_assignment']['mode'] ?? 'all',
                'users' => $validated['task_assignment']['users'] ?? [],
            ];
        }

        if ($request->has('children_can_change_avatar')) {
            $settings['children_can_change_avatar'] = (bool) $validated['children_can_change_avatar'];
        }

        if ($request->filled('week_start_day')) {
            $settings['week_start_day'] = $validated['week_start_day'];
        }

        if ($request->has('meal_slots')) {
            $settings['meal_slots'] = $validated['meal_slots'];
        }

        // Encrypt the API key before storing. Only update if a non-empty value is sent.
        // Sending an empty string clears the key.
        if ($request->has('ai_api_key')) {
            $rawKey = $validated['ai_api_key'] ?? '';
            if ($rawKey !== '') {
                $settings['ai_api_key'] = encrypt($rawKey);
            } else {
                $settings['ai_api_key'] = '';
            }
        }

        $family->update(['settings' => $settings]);

        return response()->json([
            'settings' => [
                'id' => $family->id,
                'name' => $family->name,
                'modules' => $settings['modules'] ?? [],
                'module_access' => $family->getAllModuleAccess(),
                'preferences' => $settings['preferences'] ?? [],
                'leaderboard_period' => $settings['leaderboard_period'] ?? 'weekly',
                'kudos_cost_enabled' => $settings['kudos_cost_enabled'] ?? false,
                'default_points_low' => $settings['default_points_low'] ?? 5,
                'default_points_medium' => $settings['default_points_medium'] ?? 10,
                'default_points_high' => $settings['default_points_high'] ?? 20,
                'ai_mode' => $settings['ai_mode'] ?? 'kinhold',
                'ai_provider' => $settings['ai_provider'] ?? 'anthropic',
                'ai_model' => $settings['ai_model'] ?? '',
                'ai_api_key_masked' => $this->maskApiKey($settings),
                'ai_has_key' => ! empty($settings['ai_api_key']),
                'ai_providers' => AgentService::availableProviders(),
                'task_assignment' => $settings['task_assignment'] ?? [
                    'mode' => 'all',
                    'users' => [],
                ],
                'children_can_change_avatar' => $settings['children_can_change_avatar'] ?? true,
                'week_start_day' => $settings['week_start_day'] ?? 'monday',
                'meal_slots' => $settings['meal_slots'] ?? ['breakfast', 'lunch', 'dinner', 'snack'],
            ],
        ], 200);
    }

    /**
     * Mask the stored API key for display. Shows first 6 and last 4 chars.
     */
    private function maskApiKey(array $settings): string
    {
        if (empty($settings['ai_api_key'])) {
            return '';
        }

        try {
            $key = decrypt($settings['ai_api_key']);
        } catch (\Exception $e) {
            return '***invalid***';
        }

        if (Str::length($key) <= 12) {
            return Str::mask($key, '*', 4);
        }

        return Str::substr($key, 0, 6).'...'.Str::substr($key, -4);
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

    /**
     * Delete the authenticated user's account.
     */
    public function deleteAccount(Request $request, AccountDeletionService $service): JsonResponse
    {
        $user = $request->user();

        if (! $user->password) {
            return response()->json(['message' => 'Password-based deletion is not available for accounts without a password. Please set a password in your profile first, or ask a parent to remove your account.'], 403);
        }

        $request->validate([
            'password' => 'required|string',
        ]);

        if (! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Incorrect password'], 403);
        }

        $blocked = $service->canDeleteSelf($user);
        if ($blocked) {
            return response()->json(['message' => $blocked], 403);
        }

        $service->deleteUser($user);

        return response()->json(['message' => 'Account deleted successfully'], 200);
    }
}
