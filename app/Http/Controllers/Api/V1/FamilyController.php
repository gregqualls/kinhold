<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\FamilyResource;
use App\Http\Resources\UserResource;
use App\Models\Family;
use App\Models\User;
use App\Notifications\FamilyInviteNotification;
use App\Notifications\WelcomeNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class FamilyController extends Controller
{
    /**
     * Get the current user's family with members.
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
     */
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();
        $family = $user->currentFamily()->firstOrFail();

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
     * Optionally send an invite email if an email address is provided.
     */
    public function invite(Request $request): JsonResponse
    {
        $user = $request->user();
        $family = $user->currentFamily()->firstOrFail();

        $this->authorize('invite', $family);

        $request->validate([
            'email' => 'nullable|email',
        ]);

        // Generate invite code if not exists
        if (!$family->invite_code) {
            $family->update([
                'invite_code' => Str::random(8),
            ]);
        }

        // Send invite email if email address provided
        $emailSent = false;
        if ($request->filled('email')) {
            Notification::route('mail', $request->input('email'))
                ->notify(new FamilyInviteNotification($family, $family->invite_code, $user->name));
            $emailSent = true;
        }

        return response()->json([
            'invite_code' => $family->invite_code,
            'email_sent' => $emailSent,
            'message' => $emailSent
                ? "Invite email sent to {$request->input('email')}"
                : 'Share this code with family members so they can join during registration',
        ], 200);
    }

    /**
     * Add a new family member directly (parent only).
     * Supports managed accounts (no email/password) for young children.
     */
    public function addMember(Request $request): JsonResponse
    {
        $user = $request->user();
        $family = $user->currentFamily()->firstOrFail();

        $this->authorize('addMember', $family);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'nullable|string|min:8',
            'role' => 'required|in:parent,child',
            'date_of_birth' => 'nullable|date',
        ]);

        $isManaged = empty($validated['email']);

        $member = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'password' => !empty($validated['password']) ? Hash::make($validated['password']) : null,
            'family_id' => $family->id,
            'family_role' => $validated['role'],
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'is_managed' => $isManaged,
            'managed_by' => $isManaged ? $user->id : null,
            'onboarding_completed_at' => $isManaged ? now() : null,
        ]);

        // Send welcome email if the member has an email and send_email was requested
        if ($member->email && ($request->boolean('send_email') || !$isManaged)) {
            $member->notify(new WelcomeNotification($family, false));
        }

        return response()->json([
            'member' => UserResource::make($member),
            'message' => $isManaged
                ? "Managed account created for {$member->name}. You can switch to their profile from Settings."
                : "Account created for {$member->name}.",
        ], 201);
    }

    /**
     * Update a family member's details (parent only).
     */
    public function updateMember(Request $request, User $member): JsonResponse
    {
        $user = $request->user();
        $family = $user->currentFamily()->firstOrFail();

        $this->authorize('updateMember', $family);

        // Ensure the member belongs to the same family
        if ($member->family_id !== $family->id) {
            return response()->json(['message' => 'Member not found in your family'], 404);
        }

        // Cannot demote yourself
        if ($member->id === $user->id && $request->input('role') === 'child') {
            return response()->json(['message' => 'You cannot change your own role'], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => [
                'nullable',
                'email',
                Rule::unique('users', 'email')->ignore($member->id),
            ],
            'role' => 'sometimes|in:parent,child',
            'date_of_birth' => 'nullable|date',
        ]);

        $updateData = [];

        if (isset($validated['name'])) {
            $updateData['name'] = $validated['name'];
        }
        if (array_key_exists('email', $validated)) {
            $updateData['email'] = $validated['email'];
        }
        if (isset($validated['role'])) {
            $updateData['family_role'] = $validated['role'];
        }
        if (array_key_exists('date_of_birth', $validated)) {
            $updateData['date_of_birth'] = $validated['date_of_birth'];
        }

        $member->update($updateData);

        return response()->json([
            'member' => UserResource::make($member->fresh()),
        ], 200);
    }

    /**
     * Remove a family member (parent only).
     */
    public function removeMember(Request $request, User $member): JsonResponse
    {
        $user = $request->user();
        $family = $user->currentFamily()->firstOrFail();

        $this->authorize('removeMember', $family);

        // Cannot remove yourself
        if ($member->id === $user->id) {
            return response()->json(['message' => 'You cannot remove yourself'], 403);
        }

        // Ensure the member belongs to the same family
        if ($member->family_id !== $family->id) {
            return response()->json(['message' => 'Member not found in your family'], 404);
        }

        // Delete managed accounts entirely, unlink non-managed ones
        if ($member->is_managed) {
            $member->tokens()->delete();
            $member->delete();
        } else {
            $member->update(['family_id' => null, 'family_role' => 'child']);
        }

        return response()->json([
            'message' => 'Member removed from family',
        ], 200);
    }
}
