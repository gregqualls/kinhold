<?php

namespace App\Mcp\Tools;

use App\Mcp\Tools\Concerns\ScopesToFamily;
use App\Services\PointsService;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;

#[Name('manage-points')]
#[Description('Give kudos or deduct points. Actions: kudos (any member, +1 point), deduct (parent only, negative points).')]
class ManagePoints extends Tool
{
    use ScopesToFamily;

    public function schema($schema): array
    {
        return [
            'action' => $schema->string()->required()->enum(['kudos', 'deduct'])->description('Action to perform'),
            'user_id' => $schema->string()->required()->description('Target family member UUID (required)'),
            'points' => $schema->integer()->description('Points to deduct (required for deduct action)'),
            'reason' => $schema->string()->required()->description('Reason for kudos or deduction (required)'),
        ];
    }

    public function handle(Request $request): Response
    {
        return match ($request->get('action')) {
            'kudos' => $this->giveKudos($request),
            'deduct' => $this->deductPoints($request),
            default => Response::error("Unknown action: {$request->get('action')}"),
        };
    }

    private function giveKudos(Request $request): Response
    {
        $user = $this->user();
        $family = $this->family();
        $target = $family->members()->findOrFail($request->get('user_id'));

        $pointsService = app(PointsService::class);

        try {
            $transaction = $pointsService->giveKudos($user, $target, $family, $request->get('reason'));
        } catch (\Exception $e) {
            return Response::error($e->getMessage());
        }

        return Response::json([
            'message' => "Kudos given to {$target->name}! +1 point.",
            'transaction_id' => $transaction->id,
        ]);
    }

    private function deductPoints(Request $request): Response
    {
        if ($denied = $this->requireParent()) {
            return $denied;
        }

        $points = $request->get('points');
        if (! $points || $points < 1) {
            return Response::error('points must be a positive integer for deduction.');
        }

        $user = $this->user();
        $target = $this->family()->members()->findOrFail($request->get('user_id'));

        $pointsService = app(PointsService::class);
        $transaction = $pointsService->deductPoints($user, $target, $points, $request->get('reason'));

        return Response::json([
            'message' => "Deducted {$points} points from {$target->name}.",
            'transaction_id' => $transaction->id,
        ]);
    }
}
