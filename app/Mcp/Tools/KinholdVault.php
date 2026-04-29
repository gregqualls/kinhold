<?php

namespace App\Mcp\Tools;

use App\Mcp\Tools\Concerns\MergesUpdates;
use App\Mcp\Tools\Concerns\RequiresModule;
use App\Mcp\Tools\Concerns\ScopesToFamily;
use App\Models\VaultCategory;
use App\Models\VaultEntry;
use App\Models\VaultPermission;
use App\Services\VaultEncryptionService;
use Illuminate\Support\Str;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;

#[Name('kinhold-vault')]
#[Description(<<<'DESC'
Encrypted family vault — categories, entries, per-user access, and guided setup playbooks.

Categories:
  category_list — Categories with entry counts.
  category_create (title*, icon?, description?) — Parent only.
  category_update (category_id*, title?, icon?, description?) — Parent only.
  category_delete (category_id*) — Parent only. Must be empty.

Entries (encrypted at rest):
  entry_list (category_id?) — Children see only personal-they-own + entries shared with them.
  entry_get (entry_id*) — Returns decrypted data. Subject to permissions.
  entry_create (title*, category_id*, data*, notes?, is_personal?) — data: { body: "markdown", sensitive_fields: { ... } }.
  entry_update (entry_id*, [title|notes|data|category_id]) — Subject to policy.
  entry_delete (entry_id*) — Subject to policy.

Per-user access (parent only):
  access_grant (entry_id*, user_id*, permission_level?) — Levels: view (default), edit.
  access_revoke (entry_id*, user_id*).

Setup playbooks (read-only, no auth needed):
  playbook_list (tag?) — Available guided workflows (house manual, medical, vehicles, etc.).
  playbook_get (slug*) — Full instructions for a playbook.
DESC)]
class KinholdVault extends Tool
{
    use MergesUpdates, RequiresModule, ScopesToFamily;

    public const MODULE = 'vault';

    public function schema($schema): array
    {
        return [
            'action' => $schema->string()->required()->enum([
                'category_list', 'category_create', 'category_update', 'category_delete',
                'entry_list', 'entry_get', 'entry_create', 'entry_update', 'entry_delete',
                'access_grant', 'access_revoke',
                'playbook_list', 'playbook_get',
            ])->description('Action to perform'),
            'entry_id' => $schema->string()->description('Vault entry UUID (required for entry_get/entry_update/entry_delete/access_*)'),
            'category_id' => $schema->string()->description('Category UUID (filter for entry_list, required for category_update/category_delete/entry_create)'),
            'user_id' => $schema->string()->description('Family member UUID (required for access_*)'),
            'title' => $schema->string()->description('Entry title (entry_create) or category name (category_create/category_update)'),
            'data' => $schema->object()->description('Entry data — { body: "markdown", sensitive_fields: { key: value } }. Encrypted at rest. Required for entry_create.'),
            'notes' => $schema->string()->description('Optional notes on entry'),
            'icon' => $schema->string()->description('Category icon name (heart, lock, briefcase, book, shield, dollar-sign)'),
            'description' => $schema->string()->description('Category description'),
            'is_personal' => $schema->boolean()->description('Personal entry — children can manage their own personal entries'),
            'permission_level' => $schema->string()->enum(['view', 'edit'])->description('Access level for access_grant (default: view)'),
            'slug' => $schema->string()->description('Playbook slug (required for playbook_get)'),
            'tag' => $schema->string()->description('Optional tag filter for playbook_list'),
        ];
    }

    public function handle(Request $request): Response
    {
        return match ($request->get('action')) {
            'category_list' => $this->categoryList(),
            'category_create' => $this->categoryCreate($request),
            'category_update' => $this->categoryUpdate($request),
            'category_delete' => $this->categoryDelete($request),
            'entry_list' => $this->entryList($request),
            'entry_get' => $this->entryGet($request),
            'entry_create' => $this->entryCreate($request),
            'entry_update' => $this->entryUpdate($request),
            'entry_delete' => $this->entryDelete($request),
            'access_grant' => $this->accessGrant($request),
            'access_revoke' => $this->accessRevoke($request),
            'playbook_list' => $this->playbookList($request),
            'playbook_get' => $this->playbookGet($request),
            default => Response::error("Unknown action: {$request->get('action')}"),
        };
    }

