<?php

namespace App\Http\Controllers\Api\Divisi;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Kelas;
use App\Models\Kurikulum;
use App\Models\KurikulumAngkatan;
use App\Models\Mahasiswa;
use App\Models\Matakuliah;
use App\Models\NamaKurikulum;
use App\Models\TahunAkademik;

class KedokteranController extends Controller
{
    public function get_mhs_kedokteran()
    {
        $data = Mahasiswa::where('program_studi_kode', '23')->get();

        return response()->json([
            'status' => true,
            'data' => $data,
        ], 200);
    }

    public function get_dosen_kedokteran()
    {
        $data = Dosen::where('homebase', '23')->get();

        return response()->json([
            'status' => true,
            'data' => $data,
        ], 200);
    }

    public function get_tahun_akademik()
    {
        $data = TahunAkademik::all();

        return response()->json([
            'status' => true,
            'data' => $data,
        ], 200);
    }

    public function get_krs_khs()
    {
        $data = Mahasiswa::select('nim')->where('program_studi_kode', '23')
            ->with('krs', 'krs_detail')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $data,
        ], 200);
    }

    public function get_matakuliah()
    {
        $data = Matakuliah::select('*', 'id_matakuliah as id')
            ->where('kode_program_studi', '23')->get();

        return response()->json([
            'status' => true,
            'data' => $data,
        ], 200);
    }

    public function get_kelas()
    {
        $data = Kelas::with('nama_kelas_kedokteran', 'dosen_kedokteran', 'mahasiswa_kedokteran')
            ->select('kelas.kelas_id', 'kelas.nama_kelas_id', 'kelas.semester', 'kelas.kode_tahun_akademik', 'kelas.kode_program_studi', 'kelas.id_matakuliah as kode_matakuliah')
            ->where('kelas.kode_program_studi', '23')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $data,
        ], 200);
    }

    public function get_kurikulum()
    {
        $data['nama_kurikulum'] = NamaKurikulum::where('kode_program_studi', '23')->get();
        $data['kurikulum'] = Kurikulum::whereIn('kode_nama_kurikulum', $data['nama_kurikulum']->pluck('kode_nama_kurikulum'))
            ->get();
        $data['kurikulum_angkatan'] = KurikulumAngkatan::whereIn('kode_nama_kurikulum', $data['nama_kurikulum']->pluck('kode_nama_kurikulum'))
            ->get();

        return response()->json([
            'status' => true,
            'data' => $data,
        ], 200);
    }
}
