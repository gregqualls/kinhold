<?php

namespace App\Mcp\Tools;

use App\Mcp\Tools\Concerns\ScopesToFamily;
use App\Models\VaultCategory;
use App\Models\VaultEntry;
use App\Services\VaultEncryptionService;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;

#[Name('manage-vault')]
#[Description('Manage the family vault — categories and encrypted entries. Actions: list_categories, list (entries), get (single entry with decrypted data), create, update, delete. Write actions are parent-only. Children only see entries shared with them.')]
class ManageVault extends Tool
{
    use ScopesToFamily;

    public function schema($schema): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'action' => [
                    'type' => 'string',
                    'enum' => ['list_categories', 'list', 'get', 'create', 'update', 'delete'],
                    'description' => 'Action to perform',
                ],
                'entry_id' => [
                    'type' => 'string',
                    'description' => 'Vault entry UUID (required for get/update/delete)',
                ],
                'category_id' => [
                    'type' => 'string',
                    'description' => 'Category UUID (filter for list, required for create)',
                ],
                'title' => [
                    'type' => 'string',
                    'description' => 'Entry title (required for create)',
                ],
                'data' => [
                    'type' => 'object',
                    'description' => 'Key-value pairs to store (required for create, encrypted at rest)',
                ],
                'notes' => [
                    'type' => 'string',
                    'description' => 'Optional notes',
                ],
            ],
            'required' => ['action'],
        ];
    }

    public function handle(Request $request): Response
    {
        return match ($request->get('action')) {
            'list_categories' => $this->listCategories(),
            'list' => $this->listEntries($request),
            'get' => $this->getEntry($request),
            'create' => $this->createEntry($request),
            'update' => $this->updateEntry($request),
            'delete' => $this->deleteEntry($request),
            default => Response::error("Unknown action: {$request->get('action')}"),
        };
    }

    private function listCategories(): Response
    {
        $categories = VaultCategory::where('family_id', $this->familyId())
            ->withCount('entries')
            ->orderBy('name')
            ->get();

        return Response::json([
            'categories' => $categories->map(fn ($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'slug' => $c->slug,
                'icon' => $c->icon,
                'entry_count' => $c->entries_count,
            ])->toArray(),
        ]);
    }

    private function listEntries(Request $request): Response
    {
        $user = $this->user();
        $query = VaultEntry::where('family_id', $this->familyId())
            ->with(['category', 'creator:id,name']);

        if ($categoryId = $request->get('category_id')) {
            $query->where('vault_category_id', $categoryId);
        }

        $entries = $query->orderByDesc('created_at')->get();

        // Children only see entries shared with them
        if (!$user->isParent()) {
            $entries = $entries->filter(
                fn ($e) => $e->permissions()->where('user_id', $user->id)->exists()
            );
        }

        return Response::json([
            'entries' => $entries->map(fn ($e) => [
                'id' => $e->id,
                'title' => $e->title,
                'category' => $e->category?->name,
                'created_by' => $e->creator?->name,
                'notes' => $e->notes,
                'created_at' => $e->created_at->toIso8601String(),
            ])->values()->toArray(),
        ]);
    }

    private function getEntry(Request $request): Response
    {
        $entryId = $request->get('entry_id');
        if (!$entryId) {
            return Response::error('entry_id is required for get.');
        }

        $entry = VaultEntry::where('family_id', $this->familyId())->findOrFail($entryId);

        // Permission check: children need explicit access
        $user = $this->user();
        if (!$user->isParent() && !$entry->permissions()->where('user_id', $user->id)->exists()) {
            return Response::error('You do not have access to this entry.');
        }

        $encryptionService = app(VaultEncryptionService::class);
        $decryptedData = $encryptionService->decrypt($entry->encrypted_data);

        return Response::json([
            'entry' => [
                'id' => $entry->id,
                'title' => $entry->title,
                'category' => $entry->category?->name,
                'data' => $decryptedData,
                'notes' => $entry->notes,
                'created_by' => $entry->creator?->name,
                'created_at' => $entry->created_at->toIso8601String(),
            ],
        ]);
    }

    private function createEntry(Request $request): Response
    {
        if ($denied = $this->requireParent()) {
            return $denied;
        }

        $title = $request->get('title');
        if (!$title) {
            return Response::error('title is required to create a vault entry.');
        }

        $categoryId = $request->get('category_id');
        if (!$categoryId) {
            return Response::error('category_id is required to create a vault entry.');
        }

        $data = $request->get('data');
        if (!$data) {
            return Response::error('data is required to create a vault entry.');
        }

        VaultCategory::where('family_id', $this->familyId())->findOrFail($categoryId);

        $encryptionService = app(VaultEncryptionService::class);
        $encryptedData = $encryptionService->encrypt($data);

        $entry = VaultEntry::create([
            'family_id' => $this->familyId(),
            'vault_category_id' => $categoryId,
            'created_by' => $this->user()->id,
            'title' => $title,
            'encrypted_data' => $encryptedData,
            'notes' => $request->get('notes'),
        ]);

        return Response::json([
            'message' => "Vault entry \"{$entry->title}\" created.",
            'entry' => ['id' => $entry->id, 'title' => $entry->title],
        ]);
    }

    private function updateEntry(Request $request): Response
    {
        if ($denied = $this->requireParent()) {
            return $denied;
        }

        $entryId = $request->get('entry_id');
        if (!$entryId) {
            return Response::error('entry_id is required for update.');
        }

        $entry = VaultEntry::where('family_id', $this->familyId())->findOrFail($entryId);

        $updates = [];
        if ($request->get('title') !== null) {
            $updates['title'] = $request->get('title');
        }
        if ($request->get('notes') !== null) {
            $updates['notes'] = $request->get('notes');
        }
        if ($request->get('data') !== null) {
            $encryptionService = app(VaultEncryptionService::class);
            $updates['encrypted_data'] = $encryptionService->encrypt($request->get('data'));
        }
        if ($request->get('category_id') !== null) {
            VaultCategory::where('family_id', $this->familyId())->findOrFail($request->get('category_id'));
            $updates['vault_category_id'] = $request->get('category_id');
        }

        $entry->update($updates);

        return Response::json([
            'message' => "Vault entry \"{$entry->title}\" updated.",
            'entry' => ['id' => $entry->id, 'title' => $entry->title],
        ]);
    }

    private function deleteEntry(Request $request): Response
    {
        if ($denied = $this->requireParent()) {
            return $denied;
        }

        $entryId = $request->get('entry_id');
        if (!$entryId) {
            return Response::error('entry_id is required for delete.');
        }

        $entry = VaultEntry::where('family_id', $this->familyId())->findOrFail($entryId);
        $title = $entry->title;
        $entry->delete();

        return Response::text("Vault entry \"{$title}\" deleted.");
    }
}
