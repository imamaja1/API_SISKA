<?php

namespace App\Http\Controllers\Simortua;

use App\Http\Controllers\Api\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use ApiResponse;

    public function LoginNim(Request $request)
    {
        $validasi = $request->validate([
            'nim' => 'required|string',
        ]);
        $nim = $validasi['nim'];
        $mahasiswa = Mahasiswa::where('nim', $nim)->first();
        if ($mahasiswa) {
            return response()->json([
                'status' => true,
                'message' => 'NIM ditemukan',
                'data' => [
                    'nim' => $mahasiswa->nim,
                    'nama' => $mahasiswa->nama_mahasiswa,
                ],
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'NIM tidak ditemukan',
            ]);
        }
    }

    public function LoginCredentials(Request $request): JsonResponse
    {
        $validasi = $request->validate([
            'nim' => 'required|string',
            'password' => 'required|date',
        ]);
        $nim = $validasi['nim'];

        try {
            $date = Carbon::parse($validasi['password'])->toDateString();
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Format tanggal lahir tidak valid',
            ], 422);
        }

        $mahasiswa = Mahasiswa::with('nama_prodi')
            ->select(
                'nim',
                'nama_mahasiswa',
                'program_studi_kode',
            )
            ->where('nim', $nim)
            ->whereDate('tanggal_lahir', $date)
            ->first();

        if (! $mahasiswa) {
            return response()->json([
                'status' => false,
                'message' => 'NIM atau tanggal lahir salah',
            ], 401);
        }

        $mahasiswa->tokens()->delete();
        $token = $mahasiswa->createToken('api-token', ['*'])->plainTextToken;

        return $this->success([
            'user' => $mahasiswa,
            'token' => $token,
        ], 'Login successful');
    }
}
