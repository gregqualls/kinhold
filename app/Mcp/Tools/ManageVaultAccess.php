<?php

namespace App\Mcp\Tools;

use App\Mcp\Tools\Concerns\ScopesToFamily;
use App\Models\VaultEntry;
use App\Models\VaultPermission;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;

#[Name('manage-vault-access')]
#[Description('Grant or revoke a family member\'s access to a vault entry. Actions: grant, revoke. Parent-only.')]
class ManageVaultAccess extends Tool
{
    use ScopesToFamily;

    public function schema($schema): array
    {
        return [
            'action' => $schema->string()->required()->enum(['grant', 'revoke'])->description('Action to perform'),
            'entry_id' => $schema->string()->required()->description('Vault entry UUID (required)'),
            'user_id' => $schema->string()->required()->description('Family member UUID to grant/revoke access (required)'),
            'permission_level' => $schema->string()->enum(['view', 'edit'])->description('Permission level (default: view, for grant action)'),
        ];
    }

    public function handle(Request $request): Response
    {
        return match ($request->get('action')) {
            'grant' => $this->grant($request),
            'revoke' => $this->revoke($request),
            default => Response::error("Unknown action: {$request->get('action')}"),
        };
    }

    private function grant(Request $request): Response
    {
        $entry = VaultEntry::where('family_id', $this->familyId())
            ->findOrFail($request->get('entry_id'));

        if ($denied = $this->authorize('managePermissions', $entry)) {
            return $denied;
        }

        $target = $this->family()->members()->findOrFail($request->get('user_id'));

        $level = $request->get('permission_level', 'view');

        $permission = VaultPermission::where('vault_entry_id', $entry->id)
            ->where('user_id', $target->id)
            ->first();

        if ($permission) {
            $permission->update(['permission_level' => $level]);
        } else {
            VaultPermission::create([
                'vault_entry_id' => $entry->id,
                'user_id' => $target->id,
                'permission_level' => $level,
            ]);
        }

        return Response::text("Granted {$level} access to \"{$entry->title}\" for {$target->name}.");
    }

    private function revoke(Request $request): Response
    {
        $entry = VaultEntry::where('family_id', $this->familyId())
            ->findOrFail($request->get('entry_id'));

        if ($denied = $this->authorize('managePermissions', $entry)) {
            return $denied;
        }

        $target = $this->family()->members()->findOrFail($request->get('user_id'));

        VaultPermission::where('vault_entry_id', $entry->id)
            ->where('user_id', $target->id)
            ->delete();

        return Response::text("Revoked access to \"{$entry->title}\" for {$target->name}.");
    }
}
