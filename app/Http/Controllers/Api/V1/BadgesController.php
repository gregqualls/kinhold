<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\User;
use App\Services\BadgeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BadgesController extends Controller
{
    public function __construct(
        private BadgeService $badgeService,
    ) {}

    /**
     * List family badges with earned status and progress for current user.
     * Auto-creates default badges if the family has none yet.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $family = $user->currentFamily()->firstOrFail();

        // Auto-create default badges if the family has fewer than the full set (27)
        if (Badge::where('family_id', $family->id)->count() < 20) {
            BadgeService::createDefaultBadges($family->id, $user->id);
        }

        // Catch up on any badges the user has earned but wasn't awarded yet
        try {
            $this->badgeService->checkAndAwardBadges($user);
            $this->awardMissingEasterEggBadges($user);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Badge catch-up failed: ' . $e->getMessage());
        }

        $badges = Badge::where('family_id', $family->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $earnedBadgeIds = $user->badges()->pluck('badges.id')->toArray();

        $badgesData = $badges->map(function ($badge) use ($user, $earnedBadgeIds) {
            $isEarned = in_array($badge->id, $earnedBadgeIds);

            // Hide details for hidden badges that aren't earned yet
            if ($badge->is_hidden && !$isEarned) {
                return [
                    'id' => $badge->id,
                    'name' => '???',
                    'description' => 'Hidden badge — complete the challenge to reveal!',
                    'icon' => null,
                    'color' => '#6b7280',
                    'is_hidden' => true,
                    'is_earned' => false,
                    'progress' => null,
                    'threshold' => null,
                ];
            }

            $progress = null;
            if (!$isEarned && $badge->trigger_threshold) {
                $progress = $this->badgeService->getCurrentValueForTrigger($user, $badge->trigger_type);
            }

            return [
                'id' => $badge->id,
                'name' => $badge->name,
                'description' => $badge->description,
                'icon' => $badge->icon,
                'custom_icon_path' => $badge->custom_icon_path,
                'color' => $badge->color,
                'trigger_type' => $badge->trigger_type,
                'trigger_threshold' => $badge->trigger_threshold,
                'is_hidden' => $badge->is_hidden,
                'is_earned' => $isEarned,
                'progress' => $progress,
                'earned_at' => $isEarned
                    ? $user->badges()->where('badges.id', $badge->id)->first()?->pivot->earned_at
                    : null,
            ];
        });

        return response()->json([
            'badges' => $badgesData,
        ]);
    }

    /**
     * Create a badge (parent only).
     */
    public function store(Request $request): JsonResponse
    {
        if (!$request->user()->isParent()) {
            return response()->json(['message' => 'Only parents can create badges'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
            'trigger_type' => 'required|string|in:points_earned,tasks_completed,task_streak,kudos_received,kudos_given,rewards_purchased,login_streak,custom,easter_egg',
            'trigger_threshold' => 'nullable|integer|min:1',
            'is_hidden' => 'nullable|boolean',
        ]);

        $family = $request->user()->currentFamily()->firstOrFail();

        $badge = Badge::create([
            'family_id' => $family->id,
            'created_by' => $request->user()->id,
            'name' => $validated['name'],
            'description' => $validated['description'],
            'icon' => $validated['icon'] ?? null,
            'color' => $validated['color'] ?? '#7d57a8',
            'trigger_type' => $validated['trigger_type'],
            'trigger_threshold' => $validated['trigger_threshold'] ?? null,
            'is_hidden' => $validated['is_hidden'] ?? false,
        ]);

        return response()->json([
            'badge' => $badge,
        ], 201);
    }

    /**
     * Update a badge (parent only).
     */
    public function update(Request $request, Badge $badge): JsonResponse
    {
        if (!$request->user()->isParent()) {
            return response()->json(['message' => 'Only parents can update badges'], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string|max:500',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
            'trigger_type' => 'sometimes|string|in:points_earned,tasks_completed,task_streak,kudos_received,kudos_given,rewards_purchased,login_streak,custom',
            'trigger_threshold' => 'nullable|integer|min:1',
            'is_hidden' => 'nullable|boolean',
            'is_active' => 'sometimes|boolean',
        ]);

        $badge->update($validated);

        return response()->json([
            'badge' => $badge,
        ]);
    }

    /**
     * Delete a badge (parent only).
     */
    public function destroy(Request $request, Badge $badge): JsonResponse
    {
        if (!$request->user()->isParent()) {
            return response()->json(['message' => 'Only parents can delete badges'], 403);
        }

        $badge->delete();

        return response()->json(null, 204);
    }

    /**
     * Manually award a badge (parent only).
     */
    public function award(Request $request, Badge $badge): JsonResponse
    {
        if (!$request->user()->isParent()) {
            return response()->json(['message' => 'Only parents can award badges'], 403);
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $family = $request->user()->currentFamily()->firstOrFail();
        $targetUser = $family->members()->findOrFail($validated['user_id']);

        $this->badgeService->manuallyAward($badge, $targetUser, $request->user());

        return response()->json([
            'message' => "Awarded '{$badge->name}' to {$targetUser->name}",
        ]);
    }

    /**
     * Revoke a badge (parent only).
     */
    public function revoke(Request $request, Badge $badge, string $userId): JsonResponse
    {
        if (!$request->user()->isParent()) {
            return response()->json(['message' => 'Only parents can revoke badges'], 403);
        }

        $family = $request->user()->currentFamily()->firstOrFail();
        $targetUser = $family->members()->findOrFail($userId);

        $this->badgeService->revokeBadge($badge, $targetUser);

        return response()->json([
            'message' => "Revoked '{$badge->name}' from {$targetUser->name}",
        ]);
    }

    /**
     * Record an easter egg discovery and award corresponding badge.
     */
    public function easterEgg(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'egg_key' => 'required|string|in:konami,seven_ate_nine,party_mode,mirror,matrix,disco',
        ]);

        $user = $request->user();
        $found = $user->easter_eggs_found ?? [];

        // Already found this one
        if (in_array($validated['egg_key'], $found)) {
            return response()->json(['already_found' => true, 'badges' => []]);
        }

        // Record the discovery
        $found[] = $validated['egg_key'];
        $user->easter_eggs_found = $found;
        $user->save();

        // Map egg keys to badge names
        $badgeNameMap = [
            'konami' => 'Code Breaker',
            'seven_ate_nine' => 'Number Cruncher',
            'party_mode' => 'Party Animal',
            'mirror' => 'Mirror Mirror',
            'matrix' => 'Red Pill',
            'disco' => 'Disco Inferno',
        ];

        $badgeName = $badgeNameMap[$validated['egg_key']];
        $badge = Badge::where('family_id', $user->family_id)
            ->where('name', $badgeName)
            ->first();

        // Auto-create easter egg badges for existing families that don't have them yet
        if (!$badge) {
            $this->ensureEasterEggBadgesExist($user->family_id, $user->id);
            $badge = Badge::where('family_id', $user->family_id)
                ->where('name', $badgeName)
                ->first();
        }

        $newBadges = [];

        if ($badge && !$user->badges()->where('badges.id', $badge->id)->exists()) {
            $this->badgeService->manuallyAward($badge, $user, $user);
            $newBadges[] = $badge;
        }

        // Check for Master Explorer (all 6 found)
        $masterBadges = $this->badgeService->checkAndAwardBadges($user);
        $newBadges = array_merge($newBadges, $masterBadges);

        return response()->json([
            'already_found' => false,
            'total_found' => count($found),
            'badges' => collect($newBadges)->map(fn($b) => [
                'id' => $b->id,
                'name' => $b->name,
                'description' => $b->description,
                'icon' => $b->icon,
                'color' => $b->color,
            ]),
        ]);
    }

    /**
     * Get current user's earned badges.
     */
    public function earned(Request $request): JsonResponse
    {
        $user = $request->user();

        $badges = $user->badges()
            ->orderByPivot('earned_at', 'desc')
            ->get()
            ->map(fn ($badge) => [
                'id' => $badge->id,
                'name' => $badge->name,
                'description' => $badge->description,
                'icon' => $badge->icon,
                'color' => $badge->color,
                'earned_at' => $badge->pivot->earned_at,
            ]);

        return response()->json([
            'badges' => $badges,
        ]);
    }

    /**
     * Award any easter egg badges the user has found but hasn't been awarded yet.
     * Handles cases where eggs were discovered before badges existed or API calls failed silently.
     */
    private function awardMissingEasterEggBadges(User $user): void
    {
        $found = $user->easter_eggs_found ?? [];
        if (empty($found)) {
            return;
        }

        $badgeNameMap = [
            'konami' => 'Code Breaker',
            'seven_ate_nine' => 'Number Cruncher',
            'party_mode' => 'Party Animal',
            'mirror' => 'Mirror Mirror',
            'matrix' => 'Red Pill',
            'disco' => 'Disco Inferno',
        ];

        foreach ($found as $eggKey) {
            $badgeName = $badgeNameMap[$eggKey] ?? null;
            if (!$badgeName) {
                continue;
            }

            $badge = Badge::where('family_id', $user->family_id)
                ->where('name', $badgeName)
                ->first();

            if ($badge && !$user->badges()->where('badges.id', $badge->id)->exists()) {
                $this->badgeService->manuallyAward($badge, $user, $user);
            }
        }
    }

    /**
     * Ensure easter egg badges exist for a family (auto-creates for existing families).
     */
    private function ensureEasterEggBadgesExist(string $familyId, string $createdBy): void
    {
        $easterEggBadges = [
            ['name' => 'Code Breaker', 'description' => 'Cracked the Konami Code', 'icon' => 'key', 'color' => '#059669'],
            ['name' => 'Number Cruncher', 'description' => 'Why was 6 afraid of 7?', 'icon' => 'hashtag', 'color' => '#f59e0b'],
            ['name' => 'Party Animal', 'description' => 'Started a legendary party', 'icon' => 'sun', 'color' => '#ec4899'],
            ['name' => 'Mirror Mirror', 'description' => 'Saw everything backwards', 'icon' => 'eye', 'color' => '#06b6d4'],
            ['name' => 'Red Pill', 'description' => 'Entered the digital rain', 'icon' => 'lightning', 'color' => '#22c55e'],
            ['name' => 'Disco Inferno', 'description' => 'Got the groove going', 'icon' => 'music-note', 'color' => '#a855f7'],
        ];

        foreach ($easterEggBadges as $i => $data) {
            Badge::firstOrCreate(
                ['family_id' => $familyId, 'name' => $data['name']],
                array_merge($data, [
                    'family_id' => $familyId,
                    'created_by' => $createdBy,
                    'trigger_type' => 'custom',
                    'trigger_threshold' => null,
                    'is_hidden' => true,
                    'is_active' => true,
                    'sort_order' => 20 + $i,
                ])
            );
        }

        // Master Explorer badge
        Badge::firstOrCreate(
            ['family_id' => $familyId, 'name' => 'Master Explorer'],
            [
                'family_id' => $familyId,
                'created_by' => $createdBy,
                'description' => 'Found every single easter egg!',
                'icon' => 'compass',
                'color' => '#d97706',
                'trigger_type' => 'easter_egg',
                'trigger_threshold' => 6,
                'is_hidden' => true,
                'is_active' => true,
                'sort_order' => 26,
            ]
        );
    }
}
