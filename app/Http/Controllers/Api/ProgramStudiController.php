<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\ProgramStudi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProgramStudiController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);
        if ($perPage < 1) {
            $perPage = 15;
        }
        if ($perPage > 100) {
            $perPage = 100;
        }

        $data = ProgramStudi::query()->paginate($perPage);

        return $this->success(
            ['data' => $data],
            'Data Program Studi retrieved successfully'
        );
    }

    public function show(Request $request, int $kode_program_studi): JsonResponse
    {
        $programStudi = ProgramStudi::query()
            ->where('kode_program_studi', $kode_program_studi)
            ->first();

        if ($programStudi === null) {
            return $this->error('Program Studi not found', 404);
        }

        return $this->success(
            ['data' => $programStudi],
            'Program Studi retrieved successfully'
        );
    }

    public function GetProgramStudi(): JsonResponse
    {
        $programStudi = ProgramStudi::select(
                            'kode_program_studi',
                            'nama_program_studi',
                            'singkatan_program_studi'
                        )->get();

        return $this->success(
            ['data' => $programStudi],
            'List of Program Studi retrieved successfully'
        );
    }
}
