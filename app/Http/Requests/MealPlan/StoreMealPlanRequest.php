<?php

namespace App\Http\Requests\MealPlan;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreMealPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'week_start' => ['required', 'date'],
        ];
    }

    /**
     * Add custom after-validation to ensure week_start is a Monday.
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->has('week_start')) {
                    return;
                }

                $date = $this->date('week_start');
                if ($date && $date->dayOfWeek !== 1) {
                    $validator->errors()->add('week_start', 'The week start date must be a Monday.');
                }
            },
        ];
    }
}
