<?php

namespace App\Http\Controllers\Api\Obe;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Kelas;
use App\Models\KelasMahasiswa;
use App\Models\TahunAkademik;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class PenilaianObeController extends Controller
{
    private $TahunAkademik;

    public function __construct()
    {
        $this->TahunAkademik = TahunAkademik::where('status', 'A')->first()->kode_tahun_akademik;
    }

    public function me(Request $request)
    {
        // Ambil user dari Sanctum session/cookie auth
        $user = $request->user();
        if (! $user instanceof Dosen) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        return response()->json([
            'status' => true,
            'data' => $user,
        ], 200);
    }

    public function kelas(Request $request)
    {
        // Ambil user dari session/cookie auth (guard: dosen_web).
        // Middleware `auth:dosen_web` akan membuat $request->user() mengacu ke guard tersebut.
        $user = $request->user();
        if (! $user instanceof Dosen) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        $kode_dosen = $user->getKey();

        $data = Kelas::select(
            'kode_dosen',
            'kelas.kelas_id as code_kelas',
            'matakuliah.nama_matakuliah',
            'kelas.nama_kelas_id',
            'nama_kelas',
            'matakuliah.id_matakuliah',
            DB::raw('COUNT(kelas_mahasiswa.kelas_id) as jumlah_mahasiswa')
        )
            ->join('mengajar', 'kelas.kelas_id', '=', 'mengajar.kelas_id')
            ->join('nama_kelas', 'kelas.nama_kelas_id', '=', 'nama_kelas.nama_kelas_id')
            ->join('matakuliah', 'kelas.id_matakuliah', '=', 'matakuliah.id_matakuliah')
            ->join('kelas_mahasiswa', 'kelas.kelas_id', '=', 'kelas_mahasiswa.kelas_id')
            ->join('krs_detail', 'kelas_mahasiswa.kode_krs_detail', '=', 'krs_detail.kode_krs_detail')
            ->join('krs', 'krs_detail.kode_krs', '=', 'krs.kode_krs')
            ->where('mengajar.kode_dosen', $kode_dosen)
            ->where('kelas.kode_tahun_akademik', $this->TahunAkademik)
            ->whereRaw('SUBSTR(krs.nim, 1, 2) > 24')
            ->groupBy(
                'kode_dosen',
                'kelas.semester',
                'kelas.kelas_id',
                'kelas.nama_kelas_id',
                'nama_kelas',
                'matakuliah.id_matakuliah',
                'matakuliah.nama_matakuliah'
            )
            ->having('jumlah_mahasiswa', '>', 0)
            ->get()
            ->map(function ($item) {
                $item->code_kelas = Crypt::encryptString($item->code_kelas);

                return $item;
            });

        return response()->json([
            'status' => true,
            'data' => $data,
        ], 200);
    }

    public function penilaian(Request $request)
    {
        $validated = $request->validate([
            'code_kelas' => 'required|string',
        ]);

        try {
            $kode_kelas = Crypt::decryptString($validated['code_kelas']);
        } catch (DecryptException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid code_kelas',
            ], 422);
        }

        $data = KelasMahasiswa::select(
            'khs_detail.kode_khs_detail as id',
            'mahasiswa.nim',
            'mahasiswa.nama_mahasiswa',
            'khs_detail.nilai_harian',
            'khs_detail.nilai_uts',
            'khs_detail.nilai_uas',
            'khs_detail.nilai_akhir',
        )
            ->join('krs_detail', 'kelas_mahasiswa.kode_krs_detail', '=', 'krs_detail.kode_krs_detail')
            ->join('khs_detail', 'krs_detail.kode_krs_detail', '=', 'khs_detail.kode_krs_detail')
            ->join('krs', 'krs_detail.kode_krs', '=', 'krs.kode_krs')
            ->join('mahasiswa', 'krs.nim', '=', 'mahasiswa.nim')
            ->limit(60)
            ->where('kelas_id', $kode_kelas)->get()
            ->map(function ($item) {
                $item->id = Crypt::encryptString($item->id);

                return $item;
            });

        return response()->json([
            'status' => true,
            'data' => $data,
        ], 200);
    }
}
