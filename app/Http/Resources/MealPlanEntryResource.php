<?php

namespace App\Http\Resources;

use App\Enums\MealSlot;
use App\Models\MealPlanEntry;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MealPlanEntryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var MealPlanEntry $entry */
        $entry = $this->resource;

        // Determine the source type from whichever FK is set.
        $type = match (true) {
            $entry->recipe_id !== null => 'recipe',
            $entry->restaurant_id !== null => 'restaurant',
            $entry->meal_preset_id !== null => 'preset',
            default => 'custom',
        };

        return [
            'id' => $this->id,
            'meal_plan_id' => $this->meal_plan_id,
            'date' => $this->date?->format('Y-m-d'),
            'meal_slot' => $this->meal_slot instanceof MealSlot
                ? $this->meal_slot->value
                : $this->meal_slot,
            'display_title' => $this->display_title,
            'type' => $type,
            // Only include the relevant source relationship when loaded.
            'recipe' => $this->when(
                $entry->recipe_id !== null,
                fn () => new RecipeResource($this->whenLoaded('recipe'))
            ),
            'restaurant' => $this->when(
                $entry->restaurant_id !== null,
                fn () => new RestaurantResource($this->whenLoaded('restaurant'))
            ),
            'preset' => $this->when(
                $entry->meal_preset_id !== null,
                fn () => new MealPresetResource($this->whenLoaded('preset'))
            ),
            'image_url' => match ($type) {
                'recipe' => $entry->recipe?->image_path ? '/storage/'.$entry->recipe->image_path : null,
                'restaurant' => $entry->restaurant?->image_url,
                default => null,
            },
            'custom_title' => $this->custom_title,
            'servings' => $this->servings,
            'effective_servings' => $this->effective_servings,
            'assigned_cooks' => $this->assigned_cooks,
            'notes' => $this->notes,
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
