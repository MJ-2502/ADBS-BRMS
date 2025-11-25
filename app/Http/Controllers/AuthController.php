<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Enums\VerificationStatus;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Resident;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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

        $user = User::create([
            'name' => trim($request->first_name . ' ' . $request->last_name),
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => UserRole::Resident->value,
            'phone' => $request->contact_number,
            'purok' => $request->purok,
            'address_line' => $request->address_line,
            'verification_status' => VerificationStatus::Pending->value,
            'verification_proof_path' => $proofPath,
            'is_active' => true,
        ]);

        Resident::create([
            'user_id' => $user->id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'contact_number' => $request->contact_number,
            'address_line' => $request->address_line,
            'purok' => $request->purok,
            'years_of_residency' => $request->years_of_residency,
            'residency_status' => 'active',
        ]);

        $this->activityLogger->log('register', 'Resident submitted registration', ['user_id' => $user->id]);

        return redirect()->route('login')->with('status', 'Registration received. Your account will be reviewed by the barangay office within 1-2 business days.');
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
