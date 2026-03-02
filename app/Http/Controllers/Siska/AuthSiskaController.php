<?php

namespace App\Http\Controllers\Siska;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthSiskaController extends Controller
{
    private function requestHasSessionStore(Request $request): bool
    {
        return method_exists($request, 'hasSession') && $request->hasSession();
    }

    public function login(Request $request)
    {
        $validasi = $request->validate([
            'nim' => 'required|string',
            'password' => 'required|string',
        ]);
        $nim = $validasi['nim'];
        $data = Mahasiswa::select(
            'nim',
            'nama_mahasiswa',
            'email',
            'sandi',
            'status',
        )
            ->where('nim', $nim)
            ->first();
        if (! $data) {
            return response()->json([
                'status' => false,
                'message' => 'NIM atau Password Tidak Valid',
            ], 401);
        }
        if (md5($validasi['password']) !== $data->sandi) {
            return response()->json([
                'status' => false,
                'message' => 'NIM atau Password Tidak Valid',
            ], 401);
        }

        // Sanctum SPA Cookie (session-based) login.
        Auth::guard('auth_mhs_siska')->login($data);

        // Session is only available for stateful SPA requests (cookie + CSRF).
        if (! $this->requestHasSessionStore($request)) {
            Auth::guard('auth_mhs_siska')->logout();

            $payload = [
                'status' => false,
                'message' => 'Session not available. Use Sanctum SPA cookie auth (call /sanctum/csrf-cookie, send credentials, and ensure your domain is in SANCTUM_STATEFUL_DOMAINS).',
            ];

            if (config('app.debug')) {
                $payload['debug'] = [
                    'origin' => $request->headers->get('origin'),
                    'referer' => $request->headers->get('referer'),
                    'host' => $request->getHost(),
                    'has_session' => $this->requestHasSessionStore($request),
                    'cookie_names' => array_keys($request->cookies->all()),
                ];
            }

            return response()->json($payload, 400);
        }

        $request->session()->regenerate();

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
        ], 200);
    }

    public function me(Request $request)
    {
        $user = $request->user();
        if (! $user instanceof Mahasiswa) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        $mahasiswa = Mahasiswa::query()->select(
            'nim',
            'nama_mahasiswa',
            'email',
            'status',
        )
            ->where('mahasiswa.nim', $user->getKey())
            ->first();

        return response()->json([
            'status' => true,
            'data' => $mahasiswa,
        ], 200);
    }

    public function logout(Request $request)
    {
        Auth::guard('auth_mhs_siska')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'status' => true,
            'message' => 'Logout successful',
        ], 200);
    }
}
