@extends('layouts.guest')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-white">Create Your Account</h2>
    <p class="mt-1 text-sm text-slate-400">Follow the guided steps to complete your request.</p>
</div>

@if($errors->any())
    <div class="mb-4 flex items-start gap-3 rounded-lg border border-rose-700 bg-rose-900/20 px-4 py-3 text-sm text-rose-300 animate-slide-in">
        <i class="fas fa-exclamation-circle mt-0.5 text-rose-500"></i>
        <span>{{ $errors->first() }}</span>
    </div>
@endif

<form method="POST" action="{{ route('register.submit') }}" class="space-y-6" enctype="multipart/form-data" data-register-wizard data-request-url="{{ route('verification-codes.request') }}" data-verify-url="{{ route('verification-codes.verify') }}">
    @csrf
    <input type="hidden" name="email_verification_token" value="{{ old('email_verification_token') }}" data-email-token>
    <input type="hidden" name="contact_verification_token" value="{{ old('contact_verification_token') }}" data-contact-token>

    <ol class="flex items-center justify-between gap-2 text-xs font-semibold uppercase tracking-wide text-slate-400">
        @php($steps = ['Profile', 'Contact', 'Residency', 'Security'])
        @foreach($steps as $index => $label)
            <li class="flex flex-1 items-center gap-2" data-progress-step="{{ $index }}">
                <span class="flex h-8 w-8 items-center justify-center rounded-full border border-slate-600 text-sm text-slate-200">{{ $index + 1 }}</span>
                <span class="hidden text-slate-300 sm:inline">{{ $label }}</span>
                @if(!$loop->last)
                    <span class="hidden flex-1 border-t border-dashed border-slate-700 sm:block"></span>
                @endif
            </li>
        @endforeach
    </ol>

    <div class="space-y-6">
        <section data-step="0" class="wizard-step space-y-4">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-300">
                <i class="fas fa-user mr-2 text-emerald-500"></i>Personal Information
            </h3>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-300">First Name</label>
                    <div class="input-group">
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" name="first_name" value="{{ old('first_name') }}" class="input-with-icon w-full rounded-lg border border-slate-700 px-3 py-2.5 text-base transition-all duration-200 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200" placeholder="Juan" required>
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-300">Last Name</label>
                    <div class="input-group">
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" class="input-with-icon w-full rounded-lg border border-slate-700 px-3 py-2.5 text-base transition-all duration-200 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200" placeholder="Dela Cruz" required>
                    </div>
                </div>
            </div>
            <div class="flex justify-end">
                <button type="button" data-next-step class="rounded-lg bg-linear-to-r from-emerald-500 to-sky-500 px-6 py-2.5 text-sm font-semibold text-white shadow-md transition-all duration-200 hover:shadow-lg hover:from-emerald-600 hover:to-sky-600"><i class="fas fa-arrow-right mr-2"></i>Next: Contact</button>
            </div>
        </section>

        <section data-step="1" class="wizard-step hidden space-y-4">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-300">
                <i class="fas fa-at mr-2 text-emerald-500"></i>Contact & Verification
            </h3>
            <div class="space-y-3 rounded-xl border border-slate-700 bg-slate-900/40 p-4">
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-300">Email Address</label>
                    <div class="input-group">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" name="email" value="{{ old('email') }}" data-input-email class="input-with-icon w-full rounded-lg border border-slate-700 px-3 py-2.5 text-base transition-all duration-200 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200" placeholder="you@example.com" required>
                    </div>
                </div>
                <div class="grid gap-3 sm:grid-cols-2">
                    <button type="button" data-send-code="email" class="rounded-lg border border-slate-600 px-4 py-2 text-sm font-semibold text-slate-200 transition-all hover:border-emerald-400 hover:bg-emerald-900/20"><i class="fas fa-paper-plane mr-2"></i>Send code</button>
                    <div class="flex items-center gap-2">
                        <input type="text" data-code-input="email" maxlength="6" pattern="[0-9]{6}" class="flex-1 rounded-lg border border-slate-700 bg-transparent px-3 py-2 text-sm text-slate-100 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200" placeholder="Enter 6-digit code">
                        <button type="button" data-verify-code="email" class="rounded-lg bg-emerald-600 px-3 py-2 text-sm font-semibold text-white transition-colors hover:bg-emerald-700"><i class="fas fa-check mr-1"></i>Verify</button>
                    </div>
                </div>
                <p data-status-message="email" class="text-xs text-slate-400">We will email a one-time code to confirm ownership.</p>
            </div>

            <div class="space-y-3 rounded-xl border border-slate-700 bg-slate-900/40 p-4">
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-300">Contact Number</label>
                    <div class="input-group">
                        <i class="fas fa-phone input-icon"></i>
                        <input type="text" name="contact_number" value="{{ old('contact_number') }}" data-input-phone class="input-with-icon w-full rounded-lg border border-slate-700 px-3 py-2.5 text-base transition-all duration-200 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200" placeholder="09XX XXX XXXX" required>
                    </div>
                </div>
                <div class="grid gap-3 sm:grid-cols-2">
                    <button type="button" data-send-code="phone" class="rounded-lg border border-slate-600 px-4 py-2 text-sm font-semibold text-slate-200 transition-all hover:border-emerald-400 hover:bg-emerald-900/20"><i class="fas fa-sms mr-2"></i>Send OTP</button>
                    <div class="flex items-center gap-2">
                        <input type="text" data-code-input="phone" maxlength="6" pattern="[0-9]{6}" class="flex-1 rounded-lg border border-slate-700 bg-transparent px-3 py-2 text-sm text-slate-100 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200" placeholder="Enter code">
                        <button type="button" data-verify-code="phone" class="rounded-lg bg-emerald-600 px-3 py-2 text-sm font-semibold text-white transition-colors hover:bg-emerald-700"><i class="fas fa-check mr-1"></i>Verify</button>
                    </div>
                </div>
                <p data-status-message="phone" class="text-xs text-slate-400">We’ll text an OTP to confirm your number. (Demo environment logs it to the console.)</p>
            </div>

            <div class="flex items-center justify-between">
                <button type="button" data-prev-step class="rounded-lg border border-slate-600 px-5 py-2.5 text-sm font-semibold text-slate-200 transition-colors hover:bg-slate-800 hover:text-white"><i class="fas fa-arrow-left mr-2"></i>Back</button>
                <button type="button" data-next-step class="rounded-lg bg-linear-to-r from-emerald-500 to-sky-500 px-6 py-2.5 text-sm font-semibold text-white shadow-md transition-all duration-200 hover:shadow-lg hover:from-emerald-600 hover:to-sky-600"><i class="fas fa-arrow-right mr-2"></i>Next: Residency</button>
            </div>
        </section>

        <section data-step="2" class="wizard-step hidden space-y-4">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-300">
                <i class="fas fa-map-marker-alt mr-2 text-emerald-500"></i>Residency Details
            </h3>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-300">Years of Residency</label>
                    <div class="input-group">
                        <i class="fas fa-calendar-alt input-icon"></i>
                        <input type="number" name="years_of_residency" min="0" value="{{ old('years_of_residency', 1) }}" class="input-with-icon w-full rounded-lg border border-slate-700 px-3 py-2.5 text-base transition-all duration-200 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200" required>
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-300">Purok</label>
                    <div class="input-group">
                        <i class="fas fa-home input-icon"></i>
                        <input type="text" name="purok" value="{{ old('purok') }}" class="input-with-icon w-full rounded-lg border border-slate-700 px-3 py-2.5 text-base transition-all duration-200 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200" placeholder="Purok 1">
                    </div>
                </div>
            </div>
            <div class="space-y-2">
                <label class="text-sm font-semibold text-slate-300">Complete Address</label>
                <div class="input-group">
                    <i class="fas fa-map-marked-alt input-icon" style="top: 16px;"></i>
                    <textarea name="address_line" rows="2" class="input-with-icon w-full rounded-lg border border-slate-700 px-3 py-2.5 text-base transition-all duration-200 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200" placeholder="Street, Municipality, Province">{{ old('address_line') }}</textarea>
                </div>
            </div>
            <div class="flex items-center justify-between">
                <button type="button" data-prev-step class="rounded-lg border border-slate-600 px-5 py-2.5 text-sm font-semibold text-slate-200 transition-colors hover:bg-slate-800 hover:text-white"><i class="fas fa-arrow-left mr-2"></i>Back</button>
                <button type="button" data-next-step class="rounded-lg bg-linear-to-r from-emerald-500 to-sky-500 px-6 py-2.5 text-sm font-semibold text-white shadow-md transition-all duration-200 hover:shadow-lg hover:from-emerald-600 hover:to-sky-600"><i class="fas fa-arrow-right mr-2"></i>Next: Security</button>
            </div>
        </section>

        <section data-step="3" class="wizard-step hidden space-y-4">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-300">
                <i class="fas fa-lock mr-2 text-emerald-500"></i>Security & Documents
            </h3>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-300">Password</label>
                    <div class="input-group">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" name="password" id="password" class="input-with-icon w-full rounded-lg border border-slate-700 px-3 py-2.5 pr-12 text-base transition-all duration-200 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200" placeholder="••••••••" required>
                        <button type="button" data-toggle-password data-target="password" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-200 transition-colors">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-300">Confirm Password</label>
                    <div class="input-group">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="input-with-icon w-full rounded-lg border border-slate-700 px-3 py-2.5 pr-12 text-base transition-all duration-200 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200" placeholder="••••••••" required>
                        <button type="button" data-toggle-password data-target="password_confirmation" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-200 transition-colors">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="space-y-2">
                <label class="text-sm font-semibold text-slate-300">Proof of Residency <span class="text-rose-500">*</span></label>
                <div class="relative">
                    <input type="file" name="proof_document" id="proof_document" accept=".jpg,.jpeg,.png,.pdf" class="hidden" required>
                    <label for="proof_document" class="flex cursor-pointer items-center justify-center rounded-lg border-2 border-dashed border-slate-700 bg-slate-800/50 px-4 py-6 text-center transition-all duration-200 hover:border-emerald-400 hover:bg-emerald-900/20">
                        <div>
                            <i class="fas fa-cloud-upload-alt text-3xl text-slate-400"></i>
                            <p class="mt-2 text-sm font-semibold text-slate-300" id="fileName">Click to upload file</p>
                            <p class="mt-1 text-xs text-slate-400">JPG, PNG, or PDF (Max 5 MB)</p>
                        </div>
                    </label>
                </div>
                <p class="text-xs text-slate-400"><i class="fas fa-info-circle mr-1 text-emerald-500"></i>Upload barangay ID, utility bill, or any residency proof.</p>
            </div>
            <div class="flex items-center justify-between">
                <button type="button" data-prev-step class="rounded-lg border border-slate-600 px-5 py-2.5 text-sm font-semibold text-slate-200 transition-colors hover:bg-slate-800 hover:text-white"><i class="fas fa-arrow-left mr-2"></i>Back</button>
                <button type="submit" class="rounded-lg bg-linear-to-r from-emerald-500 to-sky-500 px-6 py-2.5 text-sm font-semibold text-white shadow-lg transition-all duration-200 hover:shadow-xl hover:from-emerald-600 hover:to-sky-600"><i class="fas fa-check-circle mr-2"></i>Submit registration</button>
            </div>

            <p class="pt-4 text-center text-sm text-slate-400">
                Already registered?
                <a href="{{ route('login') }}" class="font-semibold text-emerald-500 hover:text-emerald-300">Sign in instead</a>
            </p>
        </section>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const wizard = document.querySelector('[data-register-wizard]');
        if (!wizard) {
            return;
        }

        const steps = Array.from(wizard.querySelectorAll('[data-step]'));
        const progress = Array.from(wizard.querySelectorAll('[data-progress-step]'));
        let currentStep = 0;

        const setStep = (index) => {
            if (index < 0 || index >= steps.length) {
                return;
            }

            currentStep = index;
            steps.forEach((step, idx) => {
                step.classList.toggle('hidden', idx !== currentStep);
            });
            progress.forEach((item, idx) => {
                const circle = item.querySelector('span');
                if (!circle) {
                    return;
                }
                circle.classList.toggle('bg-emerald-500', idx <= currentStep);
                circle.classList.toggle('border-emerald-500', idx <= currentStep);
            });
        };

        wizard.querySelectorAll('[data-next-step]').forEach((btn) => {
            btn.addEventListener('click', () => setStep(currentStep + 1));
        });

        wizard.querySelectorAll('[data-prev-step]').forEach((btn) => {
            btn.addEventListener('click', () => setStep(currentStep - 1));
        });

        const fileInput = document.getElementById('proof_document');
        if (fileInput) {
            fileInput.addEventListener('change', (event) => {
                const label = document.getElementById('fileName');
                if (label) {
                    const files = event.target.files;
                    label.textContent = files && files[0] ? files[0].name : 'Click to upload file';
                }
            });
        }

        const togglePasswordButtons = wizard.querySelectorAll('[data-toggle-password]');
        togglePasswordButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const targetId = button.getAttribute('data-target');
                const input = document.getElementById(targetId);
                const icon = button.querySelector('i');
                if (!input || !icon) {
                    return;
                }
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const requestUrl = wizard.getAttribute('data-request-url');
        const verifyUrl = wizard.getAttribute('data-verify-url');

        const statusMessage = (type, message, tone = 'muted') => {
            const el = wizard.querySelector(`[data-status-message="${type}"]`);
            if (!el) {
                return;
            }
            el.textContent = message;
            el.classList.remove('text-slate-400', 'text-emerald-400', 'text-rose-400');
            el.classList.add(tone === 'success' ? 'text-emerald-400' : tone === 'error' ? 'text-rose-400' : 'text-slate-400');
        };

        const setTokenValue = (type, token) => {
            const input = type === 'email' ? wizard.querySelector('[data-email-token]') : wizard.querySelector('[data-contact-token]');
            if (input) {
                input.value = token;
            }
        };

        const sendButtons = wizard.querySelectorAll('[data-send-code]');
        sendButtons.forEach((button) => {
            button.addEventListener('click', async () => {
                const type = button.getAttribute('data-send-code');
                const targetInput = wizard.querySelector(type === 'email' ? '[data-input-email]' : '[data-input-phone]');
                if (!targetInput || !targetInput.value) {
                    statusMessage(type, 'Please enter a value first.', 'error');
                    return;
                }

                button.disabled = true;
                statusMessage(type, 'Sending code...', 'muted');

                try {
                    const response = await fetch(requestUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken ?? '',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            type: type === 'email' ? 'email' : 'phone',
                            target: targetInput.value,
                        }),
                    });

                    if (!response.ok) {
                        const errorData = await response.json().catch(() => ({}));
                        throw new Error(errorData.message ?? (errorData.errors?.target?.[0] ?? 'Unable to send code.'));
                    }

                    statusMessage(type, 'Verification code sent! Check your inbox/device.', 'success');
                } catch (error) {
                    statusMessage(type, error.message ?? 'Unable to send code.', 'error');
                } finally {
                    button.disabled = false;
                }
            });
        });

        const verifyButtons = wizard.querySelectorAll('[data-verify-code]');
        verifyButtons.forEach((button) => {
            button.addEventListener('click', async () => {
                const type = button.getAttribute('data-verify-code');
                const targetInput = wizard.querySelector(type === 'email' ? '[data-input-email]' : '[data-input-phone]');
                const codeInput = wizard.querySelector(`[data-code-input="${type}"]`);
                if (!targetInput || !codeInput || !targetInput.value || !codeInput.value) {
                    statusMessage(type, 'Enter the code you received.', 'error');
                    return;
                }

                button.disabled = true;
                statusMessage(type, 'Verifying code...', 'muted');

                try {
                    const response = await fetch(verifyUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken ?? '',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            type: type === 'email' ? 'email' : 'phone',
                            target: targetInput.value,
                            code: codeInput.value,
                        }),
                    });

                    if (!response.ok) {
                        const errorData = await response.json().catch(() => ({}));
                        throw new Error(errorData.message ?? (errorData.errors?.code?.[0] ?? 'Unable to verify code.'));
                    }

                    const data = await response.json();
                    setTokenValue(type, data.token ?? '');
                    statusMessage(type, 'Verified successfully!', 'success');
                } catch (error) {
                    statusMessage(type, error.message ?? 'Unable to verify code.', 'error');
                } finally {
                    button.disabled = false;
                }
            });
        });

        setStep(0);
    });
</script>
@endsection
