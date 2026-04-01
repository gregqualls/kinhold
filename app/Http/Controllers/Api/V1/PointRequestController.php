<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\PointRequestStatus;
use App\Enums\PointTransactionType;
use App\Http\Controllers\Controller;
use App\Models\PointRequest;
use App\Models\PointTransaction;
use App\Services\BadgeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PointRequestController extends Controller
{
    public function __construct(
        private BadgeService $badgeService,
    ) {}

    /**
     * List point requests.
     * Parents see all pending requests for the family.
     * Children see their own requests.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $family = $user->currentFamily()->firstOrFail();

        $query = PointRequest::where('family_id', $family->id)
            ->with(['user:id,name,avatar', 'reviewer:id,name,avatar']);

        // Children only see their own requests
        if (! $user->isParent()) {
            $query->where('user_id', $user->id);
        }

        // Filter by status if provided
        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        } else {
            // Default: show pending first for parents
            $query->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END");
        }

        $requests = $query->orderByDesc('created_at')->get();

        return response()->json([
            'requests' => $requests,
        ]);
    }

    /**
     * Create a new point request (children request points from parents).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'points' => 'required|integer|min:1|max:1000',
            'reason' => 'required|string|max:255',
        ]);

        $user = $request->user();
        $family = $user->currentFamily()->firstOrFail();

        $pointRequest = PointRequest::create([
            'family_id' => $family->id,
            'user_id' => $user->id,
            'points' => $validated['points'],
            'reason' => $validated['reason'],
            'status' => PointRequestStatus::Pending,
        ]);

        $pointRequest->load(['user:id,name,avatar']);

        return response()->json([
            'request' => $pointRequest,
            'message' => 'Point request submitted for approval',
        ], 201);
    }

    /**
     * Approve a point request (parent only).
     */
    public function approve(Request $request, PointRequest $pointRequest): JsonResponse
    {
        $user = $request->user();

        if (! $user->isParent()) {
            return response()->json(['message' => 'Only parents can approve point requests'], 403);
        }

        // Verify same family
        $family = $user->currentFamily()->firstOrFail();
        if ($pointRequest->family_id !== $family->id) {
            return response()->json(['message' => 'Request not found'], 404);
        }

        if (! $pointRequest->isPending()) {
            return response()->json(['message' => 'This request has already been reviewed'], 422);
        }

        $pointRequest->update([
            'status' => PointRequestStatus::Approved,
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
        ]);

        // Award the points
        $transaction = PointTransaction::create([
            'family_id' => $pointRequest->family_id,
            'user_id' => $pointRequest->user_id,
            'type' => PointTransactionType::PointRequest,
            'points' => $pointRequest->points,
            'description' => "Approved request: {$pointRequest->reason}",
            'source_type' => PointRequest::class,
            'source_id' => $pointRequest->id,
            'awarded_by' => $user->id,
        ]);

        // Check for badges on the requesting user
        $requestingUser = $pointRequest->user;
        $newBadges = $this->badgeService->checkAndAwardBadges($requestingUser);

        $pointRequest->load(['user:id,name,avatar', 'reviewer:id,name,avatar']);

        $response = [
            'request' => $pointRequest,
            'transaction' => $transaction,
            'message' => "Approved {$pointRequest->points} points for {$requestingUser->name}",
        ];

        if (! empty($newBadges)) {
            $response['badges_earned'] = collect($newBadges)->map(fn ($b) => [
                'id' => $b->id,
                'name' => $b->name,
                'description' => $b->description,
                'icon' => $b->icon,
                'color' => $b->color,
                'user_id' => $requestingUser->id,
            ]);
        }

        return response()->json($response, 200);
    }

    /**
     * Deny a point request (parent only).
     */
    public function deny(Request $request, PointRequest $pointRequest): JsonResponse
    {
        $user = $request->user();

        if (! $user->isParent()) {
            return response()->json(['message' => 'Only parents can deny point requests'], 403);
        }

        // Verify same family
        $family = $user->currentFamily()->firstOrFail();
        if ($pointRequest->family_id !== $family->id) {
            return response()->json(['message' => 'Request not found'], 404);
        }

        if (! $pointRequest->isPending()) {
            return response()->json(['message' => 'This request has already been reviewed'], 422);
        }

        $pointRequest->update([
            'status' => PointRequestStatus::Denied,
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
        ]);

        $pointRequest->load(['user:id,name,avatar', 'reviewer:id,name,avatar']);

        return response()->json([
            'request' => $pointRequest,
            'message' => "Denied point request from {$pointRequest->user->name}",
        ], 200);
    }
}
