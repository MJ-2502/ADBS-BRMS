<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Enums\VerificationStatus;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\RegistrationRequest;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(private readonly ActivityLogger $activityLogger)
    {
    }

    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['email' => 'Invalid credentials.'])->withInput($request->except('password'));
        }

        if (!$user->is_active) {
            return back()->withErrors(['email' => 'This account is inactive. Please contact the barangay office.'])->withInput($request->except('password'));
        }

        if ($user->isResident() && $user->verification_status !== VerificationStatus::Approved) {
            $message = $user->verification_status === VerificationStatus::Rejected
                ? 'Your registration was rejected. Please visit the barangay office to resubmit your documents.'
                : 'Your account is still pending verification. You will be notified once it is approved.';

            return back()->withErrors(['email' => $message])->withInput($request->except('password'));
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();
        $user->forceFill(['last_login_at' => now()])->save();
        $this->activityLogger->log('login', 'User logged in');

        return redirect()->intended(route('dashboard'));
    }

    public function showRegistrationForm(): View
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $proofPath = $request->file('proof_document')->store('resident-proofs');
        $formattedPurok = $this->formatPurok($request->purok);

        $registration = RegistrationRequest::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'contact_number' => $request->contact_number,
            'address_line' => $request->address_line,
            'purok' => $formattedPurok,
            'years_of_residency' => $request->years_of_residency,
            'proof_document_path' => $proofPath,
            'status' => VerificationStatus::Pending->value,
        ]);

        $this->activityLogger->log('register', 'Resident registration request submitted', [
            'registration_request_id' => $registration->id,
            'email' => $registration->email,
        ]);

        return redirect()->route('login')->with('status', 'Registration received. Your account will be reviewed by the barangay office within 1-2 business days.');
    }

    private function formatPurok(?string $purok): ?string
    {
        if (!$purok) {
            return null;
        }

        $trimmed = trim($purok);
        if ($trimmed === '') {
            return null;
        }

        return Str::startsWith(Str::lower($trimmed), 'purok')
            ? ucfirst($trimmed)
            : 'Purok ' . $trimmed;
    }

    public function logout(Request $request): RedirectResponse
    {
        $this->activityLogger->log('logout', 'User logged out');

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
