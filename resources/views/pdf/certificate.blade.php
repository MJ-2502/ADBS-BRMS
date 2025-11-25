<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; color: #0f172a; margin: 40px; }
        .header { text-align: center; margin-bottom: 24px; }
        .title { font-size: 20px; font-weight: bold; text-transform: uppercase; }
        .subtitle { font-size: 12px; letter-spacing: 1px; }
        .section { margin-top: 24px; }
        .label { text-transform: uppercase; font-size: 10px; color: #64748b; letter-spacing: 1px; }
        .value { font-size: 14px; font-weight: bold; margin-top: 6px; }
        .signature { margin-top: 48px; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <div class="subtitle">Republic of the Philippines</div>
        <div class="subtitle">Barangay Official Records</div>
        <div class="title">{{ $certificate->certificate_type->label() }}</div>
        <div class="subtitle">Reference No: {{ $certificate->reference_no }}</div>
    </div>
    <div class="section">
        <div class="label">Issued To</div>
        <div class="value">{{ $certificate->resident?->full_name }}</div>
    </div>
    <div class="section">
        <div class="label">Address</div>
        <div class="value">{{ $certificate->resident?->address_line }}</div>
    </div>
    <div class="section">
        <div class="label">Purpose</div>
        <div class="value">{{ $certificate->purpose }}</div>
    </div>
    <div class="section">
        <div class="label">Issued On</div>
        <div class="value">{{ $certificate->released_at?->format('F d, Y') ?? now()->format('F d, Y') }}</div>
    </div>
    <div class="signature">
        <div class="label">Approved By</div>
        <div class="value">{{ $certificate->approver?->name ?? 'Barangay Captain' }}</div>
    </div>
</body>
</html>
