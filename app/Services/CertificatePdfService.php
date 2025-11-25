<?php

namespace App\Services;

use App\Models\CertificateRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class CertificatePdfService
{
    public function generate(CertificateRequest $certificate): string
    {
        $certificate->loadMissing(['resident', 'requester', 'approver']);

        $pdf = Pdf::loadView('pdf.certificate', [
            'certificate' => $certificate,
        ])->setPaper('a4');

        $filePath = 'certificates/' . $certificate->reference_no . '.pdf';
        Storage::disk('local')->put($filePath, $pdf->output());

        return $filePath;
    }
}
