<?php

namespace App\Services;

use App\Models\TahunAkademik;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class ServisTahunAkademik
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function getTahunAkademik()
    {
        $data = TahunAkademik::query()
            ->select([
                'kode_tahun_akademik as id',
                'kode_tahun_akademik as kode',
                'tahun_akademik',
                DB::raw("CASE WHEN semester = 1 THEN 'Ganjil' ELSE 'Genap' END as semester"),
                'status',
            ])
            ->orderByDesc('kode_tahun_akademik')
            ->get()
            ->map(function ($item) {
                $item->kode = Crypt::encryptString((string) $item->kode);

                return $item;
            })
            ->values();
        if ($data->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Tahun Akademik Tidak Ditemukan',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Tahun Akademik Ditemukan',
            'data' => $data,
        ], 200);
    }

    public function getTahunAkademikAktif()
    {
        $data = TahunAkademik::query()
            ->where('status', 'A')
            ->select([
                'kode_tahun_akademik as id',
                'kode_tahun_akademik as kode',
                'tahun_akademik',
                DB::raw("CASE WHEN semester = 1 THEN 'Ganjil' ELSE 'Genap' END as semester"),
                'status',
            ])
            ->first();

        if (! $data) {
            return response()->json([
                'status' => false,
                'message' => 'Tahun Akademik Aktif Tidak Ditemukan',
            ], 404);
        }

        $data->kode = Crypt::encryptString((string) $data->kode);

        return response()->json([
            'status' => true,
            'message' => 'Tahun Akademik Aktif Ditemukan',
            'data' => $data,
        ], 200);
    }

    public function getTahunAkademikByKode($kode)
    {
        $kode = Crypt::decryptString($kode);
        $data = TahunAkademik::query()
            ->where('kode_tahun_akademik', $kode)
            ->select([
                'kode_tahun_akademik as id',
                'kode_tahun_akademik as kode',
                'tahun_akademik',
                DB::raw("CASE WHEN semester = 1 THEN 'Ganjil' ELSE 'Genap' END as semester"),
                'status',
            ])
            ->first();

        if (! $data) {
            return response()->json([
                'status' => false,
                'message' => 'Tahun Akademik Tidak Ditemukan',
            ], 404);
        }

        $data->kode = Crypt::encryptString((string) $data->kode);

        return response()->json([
            'status' => true,
            'message' => 'Tahun Akademik Ditemukan',
            'data' => $data,
        ], 200);
    }
}
