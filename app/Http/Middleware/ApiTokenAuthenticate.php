<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiTokenAuthenticate
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            abort(401, 'Missing token');
        }

        $user = User::findByPlainTextToken($token);

        if (!$user) {
            abort(401, 'Invalid token');
        }

        auth()->setUser($user);

        return $next($request);
    }
}