    private function categoryList(): Response
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

    private function categoryCreate(Request $request): Response
    {
        if ($denied = $this->requireParent()) {
            return $denied;
        }

        $name = $request->get('title');
        if (! $name) {
            return Response::error('title is required to create a category.');
        }

        $slug = Str::slug($name);
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

    private function categoryUpdate(Request $request): Response
    {
        if ($denied = $this->requireParent()) {
            return $denied;
        }

        $categoryId = $request->get('category_id');
        if (! $categoryId) {
            return Response::error('category_id is required for category_update.');
        }

        $category = VaultCategory::where('family_id', $this->familyId())->findOrFail($categoryId);

        $updates = [];
        if ($request->get('title') !== null) {
            $updates['name'] = $request->get('title');
            $slug = Str::slug($updates['name']);
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

    private function categoryDelete(Request $request): Response
    {
        if ($denied = $this->requireParent()) {
            return $denied;
        }

        $categoryId = $request->get('category_id');
        if (! $categoryId) {
            return Response::error('category_id is required for category_delete.');
        }

        $category = VaultCategory::where('family_id', $this->familyId())->findOrFail($categoryId);

        if ($category->entries()->count() > 0) {
            return Response::error("Cannot delete \"{$category->name}\" — it still has entries. Move or delete them first.");
        }

        $name = $category->name;
        $category->delete();

        return Response::text("Category \"{$name}\" deleted.");
    }

    private function entryList(Request $request): Response
    {
        $user = $this->user();
        $query = VaultEntry::where('family_id', $this->familyId())
            ->with(['category', 'creator:id,name', 'permissions']);

        if ($categoryId = $request->get('category_id')) {
            $query->where('vault_category_id', $categoryId);
        }

        $entries = $query->orderByDesc('created_at')->get();

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

    private function entryGet(Request $request): Response
    {
        $entryId = $request->get('entry_id');
        if (! $entryId) {
            return Response::error('entry_id is required for entry_get.');
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

    private function entryCreate(Request $request): Response
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

        $category = VaultCategory::where('family_id', $this->familyId())->findOrFail($categoryId);

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
            'message' => "Vault entry \"{$entry->title}\" created in {$category->name}.".($isPersonal ? ' (personal)' : ''),
            'entry' => ['id' => $entry->id, 'title' => $entry->title, 'category' => $category->name],
        ]);
    }

    private function entryUpdate(Request $request): Response
    {
        $entryId = $request->get('entry_id');
        if (! $entryId) {
            return Response::error('entry_id is required for entry_update.');
        }

        $entry = VaultEntry::where('family_id', $this->familyId())->findOrFail($entryId);

        if ($denied = $this->authorize('update', $entry)) {
            return $denied;
        }

        // Title is required; notes is nullable text where "" should clear.
        $updates = $this->mergeUpdates(
            $request,
            simpleFields: ['title'],
            nullableFields: ['notes'],
        );

        $input = $request->all();

        // data → encrypted_data, runs through the encryption service.
        if (array_key_exists('data', $input) && $input['data'] !== null) {
            $encryptionService = app(VaultEncryptionService::class);
            $updates['encrypted_data'] = $encryptionService->encrypt($input['data']);
        }

        // category_id → vault_category_id (column rename) + family-scope check.
        if (array_key_exists('category_id', $input) && $input['category_id'] !== null && $input['category_id'] !== '') {
            VaultCategory::where('family_id', $this->familyId())->findOrFail($input['category_id']);
            $updates['vault_category_id'] = $input['category_id'];
        }

        $entry->update($updates);

        return Response::json([
            'message' => "Vault entry \"{$entry->title}\" updated.",
            'entry' => ['id' => $entry->id, 'title' => $entry->title],
        ]);
    }

    private function entryDelete(Request $request): Response
    {
        $entryId = $request->get('entry_id');
        if (! $entryId) {
            return Response::error('entry_id is required for entry_delete.');
        }

        $entry = VaultEntry::where('family_id', $this->familyId())->findOrFail($entryId);

        if ($denied = $this->authorize('delete', $entry)) {
            return $denied;
        }
        $title = $entry->title;
        $entry->delete();

        return Response::text("Vault entry \"{$title}\" deleted.");
    }

    private function accessGrant(Request $request): Response
    {
        $entryId = $request->get('entry_id');
        $userId = $request->get('user_id');
        if (! $entryId || ! $userId) {
            return Response::error('entry_id and user_id are required for access_grant.');
        }

        $entry = VaultEntry::where('family_id', $this->familyId())->findOrFail($entryId);

        if ($denied = $this->authorize('managePermissions', $entry)) {
            return $denied;
        }

        $target = $this->family()->members()->findOrFail($userId);
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

    private function accessRevoke(Request $request): Response
    {
        $entryId = $request->get('entry_id');
        $userId = $request->get('user_id');
        if (! $entryId || ! $userId) {
            return Response::error('entry_id and user_id are required for access_revoke.');
        }

        $entry = VaultEntry::where('family_id', $this->familyId())->findOrFail($entryId);

        if ($denied = $this->authorize('managePermissions', $entry)) {
            return $denied;
        }

        $target = $this->family()->members()->findOrFail($userId);

        VaultPermission::where('vault_entry_id', $entry->id)
            ->where('user_id', $target->id)
            ->delete();

        return Response::text("Revoked access to \"{$entry->title}\" for {$target->name}.");
    }

    private function playbookList(Request $request): Response
    {
        $playbookDir = resource_path('playbooks/vault');

        if (! is_dir($playbookDir)) {
            return Response::json(['playbooks' => [], 'message' => 'No playbooks directory found.']);
        }

        $files = glob($playbookDir.'/*.md');
        $tagFilter = $request->get('tag');

        $playbooks = [];
        foreach ($files as $file) {
            $parsed = $this->parsePlaybookFrontmatter($file);
            if (! $parsed) {
                continue;
            }

            if ($tagFilter && ! in_array(strtolower($tagFilter), array_map('strtolower', $parsed['tags'] ?? []), true)) {
                continue;
            }

            $playbooks[] = [
                'slug' => pathinfo($file, PATHINFO_FILENAME),
                'name' => $parsed['name'],
                'description' => $parsed['description'],
                'category' => $parsed['category'] ?? null,
                'icon' => $parsed['icon'] ?? null,
                'tags' => $parsed['tags'] ?? [],
            ];
        }

        return Response::json([
            'playbooks' => $playbooks,
            'count' => count($playbooks),
            'usage' => 'Use playbook_get with a slug to retrieve full instructions.',
        ]);
    }

    private function playbookGet(Request $request): Response
    {
        $slug = $request->get('slug');
        if (! $slug) {
            return Response::error('slug is required for playbook_get.');
        }

        $slug = preg_replace('/[^a-zA-Z0-9\-_]/', '', $slug);
        $file = resource_path("playbooks/vault/{$slug}.md");

        if (! file_exists($file)) {
            return Response::error("Playbook \"{$slug}\" not found. Use playbook_list to see available playbooks.");
        }

        $content = file_get_contents($file);
        $content = preg_replace('/^---\s*\n.*?\n---\s*\n/s', '', $content);

        return Response::text(trim($content));
    }

    private function parsePlaybookFrontmatter(string $file): ?array
    {
        $content = file_get_contents($file);
        if (! $content) {
            return null;
        }

        if (! preg_match('/^---\s*\n(.*?)\n---/s', $content, $matches)) {
            return null;
        }

        $frontmatter = [];
        foreach (explode("\n", $matches[1]) as $line) {
            if (preg_match('/^(\w+):\s*(.+)$/', trim($line), $kv)) {
                $value = trim($kv[2]);
                if (str_starts_with($value, '[') && str_ends_with($value, ']')) {
                    $value = array_map('trim', explode(',', trim($value, '[]')));
                }
                $frontmatter[$kv[1]] = $value;
            }
        }

        return $frontmatter ?: null;
    }
}
