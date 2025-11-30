<?php

namespace App\Http\Controllers;

use App\Models\VerificationCode;
use App\Notifications\VerificationCodeNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class VerificationCodeController extends Controller
{
    public function requestCode(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:' . implode(',', [VerificationCode::TYPE_EMAIL, VerificationCode::TYPE_PHONE])],
            'target' => ['required', 'string', 'max:191'],
        ]);

        $type = $validated['type'];
        $target = trim($validated['target']);

        $existing = VerificationCode::query()
            ->where('type', $type)
            ->where('target', $target)
            ->whereNull('verified_at')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if ($existing && $existing->created_at && $existing->created_at->diffInSeconds(now()) < 60) {
            throw ValidationException::withMessages([
                'target' => 'Please wait a minute before requesting another code.',
            ]);
        }

        $code = (string) random_int(100000, 999999);

        $verification = VerificationCode::create([
            'type' => $type,
            'target' => $target,
            'code' => $code,
            'expires_at' => now()->addMinutes(10),
            'session_id' => $request->session()->getId(),
            'ip_address' => $request->ip(),
        ]);

        if ($type === VerificationCode::TYPE_EMAIL) {
            Notification::route('mail', $target)->notify(new VerificationCodeNotification($code, 'email address'));
        } else {
            Log::info('Simulated phone verification code dispatch', [
                'contact_number' => $target,
                'code' => $code,
            ]);
        }

        return response()->json([
            'message' => 'Verification code sent.',
            'expires_at' => $verification->expires_at->toIso8601String(),
        ]);
    }

    public function verifyCode(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:' . implode(',', [VerificationCode::TYPE_EMAIL, VerificationCode::TYPE_PHONE])],
            'target' => ['required', 'string', 'max:191'],
            'code' => ['required', 'digits:6'],
        ]);

        $verification = VerificationCode::query()
            ->where('type', $validated['type'])
            ->where('target', trim($validated['target']))
            ->latest()
            ->first();

        if (!$verification || $verification->isExpired()) {
            throw ValidationException::withMessages([
                'code' => 'The verification code has expired. Please request a new one.',
            ]);
        }

        if ($verification->attempts >= 5) {
            throw ValidationException::withMessages([
                'code' => 'Too many attempts. Please request a new code.',
            ]);
        }

        if ($verification->code !== $validated['code']) {
            $verification->increment('attempts');

            throw ValidationException::withMessages([
                'code' => 'The verification code is incorrect.',
            ]);
        }

        $token = (string) Str::uuid();
        $verification->markVerified($token);

        return response()->json([
            'message' => 'Verification successful.',
            'token' => $token,
        ]);
    }
}
