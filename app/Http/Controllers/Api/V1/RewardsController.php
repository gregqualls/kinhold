<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Reward;
use App\Models\RewardBid;
use App\Models\RewardPurchase;
use App\Models\User;
use App\Services\AuctionService;
use App\Services\BadgeService;
use App\Services\PointsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RewardsController extends Controller
{
    public function __construct(
        private PointsService $pointsService,
        private BadgeService $badgeService,
        private AuctionService $auctionService,
    ) {}

    /**
     * List family rewards.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $family = $user->currentFamily()->firstOrFail();

        $query = Reward::where('family_id', $family->id)
            ->visibleTo($user)
            ->with(['activeBids', 'bids'])
            ->orderBy('sort_order')
            ->orderBy('point_cost');

        // Parents see all rewards (including inactive/expired) for management
        // Children only see purchasable rewards
        if (! $user->isParent()) {
            $query->purchasable();
        }

        $rewardModels = $query->get();

        // Batch-load visible_to names to avoid N+1
        $visibleToNames = [];
        if ($user->isParent()) {
            $allVisibleIds = $rewardModels
                ->pluck('visible_to')
                ->filter()
                ->flatten()
                ->unique()
                ->values();

            if ($allVisibleIds->isNotEmpty()) {
                $visibleToNames = User::whereIn('id', $allVisibleIds)->pluck('name', 'id');
            }
        }

        $rewards = $rewardModels->map(function (Reward $reward) use ($user, $visibleToNames) {
            $data = $reward->toArray();
            $data['remaining_stock'] = $reward->remainingStock();
            $data['is_expired'] = $reward->isExpired();
            $data['is_purchasable'] = $reward->isPurchasable();

            // Auction metadata (uses eager-loaded activeBids to avoid N+1)
            if ($reward->isAuction()) {
                $active = $reward->activeBids;
                /** @var RewardBid|null $highBid */
                $highBid = $active->sortByDesc('bid_amount')->first();
                /** @var RewardBid|null $myBid */
                $myBid = $active->firstWhere('user_id', $user->id);
                $data['bidding_open'] = $reward->isBiddingOpen();
                $data['highest_bid'] = $highBid?->bid_amount;
                $data['total_bids'] = $active->count();
                $data['my_bid'] = $myBid?->bid_amount;
                $data['is_resolved'] = $reward->bids->contains('is_winning', true);
            }

            // Resolve visible_to names for parents
            /** @var array<string>|null $visibleTo */
            $visibleTo = $reward->visible_to;
            if ($user->isParent() && is_array($visibleTo) && count($visibleTo) > 0) {
                $data['visible_to_names'] = collect($visibleTo)
                    ->map(fn ($id) => $visibleToNames[$id] ?? null)
                    ->filter()
                    ->values()
                    ->toArray();
            }

            return $data;
        });

        return response()->json([
            'rewards' => $rewards,
        ]);
    }

    /**
     * Create a reward (parent only).
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Reward::class);

        $family = $request->user()->currentFamily()->firstOrFail();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'point_cost' => 'required|integer|min:1',
            'icon' => 'nullable|string|max:50',
            'quantity' => 'nullable|integer|min:0',
            'expires_at' => 'nullable|date|after:now',
            'visibility' => 'sometimes|string|in:everyone,parent_only,child_only,specific',
            'visible_to' => 'nullable|array',
            'visible_to.*' => ['uuid', function (string $attribute, mixed $value, \Closure $fail) use ($family) {
                if (! User::where('family_id', $family->id)->where('id', $value)->exists()) {
                    $fail('The selected user does not belong to this family.');
                }
            }],
            'min_age' => 'nullable|integer|min:0|max:99',
            'max_age' => 'nullable|integer|min:0|max:99',
            'reward_type' => 'sometimes|string|in:standard,auction',
            'min_bid' => 'nullable|integer|min:1',
            'bid_start_at' => 'nullable|date',
            'bid_end_at' => 'nullable|date',
        ]);

        // Validate max_age >= min_age when both are set
        if (isset($validated['min_age'], $validated['max_age']) && $validated['max_age'] < $validated['min_age']) {
            return response()->json(['message' => 'max_age must be greater than or equal to min_age'], 422);
        }

        $reward = Reward::create([
            'family_id' => $family->id,
            'created_by' => $request->user()->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'point_cost' => $validated['point_cost'],
            'icon' => $validated['icon'] ?? null,
            'quantity' => $validated['quantity'] ?? null,
            'expires_at' => $validated['expires_at'] ?? null,
            'visibility' => $validated['visibility'] ?? 'everyone',
            'visible_to' => $validated['visible_to'] ?? null,
            'min_age' => $validated['min_age'] ?? null,
            'max_age' => $validated['max_age'] ?? null,
            'reward_type' => $validated['reward_type'] ?? 'standard',
            'min_bid' => $validated['min_bid'] ?? null,
            'bid_start_at' => $validated['bid_start_at'] ?? null,
            'bid_end_at' => $validated['bid_end_at'] ?? null,
        ]);

        return response()->json([
            'reward' => $reward,
        ], 201);
    }

    /**
     * Update a reward (parent only).
     */
    public function update(Request $request, Reward $reward): JsonResponse
    {
        abort_unless($reward->family_id === $request->user()->family_id, 404);
        $this->authorize('update', $reward);

        $familyId = $request->user()->family_id;

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'point_cost' => 'sometimes|integer|min:1',
            'icon' => 'nullable|string|max:50',
            'is_active' => 'sometimes|boolean',
            'sort_order' => 'sometimes|integer',
            'quantity' => 'nullable|integer|min:0',
            'expires_at' => 'nullable|date',
            'visibility' => 'sometimes|string|in:everyone,parent_only,child_only,specific',
            'visible_to' => 'nullable|array',
            'visible_to.*' => ['uuid', function (string $attribute, mixed $value, \Closure $fail) use ($familyId) {
                if (! User::where('family_id', $familyId)->where('id', $value)->exists()) {
                    $fail('The selected user does not belong to this family.');
                }
            }],
            'min_age' => 'nullable|integer|min:0|max:99',
            'max_age' => 'nullable|integer|min:0|max:99',
            'reward_type' => 'sometimes|string|in:standard,auction',
            'min_bid' => 'nullable|integer|min:1',
            'bid_start_at' => 'nullable|date',
            'bid_end_at' => 'nullable|date',
        ]);

        $reward->update($validated);

        return response()->json([
            'reward' => $reward,
        ]);
    }

    /**
     * Delete a reward (parent only).
     */
    public function destroy(Request $request, Reward $reward): JsonResponse
    {
        abort_unless($reward->family_id === $request->user()->family_id, 404);
        $this->authorize('delete', $reward);

        $reward->delete();

        return response()->json(null, 204);
    }

    /**
     * Purchase a reward.
     */
    public function purchase(Request $request, Reward $reward): JsonResponse
    {
        $user = $request->user();
        abort_unless($reward->family_id === $user->family_id, 404);
        $this->authorize('purchase', $reward);

        try {
            $result = $this->pointsService->redeemReward($reward, $user);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        // Check for badges
        $newBadges = $this->badgeService->checkAndAwardBadges($user);

        $response = [
            'message' => "Purchased: {$reward->title}",
            'purchase' => $result['purchase'],
            'new_bank' => $user->pointBank(),
            'remaining_stock' => $result['reward']->remainingStock(),
        ];

        if (! empty($newBadges)) {
            $response['badges_earned'] = collect($newBadges)->map(fn ($b) => [
                'id' => $b->id,
                'name' => $b->name,
                'icon' => $b->icon,
                'color' => $b->color,
            ]);
        }

        return response()->json($response, 201);
    }

    /**
     * List purchases.
     */
    public function purchases(Request $request): JsonResponse
    {
        $family = $request->user()->currentFamily()->firstOrFail();

        $purchases = RewardPurchase::where('family_id', $family->id)
            ->with(['reward:id,title,icon,point_cost', 'user:id,name,avatar'])
            ->orderByDesc('purchased_at')
            ->paginate(30);

        return response()->json([
            'purchases' => $purchases->items(),
            'pagination' => [
                'current_page' => $purchases->currentPage(),
                'last_page' => $purchases->lastPage(),
                'total' => $purchases->total(),
            ],
        ]);
    }

    /**
     * Place or update a bid on an auction reward.
     */
    public function bid(Request $request, Reward $reward): JsonResponse
    {
        $user = $request->user();
        abort_unless($reward->family_id === $user->family_id, 404);
        $this->authorize('bid', $reward);

        $validated = $request->validate([
            'bid_amount' => 'required|integer|min:1',
        ]);

        try {
            $bid = $this->auctionService->placeBid($reward, $user, $validated['bid_amount']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'message' => "Bid of {$bid->bid_amount} points placed on {$reward->title}",
            'bid' => [
                'id' => $bid->id,
                'bid_amount' => $bid->bid_amount,
                'held_points' => $bid->held_points,
            ],
            'available_points' => $user->availablePoints(),
        ], 201);
    }

    /**
     * List bids for an auction reward.
     */
    public function bids(Request $request, Reward $reward): JsonResponse
    {
        $user = $request->user();
        abort_unless($reward->family_id === $user->family_id, 404);
        abort_unless($reward->isVisibleTo($user), 404);

        $bids = $reward->bids()
            ->with('user:id,name,avatar')
            ->orderByDesc('bid_amount')
            ->orderBy('created_at')
            ->get();

        // Children see own bid + anonymous highest; parents see all
        if ($user->isParent()) {
            $data = $bids->map(fn ($b) => [
                'id' => $b->id,
                'user' => $b->user ? ['id' => $b->user->id, 'name' => $b->user->name] : null,
                'bid_amount' => $b->bid_amount,
                'is_winning' => $b->is_winning,
                'created_at' => $b->created_at->toIso8601String(),
            ]);
        } else {
            $highestBid = $bids->first();
            $myBid = $bids->firstWhere('user_id', $user->id);

            $data = collect();
            if ($highestBid) {
                $data->push([
                    'bid_amount' => $highestBid->bid_amount,
                    'is_highest' => true,
                    'is_mine' => (string) $highestBid->user_id === (string) $user->id,
                ]);
            }
            if ($myBid && $myBid->id !== $highestBid?->id) {
                $data->push([
                    'bid_amount' => $myBid->bid_amount,
                    'is_highest' => false,
                    'is_mine' => true,
                ]);
            }
        }

        return response()->json([
            'bids' => $data,
            'total_bids' => $bids->count(),
            'bidding_open' => $reward->isBiddingOpen(),
        ]);
    }

    /**
     * Close a parent-called auction (parent only).
     */
    public function closeAuction(Request $request, Reward $reward): JsonResponse
    {
        $user = $request->user();
        abort_unless($reward->family_id === $user->family_id, 404);
        $this->authorize('closeAuction', $reward);

        try {
            $winner = $this->auctionService->closeAuction($reward);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'message' => $winner
                ? "Auction closed! {$winner->user->name} won with {$winner->bid_amount} points."
                : 'Auction closed with no bids.',
            'winner' => $winner ? [
                'user_name' => $winner->user->name,
                'bid_amount' => $winner->bid_amount,
            ] : null,
        ]);
    }

    /**
     * Cancel an auction (parent only) — releases all holds.
     */
    public function cancelAuction(Request $request, Reward $reward): JsonResponse
    {
        $user = $request->user();
        abort_unless($reward->family_id === $user->family_id, 404);
        $this->authorize('cancelAuction', $reward);

        try {
            $this->auctionService->cancelAuction($reward);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'message' => "Auction for {$reward->title} cancelled. All held points released.",
        ]);
    }
}
