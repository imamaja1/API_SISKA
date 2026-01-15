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

class KHSController extends Controller
{
    use ApiResponse;

    public function CekKHS(Request $request): JsonResponse
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
            ->with(['CekKhs' => function ($query) use ($nim) {
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

    public function ShowKhs(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_krs' => 'required|string|max:11',
        ]);
        $kode_krs = $request->input('kode_krs');
        if ($validator->fails()) {
            return $this->error('Validation Error', 422, $validator->errors()->toArray());
        }
        $kode_sistem_penilaian = 1;
        $data = KRSDetail::select(
            'krs_detail.id_matakuliah',
            'khs_detail.nilai_akhir',
            'spd.grade as grade',
            'spd.bobot_nilai as bobot'
        )
            ->join('khs_detail', 'khs_detail.kode_krs_detail', '=', 'krs_detail.kode_krs_detail')
            ->join('sistem_penilaian_detail as spd', function ($join) use ($kode_sistem_penilaian) {
                $join->on('khs_detail.nilai_akhir', '>=', 'spd.nilai_minimum')
                    ->on('khs_detail.nilai_akhir', '<=', 'spd.nilai_maksimum')
                    ->where('spd.kode_sistem_penilaian', '=', $kode_sistem_penilaian);
            })
            ->with('matakuliah')
            ->where('krs_detail.kode_krs', $kode_krs)
            ->get();

        return $this->success(
            $data,
            'KRS Detail retrieved successfully'
        );
    }
}
