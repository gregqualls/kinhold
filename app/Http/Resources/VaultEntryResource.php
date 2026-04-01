<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VaultEntryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'vault_category_id' => $this->vault_category_id,
            'title' => $this->title,
            'category' => VaultCategoryResource::make($this->whenLoaded('category')),
            'data' => $this->decrypted_data ?? null,
            'notes' => $this->notes,
            'metadata' => $this->metadata,
            'creator' => UserResource::make($this->whenLoaded('creator')),
            'permissions' => $this->whenLoaded('permissions', function () {
                return $this->permissions->map(fn ($perm) => [
                    'user_id' => $perm->user_id,
                    'permission_level' => $perm->permission_level,
                    'user' => $perm->relationLoaded('user') && $perm->user ? [
                        'id' => $perm->user->id,
                        'name' => $perm->user->name,
                        'avatar' => $perm->user->avatar,
                        'avatar_color' => $perm->user->avatar_color,
                    ] : null,
                ]);
            }),
            'documents' => DocumentResource::collection($this->whenLoaded('documents')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
