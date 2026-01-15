<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Services\Kurikulum;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PetikanNilai extends Controller
{
    use ApiResponse;

    public function GetPetikanNilai(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nim' => 'required|string|max:11',
        ]);
        if ($validator->fails()) {
            return $this->error('Validation Error', 422, $validator->errors()->toArray());
        }
        $nim = $request->input('nim');
        $angkatan = 20 .substr($nim, 0, 2);
        $prodi = Mahasiswa::where('nim', $nim)->get()->first()->program_studi_kode;
        $kurikulumService = new Kurikulum;
        $kurikulumInfo = $kurikulumService->getKurikulumInfo($prodi, $angkatan, $nim);

        return $this->success(
            $kurikulumInfo,
            'Petikan Nilai URL retrieved successfully'
        );
    }
}
