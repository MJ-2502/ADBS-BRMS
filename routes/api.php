<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\Api\CertificateApiController;
use App\Http\Controllers\Api\ResidentApiController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth.api')->group(function (): void {
    Route::get('/analytics/summary', [AnalyticsController::class, 'summary']);
    Route::get('/residents', [ResidentApiController::class, 'index']);
    Route::get('/certificates', [CertificateApiController::class, 'index']);
    Route::post('/certificates', [CertificateApiController::class, 'store']);
});
