<?php

namespace App\Http\Controllers;

use App\Enums\CertificateType;
use App\Http\Requests\Certificate\UpdateCertificateFeeRequest;
use App\Models\CertificateFee;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class CertificateFeeController extends Controller
{
    public function edit(): View
    {
        $fees = CertificateFee::feeMap();

        return view('certificates.fees', [
            'types' => CertificateType::cases(),
            'fees' => $fees,
        ]);
    }

    public function update(UpdateCertificateFeeRequest $request): RedirectResponse
    {
        CertificateFee::syncFees($request->validatedFees());

        return back()->with('status', 'Certificate fees updated.');
    }
}
