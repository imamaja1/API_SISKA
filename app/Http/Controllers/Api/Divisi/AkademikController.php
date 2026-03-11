<?php

namespace App\Http\Controllers\Api\Divisi;

use App\Http\Controllers\Controller;
use App\Models\StatusPerkuliahan;
use App\Models\TahunAkademik;
use App\Services\ServisChart;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class AkademikController extends Controller
{
    protected $kodeTahunAkademikAktif;

    protected $servisChart;

    public function __construct()
    {
        // Use query builder's value() (first()->value() would throw because Model has no value()).
        $this->servisChart = new ServisChart;
        $this->kodeTahunAkademikAktif = TahunAkademik::query()
            ->where('status', 'A')
            ->value('kode_tahun_akademik');
    }

    public function getStatusPerkuliahan()
    {
        try {
            if (! $this->kodeTahunAkademikAktif) {
                return response()->json([
                    'status' => false,
                    'message' => 'Tahun Akademik Aktif Tidak Ditemukan',
                ], 404);
            }

            $data = StatusPerkuliahan::query()
                ->select(
                    'kode_status_perkuliahan as id',
                    'kode_status_perkuliahan as kode',
                    'mahasiswa.nim',
                    'nama_mahasiswa',
                    'nama_program_studi',
                    'status_perkuliahan',
                    'pembayaran_spp',
                    'pembayaran_sks',
                    'pembayaran_lab',
                    'pengumpulan_krs',
                )
                ->join('mahasiswa', 'status_perkuliahan.nim', '=', 'mahasiswa.nim')
                ->join('program_studi', 'mahasiswa.program_studi_kode', '=', 'program_studi.kode_program_studi')
                ->where('kode_tahun_akademik', $this->kodeTahunAkademikAktif)
                ->get()
                ->map(function ($item) {
                    $item->kode = Crypt::encryptString((string) $item->kode);

                    return $item;
                });

            if ($data->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Status Perkuliahan Tidak Ditemukan',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Status Perkuliahan Ditemukan',
                'data' => $data,
            ], 200);
        } catch (\Throwable $e) {
            report($e);

            $payload = [
                'status' => false,
                'message' => 'Internal Server Error',
            ];

            if (config('app.debug')) {
                $payload['debug'] = [
                    'error' => $e->getMessage(),
                ];
            }

            return response()->json($payload, 500);
        }
    }

    public function getStatusPerkuliahanKumpul()
    {
        try {
            if (! $this->kodeTahunAkademikAktif) {
                return response()->json([
                    'status' => false,
                    'message' => 'Tahun Akademik Aktif Tidak Ditemukan',
                ], 404);
            }

            $data = StatusPerkuliahan::query()
                ->select(
                    'kode_status_perkuliahan as id',
                    'kode_status_perkuliahan as kode',
                    'mahasiswa.nim',
                    'nama_mahasiswa',
                    'nama_program_studi',
                    'status_perkuliahan',
                    'pembayaran_spp',
                    'pembayaran_sks',
                    'pembayaran_lab',
                    'pengumpulan_krs',
                )
                ->join('mahasiswa', 'status_perkuliahan.nim', '=', 'mahasiswa.nim')
                ->join('program_studi', 'mahasiswa.program_studi_kode', '=', 'program_studi.kode_program_studi')
                ->where('kode_tahun_akademik', $this->kodeTahunAkademikAktif)
                ->where('pengumpulan_krs', '1')
                ->get()
                ->map(function ($item) {
                    $item->kode = Crypt::encryptString((string) $item->kode);

                    return $item;
                });

            if ($data->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Status Perkuliahan Tidak Ditemukan',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Status Perkuliahan Ditemukan',
                'data' => $data,
            ], 200);
        } catch (\Throwable $e) {
            report($e);

            $payload = [
                'status' => false,
                'message' => 'Internal Server Error',
            ];

            if (config('app.debug')) {
                $payload['debug'] = [
                    'error' => $e->getMessage(),
                ];
            }

            return response()->json($payload, 500);
        }
    }

    public function getStatusPerkuliahanNotKumpul()
    {
        try {
            if (! $this->kodeTahunAkademikAktif) {
                return response()->json([
                    'status' => false,
                    'message' => 'Tahun Akademik Aktif Tidak Ditemukan',
                ], 404);
            }

            $data = StatusPerkuliahan::query()
                ->select(
                    'kode_status_perkuliahan as id',
                    'kode_status_perkuliahan as kode',
                    'mahasiswa.nim',
                    'nama_mahasiswa',
                    'nama_program_studi',
                    'status_perkuliahan',
                    'pembayaran_spp',
                    'pembayaran_sks',
                    'pembayaran_lab',
                    'pengumpulan_krs',
                )
                ->join('mahasiswa', 'status_perkuliahan.nim', '=', 'mahasiswa.nim')
                ->join('program_studi', 'mahasiswa.program_studi_kode', '=', 'program_studi.kode_program_studi')
                ->where('kode_tahun_akademik', $this->kodeTahunAkademikAktif)
                ->where('pengumpulan_krs', '0')
                ->get()
                ->map(function ($item) {
                    $item->kode = Crypt::encryptString((string) $item->kode);

                    return $item;
                });

            if ($data->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Status Perkuliahan Tidak Ditemukan',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Status Perkuliahan Ditemukan',
                'data' => $data,
            ], 200);
        } catch (\Throwable $e) {
            report($e);

            $payload = [
                'status' => false,
                'message' => 'Internal Server Error',
            ];

            if (config('app.debug')) {
                $payload['debug'] = [
                    'error' => $e->getMessage(),
                ];
            }

            return response()->json($payload, 500);
        }
    }

    public function getStatusPerkuliahanByProdi(Request $request)
    {
        try {
            if (! $this->kodeTahunAkademikAktif) {
                return response()->json([
                    'status' => false,
                    'message' => 'Tahun Akademik Aktif Tidak Ditemukan',
                ], 404);
            }

            $validated = $request->validate([
                'kode' => 'required|string',
            ]);

            try {
                $kodeProgramStudi = Crypt::decryptString($validated['kode']);
            } catch (DecryptException $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'kode_prodi tidak valid',
                ], 400);
            }

            $data = StatusPerkuliahan::query()
                ->select(
                    'kode_status_perkuliahan as id',
                    'kode_status_perkuliahan as kode',
                    'mahasiswa.nim',
                    'nama_mahasiswa',
                    'nama_program_studi',
                    'status_perkuliahan',
                    'pembayaran_spp',
                    'pembayaran_sks',
                    'pembayaran_lab',
                    'pengumpulan_krs',
                )
                ->join('mahasiswa', 'status_perkuliahan.nim', '=', 'mahasiswa.nim')
                ->join('program_studi', 'mahasiswa.program_studi_kode', '=', 'program_studi.kode_program_studi')
                ->where('status_perkuliahan.kode_tahun_akademik', $this->kodeTahunAkademikAktif)
                ->where('program_studi.kode_program_studi', $kodeProgramStudi)
                ->get()
                ->map(function ($item) {
                    $item->kode = Crypt::encryptString((string) $item->kode);

                    return $item;
                });

            if ($data->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Status Perkuliahan Tidak Ditemukan untuk Program Studi ini',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Status Perkuliahan Ditemukan untuk Program Studi ini',
                'data' => $data,
            ], 200);
        } catch (\Throwable $e) {
            report($e);

            $payload = [
                'status' => false,
                'message' => 'Internal Server Error',
            ];

            if (config('app.debug')) {
                $payload['debug'] = [
                    'error' => $e->getMessage(),
                ];
            }

            return response()->json($payload, 500);
        }
    }

    public function getStatusPerkuliahanByProdiKumpul(Request $request)
    {
        try {
            if (! $this->kodeTahunAkademikAktif) {
                return response()->json([
                    'status' => false,
                    'message' => 'Tahun Akademik Aktif Tidak Ditemukan',
                ], 404);
            }

            $validated = $request->validate([
                'kode' => 'required|string',
            ]);

            try {
                $kodeProgramStudi = Crypt::decryptString($validated['kode']);
            } catch (DecryptException $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'kode_prodi tidak valid',
                ], 400);
            }

            $data = StatusPerkuliahan::query()
                ->select(
                    'kode_status_perkuliahan as id',
                    'kode_status_perkuliahan as kode',
                    'mahasiswa.nim',
                    'nama_mahasiswa',
                    'nama_program_studi',
                    'status_perkuliahan',
                    'pembayaran_spp',
                    'pembayaran_sks',
                    'pembayaran_lab',
                    'pengumpulan_krs',
                )
                ->join('mahasiswa', 'status_perkuliahan.nim', '=', 'mahasiswa.nim')
                ->join('program_studi', 'mahasiswa.program_studi_kode', '=', 'program_studi.kode_program_studi')
                ->where('status_perkuliahan.kode_tahun_akademik', $this->kodeTahunAkademikAktif)
                ->where('program_studi.kode_program_studi', $kodeProgramStudi)
                ->where('pengumpulan_krs', '1')
                ->get()
                ->map(function ($item) {
                    $item->kode = Crypt::encryptString((string) $item->kode);

                    return $item;
                });

            if ($data->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Status Perkuliahan Tidak Ditemukan untuk Program Studi ini',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Status Perkuliahan Ditemukan untuk Program Studi ini',
                'data' => $data,
            ], 200);
        } catch (\Throwable $e) {
            report($e);

            $payload = [
                'status' => false,
                'message' => 'Internal Server Error',
            ];

            if (config('app.debug')) {
                $payload['debug'] = [
                    'error' => $e->getMessage(),
                ];
            }

            return response()->json($payload, 500);
        }
    }

    public function getStatusPerkuliahanByProdiNotKumpul(Request $request)
    {
        try {
            if (! $this->kodeTahunAkademikAktif) {
                return response()->json([
                    'status' => false,
                    'message' => 'Tahun Akademik Aktif Tidak Ditemukan',
                ], 404);
            }

            $validated = $request->validate([
                'kode' => 'required|string',
            ]);

            try {
                $kodeProgramStudi = Crypt::decryptString($validated['kode']);
            } catch (DecryptException $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'kode_prodi tidak valid',
                ], 400);
            }

            $data = StatusPerkuliahan::query()
                ->select(
                    'kode_status_perkuliahan as id',
                    'kode_status_perkuliahan as kode',
                    'mahasiswa.nim',
                    'nama_mahasiswa',
                    'nama_program_studi',
                    'status_perkuliahan',
                    'pembayaran_spp',
                    'pembayaran_sks',
                    'pembayaran_lab',
                    'pengumpulan_krs',
                )
                ->join('mahasiswa', 'status_perkuliahan.nim', '=', 'mahasiswa.nim')
                ->join('program_studi', 'mahasiswa.program_studi_kode', '=', 'program_studi.kode_program_studi')
                ->where('status_perkuliahan.kode_tahun_akademik', $this->kodeTahunAkademikAktif)
                ->where('program_studi.kode_program_studi', $kodeProgramStudi)
                ->where('pengumpulan_krs', '0')
                ->get()
                ->map(function ($item) {
                    $item->kode = Crypt::encryptString((string) $item->kode);

                    return $item;
                });

            if ($data->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Status Perkuliahan Tidak Ditemukan untuk Program Studi ini',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Status Perkuliahan Ditemukan untuk Program Studi ini',
                'data' => $data,
            ], 200);
        } catch (\Throwable $e) {
            report($e);

            $payload = [
                'status' => false,
                'message' => 'Internal Server Error',
            ];

            if (config('app.debug')) {
                $payload['debug'] = [
                    'error' => $e->getMessage(),
                ];
            }

            return response()->json($payload, 500);
        }
    }

    public function updatePengumpulanKRS(Request $request)
    {
        try {
            $validated = $request->validate([
                'nim' => 'required|numeric',
            ]);
            try {
                $nim = $validated['nim'];
            } catch (DecryptException $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'kode_status_perkuliahan tidak valid',
                ], 400);
            }

            $statusPerkuliahan = StatusPerkuliahan::query()->where('nim', $nim)
                ->where('kode_tahun_akademik', $this->kodeTahunAkademikAktif)
                ->first();
            if (! $statusPerkuliahan) {
                return response()->json([
                    'status' => false,
                    'message' => 'Status Perkuliahan Tidak Ditemukan',
                ], 404);
            }
            if ($statusPerkuliahan->pengumpulan_krs == '1') {
                $statusPerkuliahan->pengumpulan_krs = '0';
            } else {
                $statusPerkuliahan->pengumpulan_krs = '1';
            }

            $statusPerkuliahan->save();

            return response()->json([
                'status' => true,
                'message' => 'Pengumpulan KRS berhasil diperbarui '.($statusPerkuliahan->pengumpulan_krs == '1' ? 'Aktif' : 'Nonaktif'),
            ], 200);
        } catch (\Throwable $e) {
            report($e);

            $payload = [
                'status' => false,
                'message' => 'Internal Server Error',
            ];

            if (config('app.debug')) {
                $payload['debug'] = [
                    'error' => $e->getMessage(),
                ];
            }

            return response()->json($payload, 500);
        }
    }

    public function chart_pengumpulan_krs()
    {
        $data = $this->servisChart->chart_pengumpulan_krs($this->kodeTahunAkademikAktif);

        return response()->json([
            'status' => true,
            'message' => 'Data Chart Pengumpulan KRS Ditemukan',
            'data' => $data,
        ], 200);
    }

    public function chart_pengumpulan_krs_by_prodi()
    {
        $data = $this->servisChart->chart_pengumpulan_krs_by_prodi($this->kodeTahunAkademikAktif);

        return response()->json([
            'status' => true,
            'message' => 'Data Chart Pengumpulan KRS by Prodi Ditemukan',
            'data' => $data,
        ], 200);
    }

    public function chart_kumpulan_krs_by_tahun_angkatan()
    {
        $data = $this->servisChart->chart_kumpulan_krs_by_tahun_angkatan($this->kodeTahunAkademikAktif);

        return response()->json([
            'status' => true,
            'message' => 'Data Chart Pengumpulan KRS by Tahun Angkatan Ditemukan',
            'data' => $data,
        ], 200);
    }

    public function chart_kumpulan_krs_by_prodi_and_tahun_angkatan()
    {
        $data = $this->servisChart->chart_pengumpulan_krs_by_prodi_angkatan($this->kodeTahunAkademikAktif);

        return response()->json([
            'status' => true,
            'message' => 'Data Chart Pengumpulan KRS by Prodi and Tahun Angkatan Ditemukan',
            'data' => $data,
        ], 200);
    }
}
