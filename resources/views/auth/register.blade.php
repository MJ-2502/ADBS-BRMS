@extends('layouts.guest')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-slate-800">Create Your Account</h2>
    <p class="mt-1 text-sm text-slate-500">Join our barangay community today</p>
</div>

@if($errors->any())
    <div class="mb-4 flex items-start gap-3 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 animate-slide-in">
        <i class="fas fa-exclamation-circle mt-0.5 text-rose-500"></i>
        <span>{{ $errors->first() }}</span>
    </div>
@endif

<form method="POST" action="{{ route('register.submit') }}" class="space-y-5" enctype="multipart/form-data">
    @csrf
    
    <!-- Personal Information Section -->
    <div class="space-y-4">
        <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-600">
            <i class="fas fa-user mr-2 text-emerald-500"></i>Personal Information
        </h3>
        
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="space-y-2">
                <label class="text-sm font-semibold text-slate-700">First Name</label>
                <div class="input-group">
                    <i class="fas fa-user input-icon"></i>
                    <input 
                        type="text" 
                        name="first_name" 
                        value="{{ old('first_name') }}" 
                        class="input-with-icon w-full rounded-lg border border-slate-300 px-3 py-2.5 text-base transition-all duration-200 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200" 
                        placeholder="Juan"
                        required
                    >
                </div>
            </div>
            <div class="space-y-2">
                <label class="text-sm font-semibold text-slate-700">Last Name</label>
                <div class="input-group">
                    <i class="fas fa-user input-icon"></i>
                    <input 
                        type="text" 
                        name="last_name" 
                        value="{{ old('last_name') }}" 
                        class="input-with-icon w-full rounded-lg border border-slate-300 px-3 py-2.5 text-base transition-all duration-200 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200" 
                        placeholder="Dela Cruz"
                        required
                    >
                </div>
            </div>
        </div>

        <div class="space-y-2">
            <label class="text-sm font-semibold text-slate-700">Email Address</label>
            <div class="input-group">
                <i class="fas fa-envelope input-icon"></i>
                <input 
                    type="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    class="input-with-icon w-full rounded-lg border border-slate-300 px-3 py-2.5 text-base transition-all duration-200 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200" 
                    placeholder="you@example.com"
                    required
                >
            </div>
        </div>

        <div class="space-y-2">
            <label class="text-sm font-semibold text-slate-700">Contact Number</label>
            <div class="input-group">
                <i class="fas fa-phone input-icon"></i>
                <input 
                    type="text" 
                    name="contact_number" 
                    value="{{ old('contact_number') }}" 
                    class="input-with-icon w-full rounded-lg border border-slate-300 px-3 py-2.5 text-base transition-all duration-200 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200" 
                    placeholder="09XX XXX XXXX"
                >
            </div>
        </div>
    </div>

    <!-- Security Section -->
    <div class="space-y-4">
        <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-600">
            <i class="fas fa-shield-alt mr-2 text-emerald-500"></i>Security
        </h3>
        
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="space-y-2">
                <label class="text-sm font-semibold text-slate-700">Password</label>
                <div class="input-group">
                    <i class="fas fa-lock input-icon"></i>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        class="input-with-icon w-full rounded-lg border border-slate-300 px-3 py-2.5 pr-12 text-base transition-all duration-200 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200" 
                        placeholder="••••••••"
                        required
                    >
                    <button 
                        type="button" 
                        onclick="togglePassword('password', 'toggleIcon1')" 
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors"
                    >
                        <i id="toggleIcon1" class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            <div class="space-y-2">
                <label class="text-sm font-semibold text-slate-700">Confirm Password</label>
                <div class="input-group">
                    <i class="fas fa-lock input-icon"></i>
                    <input 
                        type="password" 
                        name="password_confirmation" 
                        id="password_confirmation" 
                        class="input-with-icon w-full rounded-lg border border-slate-300 px-3 py-2.5 pr-12 text-base transition-all duration-200 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200" 
                        placeholder="••••••••"
                        required
                    >
                    <button 
                        type="button" 
                        onclick="togglePassword('password_confirmation', 'toggleIcon2')" 
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors"
                    >
                        <i id="toggleIcon2" class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Residency Information Section -->
    <div class="space-y-4">
        <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-600">
            <i class="fas fa-map-marker-alt mr-2 text-emerald-500"></i>Residency Information
        </h3>
        
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="space-y-2">
                <label class="text-sm font-semibold text-slate-700">Years of Residency</label>
                <div class="input-group">
                    <i class="fas fa-calendar-alt input-icon"></i>
                    <input 
                        type="number" 
                        name="years_of_residency" 
                        min="0" 
                        value="{{ old('years_of_residency', 1) }}" 
                        class="input-with-icon w-full rounded-lg border border-slate-300 px-3 py-2.5 text-base transition-all duration-200 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200" 
                        required
                    >
                </div>
            </div>
            <div class="space-y-2">
                <label class="text-sm font-semibold text-slate-700">Purok</label>
                <div class="input-group">
                    <i class="fas fa-home input-icon"></i>
                    <input 
                        type="text" 
                        name="purok" 
                        value="{{ old('purok') }}" 
                        class="input-with-icon w-full rounded-lg border border-slate-300 px-3 py-2.5 text-base transition-all duration-200 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200" 
                        placeholder="Purok 1"
                    >
                </div>
            </div>
        </div>

        <div class="space-y-2">
            <label class="text-sm font-semibold text-slate-700">Complete Address</label>
            <div class="input-group">
                <i class="fas fa-map-marked-alt input-icon" style="top: 16px;"></i>
                <textarea 
                    name="address_line" 
                    rows="2" 
                    class="input-with-icon w-full rounded-lg border border-slate-300 px-3 py-2.5 text-base transition-all duration-200 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200" 
                    placeholder="Street, Municipality, Province"
                >{{ old('address_line') }}</textarea>
            </div>
        </div>
    </div>

    <!-- Document Upload Section -->
    <div class="space-y-4">
        <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-600">
            <i class="fas fa-file-upload mr-2 text-emerald-500"></i>Document Upload
        </h3>
        
        <div class="space-y-2">
            <label class="text-sm font-semibold text-slate-700">
                Proof of Residency <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <input 
                    type="file" 
                    name="proof_document" 
                    id="proof_document" 
                    accept=".jpg,.jpeg,.png,.pdf" 
                    class="hidden" 
                    required
                    onchange="updateFileName(this)"
                >
                <label 
                    for="proof_document" 
                    class="flex cursor-pointer items-center justify-center rounded-lg border-2 border-dashed border-slate-300 bg-slate-50 px-4 py-6 text-center transition-all duration-200 hover:border-emerald-400 hover:bg-emerald-50"
                >
                    <div>
                        <i class="fas fa-cloud-upload-alt text-3xl text-slate-400"></i>
                        <p class="mt-2 text-sm font-semibold text-slate-600" id="fileName">Click to upload file</p>
                        <p class="mt-1 text-xs text-slate-500">JPG, PNG, or PDF (Max 5 MB)</p>
                    </div>
                </label>
            </div>
            <p class="text-xs text-slate-500">
                <i class="fas fa-info-circle mr-1 text-emerald-500"></i>
                Upload barangay ID, utility bill, or any document proving residency
            </p>
        </div>
    </div>

    <!-- Footer -->
    <div class="border-t border-slate-200 pt-5">
        <div class="mb-4 text-center text-sm">
            <span class="text-slate-600">Already have an account? </span>
            <a href="{{ route('login') }}" class="font-semibold text-emerald-600 hover:text-emerald-700 transition-colors">
                Sign in here
            </a>
        </div>

        <button 
            type="submit" 
            class="w-full rounded-lg bg-linear-to-r from-emerald-500 to-sky-500 py-3.5 text-base font-semibold text-white shadow-lg transition-all duration-200 hover:shadow-xl hover:from-emerald-600 hover:to-sky-600 focus:outline-none focus:ring-4 focus:ring-emerald-200"
        >
            <i class="fas fa-user-plus mr-2"></i>
            Create Account
        </button>
    </div>
</form>

<script>
    function togglePassword(inputId, iconId) {
        const passwordInput = document.getElementById(inputId);
        const toggleIcon = document.getElementById(iconId);
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }

    function updateFileName(input) {
        const fileNameElement = document.getElementById('fileName');
        if (input.files && input.files[0]) {
            fileNameElement.textContent = input.files[0].name;
        } else {
            fileNameElement.textContent = 'Click to upload file';
        }
    }
</script>
@endsection
