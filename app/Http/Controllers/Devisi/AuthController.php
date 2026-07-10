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
        // Sanctum SPA Cookie (session-based) login.
        Auth::guard("auth_divisi_siska")->login($datapengguna);

        // Session is only available for stateful SPA requests (cookie + CSRF).
        if (!$this->requestHasSessionStore($request)) {
            Auth::guard("auth_divisi_siska")->logout();

            $payload = [
                "status" => false,
                "message" =>
                    "Session not available. Use Sanctum SPA cookie auth (call /sanctum/csrf-cookie, send credentials, and ensure your domain is in SANCTUM_STATEFUL_DOMAINS).",
            ];

            if (config("app.debug")) {
                $payload["debug"] = [
                    "origin" => $request->headers->get("origin"),
                    "referer" => $request->headers->get("referer"),
                    "host" => $request->getHost(),
                    "has_session" => $this->requestHasSessionStore($request),
                    "cookie_names" => array_keys($request->cookies->all()),
                ];
            }

            return response()->json($payload, 400);
        }

        $request->session()->regenerate();

        return response()->json(
            [
                "status" => true,
                "message" => "Login successful",
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
