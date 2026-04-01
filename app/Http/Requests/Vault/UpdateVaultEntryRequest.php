<?php

namespace App\Http\Requests\Vault;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateVaultEntryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        $familyId = $this->user()->family_id;

        return [
            'vault_category_id' => "sometimes|exists:vault_categories,id,family_id,{$familyId}",
            'title' => 'sometimes|string|max:255',
            'data' => 'sometimes|array',
            'notes' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:users,id',
        ];
    }
}
