<?php

namespace App\Http\Controllers\Api\Feeder;

use App\Http\Controllers\Api\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\FeederSyncLog;
use App\Models\KHSDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SyncController extends Controller
{
    use ApiResponse;

    public function sync(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.kode_khs_detail' => 'required|string',
            'items.*.nilai_harian' => 'required|numeric|min:0|max:100',
            'items.*.nilai_uts' => 'required|numeric|min:0|max:100',
            'items.*.nilai_uas' => 'required|numeric|min:0|max:100',
            'items.*.nilai_akhir' => 'required|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation Error', 422, $validator->errors()->toArray());
        }

        $items = $request->input('items');
        $updated = 0;
        $unchanged = 0;
        $failed = 0;
        $details = [];

        DB::beginTransaction();

        try {
            foreach ($items as $item) {
                try {
                    $kodeKhsDetail = Crypt::decryptString($item['kode_khs_detail']);
                } catch (\Exception $e) {
                    $failed++;
                    $details[] = [
                        'kode_khs_detail' => $item['kode_khs_detail'],
                        'status' => 'failed',
                        'keterangan' => 'kode_khs_detail tidak valid',
                    ];

                    continue;
                }

                $khsDetail = KHSDetail::where('kode_khs_detail', $kodeKhsDetail)->first();

                if (! $khsDetail) {
                    $failed++;
                    $details[] = [
                        'kode_khs_detail' => $item['kode_khs_detail'],
                        'status' => 'failed',
                        'keterangan' => 'Data tidak ditemukan',
                    ];

                    continue;
                }

                $nilaiHarian = (float) $item['nilai_harian'];
                $nilaiUts = (float) $item['nilai_uts'];
                $nilaiUas = (float) $item['nilai_uas'];
                $nilaiAkhir = (float) $item['nilai_akhir'];

                $isUnchanged = (float) $khsDetail->nilai_harian === $nilaiHarian
                    && (float) $khsDetail->nilai_uts === $nilaiUts
                    && (float) $khsDetail->nilai_uas === $nilaiUas
                    && (float) $khsDetail->nilai_akhir === $nilaiAkhir;

                if ($isUnchanged) {
                    $unchanged++;
                    $details[] = [
                        'kode_khs_detail' => $item['kode_khs_detail'],
                        'status' => 'unchanged',
                        'keterangan' => 'Tidak ada perubahan',
                    ];

                    continue;
                }

                $nilaiHarianSiska = (float) $khsDetail->nilai_harian;
                $nilaiUtsSiska = (float) $khsDetail->nilai_uts;
                $nilaiUasSiska = (float) $khsDetail->nilai_uas;
                $nilaiAkhirSiska = (float) $khsDetail->nilai_akhir;

                KHSDetail::where('kode_khs_detail', $kodeKhsDetail)->update([
                    'nilai_harian' => $nilaiHarian,
                    'nilai_uts' => $nilaiUts,
                    'nilai_uas' => $nilaiUas,
                    'nilai_akhir' => $nilaiAkhir,
                ]);

                $updated++;
                $details[] = [
                    'kode_khs_detail' => $item['kode_khs_detail'],
                    'nilai_sebelum' => [
                        'nilai_harian' => $nilaiHarianSiska,
                        'nilai_uts' => $nilaiUtsSiska,
                        'nilai_uas' => $nilaiUasSiska,
                        'nilai_akhir' => $nilaiAkhirSiska,
                    ],
                    'nilai_sesudah' => [
                        'nilai_harian' => $nilaiHarian,
                        'nilai_uts' => $nilaiUts,
                        'nilai_uas' => $nilaiUas,
                        'nilai_akhir' => $nilaiAkhir,
                    ],
                    'grade_sebelum' => $this->hitungGrade($nilaiAkhirSiska),
                    'grade_sesudah' => $this->hitungGrade($nilaiAkhir),
                    'status' => 'updated',
                    'keterangan' => 'Berhasil diupdate',
                ];
            }

            $totalData = count($items);

            FeederSyncLog::create([
                'tipe' => 'sync',
                'tipe_sync' => 'sync',
                'referensi' => $items[0]['kode_khs_detail'] ?? '',
                'jumlah_data_feeder' => $totalData,
                'jumlah_data_siska' => $totalData,
                'jumlah_sync' => $updated,
                'jumlah_gagal' => $failed,
                'status' => $failed === 0 ? 'success' : ($updated === 0 ? 'failed' : 'partial'),
                'synced_by' => $request->user()->id,
                'log_detail' => $details,
            ]);

            DB::commit();

            return $this->success([
                'data' => [
                    'message' => 'Data berhasil diupdate',
                    'total_updated' => $updated,
                ],
            ], 'Sync berhasil');
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->error('Sync gagal: '.$e->getMessage(), 500);
        }
    }

    public function history(Request $request): JsonResponse
    {
        $query = FeederSyncLog::with('syncedBy:id,name');

        if ($request->query('tipe_sync')) {
            $query->where('tipe_sync', $request->query('tipe_sync'));
        }

        if ($request->query('referensi')) {
            $query->where('referensi', $request->query('referensi'));
        }

        $perPage = min((int) $request->query('per_page', 15), 100);

        $data = $query->orderByDesc('id')->paginate($perPage);

        return $this->success([
            'data' => $data,
        ], 'History berhasil diambil');
    }

    private function hitungGrade(float $nilai): string
    {
        return match (true) {
            $nilai >= 81 => 'A',
            $nilai >= 71 => 'B+',
            $nilai >= 66 => 'B',
            $nilai >= 61 => 'C+',
            $nilai >= 50 => 'C',
            $nilai >= 30 => 'D',
            default => 'E',
        };
    }
}
