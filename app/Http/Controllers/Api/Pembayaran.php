<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\TahunAkademik;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class Pembayaran extends Controller
{
    use ApiResponse;

    public function CheckPembayaran(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nim' => 'required|string|max:11',
        ]);
        $nim = $request->input('nim');
        if ($validator->fails()) {
            return $this->error('Validation Error', 422, $validator->errors()->toArray());
        }
        $data = TahunAkademik::select(
            'tahun_akademik',
            'semester',
            'kode_tahun_akademik',
        )
            ->with(['statusPerkuliahan' => function ($query) use ($nim) {
                $query->where('nim', $nim);
            }])
            ->where(
                DB::raw('SUBSTRING(tahun_akademik, 3, 2)'), '>=', substr($nim, 0, 2)
            )
            ->orderByDesc('kode_tahun_akademik')
            ->limit('14')
            ->get();

        return $this->success(
            $data,
            'Pembayaran retrieved successfully'
        );
    }
}
