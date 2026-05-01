<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\ChatMessage;
use App\Models\Family;
use App\Models\User;
use App\Notifications\WelcomeNotification;
use App\Services\BadgeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Register a new user and create or join a family.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $family = null;
        $role = 'child';

        if ($request->filled('invite_code')) {
            $family = Family::where('invite_code', $request->validated('invite_code'))->firstOrFail();
            // SECURITY: Always assign 'child' role when joining via invite code.
            // Parents can promote members after they join.
            $role = 'child';
        } else {
            $family = Family::create([
                'name' => $request->validated('family_name'),
                'slug' => Str::slug($request->validated('family_name')),
                'invite_code' => Str::random(16),
            ]);
            $role = 'parent';
        }

        $isNewFamily = ! $request->filled('invite_code');

        $user = User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => Hash::make($request->validated('password')),
            'family_id' => $family->id,
            'family_role' => $role,
        ]);

        // Seed default badges for newly created families
        if ($isNewFamily) {
            BadgeService::createDefaultBadges($family->id, $user->id);
        }

        // Send welcome email
        $user->notify(new WelcomeNotification($family, $isNewFamily));

        // Self-hosted: auto-verify since there's likely no mail server
        if (env('SELF_HOSTED', false)) {
            $user->update(['email_verified_at' => now()]);
        } elseif ($user->email) {
            // Send email verification (new users with email only, not managed accounts)
            $user->sendEmailVerificationNotification();
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'token' => $token,
            'user' => UserResource::make($user),
            'family' => [
                'id' => $family->id,
                'name' => $family->name,
                'invite_code' => $family->invite_code,
            ],
        ], 201);
    }

    /**
     * Login an existing user.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->validated('email'))->first();

        if (! $user || ! Hash::check($request->validated('password'), $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => UserResource::make($user->load('family')),
        ], 200);
    }

    /**
     * Log in as a demo family member (no password required).
     * Only works for the reserved demo family (slug: q32-demo-family).
     */
    public function demoLogin(Request $request): JsonResponse
    {
        $request->validate([
            'member' => 'required|string|in:mike,sarah,emma,jake,lily',
        ]);

        $family = Family::where('slug', 'q32-demo-family')->first();

        if (! $family) {
            return response()->json([
                'message' => 'Demo family not available.',
            ], 404);
        }

        $user = User::where('family_id', $family->id)
            ->where('name', ucfirst($request->member))
            ->first();

        if (! $user) {
            return response()->json([
                'message' => 'Demo member not found.',
            ], 404);
        }

        // Clean slate per visitor: chat is user-scoped, so clearing this
        // demo user's history means the next visitor never sees the previous
        // visitor's questions. Real families keep their chat permanently.
        ChatMessage::where('user_id', $user->id)->delete();

        // Same reasoning for push subscriptions: a previous visitor's browser
        // endpoint must not keep receiving demo notifications after they've
        // moved on. Without this, anyone who ever subscribed to push as a
        // demo user would get pinged forever for activity in the demo family.
        $user->pushSubscriptions()->delete();

        $token = $user->createToken('demo_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => UserResource::make($user->load('family')),
        ]);
    }

    /**
     * Resend email verification notification.
     */
    public function resendVerification(Request $request): JsonResponse
    {
        if (env('SELF_HOSTED', false)) {
            return response()->json(['message' => 'Email verification is not required for self-hosted instances'], 200);
        }

        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified'], 200);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification email sent']);
    }

    /**
     * Logout the current user (revoke current token).
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ], 200);
    }

    /**
     * Switch to a managed child's profile (parent only).
     * Logs the parent out and creates a new session for the child.
     * To get back, sign out and sign back in as parent.
     */
    public function switchProfile(Request $request): JsonResponse
    {
        $parent = $request->user();

        if (! $parent->isParent()) {
            return response()->json(['message' => 'Only parents can switch profiles'], 403);
        }

        $validated = $request->validate([
            'user_id' => 'required|uuid|exists:users,id',
        ]);

        $child = User::where('id', $validated['user_id'])
            ->where('family_id', $parent->family_id)
            ->where('is_managed', true)
            ->first();

        if (! $child) {
            return response()->json(['message' => 'Can only switch to managed accounts in your family'], 403);
        }

        // Revoke the parent's current token
        $parent->currentAccessToken()->delete();

        // Create a token for the child
        $token = $child->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => UserResource::make($child),
            'message' => "Switched to {$child->name}'s profile",
        ], 200);
    }

    /**
     * Update the authenticated user's profile.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'timezone' => 'nullable|string|timezone',
            // Kin accent families are the canonical set going forward.
            // Legacy values kept for backwards compatibility with existing user data.
            'avatar_color' => 'nullable|string|in:lavender,peach,mint,sun,teal,amber,sage,steel,plum,rose,sienna,cyan,olive,berry,forest',
            'user_id' => 'nullable|string|exists:users,id',
        ]);

        $target = $this->resolveAvatarTarget($request);
        unset($validated['user_id']);
        $target->update($validated);

        return response()->json([
            'user' => UserResource::make($target->fresh()),
            'message' => 'Profile updated.',
        ]);
    }

    /**
     * Restore the user's Google profile photo as their avatar.
     */
    public function restoreGoogleAvatar(Request $request): JsonResponse
    {
        $user = $this->resolveAvatarTarget($request);
        $this->authorizeAvatarChange($user, $request->user());

        if (! $user->google_avatar) {
            return response()->json(['message' => 'No Google photo available.'], 422);
        }

        $this->deleteUploadedAvatarFile($user);
        $user->update(['avatar' => $user->google_avatar]);

        return response()->json([
            'user' => UserResource::make($user->fresh()),
            'message' => 'Google photo restored.',
        ]);
    }

    /**
     * Upload a profile avatar image.
     */
    public function uploadAvatar(Request $request): JsonResponse
    {
        $user = $this->resolveAvatarTarget($request);
        $this->authorizeAvatarChange($user, $request->user());

        $request->validate([
            'avatar' => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
        ]);

        $file = $request->file('avatar');
        $ext = $file->getClientOriginalExtension();

        // Delete any existing uploaded avatar
        $this->deleteUploadedAvatarFile($user);

        $file->storeAs('avatars', "{$user->id}.{$ext}", 'public');
        $url = url("/api/v1/user/avatar/{$user->id}").'?t='.time();

        $user->update(['avatar' => $url]);

        return response()->json([
            'user' => UserResource::make($user->fresh()),
            'message' => 'Avatar updated.',
        ]);
    }

    /**
     * Remove the user's avatar.
     */
    public function deleteAvatar(Request $request): JsonResponse
    {
        $user = $this->resolveAvatarTarget($request);
        $this->authorizeAvatarChange($user, $request->user());

        $this->deleteUploadedAvatarFile($user);
        $user->update(['avatar' => null]);

        return response()->json([
            'user' => UserResource::make($user->fresh()),
            'message' => 'Avatar removed.',
        ]);
    }

    /**
     * Set a preset Phosphor icon as avatar.
     */
    public function setPresetAvatar(Request $request): JsonResponse
    {
        $user = $this->resolveAvatarTarget($request);
        $this->authorizeAvatarChange($user, $request->user());

        $request->validate([
            'preset' => 'required|string|in:'.implode(',', self::AVATAR_PRESETS),
        ]);

        $this->deleteUploadedAvatarFile($user);
        $user->update(['avatar' => 'phosphor:'.$request->input('preset')]);

        return response()->json([
            'user' => UserResource::make($user->fresh()),
            'message' => 'Avatar updated.',
        ]);
    }

    /**
     * Allowed preset avatar icon names.
     */
    private const AVATAR_PRESETS = [
        'horse', 'bird', 'dog', 'cat', 'fish', 'butterfly', 'paw-print',
        'tree', 'flower-lotus', 'mountains', 'sun', 'moon-stars',
        'rocket', 'planet', 'shooting-star', 'alien',
        'crown', 'diamond', 'lightning', 'shield-star', 'sword', 'guitar', 'game-controller',
        'smiley', 'heart', 'peace',
    ];

    /**
     * Check if the user is allowed to change their avatar.
     */
    /**
     * Resolve the target user for avatar changes.
     * If user_id is provided and the requester is a parent in the same family, target that user.
     * Otherwise, target the authenticated user.
     */
    private function resolveAvatarTarget(Request $request): User
    {
        $currentUser = $request->user();
        $targetId = $request->input('user_id');

        if ($targetId && $targetId !== $currentUser->id) {
            // Only parents can change another member's avatar
            if (! $currentUser->isParent()) {
                abort(403, 'Only parents can change another member\'s avatar.');
            }

            $target = User::where('id', $targetId)
                ->where('family_id', $currentUser->family_id)
                ->firstOrFail();

            return $target;
        }

        return $currentUser;
    }

    private function authorizeAvatarChange(User $user, User $requester): void
    {
        // Parents can always change any family member's avatar
        if ($requester->isParent() && $requester->family_id === $user->family_id) {
            return;
        }

        if ($user->isChild()) {
            $family = $user->family;
            $settings = $family->settings ?? [];
            $allowed = $settings['children_can_change_avatar'] ?? true;

            if (! $allowed) {
                abort(403, 'Avatar changes are disabled for children.');
            }
        }
    }

    /**
     * Delete any previously uploaded avatar file from storage.
     */
    private function deleteUploadedAvatarFile(User $user): void
    {
        if (! $user->avatar || ! str_starts_with($user->avatar, 'http')) {
            return;
        }

        // Check for files matching avatars/{user_id}.* on the public disk
        $disk = Storage::disk('public');
        foreach (['jpg', 'jpeg', 'png', 'gif', 'webp'] as $ext) {
            $path = "avatars/{$user->id}.{$ext}";
            if ($disk->exists($path)) {
                $disk->delete($path);
            }
        }
    }

    /**
     * Serve a user's uploaded avatar image.
     */
    public function serveAvatar(string $userId)
    {
        $disk = Storage::disk('public');
        foreach (['jpg', 'jpeg', 'png', 'gif', 'webp'] as $ext) {
            $path = "avatars/{$userId}.{$ext}";
            if ($disk->exists($path)) {
                return response($disk->get($path), 200)
                    ->header('Content-Type', $disk->mimeType($path))
                    ->header('Cache-Control', 'public, max-age=86400');
            }
        }

        abort(404);
    }

    /**
     * Get the authenticated user with family data.
     */
    public function user(Request $request): JsonResponse
    {
        $user = $request->user()->load('family.members');

        return response()->json([
            'user' => UserResource::make($user),
            'family' => $user->family ? [
                'id' => $user->family->id,
                'name' => $user->family->name,
                'slug' => $user->family->slug,
                'invite_code' => $user->isParent() ? $user->family->invite_code : null,
                'settings' => $user->family->settings ?? [],
                'billing_owner_id' => $user->family->billing_owner_id,
                'module_access' => $user->family->getAllModuleAccess(),
                'members' => $user->family->members->map(fn ($m) => [
                    'id' => $m->id,
                    'name' => $m->name,
                    'email' => $m->email,
                    'family_role' => $m->family_role,
                    'role' => $m->family_role,
                    'is_managed' => $m->is_managed,
                    'managed_by' => $m->managed_by,
                    'avatar' => $m->avatar,
                    'avatar_color' => $m->avatar_color,
                    'google_avatar' => $m->google_avatar,
                    'date_of_birth' => $m->date_of_birth?->format('Y-m-d'),
                ]),
            ] : null,
        ], 200);
    }
}
