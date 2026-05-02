<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vault\GrantPermissionRequest;
use App\Http\Requests\Vault\StoreVaultEntryRequest;
use App\Http\Requests\Vault\UpdateVaultEntryRequest;
use App\Http\Resources\DocumentResource;
use App\Http\Resources\VaultCategoryResource;
use App\Http\Resources\VaultEntryResource;
use App\Models\Document;
use App\Models\VaultCategory;
use App\Models\VaultEntry;
use App\Models\VaultPermission;
use App\Services\VaultEncryptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VaultController extends Controller
{
    private VaultEncryptionService $encryptionService;

    public function __construct(VaultEncryptionService $encryptionService)
    {
        $this->encryptionService = $encryptionService;
    }

    /**
     * Get all vault categories.
     */
    public function categories(Request $request): JsonResponse
    {
        $family = $request->user()->currentFamily()->firstOrFail();

        $categories = VaultCategory::where('family_id', $family->id)
            ->withCount('entries')
            ->orderBy('name')
            ->get();

        return response()->json([
            'categories' => VaultCategoryResource::collection($categories),
        ], 200);
    }

    /**
     * Create a new vault category (parent only).
     */
    public function storeCategory(Request $request): JsonResponse
    {
        $this->authorize('create', VaultCategory::class);

        $family = $request->user()->currentFamily()->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:500',
        ]);

        $slug = \Str::slug($validated['name']);

        // Ensure unique slug within family
        $baseSlug = $slug;
        $counter = 1;
        while (VaultCategory::where('family_id', $family->id)->where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$counter++;
        }

        $category = VaultCategory::create([
            'family_id' => $family->id,
            'name' => $validated['name'],
            'slug' => $slug,
            'icon' => $validated['icon'] ?? 'lock',
            'description' => $validated['description'] ?? null,
        ]);

        return response()->json([
            'category' => VaultCategoryResource::make($category->loadCount('entries')),
        ], 201);
    }

    /**
     * Update a vault category (parent only).
     */
    public function updateCategory(Request $request, string $category): JsonResponse
    {
        $family = $request->user()->currentFamily()->firstOrFail();
        $category = VaultCategory::where('family_id', $family->id)->findOrFail($category);
        $this->authorize('update', $category);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'icon' => 'sometimes|string|max:50',
            'description' => 'nullable|string|max:500',
        ]);

        if (isset($validated['name'])) {
            $slug = \Str::slug($validated['name']);
            $baseSlug = $slug;
            $counter = 1;
            while (VaultCategory::where('family_id', $category->family_id)
                ->where('slug', $slug)
                ->where('id', '!=', $category->id)
                ->exists()) {
                $slug = $baseSlug.'-'.$counter++;
            }
            $validated['slug'] = $slug;
        }

        $category->update($validated);

        return response()->json([
            'category' => VaultCategoryResource::make($category->loadCount('entries')),
        ], 200);
    }

    /**
     * Delete a vault category (parent only). Must be empty.
     */
    public function destroyCategory(Request $request, string $category): JsonResponse
    {
        $family = $request->user()->currentFamily()->firstOrFail();
        $category = VaultCategory::where('family_id', $family->id)->findOrFail($category);
        $this->authorize('delete', $category);

        if ($category->entries()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete a category that still has entries. Move or delete the entries first.',
            ], 422);
        }

        $category->delete();

        return response()->json(null, 204);
    }

    /**
     * List vault entries accessible to the current user.
     */
    public function index(Request $request): JsonResponse
    {
        $family = $request->user()->currentFamily()->firstOrFail();
        $user = $request->user();

        $query = VaultEntry::where('family_id', $family->id);

        // Filter by category
        if ($request->filled('category')) {
            $query->where('vault_category_id', $request->query('category'));
        }

        $query->with(['category', 'creator', 'permissions']);

        $entries = $query->orderBy('created_at', 'desc')->get();

        // Filter by permissions: parents see all, children see personal + shared entries
        $entries = $entries->filter(function ($entry) use ($user) {
            if ($user->isParent()) {
                return true;
            }

            // Children can see their own personal entries
            if ($entry->is_personal && $entry->created_by === $user->id) {
                return true;
            }

            return $entry->permissions->contains('user_id', $user->id);
        });

        return response()->json([
            'entries' => VaultEntryResource::collection($entries),
        ], 200);
    }

    /**
     * Create a new vault entry. Parents can create any entry. Children can create personal entries only.
     */
    public function store(StoreVaultEntryRequest $request): JsonResponse
    {
        $isPersonal = (bool) $request->input('is_personal', false);
        $this->authorize('create', [VaultEntry::class, $isPersonal]);

        $family = $request->user()->currentFamily()->firstOrFail();
        $validated = $request->validated();

        // Verify category belongs to family
        VaultCategory::where('family_id', $family->id)
            ->findOrFail($validated['vault_category_id']);

        // Encrypt the data
        $encryptedData = $this->encryptionService->encrypt($validated['data']);

        $entry = VaultEntry::create([
            'family_id' => $family->id,
            'vault_category_id' => $validated['vault_category_id'],
            'created_by' => $request->user()->id,
            'title' => $validated['title'],
            'encrypted_data' => $encryptedData,
            'notes' => $validated['notes'] ?? null,
            'is_personal' => $isPersonal,
        ]);

        // Grant permissions to specified users if provided
        if ($request->filled('permissions')) {
            foreach ($validated['permissions'] as $userId) {
                VaultPermission::create([
                    'vault_entry_id' => $entry->id,
                    'user_id' => $userId,
                    'permission_level' => 'view',
                ]);
            }
        }

        return response()->json([
            'entry' => VaultEntryResource::make($entry->load(['category', 'creator', 'permissions'])),
        ], 201);
    }

    /**
     * Get a specific vault entry with decrypted data.
     */
    public function show(Request $request, VaultEntry $entry): JsonResponse
    {
        $this->authorize('view', $entry);

        $entry->load(['category', 'creator', 'permissions.user', 'documents']);

        // Decrypt the data for display
        $entry->decrypted_data = $this->encryptionService->decrypt($entry->encrypted_data);

        return response()->json([
            'entry' => VaultEntryResource::make($entry),
        ], 200);
    }

    /**
     * Update a vault entry.
     */
    public function update(UpdateVaultEntryRequest $request, VaultEntry $entry): JsonResponse
    {
        $this->authorize('update', $entry);

        $validated = $request->validated();

        if ($request->filled('data')) {
            $validated['encrypted_data'] = $this->encryptionService->encrypt($validated['data']);
            unset($validated['data']);
        }

        $entry->update($validated);

        return response()->json([
            'entry' => VaultEntryResource::make($entry->load(['category', 'creator', 'permissions'])),
        ], 200);
    }

    /**
     * Delete a vault entry (parent only).
     */
    public function destroy(Request $request, VaultEntry $entry): JsonResponse
    {
        $this->authorize('delete', $entry);

        $entry->delete();

        return response()->json(null, 204);
    }

    /**
     * Grant a user access to a vault entry (parent only).
     */
    public function grantPermission(GrantPermissionRequest $request, VaultEntry $entry): JsonResponse
    {
        $this->authorize('managePermissions', $entry);

        $validated = $request->validated();

        // Verify user is in same family
        $entry->vaultCategory->family->members()->findOrFail($validated['user_id']);

        // Check if permission already exists
        $permission = VaultPermission::where('vault_entry_id', $entry->id)
            ->where('user_id', $validated['user_id'])
            ->first();

        if ($permission) {
            $permission->update(['permission_level' => $validated['permission_level']]);
        } else {
            $permission = VaultPermission::create([
                'vault_entry_id' => $entry->id,
                'user_id' => $validated['user_id'],
                'permission_level' => $validated['permission_level'],
            ]);
        }

        return response()->json([
            'message' => 'Permission granted',
            'permission' => [
                'user_id' => $permission->user_id,
                'permission_level' => $permission->permission_level,
            ],
        ], 200);
    }

    /**
     * Revoke a user's access to a vault entry (parent only).
     */
    public function revokePermission(Request $request, VaultEntry $entry, $userId): JsonResponse
    {
        $this->authorize('managePermissions', $entry);

        VaultPermission::where('vault_entry_id', $entry->id)
            ->where('user_id', $userId)
            ->delete();

        return response()->json([
            'message' => 'Permission revoked',
        ], 200);
    }

    /**
     * Upload a document to a vault entry.
     */
    public function uploadDocument(Request $request, VaultEntry $entry): JsonResponse
    {
        $this->authorize('view', $entry);

        // Block uploads for demo family
        $family = $request->user()->currentFamily()->first();
        if ($family && $family->slug === 'q32-demo-family') {
            return response()->json(['message' => 'File uploads are disabled for the demo family.'], 403);
        }

        // Block new uploads when the family is in a billing-failed soft cap
        // (set by the day-7 grace-period downgrade — see EnforceGracePeriod).
        // Existing data is never deleted; only new uploads are blocked until
        // payment is restored. See 70-H (#221).
        if ($family && ! empty($family->settings['storage_soft_capped'])) {
            return response()->json([
                'message' => 'New uploads are paused while your subscription is past due. Restore your subscription to upload new files.',
            ], 402);
        }

        $validated = $request->validate([
            'file' => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png,gif,webp,doc,docx,xls,xlsx,txt,csv',
        ]);

        $file = $validated['file'];

        // Store file and create document record
        $path = $file->store("vault/{$entry->id}", 'private');

        $document = Document::create([
            'documentable_type' => VaultEntry::class,
            'documentable_id' => $entry->id,
            'uploaded_by' => $request->user()->id,
            'original_filename' => $file->getClientOriginalName(),
            'stored_filename' => basename($path),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'disk' => 'private',
            'path' => $path,
            'encrypted' => false,
        ]);

        return response()->json([
            'document' => DocumentResource::make($document),
        ], 201);
    }

    /**
     * Delete a document from a vault entry.
     *
     * @return JsonResponse
     */
    /**
     * Download a vault document.
     */
    public function downloadDocument(Request $request, Document $document)
    {
        // Ensure the document belongs to a vault entry in the user's family
        $entry = $document->documentable;

        if (! $entry || ! ($entry instanceof VaultEntry)) {
            return response()->json(['message' => 'Document not found.'], 404);
        }

        $this->authorize('view', $entry);

        $disk = \Storage::disk($document->disk ?? 'private');

        if (! $disk->exists($document->path)) {
            return response()->json(['message' => 'File not found.'], 404);
        }

        return $disk->download($document->path, $document->original_filename);
    }

    public function deleteDocument(Request $request, Document $document): JsonResponse
    {
        $entry = $document->documentable;

        if (! $entry || ! ($entry instanceof VaultEntry)) {
            return response()->json(['message' => 'Document not found.'], 404);
        }

        $this->authorize('update', $entry);

        // Block deletion for demo family
        $family = $request->user()->currentFamily()->first();
        if ($family && $family->slug === 'q32-demo-family') {
            return response()->json(['message' => 'Document deletion is disabled for the demo family.'], 403);
        }

        // Delete file from storage
        \Storage::disk($document->disk ?? 'private')->delete($document->path);

        $document->delete();

        return response()->json(null, 204);
    }
}
