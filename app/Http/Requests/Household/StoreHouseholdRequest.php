<?php

namespace App\Http\Requests\Household;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreHouseholdRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'household_number' => ['required', 'string', 'max:50', Rule::unique('households', 'household_number')->ignore($this->route('household'))],
            'address_line' => ['required', 'string', 'max:255'],
            'purok' => ['nullable', 'string', 'max:50'],
            'zone' => ['nullable', 'string', 'max:50'],
            'head_name' => ['nullable', 'string', 'max:120'],
            'contact_number' => ['nullable', 'string', 'max:30'],
            'members_count' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
