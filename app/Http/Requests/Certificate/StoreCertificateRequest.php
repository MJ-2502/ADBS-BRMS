<?php

namespace App\Http\Requests\Certificate;

use App\Enums\CertificateType;
use App\Support\CertificateFormSchema;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreCertificateRequest extends FormRequest
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
        $isAdmin = $this->user()?->canManageRecords();

        $rules = [
            'resident_first_name' => array_filter([
                $isAdmin ? 'required' : 'nullable',
                'string',
                'max:120',
            ]),
            'resident_middle_initial' => ['nullable', 'string', 'max:2'],
            'resident_last_name' => array_filter([
                $isAdmin ? 'required' : 'nullable',
                'string',
                'max:120',
            ]),
            'certificate_type' => ['required', new Enum(CertificateType::class)],
            'purpose' => ['required', 'string', 'max:255'],
            'remarks' => ['nullable', 'string'],
            'details' => ['nullable', 'array'],
        ];

        $schemaRules = CertificateFormSchema::rules($this->input('certificate_type'));
        foreach ($schemaRules as $field => $fieldRules) {
            $rules['details.' . $field] = $fieldRules;
        }

        return $rules;
    }
}
