<?php

namespace App\Http\Controllers\Api\Obe;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Kelas;
use App\Models\KelasMahasiswa;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
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
            'semester',
            'kelas.kelas_id as code_kelas',
            'kelas.nama_kelas_id',
            'nama_kelas',
            'id_matakuliah',
        )->with('matakuliah')
            ->join('mengajar', 'kelas.kelas_id', '=', 'mengajar.kelas_id')
            ->join('nama_kelas', 'kelas.nama_kelas_id', '=', 'nama_kelas.nama_kelas_id')
            ->where('mengajar.kode_dosen', $kode_dosen)
            ->where('kelas.kode_tahun_akademik', $this->TahunAkademik)
            ->limit(10)
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
