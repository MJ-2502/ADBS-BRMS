<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ApiTokenController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        $user = $request->user();

        abort_unless(Hash::check($request->password, $user->password), 403, 'Password mismatch.');

        $token = $user->generateApiToken();

        return response()->json([
            'token' => $token,
        ]);
    }
}
