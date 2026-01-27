<?php

namespace App\Http\Controllers\Obe;

use App\Http\Controllers\Api\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Dosen;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use ApiResponse;

    public function login(Request $request)
    {
        $validasi = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);
        $email = $validasi['email'];
        $data = Dosen::select(
            'kode_dosen',
            'nama_dosen',
            'alamat_email',
            'sandi_pengguna',
            'status_login',
        )
            ->where('alamat_email', $email)
            ->where('sandi_pengguna', md5($validasi['password']))->first();
        if (! $data) {
            return response()->json([
                'status' => false,
                'message' => 'Email atau Password Tidak Valid',
            ], 401);
        }
        if (md5($validasi['password']) !== $data->sandi_pengguna) {
            return response()->json([
                'status' => false,
                'message' => 'Email atau Password Tidak Valid',
            ], 401);
        }

        $data->tokens()->delete();
        $token = $data->createToken('api-token', ['*'])->plainTextToken;

        return response()->json([
            'status' => true,
            'token' => $token,
        ], 200);
    }

    public function me(Request $request)
    {
        $dosen = $request->user()->select(
            'kode_dosen',
            'nama_dosen',
            'field_studi',
            'alumni',
            'nik',
            'no_telp',
            'status_dosen',
            'program_studi.nama_program_studi',
            'alamat_email',
            'status_login',
        )
            ->join('program_studi', 'dosen.homebase', '=', 'program_studi.kode_program_studi', 'left')
            ->first();

        return response()->json([
            'status' => true,
            'data' => $dosen,
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success(message: 'Logout successful');
    }
}
