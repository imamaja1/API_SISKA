<?php

namespace App\Http\Controllers\Simortua;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mahasiswa;

class AuthController extends Controller
{
    public function LoginNim(Request $request)
    {
        // pengecekan nim saja, jika ada true, jika tidak false
        $validasi = $request->validate([
            'nim' => 'required|string'
        ]);
        $nim = $validasi['nim'];
        // cek di database
        $mahasiswa = Mahasiswa::where('nim', $nim)->first();
        if ($mahasiswa) {
            return response()->json([
                'status' => true,
                'message' => 'NIM ditemukan',
                'data' => [
                    'nim' => $mahasiswa->nim,
                    'nama' => $mahasiswa->nama,
                ]
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'NIM tidak ditemukan',
            ]);
        }
    }
    public function LoginPassword(Request $request)
    {
        // jika nim sudah ada pengecekan password berupa tanggal lahir
        // setelah itu generate token scentume
        $validasi = $request->validate([
            'nim' => 'required|string',
            'password' => 'required|string'
        ]);
        $nim = $validasi['nim'];
        $password = $validasi['password'];
        // cek di database
        $mahasiswa = Mahasiswa::where('nim', $nim)
                                ->where('tanggal_lahir', $password)
                                ->first();
        if ($mahasiswa) {
            // Revoke old tokens (optional - remove if you want multiple active tokens)
            $mahasiswa->tokens()->delete();
            $token = $mahasiswa->createToken('api-token', ['*'])->plainTextToken;
            return response()->json([
                'status' => true,
                'message' => 'Login berhasil',
                'data' => [
                    'token' => $token,
                    'nim' => $mahasiswa->nim,
                    'nama' => $mahasiswa->nama_mahasiswa,
                ]
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'NIM atau password salah',
            ]);
        }
    }
}