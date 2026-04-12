<?php

namespace App\Http\Requests\Recipe;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRecipeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isParent();
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'servings' => ['nullable', 'integer', 'min:1', 'max:100'],
            'prep_time_minutes' => ['nullable', 'integer', 'min:0'],
            'cook_time_minutes' => ['nullable', 'integer', 'min:0'],
            'total_time_minutes' => ['nullable', 'integer', 'min:0'],
            'source_url' => ['nullable', 'url:http,https', 'max:2048'],
            'source_type' => ['nullable', 'in:manual,url,photo,social_media'],
            'image_path' => ['nullable', 'string', 'max:512'],
            'instructions' => ['nullable', 'array'],
            'instructions.*.step' => ['required_with:instructions', 'integer'],
            'instructions.*.text' => ['required_with:instructions', 'string'],
            'notes' => ['nullable', 'string'],
            'is_favorite' => ['nullable', 'boolean'],
            'ingredients' => ['nullable', 'array'],
            'ingredients.*.name' => ['required_with:ingredients', 'string', 'max:255'],
            'ingredients.*.quantity' => ['nullable', 'numeric', 'min:0'],
            'ingredients.*.unit' => ['nullable', 'string', 'max:50'],
            'ingredients.*.preparation' => ['nullable', 'string', 'max:255'],
            'ingredients.*.group_name' => ['nullable', 'string', 'max:100'],
            'ingredients.*.is_optional' => ['nullable', 'boolean'],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => [Rule::exists('tags', 'id')->where('family_id', $this->user()->family_id)],
        ];
    }
}
