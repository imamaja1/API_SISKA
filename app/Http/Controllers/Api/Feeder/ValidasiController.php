<?php

namespace App\Http\Controllers\Api\Feeder;

use App\Http\Controllers\Api\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Kurikulum;
use App\Models\Mahasiswa;
use App\Models\ProgramStudi;
use App\Models\TahunAkademik;
use App\Services\FeederService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ValidasiController extends Controller
{
    use ApiResponse;

    private FeederService $feederService;

    public function __construct(FeederService $feederService)
    {
        $this->feederService = $feederService;
    }

    public function validasiMahasiswa(Request $request): JsonResponse
    {
        $nim = $request->query('nim');

        if (! $nim) {
            return $this->error('Parameter nim wajib diisi.', 422);
        }

        if (! $this->feederService->isConfigured()) {
            return $this->error(
                'Feeder belum dikonfigurasi. Silakan isi credential Feeder terlebih dahulu.',
                400,
            );
        }

        $mahasiswa = Mahasiswa::where('nim', $nim)->first();

        if (! $mahasiswa) {
            return $this->error(
                'Mahasiswa dengan NIM '.$nim.' tidak ditemukan.',
                404,
            );
        }

        try {
            $dataSiska = $this->getDataSiskaPerNim(
                $nim,
                $request->query('kode_tahun_akademik'),
                $request->query('semester'),
            );

            $dataFeeder = $this->getFeederDataPerNim($nim);

            $detail = $this->bandingkanPerNim($dataFeeder, $dataSiska);

            $sudahSync = collect($detail)
                ->where('status', 'sudah_sync')
                ->count();
            $belumSync = collect($detail)
                ->where('status', 'belum_sync')
                ->count();

            return $this->success(
                [
                    'data' => [
                        'nim' => $nim,
                        'nama_mahasiswa' => $mahasiswa->nama_mahasiswa,
                        'detail' => $detail,
                        'summary' => [
                            'total' => count($detail),
                            'sudah_sync' => $sudahSync,
                            'belum_sync' => $belumSync,
                        ],
                    ],
                ],
                'Validasi nilai mahasiswa berhasil',
            );
        } catch (\Exception $e) {
            return $this->error(
                'Gagal mengambil data: '.$e->getMessage(),
                500,
            );
        }
    }

    public function validasiKelas(Request $request): JsonResponse
    {
        $validator = Validator::make($request->query(), [
            'tahun_akademik' => 'required|string',
            'semester' => 'required|string|in:1,2',
            'kode_matakuliah' => 'required|string',
            'kelas' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->error(
                'Validation Error',
                422,
                $validator->errors()->toArray(),
            );
        }

        if (! $this->feederService->isConfigured()) {
            return $this->error(
                'Feeder belum dikonfigurasi. Silakan isi credential Feeder terlebih dahulu.',
                400,
            );
        }

        try {
            $tahunAkademikStr = $request->query('tahun_akademik');
            $semesterInput = $request->query('semester');
            $semesterDb = $semesterInput === '2' ? '0' : $semesterInput;
            $kodeMatakuliah = $request->query('kode_matakuliah');
            $kelasSiska = $request->query('kelas');

            $tahunAkademik = \App\Models\TahunAkademik::where(
                'tahun_akademik',
                $tahunAkademikStr,
            )
                ->where('semester', $semesterDb)
                ->first();

            if (! $tahunAkademik) {
                return $this->error(
                    'Tahun akademik '.
                        $tahunAkademikStr.
                        ' semester '.
                        $semesterInput.
                        ' tidak ditemukan.',
                    404,
                );
            }

            $idSemester = $this->idSemesterFromTahunAkademik($tahunAkademik);

            $matakuliah = \App\Models\Matakuliah::where(
                'kode_matakuliah',
                $kodeMatakuliah,
            )->first();
            if (! $matakuliah) {
                return $this->error(
                    'Matakuliah dengan kode '.
                        $kodeMatakuliah.
                        ' tidak ditemukan.',
                    404,
                );
            }

            $namaMatakuliah = $matakuliah->nama_matakuliah;

            $dataSiska = $this->getDataSiskaKelasPerMatakuliah(
                $tahunAkademikStr,
                $semesterDb,
                $kodeMatakuliah,
                $kelasSiska,
            );

            if (count($dataSiska) === 0) {
                return $this->error(
                    'Kelas '.$kelasSiska.' tidak ada di SISKA untuk matakuliah '.$kodeMatakuliah.' tahun akademik '.$tahunAkademikStr.' semester '.$semesterInput,
                    404,
                );
            }

            $dataFeeder = $this->getFeederDataKelasPerMatakuliah(
                $idSemester,
                $kodeMatakuliah,
                $kelasSiska,
            );

            $detail = $this->bandingkanKelasPerMatakuliah(
                $dataFeeder,
                $dataSiska,
            );

            $sudahSync = collect($detail)
                ->where('status', 'sudah_sync')
                ->count();
            $belumSync = collect($detail)
                ->where('status', 'belum_sync')
                ->count();

            return $this->success(
                [
                    'data' => [
                        'tahun_akademik' => $tahunAkademikStr,
                        'semester' => $semesterInput === '1' ? 'Ganjil' : 'Genap',
                        'kode_matakuliah' => $kodeMatakuliah,
                        'nama_matakuliah' => $namaMatakuliah,
                        'kelas_siska' => $kelasSiska,
                        'filter_feeder' => $kelasSiska,
                        'detail' => $detail,
                        'summary' => [
                            'total' => count($detail),
                            'sudah_sync' => $sudahSync,
                            'belum_sync' => $belumSync,
                        ],
                    ],
                ],
                'Validasi kelas berhasil',
            );
        } catch (\Exception $e) {
            return $this->error(
                'Gagal mengambil data: '.$e->getMessage(),
                500,
            );
        }
    }

    /**
     * GET /api/v1/feeder/prodi
     */
    public function getProdi(): JsonResponse
    {
        $prodi = ProgramStudi::select(
            'kode_program_studi',
            'nama_program_studi',
            'singkatan_program_studi',
        )
            ->get()
            ->map(function ($item) {
                return [
                    'kode_program_studi' => Crypt::encryptString(
                        (string) $item->kode_program_studi,
                    ),
                    'nama_program_studi' => $item->nama_program_studi,
                    'singkatan' => $item->singkatan_program_studi,
                ];
            });

        return $this->success(['data' => $prodi]);
    }

    /**
     * GET /api/v1/feeder/ta
     */
    public function getTahunAkademik(): JsonResponse
    {
        $ta = TahunAkademik::select(
            'kode_tahun_akademik',
            'tahun_akademik',
            'semester',
        )
            ->orderByDesc('kode_tahun_akademik')
            ->get()
            ->map(function ($item) {
                return [
                    'kode_tahun_akademik' => Crypt::encryptString(
                        (string) $item->kode_tahun_akademik,
                    ),
                    'tahun_akademik' => $item->tahun_akademik,
                    'semester' => $item->semester === '0' ? '2' : $item->semester,
                    'semester_nama' => $item->semester === '1' ? 'Ganjil' : 'Genap',
                ];
            });

        return $this->success(['data' => $ta]);
    }

    /**
     * GET /api/v1/feeder/matakuliah?kode_program_studi=...&kode_tahun_akademik=...
     */
    public function getMatakuliah(Request $request): JsonResponse
    {
        $request->validate([
            'kode_program_studi' => 'required|string',
            'kode_tahun_akademik' => 'required|string',
        ]);

        $kodeProdi = (int) Crypt::decryptString(
            $request->query('kode_program_studi'),
        );
        $kodeTa = (int) Crypt::decryptString(
            $request->query('kode_tahun_akademik'),
        );

        $ta = TahunAkademik::where('kode_tahun_akademik', $kodeTa)->first();
        if (! $ta) {
            return $this->error('Tahun akademik tidak ditemukan', 404);
        }
        $angkatan = explode('/', $ta->tahun_akademik)[0];

        $matakuliah = Kurikulum::whereHas('namaKurikulum', function ($q) use (
            $kodeProdi,
        ) {
            $q->where('kode_program_studi', $kodeProdi);
        })
            ->whereHas('namaKurikulum.kurikulumAngkatan', function ($q) use (
                $angkatan,
            ) {
                $q->where('angkatan', $angkatan);
            })
            ->with('matakuliah')
            ->get()
            ->pluck('matakuliah')
            ->filter()
            ->values()
            ->map(function ($item) {
                return [
                    'id_matakuliah' => Crypt::encryptString(
                        (string) $item->id_matakuliah,
                    ),
                    'kode_matakuliah' => $item->kode_matakuliah,
                    'nama_matakuliah' => $item->nama_matakuliah,
                    'sks_teori' => $item->sks_teori,
                    'sks_praktek' => $item->sks_praktek,
                    'sks_praktikum' => $item->sks_praktikum,
                ];
            });

        return $this->success(['data' => $matakuliah]);
    }

    /**
     * GET /api/v1/feeder/kelas?kode_tahun_akademik=...&id_matakuliah=...
     */
    public function getKelas(Request $request): JsonResponse
    {
        $request->validate([
            'kode_tahun_akademik' => 'required|string',
            'id_matakuliah' => 'required|string',
        ]);

        $kodeTa = (int) Crypt::decryptString(
            $request->query('kode_tahun_akademik'),
        );
        $idMatkul = (int) Crypt::decryptString(
            $request->query('id_matakuliah'),
        );

        $kelas = Kelas::where('kelas.kode_tahun_akademik', $kodeTa)
            ->where('kelas.id_matakuliah', $idMatkul)
            ->join(
                'nama_kelas',
                'kelas.nama_kelas_id',
                '=',
                'nama_kelas.nama_kelas_id',
            )
            ->select('kelas.kelas_id', 'nama_kelas.nama_kelas')
            ->distinct()
            ->get()
            ->map(function ($item) {
                return [
                    'kelas_id' => Crypt::encryptString(
                        (string) $item->kelas_id,
                    ),
                    'nama_kelas' => $item->nama_kelas,
                ];
            });

        return $this->success(['data' => $kelas]);
    }

    private function idSemesterFromTahunAkademik(
        \App\Models\TahunAkademik $ta,
    ): string {
        $tahunParts = explode('/', $ta->tahun_akademik);
        $startYear = $tahunParts[0];
        $semesterDigit = $ta->semester === '1' ? '1' : '2';

        return $startYear.$semesterDigit;
    }

    private function getDataSiskaKelasPerMatakuliah(
        string $tahunAkademikStr,
        string $semester,
        string $kodeMatakuliah,
        string $kelasSiska,
    ): array {
        return DB::table('khs_detail')
            ->join(
                'krs_detail',
                'khs_detail.kode_krs_detail',
                '=',
                'krs_detail.kode_krs_detail',
            )
            ->join('krs', 'krs_detail.kode_krs', '=', 'krs.kode_krs')
            ->join('mahasiswa', 'krs.nim', '=', 'mahasiswa.nim')
            ->join(
                'kelas_mahasiswa',
                'kelas_mahasiswa.kode_krs_detail',
                '=',
                'krs_detail.kode_krs_detail',
            )
            ->join('kelas', 'kelas.kelas_id', '=', 'kelas_mahasiswa.kelas_id')
            ->join(
                'nama_kelas',
                'kelas.nama_kelas_id',
                '=',
                'nama_kelas.nama_kelas_id',
            )
            ->join(
                'matakuliah',
                'krs_detail.id_matakuliah',
                '=',
                'matakuliah.id_matakuliah',
            )
            ->join(
                'tahun_akademik',
                'kelas.kode_tahun_akademik',
                '=',
                'tahun_akademik.kode_tahun_akademik',
            )
            ->leftJoin('sistem_penilaian_detail', function ($join) {
                $join
                    ->where('sistem_penilaian_detail.kode_sistem_penilaian', 1)
                    ->whereColumn(
                        'khs_detail.nilai_akhir',
                        '>=',
                        'sistem_penilaian_detail.nilai_minimum',
                    )
                    ->whereColumn(
                        'khs_detail.nilai_akhir',
                        '<=',
                        'sistem_penilaian_detail.nilai_maksimum',
                    );
            })
            ->where('tahun_akademik.tahun_akademik', $tahunAkademikStr)
            ->where('tahun_akademik.semester', $semester)
            ->where('matakuliah.kode_matakuliah', $kodeMatakuliah)
            ->where('nama_kelas.nama_kelas', $kelasSiska)
            ->select(
                'khs_detail.kode_krs_detail',
                'mahasiswa.nim',
                'mahasiswa.nama_mahasiswa',
                'khs_detail.nilai_harian',
                'khs_detail.nilai_uts',
                'khs_detail.nilai_uas',
                'khs_detail.nilai_akhir',
                'sistem_penilaian_detail.grade',
                DB::raw(
                    '(matakuliah.sks_teori + matakuliah.sks_praktek + matakuliah.sks_praktikum) AS total_sks',
                ),
            )
            ->distinct()
            ->get()
            ->toArray();
    }

    private function mapKelasSiskaToFeederFilter(string $kelasSiska): string
    {
        return match ($kelasSiska) {
            'EX' => 'Eks',
            default => $kelasSiska,
        };
    }

    private function getFeederDataKelasPerMatakuliah(
        string $idSemester,
        string $kodeMatakuliah,
        string $kelasSiska,
    ): array {
        $kelasFeeder = $this->mapKelasSiskaToFeederFilter($kelasSiska);

        $allData = $this->feederService->getData(
            'DetailNilaiPerkuliahanKelas',
            [
                'filter' => "id_semester='".
                    $idSemester.
                    "' AND kode_mata_kuliah='".
                    str_replace("'", "\\'", $kodeMatakuliah).
                    "' AND nama_kelas_kuliah LIKE '%".
                    str_replace("'", "\\'", $kelasFeeder).
                    "'",
                'limit' => '',
                'offset' => '',
                'order' => '',
            ],
        );

        return $allData;
    }

    private function bandingkanKelasPerMatakuliah(
        array $dataFeeder,
        array $dataSiska,
    ): array {
        $result = [];

        $siskaMap = [];
        foreach ($dataSiska as $s) {
            $siskaMap[$s->nim] = [
                'kode_krs_detail' => $s->kode_krs_detail,
                'nim' => $s->nim,
                'nama_mahasiswa' => $s->nama_mahasiswa,
                'nilai_harian' => $s->nilai_harian,
                'nilai_uts' => $s->nilai_uts,
                'nilai_uas' => $s->nilai_uas,
                'nilai_akhir' => $s->nilai_akhir,
                'grade' => $s->grade,
                'total_sks' => $s->total_sks,
            ];
        }

        $feederMap = [];
        foreach ($dataFeeder as $f) {
            $nimFeeder = $f['nim'] ?? '';
            $feederMap[$nimFeeder] = [
                'nim' => $nimFeeder,
                'nama_mahasiswa' => $f['nama_mahasiswa'] ?? '',
                'nilai' => (float) ($f['nilai_angka'] ?? 0),
                'nilai_huruf' => $f['nilai_huruf'] ?? null,
                'sks' => $f['sks_mata_kuliah'] ?? null,
            ];
        }

        $semuaNim = array_unique(
            array_merge(array_keys($feederMap), array_keys($siskaMap)),
        );

        foreach ($semuaNim as $nim) {
            $f = $feederMap[$nim] ?? null;
            $s = $siskaMap[$nim] ?? null;

            $row = [
                'nim' => $nim,
                'kode_krs_detail' => $s
                    ? Crypt::encryptString((string) $s['kode_krs_detail'])
                    : null,
                'nama_mahasiswa' => $s['nama_mahasiswa'] ?? ($f['nama_mahasiswa'] ?? ''),
                'data_feeder' => [
                    'nilai' => $f['nilai'] ?? null,
                    'nilai_huruf' => $f['nilai_huruf'] ?? null,
                ],
                'data_siska' => [
                    'nilai_harian' => $s['nilai_harian'] ?? null,
                    'nilai_uts' => $s['nilai_uts'] ?? null,
                    'nilai_uas' => $s['nilai_uas'] ?? null,
                    'nilai_akhir' => $s['nilai_akhir'] ?? null,
                    'grade' => $s['grade'] ?? null,
                ],
                'status' => '',
                'ket_grade' => '',
                'ket_nilai' => '',
            ];

            if (! $f) {
                $row['status'] = 'belum_sync';
                $row['ket_grade'] = 'tidak sesuai';
                $row['ket_nilai'] = 'tidak sesuai';
            } elseif (! $s) {
                $row['status'] = 'belum_sync';
                $row['ket_grade'] = 'tidak sesuai';
                $row['ket_nilai'] = 'tidak sesuai';
            } else {
                $gradeFeeder = $f['nilai_huruf'] ?? null;
                $gradeSiska = $s['grade'] ?? null;
                $row['ket_grade'] =
                    $gradeFeeder === $gradeSiska ? 'sesuai' : 'tidak sesuai';

                $nilaiFeeder = (float) ($f['nilai'] ?? 0);
                $nilaiSiska = (float) ($s['nilai_akhir'] ?? 0);
                $row['ket_nilai'] =
                    $nilaiFeeder === $nilaiSiska ? 'sesuai' : 'tidak sesuai';

                $row['status'] =
                    $row['ket_grade'] === 'sesuai' &&
                    $row['ket_nilai'] === 'sesuai'
                        ? 'sudah_sync'
                        : 'belum_sync';
            }

            $result[] = $row;
        }

        return $result;
    }

    private function getFeederDataPerNim(string $nim): array
    {
        $allData = $this->feederService->getData(
            'DetailNilaiPerkuliahanKelas',
            [
                'filter' => "nim='".str_replace("'", "\\'", $nim)."'",
                'limit' => '',
                'offset' => '',
                'order' => '',
            ],
        );

        return $allData;
    }

    private function getDataSiskaPerNim(
        string $nim,
        ?int $kodeTa,
        ?string $semester,
    ): array {
        $query = DB::table('khs_detail')
            ->join(
                'krs_detail',
                'khs_detail.kode_krs_detail',
                '=',
                'krs_detail.kode_krs_detail',
            )
            ->join('krs', 'krs_detail.kode_krs', '=', 'krs.kode_krs')
            ->join(
                'matakuliah',
                'krs_detail.id_matakuliah',
                '=',
                'matakuliah.id_matakuliah',
            )
            ->leftJoin('sistem_penilaian_detail', function ($join) {
                $join
                    ->whereColumn(
                        'khs_detail.nilai_akhir',
                        '>=',
                        'sistem_penilaian_detail.nilai_minimum',
                    )
                    ->whereColumn(
                        'khs_detail.nilai_akhir',
                        '<=',
                        'sistem_penilaian_detail.nilai_maksimum',
                    );
            })
            ->leftJoin(
                'tahun_akademik',
                'krs.kode_tahun_akademik',
                '=',
                'tahun_akademik.kode_tahun_akademik',
            )
            ->where('krs.nim', $nim)
            ->select(
                'khs_detail.kode_krs_detail',
                'matakuliah.id_matakuliah',
                'matakuliah.kode_matakuliah',
                'matakuliah.nama_matakuliah',
                'khs_detail.nilai_harian',
                'khs_detail.nilai_uts',
                'khs_detail.nilai_uas',
                'khs_detail.nilai_akhir',
                'sistem_penilaian_detail.grade',
                'matakuliah.sks_teori',
                'tahun_akademik.tahun_akademik',
                'tahun_akademik.semester as semester_ta',
            );

        if ($kodeTa) {
            $query->where('krs.kode_tahun_akademik', $kodeTa);
        }

        if ($semester) {
            $query->where('tahun_akademik.semester', $semester);
        }

        return $query->get()->toArray();
    }

    private function bandingkanPerNim(
        array $dataFeeder,
        array $dataSiska,
    ): array {
        $result = [];

        $siskaMap = [];
        foreach ($dataSiska as $s) {
            $key = $s->tahun_akademik.'_'.$s->kode_matakuliah;
            $siskaMap[$key] = [
                'kode_krs_detail' => $s->kode_krs_detail,
                'kode_matakuliah' => $s->kode_matakuliah,
                'nama_matakuliah' => $s->nama_matakuliah,
                'nilai_harian' => $s->nilai_harian,
                'nilai_uts' => $s->nilai_uts,
                'nilai_uas' => $s->nilai_uas,
                'nilai_akhir' => $s->nilai_akhir,
                'grade' => $s->grade,
                'sks' => $s->sks_teori,
                'tahun_akademik' => $s->tahun_akademik,
                'semester' => $s->semester_ta === '1' ? 'Ganjil' : 'Genap',
            ];
        }

        $feederMap = [];
        foreach ($dataFeeder as $f) {
            $tahunAkademik = $this->tahunAkademikFromFeeder(
                $f['id_semester'] ?? '',
            );
            $key = $tahunAkademik.'_'.$f['kode_mata_kuliah'];
            $feederMap[$key] = [
                'kode_matakuliah' => $f['kode_mata_kuliah'] ?? '',
                'nama_matakuliah' => $f['nama_mata_kuliah'] ?? '',
                'nilai' => (float) ($f['nilai_angka'] ?? 0),
                'nilai_huruf' => $f['nilai_huruf'] ?? null,
                'sks' => $f['sks_mata_kuliah'] ?? null,
                'tahun_akademik' => $tahunAkademik,
                'semester' => $this->parseSemesterFeeder($f['id_semester'] ?? '') === '1'
                        ? 'Ganjil'
                        : 'Genap',
            ];
        }

        $semuaKey = array_unique(
            array_merge(array_keys($feederMap), array_keys($siskaMap)),
        );

        foreach ($semuaKey as $key) {
            $f = $feederMap[$key] ?? null;
            $s = $siskaMap[$key] ?? null;

            $row = [
                'kode_krs_detail' => $s
                    ? Crypt::encryptString((string) $s['kode_krs_detail'])
                    : null,
                'kode_matakuliah' => $s['kode_matakuliah'] ?? ($f['kode_matakuliah'] ?? ''),
                'nama_matakuliah' => $s['nama_matakuliah'] ?? ($f['nama_matakuliah'] ?? ''),
                'sks' => $s['sks'] ?? ($f['sks'] ?? null),
                'data_feeder' => [
                    'nilai' => $f['nilai'] ?? null,
                    'nilai_huruf' => $f['nilai_huruf'] ?? null,
                ],
                'data_siska' => [
                    'nilai_harian' => $s['nilai_harian'] ?? null,
                    'nilai_uts' => $s['nilai_uts'] ?? null,
                    'nilai_uas' => $s['nilai_uas'] ?? null,
                    'nilai_akhir' => $s['nilai_akhir'] ?? null,
                    'grade' => $s['grade'] ?? null,
                ],
                'status' => '',
                'ket_grade' => '',
                'ket_nilai' => '',
            ];

            if (! $f) {
                $row['status'] = 'belum_sync';
                $row['ket_grade'] = 'tidak sesuai';
                $row['ket_nilai'] = 'tidak sesuai';
            } elseif (! $s) {
                $row['status'] = 'belum_sync';
                $row['ket_grade'] = 'tidak sesuai';
                $row['ket_nilai'] = 'tidak sesuai';
            } else {
                $gradeFeeder = $f['nilai_huruf'] ?? null;
                $gradeSiska = $s['grade'] ?? null;
                $row['ket_grade'] =
                    $gradeFeeder === $gradeSiska ? 'sesuai' : 'tidak sesuai';

                $nilaiFeeder = (float) ($f['nilai'] ?? 0);
                $nilaiSiska = (float) ($s['nilai_akhir'] ?? 0);
                $row['ket_nilai'] =
                    $nilaiFeeder === $nilaiSiska ? 'sesuai' : 'tidak sesuai';

                $row['status'] =
                    $row['ket_grade'] === 'sesuai' &&
                    $row['ket_nilai'] === 'sesuai'
                        ? 'sudah_sync'
                        : 'belum_sync';
            }

            $result[] = $row;
        }

        return $result;
    }

    private function tahunAkademikFromFeeder(string $idSemester): string
    {
        if (strlen($idSemester) < 5) {
            return '';
        }

        $year = (int) substr($idSemester, 0, 4);

        return $year.'/'.($year + 1);
    }

    private function parseSemesterFeeder(string $idSemester): string
    {
        if (strlen($idSemester) < 5) {
            return '';
        }

        return substr($idSemester, -1);
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
