<?php

namespace App\Http\Resources;

use App\Models\MealPlan;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MealPlanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var MealPlan $plan */
        $plan = $this->resource;

        return [
            'id' => $this->id,
            'family_id' => $this->family_id,
            'created_by' => $this->created_by,
            'week_start' => $this->week_start?->format('Y-m-d'),
            'notes' => $this->notes,
            'shopping_list_id' => $this->shopping_list_id,
            'entries' => MealPlanEntryResource::collection($this->whenLoaded('entries')),
            'shopping_list_summary' => $this->when(
                $plan->relationLoaded('shoppingList') && $plan->shoppingList !== null,
                function () use ($plan): array {
                    $list = $plan->shoppingList;

                    // Compute item counts only when the items relation is loaded
                    // on the shopping list; otherwise fall back to null.
                    $totalItems = $list->relationLoaded('items') ? $list->items->count() : null;
                    $checkedItems = $list->relationLoaded('items')
                        ? $list->items->where('is_checked', true)->count()
                        : null;

                    return [
                        'id' => $list->id,
                        'name' => $list->name,
                        'total_items' => $totalItems,
                        'checked_items' => $checkedItems,
                    ];
                }
            ),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
