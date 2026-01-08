<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\TahunAkademik;
use App\Http\Controllers\Api\Traits\ApiResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Exception;


class TahunAkademikController extends Controller
{
    use ApiResponse;

    public function GetTahunAkademik(Request $request): JsonResponse
    {
        $data = TahunAkademik::select(
            'tahun_akademik',
            DB::raw('SUBSTRING(tahun_akademik, 1, 4) as periode'),
            'semester',
            'status'
        )->orderByDesc('kode_tahun_akademik')->get();
        return $this->success(['data' => $data], 'Data Tahun Akademik retrieved successfully');
    }
    public function CreateTahunAkademik(Request $request): JsonResponse
    {
        $rules = [
            'tahun_akademik' => 'required|string|max:255',
            'semester' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_berakhir' => 'required|date',
        ];

        $validator = Validator::make($request->all(), $rules);
       
        if ($validator->fails()) {
            return $this->error('Validation Error', 422, $validator->errors()->toArray());
        }

        $validated = $validator->validated();
        $validated['kode_pengguna'] = $request->user()->id;
        $validated['status'] = 'N';

        try {
            $tahunAkademik = TahunAkademik::create($validated);
        } catch (QueryException $e) {
            return $this->error('Database Error', 500, ['exception' => $e->getMessage()]);
        } catch (Exception $e) {
            return $this->error('Server Error', 500, ['exception' => $e->getMessage()]);
        }

        return $this->success([
            'data' => $tahunAkademik->select(
                'tahun_akademik',
                'semester',
                'tanggal_mulai',
                'tanggal_berakhir',
            ),
        ], 'Tahun Akademik created successfully', 201);
    }
    public function ShowTahunAkademik(Request $request): JsonResponse
    {
        try {
            $tahunAkademik = TahunAkademik::where('kode_tahun_akademik',$request->route('id'))
                                            ->select(
                                                'tahun_akademik',
                                                'semester',
                                                'tanggal_mulai',
                                                'tanggal_berakhir',
                                                'status',
                                            )
                                            ->first();
            if ($tahunAkademik === null) {
                return $this->error('Tahun Akademik not found', 404);
            }
        } catch (Exception $e) {
            return $this->error('Tahun Akademik not found', 404);
        }

        return $this->success([
            'data' => $tahunAkademik,
        ], 'Tahun Akademik retrieved successfully');
    }
    public function UpdateTahunAkademik(Request $request): JsonResponse
    {
        $rules = [
            'tahun_akademik' => 'required|string|max:255',
            'semester' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_berakhir' => 'required|date',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->error('Validation Error', 422, $validator->errors()->toArray());
        }
        $validated = $validator->validated();
        $validated['kode_pengguna'] = $request->user()->id;
        try {
            $tahunAkademik = TahunAkademik::where('kode_tahun_akademik', $request->route('id'))->first();
            if ($tahunAkademik === null) {
                return $this->error('Tahun Akademik not found', 404);
            }
            $tahunAkademik->update($validated);
        } catch (QueryException $e) {
            return $this->error('Database Error', 500, ['exception' => $e->getMessage()]);
        } catch (Exception $e) {
            return $this->error('Server Error', 500, ['exception' => $e->getMessage()]);
        }
        return $this->success([
            'data' => $tahunAkademik->refresh()->only([
                'tahun_akademik',
                'semester',
                'tanggal_mulai',
                'tanggal_berakhir',
                'status',
            ])
        ], 'Tahun Akademik updated successfully');
    }
    public function DeleteTahunAkademik(Request $request): JsonResponse
    {
        try {
            $tahunAkademik = TahunAkademik::where('kode_tahun_akademik', $request->route('id'))->first();
            if ($tahunAkademik === null) {
                return $this->error('Tahun Akademik not found', 404);
            }
            if ($tahunAkademik->status === "A") {
                return $this->error('Tahun Akademik status Aktif, can`t delete', 404);
            }
            $tahunAkademik->delete();
        } catch (QueryException $e) {
            return $this->error('Database Error', 500, ['exception' => $e->getMessage()]);
        } catch (Exception $e) {
            return $this->error('Server Error', 500, ['exception' => $e->getMessage()]);
        }
        return $this->success([
                'data' => $tahunAkademik->refresh()->only([
                    'tahun_akademik',
                    'semester',
                    'tanggal_mulai',
                    'tanggal_berakhir',
                    'status',
                ])
            ], 'Tahun Akademik deleted successfully'
        );
    }
    public function UpdateStatusTahunAkademik(Request $request): JsonResponse
    {
        $rules = [
            'status' => 'required|string|in:A,N',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->error('Validation Error', 422, $validator->errors()->toArray());
        }
        $validated = $validator->validated();
        try {
            $tahunAkademik = TahunAkademik::where('kode_tahun_akademik', $request->route('id'))->first();
            if ($tahunAkademik === null) {
                return $this->error('Tahun Akademik not found', 404);
            }
            if ($tahunAkademik->status === "A" && $validated['status'] === "N") {
                return $this->error('Cannot deactivate an active Tahun Akademik', 400);
            }else{
                if ($validated['status'] === "A") {
                    TahunAkademik::where('status', 'A')->update(['status' => 'N']);
                }
            }
            $tahunAkademik->update($validated);
        } catch (QueryException $e) {
            return $this->error('Database Error', 500, ['exception' => $e->getMessage()]);
        } catch (Exception $e) {
            return $this->error('Server Error', 500, ['exception' => $e->getMessage()]);
        }
        return $this->success([
            'data' => $tahunAkademik->refresh()->only([
                'tahun_akademik',
                'semester',
                'tanggal_mulai',
                'tanggal_berakhir',
                'status',
            ])
        ], 'Tahun Akademik status updated successfully');
    }
}
