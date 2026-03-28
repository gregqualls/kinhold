<?php

namespace App\Mcp\Tools;

use App\Enums\PointRequestStatus;
use App\Enums\PointTransactionType;
use App\Mcp\Tools\Concerns\ScopesToFamily;
use App\Models\PointRequest;
use App\Models\PointTransaction;
use App\Services\BadgeService;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;

#[Name('manage-point-requests')]
#[Description('List, approve, or deny point requests. Children can request points; parents approve or deny. Actions: list, approve, deny.')]
class ManagePointRequests extends Tool
{
    use ScopesToFamily;

    public function schema($schema): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'action' => [
                    'type' => 'string',
                    'enum' => ['list', 'approve', 'deny'],
                    'description' => 'Action to perform',
                ],
                'request_id' => [
                    'type' => 'string',
                    'description' => 'Point request UUID (required for approve/deny)',
                ],
                'status' => [
                    'type' => 'string',
                    'enum' => ['pending', 'approved', 'denied'],
                    'description' => 'Filter by status (for list action)',
                ],
            ],
            'required' => ['action'],
        ];
    }

    public function handle(Request $request): Response
    {
        return match ($request->get('action')) {
            'list' => $this->listRequests($request),
            'approve' => $this->approveRequest($request),
            'deny' => $this->denyRequest($request),
            default => Response::error("Unknown action: {$request->get('action')}"),
        };
    }

    private function listRequests(Request $request): Response
    {
        $user = $this->user();
        $query = PointRequest::where('family_id', $this->familyId())
            ->with(['user:id,name', 'reviewer:id,name']);

        if (!$user->isParent()) {
            $query->where('user_id', $user->id);
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        } else {
            $query->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END");
        }

        $requests = $query->orderByDesc('created_at')->limit(50)->get();

        return Response::json([
            'requests' => $requests->map(fn ($r) => [
                'id' => $r->id,
                'user' => $r->user?->name,
                'points' => $r->points,
                'reason' => $r->reason,
                'status' => $r->status->value,
                'reviewer' => $r->reviewer?->name,
                'reviewed_at' => $r->reviewed_at?->toIso8601String(),
                'created_at' => $r->created_at->toIso8601String(),
            ])->toArray(),
        ]);
    }

    private function approveRequest(Request $request): Response
    {
        if ($denied = $this->requireParent()) {
            return $denied;
        }

        $requestId = $request->get('request_id');
        if (!$requestId) {
            return Response::error('request_id is required for approve.');
        }

        $pointRequest = PointRequest::where('family_id', $this->familyId())->findOrFail($requestId);

        if (!$pointRequest->isPending()) {
            return Response::error('This request has already been reviewed.');
        }

        $user = $this->user();

        $pointRequest->update([
            'status' => PointRequestStatus::Approved,
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
        ]);

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

        $requestingUser = $pointRequest->user;
        $badgeService = app(BadgeService::class);
        $newBadges = $badgeService->checkAndAwardBadges($requestingUser);

        $result = [
            'message' => "Approved {$pointRequest->points} points for {$requestingUser->name}.",
            'transaction_id' => $transaction->id,
        ];

        if (!empty($newBadges)) {
            $result['badges_earned'] = collect($newBadges)->map(fn ($b) => $b->name)->toArray();
        }

        return Response::json($result);
    }

    private function denyRequest(Request $request): Response
    {
        if ($denied = $this->requireParent()) {
            return $denied;
        }

        $requestId = $request->get('request_id');
        if (!$requestId) {
            return Response::error('request_id is required for deny.');
        }

        $pointRequest = PointRequest::where('family_id', $this->familyId())->findOrFail($requestId);

        if (!$pointRequest->isPending()) {
            return Response::error('This request has already been reviewed.');
        }

        $pointRequest->update([
            'status' => PointRequestStatus::Denied,
            'reviewed_by' => $this->user()->id,
            'reviewed_at' => now(),
        ]);

        return Response::text("Denied point request from {$pointRequest->user->name}.");
    }
}
