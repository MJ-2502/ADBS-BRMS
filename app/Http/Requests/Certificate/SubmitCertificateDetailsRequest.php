<?php

namespace App\Http\Requests\Certificate;

use App\Models\CertificateRequest;
use App\Support\CertificateFormSchema;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SubmitCertificateDetailsRequest extends FormRequest
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
        /** @var CertificateRequest $certificate */
        $certificate = $this->route('certificate');

        return CertificateFormSchema::rules($certificate->certificate_type);
    }
}
