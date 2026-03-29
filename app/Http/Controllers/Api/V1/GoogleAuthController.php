<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Family;
use App\Models\User;
use App\Services\BadgeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
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

        $isNewUser = ! User::where('google_id', $googleUser->getId())->exists()
            && ! User::whereRaw('LOWER(email) = ?', [strtolower($googleUser->getEmail())])->exists();

        $user = $this->findOrCreateUser($googleUser);
        $token = $user->createToken('google_auth')->plainTextToken;

        return redirect('/login?token=' . $token . ($isNewUser ? '&new_account=1' : ''));
    }

    /**
     * Redirect to Google OAuth for session-based login.
     *
     * Used by Passport's /oauth/authorize endpoint which needs a web session.
     * Unlike the SPA flow, this uses sessions (not stateless) so that after
     * callback we can Auth::login() and redirect to /oauth/authorize.
     */
    public function oauthLogin(): RedirectResponse
    {
        return Socialite::driver('google')
            ->redirectUrl(route('google.oauth-callback'))
            ->redirect();
    }

    /**
     * Handle Google OAuth callback for session-based login.
     *
     * Creates/finds the user, establishes a web session, then redirects
     * to the originally intended URL (/oauth/authorize).
     */
    public function oauthCallback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')
                ->redirectUrl(route('google.oauth-callback'))
                ->user();
        } catch (\Exception $e) {
            Log::error('Google OAuth callback failed (MCP oauth flow)', [
                'error' => $e->getMessage(),
            ]);

            return redirect('/?auth_error=' . urlencode('Authentication failed.'));
        }

        $user = $this->findOrCreateUser($googleUser);

        // Establish web session (key difference from the stateless SPA flow)
        Auth::login($user);

        // Redirect to the originally intended URL (/oauth/authorize?...)
        return redirect()->intended('/');
    }

    /**
     * Find an existing user by Google ID or email, or create a new one.
     * Shared logic between the SPA callback and OAuth callback.
     */
    private function findOrCreateUser(object $googleUser): User
    {
        // 1. Check if a user with this google_id already exists
        $user = User::where('google_id', $googleUser->getId())->first();

        if ($user) {
            $user->update(['google_avatar' => $googleUser->getAvatar()]);
            return $user;
        }

        // 2. Check if a user with this email exists (link Google account)
        $user = User::whereRaw('LOWER(email) = ?', [strtolower($googleUser->getEmail())])->first();

        if ($user) {
            $user->update([
                'google_id' => $googleUser->getId(),
                'avatar' => $user->avatar ?? $googleUser->getAvatar(),
                'google_avatar' => $googleUser->getAvatar(),
            ]);
            return $user;
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

        BadgeService::createDefaultBadges($family->id, $user->id);

        return $user;
    }
}
