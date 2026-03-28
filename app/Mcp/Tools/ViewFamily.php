<?php

namespace App\Mcp\Tools;

use App\Mcp\Tools\Concerns\ScopesToFamily;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Name('view-family')]
#[Description('View family info and members. Actions: info (family details), members (list all members), member (single member by ID).')]
#[IsReadOnly]
class ViewFamily extends Tool
{
    use ScopesToFamily;

    public function schema($schema): array
    {
        return [
            'action' => $schema->string()->required()->enum(['info', 'members', 'member'])->description('Action to perform'),
            'member_id' => $schema->string()->description('User UUID (required for member action)'),
        ];
    }

    public function handle(Request $request): Response
    {
        $family = $this->family();
        $action = $request->get('action', 'info');

        return match ($action) {
            'info' => $this->info($family),
            'members' => $this->members($family),
            'member' => $this->member($family, $request->get('member_id')),
            default => Response::error("Unknown action: {$action}"),
        };
    }

    private function info($family): Response
    {
        $family->load('members');

        return Response::json([
            'family' => [
                'id' => $family->id,
                'name' => $family->name,
                'slug' => $family->slug,
                'invite_code' => $this->isParent() ? $family->invite_code : null,
                'member_count' => $family->members->count(),
                'members' => $family->members->map(fn ($m) => [
                    'id' => $m->id,
                    'name' => $m->name,
                    'role' => $m->family_role->value ?? $m->family_role,
                ])->toArray(),
            ],
        ]);
    }

    private function members($family): Response
    {
        $members = $family->members()->get();

        return Response::json([
            'members' => $members->map(fn ($m) => [
                'id' => $m->id,
                'name' => $m->name,
                'email' => $m->email,
                'role' => $m->family_role->value ?? $m->family_role,
                'avatar' => $m->avatar,
                'date_of_birth' => $m->date_of_birth?->format('Y-m-d'),
                'timezone' => $m->timezone,
                'points_bank' => $m->pointBank(),
            ])->toArray(),
        ]);
    }

    private function member($family, ?string $memberId): Response
    {
        if (!$memberId) {
            return Response::error('member_id is required for the member action.');
        }

        $member = $family->members()->find($memberId);

        if (!$member) {
            return Response::error('Family member not found.');
        }

        return Response::json([
            'member' => [
                'id' => $member->id,
                'name' => $member->name,
                'email' => $member->email,
                'role' => $member->family_role->value ?? $member->family_role,
                'avatar' => $member->avatar,
                'date_of_birth' => $member->date_of_birth?->format('Y-m-d'),
                'timezone' => $member->timezone,
                'points_bank' => $member->pointBank(),
            ],
        ]);
    }
}
