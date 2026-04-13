<?php

namespace App\Http\Resources;

use App\Models\ShoppingList;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShoppingListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var ShoppingList $list */
        $list = $this->resource;

        $groupedItems = null;
        if ($list->relationLoaded('items')) {
            $groupedItems = $list->items
                ->groupBy(fn ($item) => $item->category ?? 'Uncategorized')
                ->map(fn ($group) => ShoppingItemResource::collection($group))
                ->toArray();
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'store_name' => $this->store_name,
            'is_active' => $this->is_active,
            'completed_at' => $this->completed_at,
            'items_count' => $list->relationLoaded('items') ? $list->items->count() : null,
            'unchecked_count' => $list->relationLoaded('items') ? $list->items->where('is_checked', false)->count() : null,
            'items' => $this->whenLoaded('items'),
            'items_by_category' => $groupedItems,
            'creator' => new UserResource($this->whenLoaded('creator')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
