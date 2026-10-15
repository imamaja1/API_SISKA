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

        // Sanctum SPA Cookie (session-based) login if session is available.
        if ($this->requestHasSessionStore($request)) {
            Auth::guard('auth_mhs_siska')->login($data);
            $request->session()->regenerate();
        }

        $token = $data->createToken('siska-token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'token' => $token,
            'user' => [
                'nim' => $data->nim,
                'nama_mahasiswa' => $data->nama_mahasiswa,
                'email' => $data->email,
                'status' => $data->status,
            ],
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
