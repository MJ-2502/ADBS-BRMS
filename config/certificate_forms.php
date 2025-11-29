<?php

use App\Enums\CertificateType;

return [
    CertificateType::BarangayClearance->value => [
        'title' => 'Barangay Clearance Details',
        'description' => 'Provide the ID that was presented during verification and the exact purpose of the clearance.',
        'requires_details' => true,
        'fields' => [
            [
                'name' => 'id_type',
                'label' => 'Government ID Presented',
                'type' => 'text',
                'placeholder' => 'e.g. PhilSys ID',
                'rules' => ['required', 'string', 'max:120'],
            ],
            [
                'name' => 'id_number',
                'label' => 'ID Number',
                'type' => 'text',
                'placeholder' => 'Enter the ID number printed on the credential',
                'rules' => ['required', 'string', 'max:80'],
            ],
            [
                'name' => 'intended_use',
                'label' => 'Intended Use / Recipient',
                'type' => 'textarea',
                'placeholder' => 'Indicate the office or organization that requires this clearance',
                'rules' => ['required', 'string', 'max:255'],
            ],
        ],
    ],
    CertificateType::Residency->value => [
        'title' => 'Residency Confirmation',
        'description' => 'Confirm the length of stay and residence details for the certificate of residency.',
        'requires_details' => true,
        'fields' => [
            [
                'name' => 'residence_since',
                'label' => 'Living in the Barangay Since',
                'type' => 'date',
                'rules' => ['required', 'date', 'before_or_equal:today'],
            ],
            [
                'name' => 'years_of_residency',
                'label' => 'Total Years of Residency',
                'type' => 'number',
                'rules' => ['required', 'integer', 'min:1', 'max:120'],
            ],
            [
                'name' => 'household_head',
                'label' => 'Household Head Name',
                'type' => 'text',
                'rules' => ['required', 'string', 'max:120'],
            ],
        ],
    ],
    CertificateType::Indigency->value => [
        'title' => 'Indigency Declaration',
        'description' => 'Share the household composition and reason why financial assistance is needed.',
        'requires_details' => true,
        'fields' => [
            [
                'name' => 'household_income',
                'label' => 'Monthly Household Income',
                'type' => 'text',
                'placeholder' => 'e.g. PHP 5,000',
                'rules' => ['required', 'string', 'max:120'],
            ],
            [
                'name' => 'dependents_count',
                'label' => 'Number of Dependents',
                'type' => 'number',
                'rules' => ['required', 'integer', 'min:0', 'max:30'],
            ],
            [
                'name' => 'assistance_needed',
                'label' => 'Requested Assistance / Purpose',
                'type' => 'textarea',
                'rules' => ['required', 'string', 'max:255'],
            ],
        ],
    ],
    CertificateType::BusinessClearance->value => [
        'title' => 'Business Details',
        'description' => 'Provide the official information about the business to be reflected in the clearance.',
        'requires_details' => true,
        'fields' => [
            [
                'name' => 'business_name',
                'label' => 'Business Name',
                'type' => 'text',
                'rules' => ['required', 'string', 'max:150'],
            ],
            [
                'name' => 'business_address',
                'label' => 'Business Address',
                'type' => 'text',
                'rules' => ['required', 'string', 'max:255'],
            ],
            [
                'name' => 'business_nature',
                'label' => 'Nature of Business',
                'type' => 'text',
                'rules' => ['required', 'string', 'max:120'],
            ],
            [
                'name' => 'dti_registration_no',
                'label' => 'DTI / SEC Registration Number',
                'type' => 'text',
                'rules' => ['nullable', 'string', 'max:80'],
            ],
        ],
    ],
    CertificateType::GoodMoral->value => [
        'title' => 'Good Moral Request Details',
        'description' => 'Identify the institution requesting the certificate and a contact person for verification.',
        'requires_details' => true,
        'fields' => [
            [
                'name' => 'requesting_institution',
                'label' => 'Requesting Institution / Company',
                'type' => 'text',
                'rules' => ['required', 'string', 'max:150'],
            ],
            [
                'name' => 'contact_person',
                'label' => 'Contact Person',
                'type' => 'text',
                'rules' => ['required', 'string', 'max:120'],
            ],
            [
                'name' => 'contact_number',
                'label' => 'Contact Number',
                'type' => 'text',
                'rules' => ['required', 'string', 'max:50'],
            ],
            [
                'name' => 'notes',
                'label' => 'Additional Notes',
                'type' => 'textarea',
                'rules' => ['nullable', 'string', 'max:255'],
            ],
        ],
    ],
];
