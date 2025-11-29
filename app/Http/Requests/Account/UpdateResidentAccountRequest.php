<?php

namespace App\Http\Requests\Account;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateResidentAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canManageAccounts() ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $resident = $this->route('resident');
        $userId = $resident?->user_id;

        return [
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'password' => ['nullable', 'string', 'min:8'],
            'phone' => ['nullable', 'string', 'max:30'],
            'purok' => ['nullable', 'string', 'max:100'],
            'address_line' => ['nullable', 'string', 'max:255'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
