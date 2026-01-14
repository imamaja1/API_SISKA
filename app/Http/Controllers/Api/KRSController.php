<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\KRSDetail;
use App\Models\TahunAkademik;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class KRSController extends Controller
{
    use ApiResponse;

    public function CekKRS(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nim' => 'required|string|max:11',
        ]);
        if ($validator->fails()) {
            return $this->error('Validation Error', 422, $validator->errors()->toArray());
        }
        $nim = $request->input('nim');
        $data = TahunAkademik::select(
            'tahun_akademik',
            'semester',
            'kode_tahun_akademik',
        )
            ->with(['CekKRS' => function ($query) use ($nim) {
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
            'KRS retrieved successfully'
        );
    }

    public function ShowKrs(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_krs' => 'required|string|max:11',
        ]);
        $kode_krs = $request->input('kode_krs');
        if ($validator->fails()) {
            return $this->error('Validation Error', 422, $validator->errors()->toArray());
        }
        $data = KRSDetail::select(
            'status',
            'id_matakuliah',
        )
            ->with('matakuliah')
            ->where('kode_krs', $kode_krs)
            ->get();

        return $this->success(
            $data,
            'KRS Detail retrieved successfully'
        );
    }
}
