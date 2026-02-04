<?php

namespace App\Http\Controllers\Api\Obe;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Kelas;
use App\Models\KelasMahasiswa;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\PersonalAccessToken;

class PenilaianObeController extends Controller
{
    private $TahunAkademik;

    public function __construct()
    {
        $this->TahunAkademik = TahunAkademik::where('status', 'A')->first()->kode_tahun_akademik;
    }

    public function me(Request $request)
    {
        // ambil data dosen mengunakan Request
        $plainTextToken = $request->bearerToken();
        $accessToken = PersonalAccessToken::findToken($plainTextToken);
        $dosen = $accessToken->tokenable;

        return response()->json([
            'status' => true,
            'data' => $dosen,
        ], 200);
    }

    public function kelas(Request $request)
    {
        $plainTextToken = $request->bearerToken();
        $accessToken = PersonalAccessToken::findToken($plainTextToken);
        $dosen = $accessToken->tokenable;
        $kode_dosen = $dosen->getKey();

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
            ->having('jumlah_mahasiswa', '>', 0) // 👈 minimal 1 mahasiswa
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
        $request->validate([
            'code_kelas' => 'required|string',
        ]);
        $kode_kelas = Crypt::decryptString($request->code_kelas);

        $data = KelasMahasiswa::select('*')
            ->with('mahasiswa', 'matakuliah', 'nilai')
            ->where('kelas_id', $kode_kelas)->get();

        return response()->json([
            'status' => true,
            'data' => $data,
        ], 200);
    }
}
