<?php

namespace App\Services;

use App\Models\StatusPerkuliahan;
use Illuminate\Support\Facades\DB;

class ServisChart
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function chart_pengumpulan_krs($tahunAkademik)
    {
        $data = StatusPerkuliahan::where('kode_tahun_akademik', $tahunAkademik)
            ->select(
                'status_perkuliahan.nim',
                'pengumpulan_krs',
            )
            ->get();

        $data_sudah = $data->where('pengumpulan_krs', '1')->count();
        $data_belum = $data->where('pengumpulan_krs', '0')->count();

        return [
            'sudah' => $data_sudah,
            'belum' => $data_belum,
        ];
    }

    public function chart_pengumpulan_krs_by_prodi($tahunAkademik)
    {
        $data = StatusPerkuliahan::where('kode_tahun_akademik', $tahunAkademik)
            ->join('mahasiswa', 'status_perkuliahan.nim', '=', 'mahasiswa.nim')
            ->join('program_studi', 'mahasiswa.program_studi_kode', '=', 'program_studi.kode_program_studi')
            ->select(
                'program_studi.nama_program_studi',
                'pengumpulan_krs',
            )
            ->get()
            ->groupBy('nama_program_studi')
            ->map(function ($item) {
                $sudah = $item->where('pengumpulan_krs', '1')->count();
                $belum = $item->where('pengumpulan_krs', '0')->count();

                return [
                    'sudah' => $sudah,
                    'belum' => $belum,
                ];
            });

        return $data;
    }

    public function chart_kumpulan_krs_by_tahun_angkatan($tahunAkademik)
    {
        $data = StatusPerkuliahan::where('kode_tahun_akademik', $tahunAkademik)
            ->join('mahasiswa', 'status_perkuliahan.nim', '=', 'mahasiswa.nim')
            ->select(
                'mahasiswa.nim',
                DB::raw('LEFT(mahasiswa.nim, 2) as tahun_angkatan'),
                'pengumpulan_krs',
            )
            ->get()
            ->groupBy('tahun_angkatan')
            ->map(function ($item) {
                $sudah = $item->where('pengumpulan_krs', '1')->count();
                $belum = $item->where('pengumpulan_krs', '0')->count();

                return [
                    'sudah' => $sudah,
                    'belum' => $belum,
                ];
            });

        return $data;
    }

    public function chart_pengumpulan_krs_by_prodi_angkatan($tahunAkademik)
    {
        $rows = StatusPerkuliahan::where('kode_tahun_akademik', $tahunAkademik)
            ->join('mahasiswa', 'status_perkuliahan.nim', '=', 'mahasiswa.nim')
            ->join('program_studi', 'mahasiswa.program_studi_kode', '=', 'program_studi.kode_program_studi')
            ->select(
                'program_studi.nama_program_studi',
                DB::raw('LEFT(mahasiswa.nim, 2) as tahun_angkatan'),
                'pengumpulan_krs',
            )
            ->get();

        $data = $rows
            ->groupBy('nama_program_studi')
            ->map(function ($items) {
                $angkatan = $items
                    ->groupBy('tahun_angkatan')
                    ->map(function ($item) {
                        $sudah = $item->where('pengumpulan_krs', '1')->count();
                        $belum = $item->where('pengumpulan_krs', '0')->count();

                        return [
                            'sudah' => $sudah,
                            'belum' => $belum,
                        ];
                    });

                return [
                    'angkatan' => $angkatan,
                ];
            });

        return $data;
    }
}
