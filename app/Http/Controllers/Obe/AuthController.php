<?php

namespace App\Http\Controllers\Obe;

use App\Http\Controllers\Api\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Dosen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use ApiResponse;

    private function requestHasSessionStore(Request $request): bool
    {
        return method_exists($request, "hasSession") && $request->hasSession();
    }

    public function login(Request $request)
    {
        $validasi = $request->validate([
            "email" => "required|string",
            "password" => "required|string",
        ]);
        $email = $validasi["email"];
        $data = Dosen::select(
            "kode_dosen",
            "nama_dosen",
            "alamat_email",
            "sandi_pengguna",
            "status_login",
        )
            ->where("alamat_email", $email)
            ->first();
        if (!$data) {
            return response()->json(
                [
                    "status" => false,
                    "message" => "Email atau Password Tidak Valid",
                ],
                401,
            );
        }
        if (md5($validasi["password"]) !== $data->sandi_pengguna) {
            return response()->json(
                [
                    "status" => false,
                    "message" => "Email atau Password Tidak Valid",
                ],
                401,
            );
        }

        // Sanctum SPA Cookie (session-based) login.
        Auth::guard("dosen_web")->login($data);

        // Session is only available for stateful SPA requests (cookie + CSRF).
        if (!$this->requestHasSessionStore($request)) {
            Auth::guard("dosen_web")->logout();

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
        if (!$user instanceof Dosen) {
            return response()->json(
                [
                    "status" => false,
                    "message" => "Unauthenticated",
                ],
                401,
            );
        }

        $dosen = Dosen::query()
            ->select(
                "kode_dosen",
                "nama_dosen",
                "field_studi",
                "alumni",
                "nik",
                "no_telp",
                "status_dosen",
                "program_studi.nama_program_studi",
                "alamat_email",
                "status_login",
            )
            ->leftJoin(
                "program_studi",
                "dosen.homebase",
                "=",
                "program_studi.kode_program_studi",
            )
            ->where("dosen.kode_dosen", $user->getKey())
            ->first();

        return response()->json(
            [
                "status" => true,
                "data" => $dosen,
            ],
            200,
        );
    }

    public function logout(Request $request)
    {
        // Sanctum SPA Cookie (session-based) logout.
        Auth::guard("dosen_web")->logout();

        if ($this->requestHasSessionStore($request)) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return response()->json(
            [
                "status" => true,
                "message" => "Logout successful",
            ],
            200,
        );
    }
}
