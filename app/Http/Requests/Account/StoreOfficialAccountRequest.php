<?php

namespace App\Http\Requests\Account;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOfficialAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canManageRecords() ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in(UserRole::staffRoles())],
            'password' => ['required', 'string', 'min:8'],
            'phone' => ['nullable', 'string', 'max:20'],
            'purok' => ['nullable', 'string', 'max:50'],
            'address_line' => ['nullable', 'string', 'max:255'],
        ];
    }
}
