<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Traits\ApiResponse;
use App\Models\Mahasiswa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MahasiswaController extends Controller
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

		$validator = Validator::make($request->query(), [
			'per_page' => 'sometimes|integer|min:1|max:100',
			'nim' => 'sometimes|string|max:11',
			'program_studi_kode' => 'sometimes|integer',
			'status_pendaftaran' => 'sometimes|in:B,T,L',
			'status' => 'sometimes|in:A,N',
            'angkatan' => 'sometimes|string|max:4',
		]);
		if ($validator->fails()) {
			return $this->error('Validation Error', 422, $validator->errors()->toArray());
		}

		$nim = $request->query('nim');
		$programStudiKode = $request->query('program_studi_kode');
		$statusPendaftaran = $request->query('status_pendaftaran');
		$status = $request->query('status');
        $angkatan = $request->query('angkatan');

		$query = Mahasiswa::query()
			->with('nama_prodi')
            ->select(
                'program_studi_kode',
                'nim',
                'nik',
                'nama_mahasiswa',
                'status',
                'status_pendaftaran'
			);

		if ($nim !== null && $nim !== '') {
			$query->where('nim', 'like' , $nim . '%');
		}

		if ($programStudiKode !== null && $programStudiKode !== '') {
			$query->where('program_studi_kode', (int) $programStudiKode);
		}

		if ($statusPendaftaran !== null && $statusPendaftaran !== '') {
			$query->where('status_pendaftaran', $statusPendaftaran);
		}

		if ($status !== null && $status !== '') {
			$query->where('status', $status);
		}

        if ($request->has('angkatan') && $request->query('angkatan') !== '') {
            $angkatan = substr($request->query('angkatan'), 2, 2);
            $query->where('nim', 'like', $angkatan . '%');
        }

		$data = $query
			->orderByDesc('nim')
			->paginate($perPage);

		return $this->success(
			['data' => $data],
			'Data Mahasiswa retrieved successfully'
		);
	}

	public function show(Request $request, string $nim): JsonResponse
	{
		$mahasiswa = Mahasiswa::query()->where('nim', $nim)->first();

		if ($mahasiswa === null) {
			return $this->error('Mahasiswa not found', 404);
		}

		return $this->success(
			['data' => $mahasiswa],
			'Mahasiswa retrieved successfully'
		);
	}
}
