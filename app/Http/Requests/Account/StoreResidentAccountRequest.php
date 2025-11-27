<?php

namespace App\Http\Requests\Account;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreResidentAccountRequest extends FormRequest
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
            'resident_id' => [
                'required',
                Rule::exists('residents', 'id')->where(fn ($query) => $query->whereNull('user_id')),
            ],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'phone' => ['nullable', 'string', 'max:30'],
            'purok' => ['nullable', 'string', 'max:100'],
            'address_line' => ['nullable', 'string', 'max:255'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
