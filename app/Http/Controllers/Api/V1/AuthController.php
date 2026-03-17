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
     * Get the authenticated user with family data.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function user(Request $request): JsonResponse
    {
        $user = $request->user()->load('family.members');

        return response()->json([
            'user' => UserResource::make($user),
            'family' => $user->family ? [
                'id' => $user->family->id,
                'name' => $user->family->name,
                'invite_code' => $user->isParent() ? $user->family->invite_code : null,
                'members' => $user->family->members->map(fn ($m) => [
                    'id' => $m->id,
                    'name' => $m->name,
                    'family_role' => $m->family_role,
                    'avatar' => $m->avatar,
                ]),
            ] : null,
        ], 200);
    }
}
