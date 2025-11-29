@php($schemas = $schemas ?? [])
@php($selectFieldId = $selectFieldId ?? 'certificate_type')
@php($wrapperId = $wrapperId ?? ('certificate-details-' . uniqid()))
@php($values = $values ?? [])

@if(!empty($schemas))
    <div class="rounded-2xl border border-dashed border-slate-200 p-4 dark:border-slate-700">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-sm font-semibold text-slate-700 dark:text-white">Certificate-specific details</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">Complete the fields required for the selected certificate type.</p>
            </div>
            <span class="text-xs text-slate-500" data-details-selected-label></span>
        </div>
        <div id="{{ $wrapperId }}" class="mt-4 space-y-6">
            <p class="text-sm text-slate-500 dark:text-slate-400" data-details-empty>
                Select a certificate type to see the required information.
            </p>
            @foreach($schemas as $type => $schema)
                <div class="hidden space-y-4" data-details-section="{{ $type }}">
                    <div>
                        <p class="text-sm font-semibold text-slate-800 dark:text-white">{{ $schema['title'] ?? 'Required details' }}</p>
                        @if(!empty($schema['description']))
                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $schema['description'] }}</p>
                        @endif
                    </div>
                    @foreach($schema['fields'] ?? [] as $field)
                        @php($name = $field['name'])
                        @php($type = $field['type'] ?? 'text')
                        @php($value = old('details.' . $name, $values[$name] ?? ''))
                        <div>
                            <label class="text-sm font-medium text-slate-600 dark:text-slate-200" for="details_{{ $name }}">{{ $field['label'] ?? str($name)->headline() }}</label>
                            @if($type === 'textarea')
                                <textarea
                                    id="details_{{ $name }}"
                                    name="details[{{ $name }}]"
                                    rows="4"
                                    placeholder="{{ $field['placeholder'] ?? '' }}"
                                    class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-base dark:border-slate-700 dark:bg-slate-900 dark:text-white"
                                >{{ $value }}</textarea>
                            @else
                                <input
                                    id="details_{{ $name }}"
                                    type="{{ in_array($type, ['text', 'number', 'date']) ? $type : 'text' }}"
                                    name="details[{{ $name }}]"
                                    value="{{ $value }}"
                                    placeholder="{{ $field['placeholder'] ?? '' }}"
                                    class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-base dark:border-slate-700 dark:bg-slate-900 dark:text-white"
                                />
                            @endif
                            @error('details.' . $name)
                                <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>

    @push('scripts')
        @once
            <script>
                window.initCertificateDetailsFields = window.initCertificateDetailsFields || function ({ selectId, wrapperId }) {
                    const select = document.getElementById(selectId);
                    const wrapper = document.getElementById(wrapperId);
                    if (!select || !wrapper) {
                        return;
                    }

                    const sections = wrapper.querySelectorAll('[data-details-section]');
                    const emptyState = wrapper.querySelector('[data-details-empty]');
                    const label = wrapper.closest('div')?.querySelector('[data-details-selected-label]') || null;

                    const update = () => {
                        const value = select.value;
                        let visible = false;
                        sections.forEach((section) => {
                            if (section.dataset.detailsSection === value) {
                                section.classList.remove('hidden');
                                visible = true;
                            } else {
                                section.classList.add('hidden');
                            }
                        });
                        if (emptyState) {
                            emptyState.classList.toggle('hidden', visible);
                        }
                        if (label) {
                            label.textContent = visible && select.options[select.selectedIndex] ? select.options[select.selectedIndex].textContent : '';
                        }
                    };

                    select.addEventListener('change', update);
                    update();
                };
            </script>
        @endonce
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                window.initCertificateDetailsFields({ selectId: '{{ $selectFieldId }}', wrapperId: '{{ $wrapperId }}' });
            });
        </script>
    @endpush
@endif
