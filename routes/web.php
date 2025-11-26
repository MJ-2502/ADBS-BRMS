<?php

use App\Http\Controllers\AccountVerificationController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\ApiTokenController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\CertificateRequestController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HouseholdController;
use App\Http\Controllers\HouseholdRecordController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResidentController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/analytics/summary', [AnalyticsController::class, 'summary'])->name('analytics.summary');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/token', [ApiTokenController::class, 'store'])->name('profile.token');

    Route::resource('certificates', CertificateRequestController::class)->only(['index', 'create', 'store', 'show', 'update']);
    Route::get('certificates/{certificate}/download', [CertificateRequestController::class, 'download'])
        ->name('certificates.download');

    Route::middleware('role:admin,clerk')->group(function (): void {
        Route::resource('residents', ResidentController::class);
        Route::resource('households', HouseholdController::class)->except(['show']);
        Route::get('household-records', [HouseholdRecordController::class, 'index'])->name('household-records.index');
        Route::post('household-records', [HouseholdRecordController::class, 'store'])->name('household-records.store');
        Route::get('household-records/template', [HouseholdRecordController::class, 'template'])->name('household-records.template');
        Route::get('household-records/{householdRecord}/download', [HouseholdRecordController::class, 'download'])->name('household-records.download');

        Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('verifications', [AccountVerificationController::class, 'index'])->name('verifications.index');
        Route::post('verifications/{registrationRequest}/approve', [AccountVerificationController::class, 'approve'])->name('verifications.approve');
        Route::post('verifications/{registrationRequest}/reject', [AccountVerificationController::class, 'reject'])->name('verifications.reject');
        Route::get('verifications/{registrationRequest}/proof', [AccountVerificationController::class, 'downloadProof'])->name('verifications.proof');

        Route::get('backups', [BackupController::class, 'index'])->name('backups.index');
        Route::post('backups', [BackupController::class, 'store'])->name('backups.store');
        Route::get('backups/{backup}/download', [BackupController::class, 'download'])->name('backups.download');
    });
});
