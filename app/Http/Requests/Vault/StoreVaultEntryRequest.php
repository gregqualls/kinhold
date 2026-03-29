<?php

namespace App\Http\Requests\Vault;

use Illuminate\Foundation\Http\FormRequest;

class StoreVaultEntryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $familyId = $this->user()->family_id;

        return [
            'vault_category_id' => "required|exists:vault_categories,id,family_id,{$familyId}",
            'title' => 'required|string|max:255',
            'data' => 'required|array',
            'notes' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => "exists:users,id,family_id,{$familyId}",
        ];
    }
}
