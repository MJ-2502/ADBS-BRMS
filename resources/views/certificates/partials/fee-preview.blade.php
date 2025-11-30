@php(
    $fieldId = $fieldId ?? 'certificate_type'
)
@php(
    $containerId = $containerId ?? ('fee-preview-' . uniqid())
)

<div class="rounded-xl border border-slate-700 bg-slate-900 px-4 py-3 text-sm text-slate-300">
    <p class="font-semibold">Certificate fee</p>
    <table class="mt-3 w-full text-sm" id="{{ $containerId }}">
        <tbody>
            <tr data-fee-empty>
                <td class="py-2 text-slate-400">Select a certificate type to view its fee.</td>
                <td class="py-2 text-right font-semibold text-white">—</td>
            </tr>
            @foreach($certificateTypeOptions as $option)
                <tr class="hidden" data-fee-row="{{ $option['value'] }}">
                    <td class="py-2 text-white">{{ $option['label'] }}</td>
                    <td class="py-2 text-right font-semibold text-white">₱ {{ number_format($fees[$option['value']] ?? 0, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@push('scripts')
    @once
        <script>
            window.initCertificateFeePreview = window.initCertificateFeePreview || function ({ selectId, containerId }) {
                const select = document.getElementById(selectId);
                const container = document.getElementById(containerId);
                if (!select || !container) {
                    return;
                }

                const rows = container.querySelectorAll('[data-fee-row]');
                const emptyRow = container.querySelector('[data-fee-empty]');

                const update = () => {
                    const value = select.value;
                    let shown = false;
                    rows.forEach((row) => {
                        if (row.dataset.feeRow === value) {
                            row.classList.remove('hidden');
                            shown = true;
                        } else {
                            row.classList.add('hidden');
                        }
                    });
                    if (emptyRow) {
                        emptyRow.classList.toggle('hidden', shown);
                    }
                };

                select.addEventListener('change', update);
                update();
            };
        </script>
    @endonce
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            window.initCertificateFeePreview({ selectId: '{{ $fieldId }}', containerId: '{{ $containerId }}' });
        });
    </script>
@endpush
