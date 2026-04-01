<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CalendarConnection;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    /**
     * Get aggregated onboarding context for the wizard.
     */
    public function status(Request $request): JsonResponse
    {
        $user = $request->user();
        $family = $user->currentFamily()->firstOrFail();

        return response()->json([
            'family_name' => $family->name,
            'invite_code' => $user->isParent() ? $family->invite_code : null,
            'timezone' => $user->timezone,
            'calendar_connected' => CalendarConnection::where('user_id', $user->id)
                ->where('is_active', true)
                ->exists(),
            'tag_count' => Tag::where('family_id', $family->id)->count(),
            'modules' => [
                'points' => $family->settings['modules']['points'] ?? true,
                'badges' => $family->settings['modules']['badges'] ?? true,
                'chat' => $family->settings['modules']['chat'] ?? true,
                'vault' => $family->settings['modules']['vault'] ?? true,
            ],
            'onboarding_completed_at' => $user->onboarding_completed_at,
            'is_parent' => $user->isParent(),
        ]);
    }

    /**
     * Mark onboarding as complete for the authenticated user.
     */
    public function complete(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user->onboarding_completed_at) {
            $user->update(['onboarding_completed_at' => now()]);
        }

        return response()->json([
            'message' => 'Onboarding complete.',
            'onboarding_completed_at' => $user->fresh()->onboarding_completed_at,
        ]);
    }
}
