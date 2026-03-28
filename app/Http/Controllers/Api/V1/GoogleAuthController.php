<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Family;
use App\Models\User;
use App\Services\BadgeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Return the Google OAuth redirect URL for the SPA to navigate to.
     *
     * @return JsonResponse
     */
    public function redirect(): JsonResponse
    {
        $url = Socialite::driver('google')
            ->stateless()
            ->redirect()
            ->getTargetUrl();

        return response()->json(['url' => $url]);
    }

    /**
     * Handle the Google OAuth callback.
     *
     * Since OAuth callbacks are full-page redirects from Google,
     * this creates a Sanctum token and redirects back to the SPA
     * with the token as a query parameter.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')
                ->stateless()
                ->user();
        } catch (\Exception $e) {
            Log::error('Google OAuth callback failed', [
                'error' => $e->getMessage(),
            ]);

            return redirect('/?auth_error=' . urlencode('Google authentication failed. Please try again.'));
        }

        // 1. Check if a user with this google_id already exists
        $user = User::where('google_id', $googleUser->getId())->first();

        if ($user) {
            // Existing Google user — refresh Google avatar and log them in
            $user->update(['google_avatar' => $googleUser->getAvatar()]);
            $token = $user->createToken('google_auth')->plainTextToken;

            return redirect('/login?token=' . $token);
        }

        // 2. Check if a user with this email exists (link Google account)
        $user = User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            // Link Google account to existing user
            $user->update([
                'google_id' => $googleUser->getId(),
                'avatar' => $user->avatar ?? $googleUser->getAvatar(),
                'google_avatar' => $googleUser->getAvatar(),
            ]);

            $token = $user->createToken('google_auth')->plainTextToken;

            return redirect('/login?token=' . $token);
        }

        // 3. New user — create account + family
        $family = Family::create([
            'name' => Str::before($googleUser->getName(), ' ') . ' Family',
            'slug' => Str::slug(Str::before($googleUser->getName(), ' ') . '-family'),
            'invite_code' => Str::random(8),
        ]);

        $user = User::create([
            'name' => $googleUser->getName(),
            'email' => $googleUser->getEmail(),
            'google_id' => $googleUser->getId(),
            'avatar' => $googleUser->getAvatar(),
            'google_avatar' => $googleUser->getAvatar(),
            'family_id' => $family->id,
            'family_role' => 'parent',
        ]);

        // Seed default badges for the newly created family
        BadgeService::createDefaultBadges($family->id, $user->id);

        $token = $user->createToken('google_auth')->plainTextToken;

        return redirect('/login?token=' . $token . '&new_account=1');
    }
}
