<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\Family;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Register a new user and create or join a family.
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $family = null;
        $role = 'child';

        if ($request->filled('invite_code')) {
            // Join existing family via invite code
            $family = Family::where('invite_code', $request->validated('invite_code'))->firstOrFail();
            $role = $request->validated('role', 'child');
        } else {
            // Create new family — creator becomes parent
            $family = Family::create([
                'name' => $request->validated('family_name'),
                'slug' => Str::slug($request->validated('family_name')),
                'invite_code' => Str::random(8),
            ]);
            $role = 'parent';
        }

        // Create the user with family assignment
        $user = User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => Hash::make($request->validated('password')),
            'family_id' => $family->id,
            'family_role' => $role,
        ]);

        // Create Sanctum token for API access
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
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->validated('email'))->first();

        if (!$user || !Hash::check($request->validated('password'), $user->password)) {
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
     * Logout the current user (revoke current token).
     *
     * @param Request $request
     * @return JsonResponse
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
     * Creates a new token for the child account. The parent's user ID
     * is stored in the token name so we can switch back.
     */
    public function switchProfile(Request $request): JsonResponse
    {
        $parent = $request->user();

        if (!$parent->isParent()) {
            return response()->json(['message' => 'Only parents can switch profiles'], 403);
        }

        $validated = $request->validate([
            'user_id' => 'required|uuid|exists:users,id',
            'password' => 'required|string',
        ]);

        // Verify the parent's password before allowing switch
        if (!Hash::check($validated['password'], $parent->password)) {
            return response()->json(['message' => 'Invalid password'], 401);
        }

        $child = User::where('id', $validated['user_id'])
            ->where('family_id', $parent->family_id)
            ->where('is_managed', true)
            ->first();

        if (!$child) {
            return response()->json(['message' => 'Can only switch to managed accounts in your family'], 403);
        }

        // Create a token for the child, encoding the parent ID in the token name
        $token = $child->createToken("switched_from:{$parent->id}")->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => UserResource::make($child),
            'switched_from' => [
                'id' => $parent->id,
                'name' => $parent->name,
            ],
            'message' => "Switched to {$child->name}'s profile",
        ], 200);
    }

    /**
     * Switch back to the parent's profile from a managed child session.
     * Requires the parent's password for security.
     */
    public function switchBack(Request $request): JsonResponse
    {
        $currentUser = $request->user();
        $currentToken = $currentUser->currentAccessToken();

        // Check if this is a switched session by looking at token name
        $tokenName = $currentToken->name;
        if (!str_starts_with($tokenName, 'switched_from:')) {
            return response()->json(['message' => 'Not in a switched session'], 400);
        }

        $parentId = str_replace('switched_from:', '', $tokenName);

        $validated = $request->validate([
            'password' => 'required|string',
        ]);

        $parent = User::find($parentId);

        if (!$parent || !Hash::check($validated['password'], $parent->password)) {
            return response()->json(['message' => 'Invalid password'], 401);
        }

        // Revoke the child's switched token
        $currentToken->delete();

        // Create a new token for the parent
        $token = $parent->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => UserResource::make($parent->load('family')),
            'message' => "Switched back to {$parent->name}'s profile",
        ], 200);
    }

    /**
     * Get the authenticated user with family data.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function user(Request $request): JsonResponse
    {
        $user = $request->user()->load('family.members');

        // Check if this is a switched session
        $switchedFrom = null;
        $currentToken = $user->currentAccessToken();
        if ($currentToken && str_starts_with($currentToken->name, 'switched_from:')) {
            $parentId = str_replace('switched_from:', '', $currentToken->name);
            $parent = User::find($parentId);
            if ($parent) {
                $switchedFrom = [
                    'id' => $parent->id,
                    'name' => $parent->name,
                ];
            }
        }

        return response()->json([
            'user' => UserResource::make($user),
            'switched_from' => $switchedFrom,
            'family' => $user->family ? [
                'id' => $user->family->id,
                'name' => $user->family->name,
                'invite_code' => $user->isParent() ? $user->family->invite_code : null,
                'settings' => $user->family->settings ?? [],
                'members' => $user->family->members->map(fn ($m) => [
                    'id' => $m->id,
                    'name' => $m->name,
                    'email' => $m->email,
                    'family_role' => $m->family_role,
                    'role' => $m->family_role,
                    'is_managed' => $m->is_managed,
                    'managed_by' => $m->managed_by,
                    'avatar' => $m->avatar,
                    'date_of_birth' => $m->date_of_birth,
                ]),
            ] : null,
        ], 200);
    }
}
