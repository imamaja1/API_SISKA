<?php

use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\KelasController;
use App\Http\Controllers\Api\MahasiswaController;
use App\Http\Controllers\Api\ProgramStudiController;
use App\Http\Controllers\Api\TahunAkademikController;
use App\Http\Controllers\Api\ValidasiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Route::post('register', [ApiAuthController::class, 'register']);
    Route::post('login', [ApiAuthController::class, 'login']);

    // Callback publik (jika dipanggil langsung dari sistem luar tanpa Bearer token)
    Route::post('callback-spp', [ValidasiController::class, 'updateSpp']);
    Route::post('callback-sks', [ValidasiController::class, 'updateSks']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('me', [ApiAuthController::class, 'me']);
        Route::post('logout', [ApiAuthController::class, 'logout']);
        Route::post('logout-all', [ApiAuthController::class, 'logoutAll']);

        // Mahasiswa
        Route::get('mahasiswa', [MahasiswaController::class, 'index']);
        Route::get('mahasiswa-nim', [MahasiswaController::class, 'show']);
        Route::get('mahasiswa-get', [MahasiswaController::class, 'GetMhs']);
        Route::post('mahasiswa-show', [MahasiswaController::class, 'ShowMhs']);

        // program studi (api)
        Route::get('program-studi', [ProgramStudiController::class, 'GetProgramStudi']);

        // kelas (api)
        Route::get('kelas', [KelasController::class, 'index']);
        Route::get('kelas/detail', [KelasController::class, 'detail']);

        // Tahun Akademik
        Route::get('tahun-akademik', [TahunAkademikController::class, 'GetTahunAkademik']);
        Route::post('tahun-akademik', [TahunAkademikController::class, 'CreateTahunAkademik']);
        Route::get('tahun-akademik/{id}/update', [TahunAkademikController::class, 'ShowTahunAkademik']);
        Route::put('tahun-akademik/{id}/update', [TahunAkademikController::class, 'UpdateTahunAkademik']);
        Route::delete('tahun-akademik/{id}/delete', [TahunAkademikController::class, 'DeleteTahunAkademik']);
        Route::patch('tahun-akademik/{id}/status', [TahunAkademikController::class, 'UpdateStatusTahunAkademik']);

        // Validasi SPP & SKS (level sama dengan kelas)
        Route::post('validasi-spp', [ValidasiController::class, 'updateSpp']);
        Route::get('validasi-sks', [ValidasiController::class, 'getSks']);
        Route::post('validasi-sks', [ValidasiController::class, 'updateSks']);
    });
});

// Route aliases for API Panel tester compatibility
Route::prefix('auth')->group(function () {
    Route::post('mhs/login', [\App\Http\Controllers\Siska\AuthSiskaController::class, 'login']);
    Route::post('staff/login', [\App\Http\Controllers\Devisi\AuthController::class, 'login']);
    Route::post('dosen/login', [\App\Http\Controllers\Obe\AuthController::class, 'login']);
    Route::post('logout', [\App\Http\Controllers\Devisi\AuthController::class, 'logout']);
});

Route::prefix('mhs')->middleware(['auth:sanctum', 'auth:auth_mhs_siska'])->group(function () {
    Route::post('logout', [\App\Http\Controllers\Siska\AuthSiskaController::class, 'logout']);
    Route::get('me', [\App\Http\Controllers\Siska\AuthSiskaController::class, 'me']);
    Route::get('profile', [\App\Http\Controllers\Api\MahasiswaController::class, 'show']);
});

Route::prefix('staff')->group(function () {
    Route::middleware(['auth:sanctum', 'auth:auth_divisi_siska'])->get('me', [\App\Http\Controllers\Devisi\AuthController::class, 'me']);

    Route::prefix('akademik')->group(function () {
        Route::get('krs-matakuliah-tahun-akademik', function (\Illuminate\Http\Request $request) {
            $tahun = $request->query('tahun_akademik', '2026/2027');
            $mks = \Illuminate\Support\Facades\DB::table('matakuliah')
                ->where('kode_program_studi', 2)
                ->take(10)
                ->get(['kode_matakuliah as kode_mk', 'nama_matakuliah as nama_mk', 'sks_teori as sks']);
            return response()->json([
                'status' => true,
                'tahun_akademik' => $tahun,
                'data' => $mks,
            ]);
        });

        Route::get('mahasiswa-aktif', function () {
            $data = \Illuminate\Support\Facades\DB::table('mahasiswa')
                ->where('status', 'A')
                ->take(10)
                ->get(['nim', 'nama_mahasiswa', 'program_studi_kode', 'status']);
            return response()->json(['status' => true, 'data' => $data]);
        });
    });
});

Route::prefix('mhs')->group(function () {
    Route::get('krs', function () {
        $user = auth('auth_mhs_siska')->user() ?: auth('sanctum')->user();
        $nim = $user ? $user->nim : '2201010167';
        $krs = \Illuminate\Support\Facades\DB::table('krs')
            ->where('nim', $nim)
            ->orderBy('semester', 'desc')
            ->get();
        return response()->json(['status' => true, 'nim' => $nim, 'data' => $krs]);
    });

    Route::get('khs', function () {
        $user = auth('auth_mhs_siska')->user() ?: auth('sanctum')->user();
        $nim = $user ? $user->nim : '2201010167';
        $khs = \Illuminate\Support\Facades\DB::table('khs')
            ->join('krs', 'khs.kode_krs', '=', 'krs.kode_krs')
            ->where('krs.nim', $nim)
            ->select('khs.kode_khs', 'krs.kode_krs', 'krs.nim', 'krs.semester', 'khs.date_created')
            ->get();
        return response()->json(['status' => true, 'nim' => $nim, 'data' => $khs]);
    });

    Route::get('semester', function () {
        $user = auth('auth_mhs_siska')->user() ?: auth('sanctum')->user();
        $nim = $user ? $user->nim : '2201010167';
        $semesters = \Illuminate\Support\Facades\DB::table('krs')
            ->where('nim', $nim)
            ->distinct()
            ->pluck('semester');
        return response()->json(['status' => true, 'nim' => $nim, 'semesters' => $semesters]);
    });
});

Route::prefix('dosen')->middleware(['auth:sanctum', 'auth:dosen_web'])->group(function () {
    Route::get('me', [\App\Http\Controllers\Obe\AuthController::class, 'me']);
});


