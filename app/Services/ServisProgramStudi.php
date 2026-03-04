<?php

namespace App\Services;

use App\Models\ProgramStudi;
use Illuminate\Support\Facades\Crypt;

class ServisProgramStudi
{
    public function __construct()
    {
        //
    }

    public function getProgramStudi()
    {
        try {
            $data = ProgramStudi::query()
                ->select([
                    'kode_program_studi as id',
                    'kode_program_studi as kode',
                    'nama_program_studi',
                ])
                ->orderBy('nama_program_studi')
                ->get()
                ->map(function ($item) {
                    $item->kode = Crypt::encryptString((string) $item->kode);

                    return $item;
                })
                ->values();

            if ($data->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Program Studi Tidak Ditemukan',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Program Studi Ditemukan',
                'data' => $data,
            ], 200);
        } catch (\Throwable $e) {
            report($e);

            $payload = [
                'status' => false,
                'message' => 'Internal Server Error',
            ];

            if (config('app.debug')) {
                $payload['debug'] = [
                    'error' => $e->getMessage(),
                ];
            }

            return response()->json($payload, 500);
        }
    }
}
