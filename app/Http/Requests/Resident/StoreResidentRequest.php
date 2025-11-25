<?php

namespace App\Http\Requests\Resident;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreResidentRequest extends FormRequest
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
            'household_id' => ['nullable', 'exists:households,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'suffix' => ['nullable', 'string', 'max:10'],
            'birthdate' => ['nullable', 'date'],
            'gender' => ['nullable', 'string', 'max:20'],
            'civil_status' => ['nullable', 'string', 'max:20'],
            'occupation' => ['nullable', 'string', 'max:120'],
            'religion' => ['nullable', 'string', 'max:120'],
            'years_of_residency' => ['nullable', 'integer', 'min:0'],
            'residency_status' => ['nullable', 'string', 'max:50'],
            'is_voter' => ['sometimes', 'boolean'],
            'voter_precinct' => ['nullable', 'string', 'max:100'],
            'contact_number' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email'],
            'address_line' => ['nullable', 'string', 'max:255'],
            'purok' => ['nullable', 'string', 'max:50'],
            'education' => ['nullable', 'string', 'max:120'],
            'emergency_contact_name' => ['nullable', 'string', 'max:120'],
            'emergency_contact_number' => ['nullable', 'string', 'max:20'],
            'remarks' => ['nullable', 'string'],
        ];
    }
}
