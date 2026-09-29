<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\ProgramStudi;
use App\Models\TahunAkademik;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class KelasController extends Controller
{
    use ApiResponse;

    /**
     * GET /api/v1/kelas?kode_tahun_akademik=...&kode_program_studi=...&page=1&per_page=15
     */
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->query(), [
            'kode_tahun_akademik' => 'required|string',
            'kode_program_studi' => 'required|string',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation Error', 422, $validator->errors()->toArray());
        }

        try {
            $kodeTa = (int) Crypt::decryptString($request->query('kode_tahun_akademik'));
            $kodeProdi = (string) Crypt::decryptString($request->query('kode_program_studi'));
        } catch (DecryptException $e) {
            return $this->error('Parameter tidak valid (gagal dekripsi).', 422);
        }

        $ta = TahunAkademik::where('kode_tahun_akademik', $kodeTa)->first();
        if ($ta === null) {
            return $this->error('Tahun Akademik not found', 404);
        }

        $prodi = ProgramStudi::where('kode_program_studi', $kodeProdi)->first();
        if ($prodi === null) {
            return $this->error('Program Studi not found', 404);
        }

        $perPage = (int) $request->query('per_page', 15);

        $pengampu = DB::table('mengajar')
            ->select('kelas_id', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('kelas_id');

        $peserta = DB::table('kelas_mahasiswa')
            ->select('kelas_id', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('kelas_id');

        $data = DB::table('kelas as k')
            ->join('matakuliah as mk', 'k.id_matakuliah', '=', 'mk.id_matakuliah')
            ->leftJoin('nama_kelas as nk', 'k.nama_kelas_id', '=', 'nk.nama_kelas_id')
            ->leftJoin('program_studi as ps', 'k.kode_program_studi', '=', 'ps.kode_program_studi')
            ->leftJoinSub($pengampu, 'pg', 'pg.kelas_id', '=', 'k.kelas_id')
            ->leftJoinSub($peserta, 'psr', 'psr.kelas_id', '=', 'k.kelas_id')
            ->where('k.kode_tahun_akademik', $kodeTa)
            ->where('k.kode_program_studi', $kodeProdi)
            ->orderBy('mk.kode_matakuliah')
            ->orderBy('nk.nama_kelas')
            ->select(
                'k.kelas_id',
                'mk.kode_matakuliah',
                'mk.nama_matakuliah',
                'mk.sks_teori',
                'mk.sks_praktek',
                'mk.sks_praktikum',
                'nk.nama_kelas',
                'k.semester',
                'k.kode_program_studi',
                'ps.nama_program_studi',
                DB::raw('COALESCE(pg.jumlah, 0) as jumlah_pengampu'),
                DB::raw('COALESCE(psr.jumlah, 0) as jumlah_peserta'),
            )
            ->paginate($perPage);

        $data->through(function ($row) {
            $row->kelas_id = Crypt::encryptString((string) $row->kelas_id);
            $row->sks = (int) $row->sks_teori + (int) $row->sks_praktek + (int) $row->sks_praktikum;

            return $row;
        });

        return $this->success([
            'kode_tahun_akademik' => $kodeTa,
            'tahun_akademik' => $ta->tahun_akademik,
            'semester' => $ta->semester === '0' ? '2' : $ta->semester,
            'kode_program_studi' => $kodeProdi,
            'nama_program_studi' => $prodi->nama_program_studi,
            'data' => $data,
        ], 'Data Kelas retrieved successfully');
    }

    /**
     * GET /api/v1/kelas/detail?kelas_id=...&page=1&per_page=15
     */
    public function detail(Request $request): JsonResponse
    {
        $validator = Validator::make($request->query(), [
            'kelas_id' => 'required|string',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation Error', 422, $validator->errors()->toArray());
        }

        try {
            $kelasId = (int) Crypt::decryptString($request->query('kelas_id'));
        } catch (DecryptException $e) {
            return $this->error('Parameter tidak valid (gagal dekripsi).', 422);
        }

        $kelas = DB::table('kelas as k')
            ->join('matakuliah as mk', 'k.id_matakuliah', '=', 'mk.id_matakuliah')
            ->leftJoin('nama_kelas as nk', 'k.nama_kelas_id', '=', 'nk.nama_kelas_id')
            ->leftJoin('program_studi as ps', 'k.kode_program_studi', '=', 'ps.kode_program_studi')
            ->leftJoin('tahun_akademik as ta', 'k.kode_tahun_akademik', '=', 'ta.kode_tahun_akademik')
            ->where('k.kelas_id', $kelasId)
            ->select(
                'k.kelas_id',
                'mk.kode_matakuliah',
                'mk.nama_matakuliah',
                'mk.sks_teori',
                'mk.sks_praktek',
                'mk.sks_praktikum',
                'nk.nama_kelas',
                'k.semester',
                'k.kode_program_studi',
                'ps.nama_program_studi',
                'k.kode_tahun_akademik',
                'ta.tahun_akademik',
                'ta.semester as semester_ta',
            )
            ->first();

        if ($kelas === null) {
            return $this->error('Kelas not found', 404);
        }

        $dosen = DB::table('mengajar as m')
            ->join('dosen as d', 'm.kode_dosen', '=', 'd.kode_dosen')
            ->where('m.kelas_id', $kelasId)
            ->orderBy('d.nama_dosen')
            ->select('d.kode_dosen', 'd.nik', 'd.nama_dosen', 'd.status_dosen')
            ->get()
            ->map(function ($row) {
                return [
                    'kode_dosen' => Crypt::encryptString((string) $row->kode_dosen),
                    'nik' => $row->nik,
                    'nama_dosen' => $row->nama_dosen,
                    'status_dosen' => $row->status_dosen,
                ];
            });

        $perPage = (int) $request->query('per_page', 15);

        $mahasiswa = DB::table('kelas_mahasiswa as km')
            ->join('krs_detail as kd', 'km.kode_krs_detail', '=', 'kd.kode_krs_detail')
            ->join('krs as kr', 'kd.kode_krs', '=', 'kr.kode_krs')
            ->join('mahasiswa as mhs', 'kr.nim', '=', 'mhs.nim')
            ->leftJoin('program_studi as ps', 'mhs.program_studi_kode', '=', 'ps.kode_program_studi')
            ->where('km.kelas_id', $kelasId)
            ->orderBy('mhs.nama_mahasiswa')
            ->select(
                'mhs.nim',
                'mhs.nama_mahasiswa',
                'mhs.program_studi_kode',
                'ps.nama_program_studi',
                'kd.status',
            )
            ->paginate($perPage);

        $mahasiswa->through(function ($row) {
            $row->nim = Crypt::encryptString((string) $row->nim);

            return $row;
        });

        $kelas->kelas_id = Crypt::encryptString((string) $kelas->kelas_id);
        $kelas->sks = (int) $kelas->sks_teori + (int) $kelas->sks_praktek + (int) $kelas->sks_praktikum;
        $kelas->semester_ta = $kelas->semester_ta === '0' ? '2' : $kelas->semester_ta;

        return $this->success([
            'kelas' => $kelas,
            'dosen' => $dosen,
            'mahasiswa' => $mahasiswa,
            'summary' => [
                'jumlah_dosen' => $dosen->count(),
                'jumlah_mahasiswa' => $mahasiswa->total(),
            ],
        ], 'Detail Kelas retrieved successfully');
    }
}
