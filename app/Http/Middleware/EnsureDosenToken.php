<?php

namespace App\Http\Middleware;

use App\Models\Dosen;
use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class EnsureDosenToken
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Preferred: Sanctum SPA cookie (session) authenticated user.
        $user = $request->user();
        if ($user instanceof Dosen) {
            $request->attributes->set('dosen', $user);
            $request->attributes->set('kode_dosen', $user->getKey());
            $request->setUserResolver(fn () => $user);

            return $next($request);
        }

        // Legacy fallback: bearer token.
        $plainTextToken = $request->bearerToken();

        if (! $plainTextToken) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        $accessToken = PersonalAccessToken::findToken($plainTextToken);

        if (! $accessToken) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        $tokenable = $accessToken->tokenable;

        if (! $tokenable instanceof Dosen) {
            return response()->json([
                'status' => false,
                'message' => 'Forbidden',
            ], 403);
        }

        $request->attributes->set('dosen', $tokenable);
        $request->attributes->set('kode_dosen', $tokenable->getKey());
        $request->setUserResolver(fn () => $tokenable);

        return $next($request);
    }
}
