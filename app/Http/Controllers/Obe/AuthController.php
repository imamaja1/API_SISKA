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
        $data = Dosen::where('alamat_email', $email)->first();
        if (! $data) {
            return $this->errorResponse(message: 'Dosen tidak ditemukan', status: 404);
        }
        if (! password_verify($validasi['password'], $data->sandi_pengguna)) {
            return $this->errorResponse(message: 'Email atau Password Tidak Valid', status: 401);
        }

        $data->tokens()->delete();
        $token = $data->createToken('api-token', ['*'])->plainTextToken;

        return $this->success([
            'user' => $data,
            'token' => $token,
        ], 'Login successful');
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success(message: 'Logout successful');
    }
}
