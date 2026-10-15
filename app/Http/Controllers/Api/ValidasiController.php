<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\KRS;
use App\Models\KRSDetail;
use App\Models\Mahasiswa;
use App\Models\Matakuliah;
use App\Models\StatusPerkuliahan;
use App\Models\TahunAkademik;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class ValidasiController extends Controller
{
    use ApiResponse;

    /**
     * Callback / Update status validasi SPP mahasiswa.
     * POST /api/v1/validasi-spp
     */
    public function updateSpp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nim' => 'required|string|max:20',
            'status' => 'required',
            'tahun_akademik' => 'nullable|string',
            'semester' => 'nullable|string',
            'kode_tahun_akademik' => 'nullable',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation Error', 422, $validator->errors()->toArray());
        }

        try {
            $nim = trim((string) $request->input('nim'));
            $statusNormalized = $this->normalizeStatus($request->input('status'));

            $ta = $this->resolveTahunAkademik(
                $request->input('kode_tahun_akademik'),
                $request->input('tahun_akademik'),
                $request->input('semester')
            );
            if ($ta === null) {
                return $this->error('Tahun Akademik tidak ditemukan.', 404);
            }

            $mahasiswa = Mahasiswa::query()->where('nim', $nim)->first();
            if ($mahasiswa === null) {
                return $this->error('Mahasiswa dengan NIM ' . $nim . ' tidak ditemukan.', 404);
            }

            $statusPerkuliahan = StatusPerkuliahan::query()
                ->where('nim', $nim)
                ->where('kode_tahun_akademik', $ta->kode_tahun_akademik)
                ->first();

            if (! $statusPerkuliahan) {
                $statusPerkuliahan = new StatusPerkuliahan();
                $statusPerkuliahan->nim = $nim;
                $statusPerkuliahan->kode_tahun_akademik = $ta->kode_tahun_akademik;
                $statusPerkuliahan->semester = (string) $ta->semester;
                $statusPerkuliahan->status_perkuliahan = 'A';
                $statusPerkuliahan->pembayaran_spp = $statusNormalized;
                $statusPerkuliahan->pembayaran_sks = '0';
                $statusPerkuliahan->pembayaran_lab = '0';
                $statusPerkuliahan->pengumpulan_krs = '0';
                $statusPerkuliahan->kode_pengguna = auth('sanctum')->id() ?? auth()->id() ?? 0;
            } else {
                $statusPerkuliahan->pembayaran_spp = $statusNormalized;
            }

            $statusPerkuliahan->save();

            $statusLabel = $statusNormalized === '1' ? 'Lunas' : 'Belum Lunas';

            return $this->success([
                'nim' => $nim,
                'nama_mahasiswa' => $mahasiswa->nama_mahasiswa,
                'tahun_akademik' => $ta->tahun_akademik,
                'semester' => $ta->semester,
                'pembayaran_spp' => $statusNormalized,
                'status_spp_label' => $statusLabel,
            ], 'Validasi SPP berhasil diperbarui menjadi ' . $statusLabel);
        } catch (\Throwable $e) {
            report($e);

            return $this->error('Terjadi kesalahan internal server: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Cek status SKS, total SKS yang diambil, dan rincian matakuliah yang akan dibayar.
     * GET /api/v1/validasi-sks?nim=...&kode_tahun_akademik=...
     */
    public function getSks(Request $request): JsonResponse
    {
        $validator = Validator::make($request->query(), [
            'nim' => 'required|string|max:20',
            'tahun_akademik' => 'nullable|string',
            'semester' => 'nullable|string',
            'kode_tahun_akademik' => 'nullable',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation Error', 422, $validator->errors()->toArray());
        }

        try {
            $nim = trim((string) $request->query('nim'));

            $ta = $this->resolveTahunAkademik(
                $request->query('kode_tahun_akademik'),
                $request->query('tahun_akademik'),
                $request->query('semester')
            );
            if ($ta === null) {
                return $this->error('Tahun Akademik tidak ditemukan.', 404);
            }

            $mahasiswa = Mahasiswa::query()
                ->with('nama_prodi')
                ->where('nim', $nim)
                ->first();

            if ($mahasiswa === null) {
                return $this->error('Mahasiswa dengan NIM ' . $nim . ' tidak ditemukan.', 404);
            }

            $statusPerkuliahan = StatusPerkuliahan::query()
                ->where('nim', $nim)
                ->where('kode_tahun_akademik', $ta->kode_tahun_akademik)
                ->first();

            $statusSks = $statusPerkuliahan ? (string) $statusPerkuliahan->pembayaran_sks : '0';
            $statusSpp = $statusPerkuliahan ? (string) $statusPerkuliahan->pembayaran_spp : '0';

            // Ambil data KRS mahasiswa pada tahun akademik tersebut
            $krs = KRS::query()
                ->where('nim', $nim)
                ->where('kode_tahun_akademik', $ta->kode_tahun_akademik)
                ->first();

            $matakuliahList = [];
            $totalSks = 0;

            if ($krs) {
                $details = KRSDetail::query()
                    ->with('matakuliah')
                    ->where('kode_krs', $krs->kode_krs)
                    ->get();

                foreach ($details as $detail) {
                    $mk = $detail->matakuliah;

                    // Fallback jika relasi id_matakuliah belum terhubung langsung
                    if (! $mk && ! empty($detail->kode_matakuliah)) {
                        $mk = Matakuliah::query()
                            ->where('kode_matakuliah', $detail->kode_matakuliah)
                            ->first();
                    }

                    $sksTeori = $mk ? (int) $mk->sks_teori : 0;
                    $sksPraktek = $mk ? (int) $mk->sks_praktek : 0;
                    $sksPraktikum = $mk ? (int) $mk->sks_praktikum : 0;
                    $totalSksMk = $sksTeori + $sksPraktek + $sksPraktikum;

                    $totalSks += $totalSksMk;

                    $matakuliahList[] = [
                        'id_matakuliah' => $mk ? $mk->getKey() : $detail->id_matakuliah,
                        'kode_matakuliah' => $mk ? $mk->kode_matakuliah : $detail->kode_matakuliah,
                        'nama_matakuliah' => $mk ? $mk->nama_matakuliah : null,
                        'sks_teori' => $sksTeori,
                        'sks_praktek' => $sksPraktek,
                        'sks_praktikum' => $sksPraktikum,
                        'total_sks' => $totalSksMk,
                        'status' => $detail->status,
                    ];
                }
            }

            return $this->success([
                'nim' => $mahasiswa->nim,
                'nama_mahasiswa' => $mahasiswa->nama_mahasiswa,
                'kode_program_studi' => $mahasiswa->program_studi_kode,
                'nama_program_studi' => $mahasiswa->nama_prodi?->nama_program_studi,
                'tahun_akademik' => $ta->tahun_akademik,
                'semester' => $ta->semester,
                'semester_nama' => $ta->semester_name,
                'status_spp' => $statusSpp,
                'status_spp_label' => $statusSpp === '1' ? 'Lunas' : 'Belum Lunas',
                'status_sks' => $statusSks,
                'status_sks_label' => $statusSks === '1' ? 'Lunas' : 'Belum Lunas',
                'kode_krs' => $krs?->kode_krs,
                'total_sks' => $totalSks,
                'total_matakuliah' => count($matakuliahList),
                'matakuliah' => $matakuliahList,
            ], 'Data status SKS dan rincian matakuliah berhasil diambil');
        } catch (\Throwable $e) {
            report($e);

            return $this->error('Terjadi kesalahan internal server: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Callback / Update status validasi SKS mahasiswa.
     * POST /api/v1/validasi-sks
     */
    public function updateSks(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nim' => 'required|string|max:20',
            'status' => 'required',
            'tahun_akademik' => 'nullable|string',
            'semester' => 'nullable|string',
            'kode_tahun_akademik' => 'nullable',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation Error', 422, $validator->errors()->toArray());
        }

        try {
            $nim = trim((string) $request->input('nim'));
            $statusNormalized = $this->normalizeStatus($request->input('status'));

            $ta = $this->resolveTahunAkademik(
                $request->input('kode_tahun_akademik'),
                $request->input('tahun_akademik'),
                $request->input('semester')
            );
            if ($ta === null) {
                return $this->error('Tahun Akademik tidak ditemukan.', 404);
            }

            $mahasiswa = Mahasiswa::query()->where('nim', $nim)->first();
            if ($mahasiswa === null) {
                return $this->error('Mahasiswa dengan NIM ' . $nim . ' tidak ditemukan.', 404);
            }

            $statusPerkuliahan = StatusPerkuliahan::query()
                ->where('nim', $nim)
                ->where('kode_tahun_akademik', $ta->kode_tahun_akademik)
                ->first();

            if (! $statusPerkuliahan) {
                $statusPerkuliahan = new StatusPerkuliahan();
                $statusPerkuliahan->nim = $nim;
                $statusPerkuliahan->kode_tahun_akademik = $ta->kode_tahun_akademik;
                $statusPerkuliahan->semester = (string) $ta->semester;
                $statusPerkuliahan->status_perkuliahan = 'A';
                $statusPerkuliahan->pembayaran_spp = '0';
                $statusPerkuliahan->pembayaran_sks = $statusNormalized;
                $statusPerkuliahan->pembayaran_lab = '0';
                $statusPerkuliahan->pengumpulan_krs = '0';
                $statusPerkuliahan->kode_pengguna = auth('sanctum')->id() ?? auth()->id() ?? 0;
            } else {
                $statusPerkuliahan->pembayaran_sks = $statusNormalized;
            }

            $statusPerkuliahan->save();

            $statusLabel = $statusNormalized === '1' ? 'Lunas' : 'Belum Lunas';

            return $this->success([
                'nim' => $nim,
                'nama_mahasiswa' => $mahasiswa->nama_mahasiswa,
                'tahun_akademik' => $ta->tahun_akademik,
                'semester' => $ta->semester,
                'pembayaran_sks' => $statusNormalized,
                'status_sks_label' => $statusLabel,
            ], 'Validasi SKS berhasil diperbarui menjadi ' . $statusLabel);
        } catch (\Throwable $e) {
            report($e);

            return $this->error('Terjadi kesalahan internal server: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Resolusi Tahun Akademik: mendukung tahun_akademik + semester (e.g. "2025/2026" & "ganjil"/"genap"),
     * kode_tahun_akademik (plain / terenkripsi), atau default tahun aktif.
     */
    private function resolveTahunAkademik(mixed $inputTa = null, ?string $inputTahun = null, ?string $inputSemester = null): ?TahunAkademik
    {
        // 1. Jika diberikan tahun_akademik (misal "2025/2026")
        if (! empty($inputTahun)) {
            $query = TahunAkademik::query()->where('tahun_akademik', trim($inputTahun));

            if (! empty($inputSemester)) {
                $sem = strtolower(trim((string) $inputSemester));
                if (in_array($sem, ['1', 'ganjil', 'odd'], true)) {
                    $query->where('semester', '1');
                } elseif (in_array($sem, ['0', '2', 'genap', 'even'], true)) {
                    $query->whereIn('semester', ['0', '2']);
                } else {
                    $query->where('semester', $sem);
                }
            }

            $ta = $query->orderByDesc('kode_tahun_akademik')->first();
            if ($ta) {
                return $ta;
            }
        }

        // 2. Jika inputTa diisi (bisa berupa kode_tahun_akademik atau tahun_akademik seperti "2025/2026")
        if (! empty($inputTa)) {
            if (str_contains((string) $inputTa, '/')) {
                return $this->resolveTahunAkademik(null, (string) $inputTa, $inputSemester);
            }

            try {
                $decrypted = Crypt::decryptString((string) $inputTa);
                $ta = TahunAkademik::query()->where('kode_tahun_akademik', $decrypted)->first();
                if ($ta) {
                    return $ta;
                }
            } catch (DecryptException $e) {
                // Bukan string terenkripsi, abaikan dan coba plain
            }

            $ta = TahunAkademik::query()->where('kode_tahun_akademik', $inputTa)->first();
            if ($ta) {
                return $ta;
            }
        }

        // 3. Default ke Tahun Akademik aktif
        return TahunAkademik::query()->where('status', 'A')->first();
    }

    /**
     * Normalisasi nilai status menjadi '1' (Lunas/Valid) atau '0' (Belum Lunas/Belum Valid).
     */
    private function normalizeStatus(mixed $status): string
    {
        if (is_bool($status)) {
            return $status ? '1' : '0';
        }

        $str = strtolower(trim((string) $status));
        if (in_array($str, ['1', 'true', 'lunas', 'valid', 'aktif', 'ya', 'yes'], true)) {
            return '1';
        }

        return '0';
    }
}
