<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\FamilyResource;
use App\Models\Family;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FamilyController extends Controller
{
    /**
     * Get the current user's family with members.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request): JsonResponse
    {
        $family = $request->user()->currentFamily()->with('members')->firstOrFail();

        return response()->json([
            'family' => FamilyResource::make($family),
        ], 200);
    }

    /**
     * Update family details (parent only).
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
            'name' => 'required|string|max:255',
            'settings' => 'nullable|array',
        ]);

        $family->update([
            'name' => $validated['name'],
            'slug' => str($validated['name'])->slug(),
            'settings' => $validated['settings'] ?? $family->settings,
        ]);

        return response()->json([
            'family' => FamilyResource::make($family->load('members')),
        ], 200);
    }

    /**
     * Generate an invite link/code for adding family members (parent only).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function invite(Request $request): JsonResponse
    {
        $user = $request->user();
        $family = $user->currentFamily()->firstOrFail();

        // Authorize: user must be parent
        $this->authorize('invite', $family);

        $validated = $request->validate([
            'role' => 'nullable|in:parent,child',
        ]);

        // Generate invite code if not exists
        if (!$family->invite_code) {
            $family->update([
                'invite_code' => Str::random(32),
            ]);
        }

        $inviteLink = url("/api/v1/family/join/{$family->invite_code}");

        return response()->json([
            'invite_code' => $family->invite_code,
            'invite_link' => $inviteLink,
            'message' => 'Share this code or link to add family members',
        ], 200);
    }
}
