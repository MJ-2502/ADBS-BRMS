<?php

use App\Http\Controllers\AccountVerificationController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\ApiTokenController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\CertificateFeeController;
use App\Http\Controllers\CertificateRequestController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HouseholdController;
use App\Http\Controllers\HouseholdRecordController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResidentAccountController;
use App\Http\Controllers\ResidentController;
use App\Http\Controllers\ResidentRecordController;
use App\Http\Controllers\OfficialAccountController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\VerificationCodeController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
    Route::get('/forgot-password', [PasswordResetController::class, 'requestForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.store');
    Route::post('/verification-codes/request', [VerificationCodeController::class, 'requestCode'])->name('verification-codes.request');
    Route::post('/verification-codes/verify', [VerificationCodeController::class, 'verifyCode'])->name('verification-codes.verify');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/analytics/summary', [AnalyticsController::class, 'summary'])->name('analytics.summary');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/token', [ApiTokenController::class, 'store'])->name('profile.token');

    Route::resource('certificates', CertificateRequestController::class);
    Route::put('certificates/{certificate}/status', [CertificateRequestController::class, 'updateStatus'])
        ->name('certificates.status');

    Route::middleware('role:admin,clerk')->group(function (): void {
        Route::resource('residents', ResidentController::class);
        Route::resource('households', HouseholdController::class)->except(['show']);
        Route::prefix('accounts')->as('accounts.')->group(function (): void {
            Route::get('residents', [ResidentAccountController::class, 'index'])->name('residents.index');
            Route::get('residents/create', [ResidentAccountController::class, 'create'])->name('residents.create');
            Route::post('residents', [ResidentAccountController::class, 'store'])->name('residents.store');
            Route::get('residents/{resident}/edit', [ResidentAccountController::class, 'edit'])->name('residents.edit');
            Route::put('residents/{resident}', [ResidentAccountController::class, 'update'])->name('residents.update');
            Route::delete('residents/{resident}', [ResidentAccountController::class, 'destroy'])->name('residents.destroy');
            Route::get('residents/{resident}/proof', [ResidentAccountController::class, 'downloadProof'])->name('residents.proof');
            Route::get('officials', [OfficialAccountController::class, 'index'])->name('officials.index');
            Route::get('officials/create', [OfficialAccountController::class, 'create'])->name('officials.create');
            Route::post('officials', [OfficialAccountController::class, 'store'])->name('officials.store');
            Route::get('officials/{official}/edit', [OfficialAccountController::class, 'edit'])->name('officials.edit');
            Route::put('officials/{official}', [OfficialAccountController::class, 'update'])->name('officials.update');
            Route::delete('officials/{official}', [OfficialAccountController::class, 'destroy'])->name('officials.destroy');
        });
        Route::get('household-records', [HouseholdRecordController::class, 'index'])->name('household-records.index');
        Route::post('household-records', [HouseholdRecordController::class, 'store'])->name('household-records.store');
        Route::get('household-records/template', [HouseholdRecordController::class, 'template'])->name('household-records.template');
        Route::get('household-records/{householdRecord}/download', [HouseholdRecordController::class, 'download'])->name('household-records.download');
        Route::get('resident-records', [ResidentRecordController::class, 'index'])->name('resident-records.index');
        Route::post('resident-records', [ResidentRecordController::class, 'store'])->name('resident-records.store');
        Route::get('resident-records/template', [ResidentRecordController::class, 'template'])->name('resident-records.template');
        Route::get('resident-records/{residentRecord}/download', [ResidentRecordController::class, 'download'])->name('resident-records.download');

        Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('verifications', [AccountVerificationController::class, 'index'])->name('verifications.index');
        Route::post('verifications/{registrationRequest}/approve', [AccountVerificationController::class, 'approve'])->name('verifications.approve');
        Route::post('verifications/{registrationRequest}/reject', [AccountVerificationController::class, 'reject'])->name('verifications.reject');
        Route::get('verifications/{registrationRequest}/proof', [AccountVerificationController::class, 'downloadProof'])->name('verifications.proof');

        Route::get('backups', [BackupController::class, 'index'])->name('backups.index');
        Route::post('backups', [BackupController::class, 'store'])->name('backups.store');
        Route::post('backups/restore/upload', [BackupController::class, 'restoreFromUpload'])->name('backups.restore-upload');
        Route::post('backups/{backup}/restore', [BackupController::class, 'restoreFromJob'])->name('backups.restore-job');
        Route::get('backups/{backup}/download', [BackupController::class, 'download'])->name('backups.download');
    });

    Route::middleware('role:admin')->group(function (): void {
        Route::get('certificate-fees', [CertificateFeeController::class, 'edit'])->name('certificates.fees.edit');
        Route::put('certificate-fees', [CertificateFeeController::class, 'update'])->name('certificates.fees.update');
    });
});
