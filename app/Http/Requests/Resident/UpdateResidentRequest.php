<?php

namespace App\Http\Requests\Resident;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateResidentRequest extends FormRequest
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
            'household_id' => ['sometimes', 'nullable', 'exists:households,id'],
            'user_id' => ['sometimes', 'nullable', 'exists:users,id'],
            'first_name' => ['sometimes', 'string', 'max:100'],
            'middle_name' => ['sometimes', 'nullable', 'string', 'max:100'],
            'last_name' => ['sometimes', 'string', 'max:100'],
            'suffix' => ['sometimes', 'nullable', 'string', 'max:10'],
            'birthdate' => ['sometimes', 'nullable', 'date'],
            'gender' => ['sometimes', 'nullable', 'string', 'max:20'],
            'civil_status' => ['sometimes', 'nullable', 'string', 'max:20'],
            'occupation' => ['sometimes', 'nullable', 'string', 'max:120'],
            'religion' => ['sometimes', 'nullable', 'string', 'max:120'],
            'years_of_residency' => ['sometimes', 'integer', 'min:0'],
            'residency_status' => ['sometimes', 'string', 'max:50'],
            'is_voter' => ['sometimes', 'boolean'],
            'voter_precinct' => ['sometimes', 'nullable', 'string', 'max:100'],
            'contact_number' => ['sometimes', 'nullable', 'string', 'max:20'],
            'email' => ['sometimes', 'nullable', 'email'],
            'address_line' => ['sometimes', 'nullable', 'string', 'max:255'],
            'purok' => ['sometimes', 'nullable', 'string', 'max:50'],
            'education' => ['sometimes', 'nullable', 'string', 'max:120'],
            'emergency_contact_name' => ['sometimes', 'nullable', 'string', 'max:120'],
            'emergency_contact_number' => ['sometimes', 'nullable', 'string', 'max:20'],
            'remarks' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
