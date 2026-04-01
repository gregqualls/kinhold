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
#[Description('Manage the family vault — categories and encrypted entries. Actions: list_categories, create_category, update_category, delete_category, list (entries), get (single entry with decrypted data), create, update, delete. Write actions are parent-only. Children only see entries shared with them.')]
class ManageVault extends Tool
{
    use ScopesToFamily;

    public function schema($schema): array
    {
        return [
            'action' => $schema->string()->required()->enum(['list_categories', 'create_category', 'update_category', 'delete_category', 'list', 'get', 'create', 'update', 'delete'])->description('Action to perform'),
            'entry_id' => $schema->string()->description('Vault entry UUID (required for get/update/delete)'),
            'category_id' => $schema->string()->description('Category UUID (filter for list, required for create/update_category/delete_category)'),
            'title' => $schema->string()->description('Entry title (required for create) or category name (for create_category)'),
            'data' => $schema->object()->description('Data to store — use { body: "markdown text", sensitive_fields: { key: value } } format. Encrypted at rest.'),
            'notes' => $schema->string()->description('Optional notes'),
            'icon' => $schema->string()->description('Icon name for category (e.g., heart, lock, briefcase, book, shield, dollar-sign)'),
            'description' => $schema->string()->description('Category description'),
            'is_personal' => $schema->boolean()->description('Mark as personal entry (children can create/edit/delete their own personal entries)'),
        ];
    }

    public function handle(Request $request): Response
    {
        return match ($request->get('action')) {
            'list_categories' => $this->listCategories(),
            'create_category' => $this->createCategory($request),
            'update_category' => $this->updateCategory($request),
            'delete_category' => $this->deleteCategory($request),
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

    private function createCategory(Request $request): Response
    {
        if ($denied = $this->requireParent()) {
            return $denied;
        }

        $name = $request->get('title');
        if (! $name) {
            return Response::error('title is required to create a category.');
        }

        $slug = \Str::slug($name);
        $baseSlug = $slug;
        $counter = 1;
        while (VaultCategory::where('family_id', $this->familyId())->where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$counter++;
        }

        $category = VaultCategory::create([
            'family_id' => $this->familyId(),
            'name' => $name,
            'slug' => $slug,
            'icon' => $request->get('icon', 'lock'),
            'description' => $request->get('description'),
        ]);

        return Response::json([
            'message' => "Category \"{$category->name}\" created.",
            'category' => ['id' => $category->id, 'name' => $category->name, 'slug' => $category->slug],
        ]);
    }

    private function updateCategory(Request $request): Response
    {
        if ($denied = $this->requireParent()) {
            return $denied;
        }

        $categoryId = $request->get('category_id');
        if (! $categoryId) {
            return Response::error('category_id is required for update_category.');
        }

        $category = VaultCategory::where('family_id', $this->familyId())->findOrFail($categoryId);

        $updates = [];
        if ($request->get('title') !== null) {
            $updates['name'] = $request->get('title');
            $slug = \Str::slug($updates['name']);
            $baseSlug = $slug;
            $counter = 1;
            while (VaultCategory::where('family_id', $this->familyId())
                ->where('slug', $slug)
                ->where('id', '!=', $category->id)
                ->exists()) {
                $slug = $baseSlug.'-'.$counter++;
            }
            $updates['slug'] = $slug;
        }
        if ($request->get('icon') !== null) {
            $updates['icon'] = $request->get('icon');
        }
        if ($request->get('description') !== null) {
            $updates['description'] = $request->get('description');
        }

        $category->update($updates);

        return Response::json([
            'message' => "Category \"{$category->name}\" updated.",
            'category' => ['id' => $category->id, 'name' => $category->name, 'slug' => $category->slug],
        ]);
    }

    private function deleteCategory(Request $request): Response
    {
        if ($denied = $this->requireParent()) {
            return $denied;
        }

        $categoryId = $request->get('category_id');
        if (! $categoryId) {
            return Response::error('category_id is required for delete_category.');
        }

        $category = VaultCategory::where('family_id', $this->familyId())->findOrFail($categoryId);

        if ($category->entries()->count() > 0) {
            return Response::error("Cannot delete \"{$category->name}\" — it still has entries. Move or delete them first.");
        }

        $name = $category->name;
        $category->delete();

        return Response::text("Category \"{$name}\" deleted.");
    }

    private function listEntries(Request $request): Response
    {
        $user = $this->user();
        $query = VaultEntry::where('family_id', $this->familyId())
            ->with(['category', 'creator:id,name', 'permissions']);

        if ($categoryId = $request->get('category_id')) {
            $query->where('vault_category_id', $categoryId);
        }

        $entries = $query->orderByDesc('created_at')->get();

        // Children see personal + shared entries
        if (! $user->isParent()) {
            $entries = $entries->filter(
                fn ($e) => ($e->is_personal && $e->created_by === $user->id)
                    || $e->permissions->contains('user_id', $user->id)
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
        if (! $entryId) {
            return Response::error('entry_id is required for get.');
        }

        $entry = VaultEntry::where('family_id', $this->familyId())->findOrFail($entryId);

        if ($denied = $this->authorize('view', $entry)) {
            return $denied;
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
        $isPersonal = (bool) $request->get('is_personal', false);
        if ($denied = $this->authorize('create', [VaultEntry::class, $isPersonal])) {
            return $denied;
        }

        $title = $request->get('title');
        if (! $title) {
            return Response::error('title is required to create a vault entry.');
        }

        $categoryId = $request->get('category_id');
        if (! $categoryId) {
            return Response::error('category_id is required to create a vault entry.');
        }

        $data = $request->get('data');
        if (! $data) {
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
            'is_personal' => $isPersonal,
        ]);

        return Response::json([
            'message' => "Vault entry \"{$entry->title}\" created.".($isPersonal ? ' (personal)' : ''),
            'entry' => ['id' => $entry->id, 'title' => $entry->title],
        ]);
    }

    private function updateEntry(Request $request): Response
    {
        $entryId = $request->get('entry_id');
        if (! $entryId) {
            return Response::error('entry_id is required for update.');
        }

        $entry = VaultEntry::where('family_id', $this->familyId())->findOrFail($entryId);

        if ($denied = $this->authorize('update', $entry)) {
            return $denied;
        }

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
        $entryId = $request->get('entry_id');
        if (! $entryId) {
            return Response::error('entry_id is required for delete.');
        }

        $entry = VaultEntry::where('family_id', $this->familyId())->findOrFail($entryId);

        if ($denied = $this->authorize('delete', $entry)) {
            return $denied;
        }
        $title = $entry->title;
        $entry->delete();

        return Response::text("Vault entry \"{$title}\" deleted.");
    }
}
