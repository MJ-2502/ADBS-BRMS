<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
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
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'required_without:contact_number', 'unique:users,email', Rule::unique('registration_requests', 'email')],
            'password' => ['required', 'confirmed', 'min:8'],
            'contact_number' => ['nullable', 'string', 'max:20', 'required_without:email'],
            'years_of_residency' => ['required', 'integer', 'min:0'],
            'purok' => ['nullable', 'string', 'max:50'],
            'address_line' => ['nullable', 'string', 'max:255'],
            'proof_document' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'email_verification_token' => ['nullable', 'uuid', 'required_with:email'],
            'contact_verification_token' => ['nullable', 'uuid', 'required_with:contact_number'],
        ];
    }
}
