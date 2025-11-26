<?php

namespace App\Providers;

use App\Enums\CertificateType;
use App\Enums\UserRole;
use App\Enums\VerificationStatus;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        View::share('certificateTypeOptions', CertificateType::options());

        $this->ensureDefaultAdmin();
    }

    private function ensureDefaultAdmin(): void
    {
        if (app()->runningInConsole()) {
            return;
        }

        $autoAdminConfig = config('app.auto_admin');

        if (!($autoAdminConfig['enabled'] ?? false)) {
            return;
        }

        $allowedEnvironments = $autoAdminConfig['environments'] ?? [];
        if ($allowedEnvironments !== [] && !in_array(config('app.env'), $allowedEnvironments, true)) {
            return;
        }

        if (User::query()->where('role', UserRole::Admin->value)->exists()) {
            return;
        }

        $email = $autoAdminConfig['email'] ?? null;
        $password = $autoAdminConfig['password'] ?? null;

        if (!$email || !$password) {
            return;
        }

        $existing = User::query()->where('email', $email)->first();
        if ($existing) {
            $existing->forceFill([
                'role' => UserRole::Admin->value,
                'verification_status' => VerificationStatus::Approved->value,
                'verified_at' => now(),
                'is_active' => true,
            ])->save();

            return;
        }

        $user = User::create([
            'name' => $autoAdminConfig['name'] ?? 'Barangay Admin',
            'email' => $email,
            'password' => Hash::make($password),
            'role' => UserRole::Admin->value,
            'verification_status' => VerificationStatus::Approved->value,
            'verified_at' => now(),
            'is_active' => true,
        ]);

        Log::info('Auto-created default admin user.', ['user_id' => $user->id, 'email' => $email]);
    }
}
