<?php

namespace App\Services;

use App\Models\Kurikulum as KurikulumModel;
use App\Models\KurikulumAngkatan;

class Kurikulum
{
    public function getKurikulumInfo($prodiKode, $angkatan, $nim)
    {
        $data = KurikulumAngkatan::select(
            'angkatan',
            'nama_kurikulum',
            'kode_program_studi',
            'kurikulum_angkatan.kode_nama_kurikulum'
        )
            ->with('program_studi')
            ->join('nama_kurikulum', 'kurikulum_angkatan.kode_nama_kurikulum', '=', 'nama_kurikulum.kode_nama_kurikulum')
            ->where('angkatan', $angkatan)
            ->where('kode_program_studi', $prodiKode)
            ->firstOrFail();

        $kurikulum = KurikulumModel::select('id_matakuliah')
            ->with([
                'matakuliah',
                'nilai' => function ($query) use ($nim) {
                    $query->where('krs.nim', $nim);
                },
            ])
            ->where('kode_nama_kurikulum', $data->kode_nama_kurikulum)
            ->get();

        return [
            'info_kurikulum' => $data,
            'kurikulum' => $kurikulum,
        ];
    }
}
