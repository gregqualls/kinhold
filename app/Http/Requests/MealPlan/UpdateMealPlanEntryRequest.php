<?php

namespace App\Http\Requests\MealPlan;

use App\Enums\MealSlot;
use App\Models\MealPlanEntry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateMealPlanEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $familyId = $this->user()->family_id;

        return [
            'date' => ['sometimes', 'date'],
            'meal_slot' => ['sometimes', 'string', Rule::enum(MealSlot::class)],
            'recipe_id' => ['sometimes', 'nullable', 'uuid', Rule::exists('recipes', 'id')->where('family_id', $familyId)],
            'restaurant_id' => ['sometimes', 'nullable', 'uuid', Rule::exists('restaurants', 'id')],
            'meal_preset_id' => ['sometimes', 'nullable', 'uuid', Rule::exists('meal_presets', 'id')->where('family_id', $familyId)],
            'custom_title' => ['sometimes', 'nullable', 'string', 'max:255'],
            'servings' => ['sometimes', 'nullable', 'integer', 'min:1'],
            'assigned_cooks' => ['sometimes', 'nullable', 'array'],
            'assigned_cooks.*' => ['uuid', Rule::exists('users', 'id')->where('family_id', $familyId)],
            'notes' => ['sometimes', 'nullable', 'string', 'max:255'],
            'sort_order' => ['sometimes', 'nullable', 'integer'],
        ];
    }

    /**
     * If any source field is present in the request, enforce mutual exclusivity.
     * Must have exactly one non-null source across all four source fields,
     * merging the entry's current values for any fields not in the request.
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $sourceFields = ['recipe_id', 'restaurant_id', 'meal_preset_id', 'custom_title'];
                $anySourcePresent = collect($sourceFields)->some(fn ($field) => $this->has($field));

                if (! $anySourcePresent) {
                    return;
                }

                // Merge request values with the entry's current state for fields not in the request
                $entry = $this->route('entry')
                    ? MealPlanEntry::find($this->route('entry'))
                    : null;

                $effectiveValues = [];
                foreach ($sourceFields as $field) {
                    $effectiveValues[] = $this->has($field)
                        ? $this->input($field)
                        : $entry?->{$field};
                }

                $nonNullCount = count(array_filter($effectiveValues));

                if ($nonNullCount !== 1) {
                    $validator->errors()->add(
                        'source',
                        'Exactly one of recipe_id, restaurant_id, meal_preset_id, or custom_title must be set.'
                    );
                }
            },
        ];
    }
}
