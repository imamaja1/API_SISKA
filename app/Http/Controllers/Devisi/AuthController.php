<?php

namespace App\Http\Controllers\Devisi;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    private function requestHasSessionStore(Request $request): bool
    {
        return method_exists($request, "hasSession") && $request->hasSession();
    }

    public function login(Request $request)
    {
        $validasi = $request->validate([
            "username" => "required|string",
            "password" => "required|string",
        ]);

        $username = $validasi["username"];
        $data = Pengguna::select(
            "kode_pengguna",
            "nama_login",
            "sandi_pengguna",
            "nama_pengguna",
        )
            ->where("nama_login", $username)
            ->get();
        if (!$data) {
            return response()->json(
                [
                    "status" => false,
                    "message" => "Username atau Password Tidak Valid",
                ],
                401,
            );
        }
        foreach ($data as $item) {
            if (!md5($validasi["password"]) === $item->sandi_pengguna) {
                return response()->json(
                    [
                        "status" => false,
                        "message" => "Username atau Password Tidak Valid",
                    ],
                    401,
                );
            }
        }
        $datapengguna = $item;
        // Sanctum SPA Cookie (session-based) login if session is available.
        if ($this->requestHasSessionStore($request)) {
            Auth::guard("auth_divisi_siska")->login($datapengguna);
            $request->session()->regenerate();
        }

        $token = $datapengguna->createToken('divisi-token')->plainTextToken;

        return response()->json(
            [
                "status" => true,
                "message" => "Login successful",
                "token" => $token,
                "user" => [
                    "kode_pengguna" => $datapengguna->kode_pengguna,
                    "nama_login" => $datapengguna->nama_login,
                    "nama_pengguna" => $datapengguna->nama_pengguna,
                ],
            ],
            200,
        );
    }

    public function me(Request $request)
    {
        $user = $request->user();
        if (!$user instanceof Pengguna) {
            return response()->json(
                [
                    "status" => false,
                    "message" => "Unauthenticated",
                ],
                401,
            );
        }

        $data = Pengguna::query()
            ->select(
                "kode_pengguna",
                "nama_login",
                "nama_pengguna",
                "role.nama_role as role",
            )
            ->join("role", "pengguna.id_role", "=", "role.id_role")
            ->where("kode_pengguna", $user->kode_pengguna)
            ->first();

        return response()->json(
            [
                "status" => true,
                "data" => $data,
            ],
            200,
        );
    }

    public function logout(Request $request)
    {
        Auth::guard("auth_divisi_siska")->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(
            [
                "status" => true,
                "message" => "Logout successful",
            ],
            200,
        );
    }
}
