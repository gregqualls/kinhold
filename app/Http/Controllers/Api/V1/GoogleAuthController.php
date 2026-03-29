<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\PendingLinkException;
use App\Http\Controllers\Controller;
use App\Models\Family;
use App\Models\User;
use App\Services\BadgeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
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

        try {
            $user = $this->findOrCreateUser($googleUser);
        } catch (PendingLinkException $e) {
            // Redirect to the SPA link-confirmation page with the pending code
            return redirect('/login?link_pending=' . $e->pendingCode . '&email=' . urlencode($e->email));
        }

        // SECURITY: Use a short-lived, single-use auth code instead of exposing the token in the URL.
        // The SPA exchanges this code for a real token via POST /api/v1/auth/exchange.
        $authCode = Str::random(64);
        Cache::put("auth_code:{$authCode}", $user->id, now()->addMinutes(2));

        return redirect('/login?code=' . $authCode . ($isNewUser ? '&new_account=1' : ''));
    }

    /**
     * Exchange a short-lived auth code for a Sanctum token.
     * Called by the SPA after the OAuth redirect.
     */
    public function exchange(Request $request): JsonResponse
    {
        $request->validate(['code' => 'required|string|size:64']);

        $userId = Cache::pull("auth_code:{$request->code}");

        if (!$userId) {
            return response()->json(['message' => 'Invalid or expired auth code'], 401);
        }

        $user = User::findOrFail($userId);
        $token = $user->createToken('google_auth')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => new \App\Http\Resources\UserResource($user->load('family')),
        ]);
    }

    /**
     * Confirm linking a Google account to an existing password-based account.
     * The user enters their password to prove they own the account.
     */
    public function confirmLink(Request $request): JsonResponse
    {
        $request->validate([
            'pending_code' => 'required|string|size:64',
            'password' => 'required|string',
        ]);

        $pending = Cache::pull("google_link_pending:{$request->pending_code}");

        if (!$pending) {
            return response()->json(['message' => 'Link request expired. Please try again.'], 401);
        }

        $user = User::findOrFail($pending['user_id']);

        if (!\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
            // Put it back so they can retry (but within the 10-min window)
            Cache::put("google_link_pending:{$request->pending_code}", $pending, now()->addMinutes(5));
            return response()->json(['message' => 'Incorrect password'], 401);
        }

        // Link the Google account
        $user->update([
            'google_id' => $pending['google_id'],
            'google_avatar' => $pending['google_avatar'],
        ]);

        // Create a token and log them in
        $token = $user->createToken('google_auth')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => new \App\Http\Resources\UserResource($user->load('family')),
            'message' => 'Google account linked successfully!',
        ]);
    }

    /**
     * Initiate Google account linking from Settings (authenticated user).
     * Returns the Google OAuth URL with a special state parameter.
     */
    public function linkRedirect(Request $request): JsonResponse
    {
        $state = encrypt('link:' . $request->user()->id);

        $url = Socialite::driver('google')
            ->stateless()
            ->with(['state' => $state])
            ->redirectUrl(url('/auth/google/link-callback'))
            ->redirect()
            ->getTargetUrl();

        return response()->json(['url' => $url]);
    }

    /**
     * Handle callback for Google account linking.
     * Links the Google account to the authenticated user's existing account.
     */
    public function linkCallback(Request $request)
    {
        $rawState = $request->query('state');

        try {
            $decrypted = decrypt($rawState);
        } catch (\Exception $e) {
            return redirect('/settings?google_error=' . urlencode('Invalid link request.'));
        }

        if (!str_starts_with($decrypted, 'link:')) {
            return redirect('/settings?google_error=' . urlencode('Invalid link request.'));
        }

        $userId = substr($decrypted, 5);

        try {
            $googleUser = Socialite::driver('google')
                ->stateless()
                ->redirectUrl(url('/auth/google/link-callback'))
                ->user();
        } catch (\Exception $e) {
            Log::error('Google link callback failed', ['error' => $e->getMessage()]);
            return redirect('/settings?google_error=' . urlencode('Google authentication failed.'));
        }

        $user = User::findOrFail($userId);

        // Check if this Google account is already linked to a different user
        $existing = User::where('google_id', $googleUser->getId())->where('id', '!=', $user->id)->first();
        if ($existing) {
            return redirect('/settings?google_error=' . urlencode('This Google account is already linked to another user.'));
        }

        // Check that the Google email matches the user's email
        if (strtolower($googleUser->getEmail()) !== strtolower($user->email)) {
            return redirect('/settings?google_error=' . urlencode('Google email does not match your account email.'));
        }

        $user->update([
            'google_id' => $googleUser->getId(),
            'google_avatar' => $googleUser->getAvatar(),
        ]);

        return redirect('/settings?google_linked=1');
    }

    /**
     * Unlink Google account from the authenticated user.
     */
    public function unlink(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->google_id) {
            return response()->json(['message' => 'No Google account linked'], 422);
        }

        // Don't allow unlinking if user has no password (would lock them out)
        if (!$user->password) {
            return response()->json(['message' => 'Set a password first before unlinking Google'], 422);
        }

        $user->update(['google_id' => null]);

        return response()->json(['message' => 'Google account unlinked']);
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

        // 2. Check if a user with this email exists
        // SECURITY: Only auto-link if the user has no password set (was created via Google).
        // If they registered with email/password, reject the login to prevent account takeover.
        $user = User::whereRaw('LOWER(email) = ?', [strtolower($googleUser->getEmail())])->first();

        if ($user) {
            if ($user->password) {
                // User registered with email/password — store Google info temporarily
                // and redirect to a page where they can enter their password to confirm the link.
                $pendingCode = Str::random(64);
                Cache::put("google_link_pending:{$pendingCode}", [
                    'user_id' => $user->id,
                    'google_id' => $googleUser->getId(),
                    'google_avatar' => $googleUser->getAvatar(),
                ], now()->addMinutes(10));

                throw new PendingLinkException($pendingCode, $user->email);
            }

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
            'invite_code' => Str::random(16),
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
