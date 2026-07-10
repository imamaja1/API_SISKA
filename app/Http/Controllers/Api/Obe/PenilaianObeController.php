<?php

namespace App\Http\Controllers\Api\Obe;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Kelas;
use App\Models\KelasMahasiswa;
use App\Models\KHSDetail;
use App\Models\TahunAkademik;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class PenilaianObeController extends Controller
{
    private $TahunAkademik;

    public function __construct()
    {
        $this->TahunAkademik = TahunAkademik::where(
            "status",
            "A",
        )->first()->kode_tahun_akademik;
    }

    public function me(Request $request)
    {
        // Ambil user dari Sanctum session/cookie auth
        $user = $request->user();
        if (!$user instanceof Dosen) {
            return response()->json(
                [
                    "status" => false,
                    "message" => "Unauthenticated",
                ],
                401,
            );
        }

        return response()->json(
            [
                "status" => true,
                "data" => $user,
            ],
            200,
        );
    }

    public function kelas(Request $request)
    {
        // Ambil user dari session/cookie auth (guard: dosen_web).
        // Middleware `auth:dosen_web` akan membuat $request->user() mengacu ke guard tersebut.
        $user = $request->user();
        if (!$user instanceof Dosen) {
            return response()->json(
                [
                    "status" => false,
                    "message" => "Unauthenticated",
                ],
                401,
            );
        }

        $kode_dosen = $user->getKey();

        $data = Kelas::select(
            "kelas.kelas_id as id",
            "kode_dosen",
            "kelas.kelas_id as code_kelas",
            "matakuliah.nama_matakuliah",
            "kelas.nama_kelas_id",
            "nama_kelas",
            "matakuliah.id_matakuliah",
            DB::raw("COUNT(kelas_mahasiswa.kelas_id) as jumlah_mahasiswa"),
        )
            ->join("mengajar", "kelas.kelas_id", "=", "mengajar.kelas_id")
            ->join(
                "nama_kelas",
                "kelas.nama_kelas_id",
                "=",
                "nama_kelas.nama_kelas_id",
            )
            ->join(
                "matakuliah",
                "kelas.id_matakuliah",
                "=",
                "matakuliah.id_matakuliah",
            )
            ->join(
                "kelas_mahasiswa",
                "kelas.kelas_id",
                "=",
                "kelas_mahasiswa.kelas_id",
            )
            ->join(
                "krs_detail",
                "kelas_mahasiswa.kode_krs_detail",
                "=",
                "krs_detail.kode_krs_detail",
            )
            ->join("krs", "krs_detail.kode_krs", "=", "krs.kode_krs")
            ->where("mengajar.kode_dosen", $kode_dosen)
            ->where("kelas.kode_tahun_akademik", $this->TahunAkademik)
            ->whereRaw("SUBSTR(krs.nim, 1, 2) > 24")
            ->groupBy(
                "kode_dosen",
                "kelas.semester",
                "kelas.kelas_id",
                "kelas.nama_kelas_id",
                "nama_kelas",
                "matakuliah.id_matakuliah",
                "matakuliah.nama_matakuliah",
            )
            ->having("jumlah_mahasiswa", ">", 0)
            ->get()
            ->map(function ($item) {
                $item->code_kelas = Crypt::encryptString($item->code_kelas);

                return $item;
            });

        return response()->json(
            [
                "status" => true,
                "data" => $data,
            ],
            200,
        );
    }

    public function penilaian(Request $request)
    {
        $validated = $request->validate([
            "code_kelas" => "required|string",
        ]);

        try {
            // Encrypted string sering mengandung '+'. Di URL query, '+' bisa terdecode jadi spasi.
            $encryptedCodeKelas = str_replace(
                " ",
                "+",
                $validated["code_kelas"],
            );
            $kode_kelas = Crypt::decryptString($encryptedCodeKelas);
        } catch (DecryptException $e) {
            return response()->json(
                [
                    "status" => false,
                    "message" => "Invalid code_kelas",
                ],
                422,
            );
        }

        $data = KelasMahasiswa::select(
            "khs_detail.kode_krs_detail as id",
            "khs_detail.kode_khs_detail as code_penilaian",
            "mahasiswa.nim",
            "mahasiswa.nama_mahasiswa",
            "khs_detail.nilai_harian",
            "khs_detail.nilai_uts",
            "khs_detail.nilai_uas",
            "khs_detail.nilai_akhir",
        )
            ->join(
                "krs_detail",
                "kelas_mahasiswa.kode_krs_detail",
                "=",
                "krs_detail.kode_krs_detail",
            )
            ->join(
                "khs_detail",
                "krs_detail.kode_krs_detail",
                "=",
                "khs_detail.kode_krs_detail",
            )
            ->join("krs", "krs_detail.kode_krs", "=", "krs.kode_krs")
            ->join("mahasiswa", "krs.nim", "=", "mahasiswa.nim")
            ->limit(60)
            ->where("kelas_id", $kode_kelas)
            ->get()
            ->map(function ($item) {
                $item->code_penilaian = Crypt::encryptString(
                    $item->code_penilaian,
                );

                return $item;
            });

        return response()->json(
            [
                "status" => true,
                "data" => $data,
            ],
            200,
        );
    }

    public function updatePenilaian(Request $request)
    {
        $validated = $request->validate([
            "code_penilaian" => "required|string",
            "nilai_harian" => "nullable|numeric|min:0|max:100",
            "nilai_uts" => "nullable|numeric|min:0|max:100",
            "nilai_uas" => "nullable|numeric|min:0|max:100",
            "nilai_akhir" => "nullable|numeric|min:0|max:100",
        ]);
        try {
            $kode_khs_detail = Crypt::decryptString(
                $validated["code_penilaian"],
            );
        } catch (DecryptException $e) {
            return response()->json(
                [
                    "status" => false,
                    "message" => "Invalid code_penilaian",
                ],
                422,
            );
        }
        $khs_detail = KHSDetail::where(
            "kode_khs_detail",
            $kode_khs_detail,
        )->first();
        if (!$khs_detail) {
            return response()->json(
                [
                    "status" => false,
                    "message" => "khs_detail not found",
                ],
                404,
            );
        } else {
            $nilai_harian =
                $validated["nilai_harian"] ?? $khs_detail->nilai_harian;
            $nilai_uts = $validated["nilai_uts"] ?? $khs_detail->nilai_uts;
            $nilai_uas = $validated["nilai_uas"] ?? $khs_detail->nilai_uas;
            $nilai_akhir =
                $validated["nilai_akhir"] ?? $khs_detail->nilai_akhir;

            KHSDetail::where("kode_khs_detail", $kode_khs_detail)->update([
                "nilai_harian" => $nilai_harian,
                "nilai_uts" => $nilai_uts,
                "nilai_uas" => $nilai_uas,
                "nilai_akhir" => $nilai_akhir,
            ]);

            return response()->json(
                [
                    "status" => true,
                    "message" => "Penilaian updated successfully",
                ],
                200,
            );
        }
    }

    public function updatePenilaianAll(Request $request)
    {
        $validated = $request->validate([
            "*.code_penilaian" => "required|string",
            "*.nilai_harian" => "nullable|numeric|min:0|max:100",
            "*.nilai_uts" => "nullable|numeric|min:0|max:100",
            "*.nilai_uas" => "nullable|numeric|min:0|max:100",
            "*.nilai_akhir" => "nullable|numeric|min:0|max:100",
        ]);

        $updates = [];
        foreach ($validated as $item) {
            try {
                $kode_khs_detail = Crypt::decryptString(
                    $item["code_penilaian"],
                );
                $updates[] = [
                    "kode_khs_detail" => $kode_khs_detail,
                    "nilai_harian" => $item["nilai_harian"] ?? null,
                    "nilai_uts" => $item["nilai_uts"] ?? null,
                    "nilai_uas" => $item["nilai_uas"] ?? null,
                    "nilai_akhir" => $item["nilai_akhir"] ?? null,
                ];
            } catch (DecryptException $e) {
                return response()->json(
                    [
                        "status" => false,
                        "message" => "Invalid code_penilaian for item with code_penilaian: {$item["code_penilaian"]}",
                    ],
                    422,
                );
            }
        }

        DB::transaction(function () use ($updates) {
            foreach ($updates as $update) {
                KHSDetail::where(
                    "kode_khs_detail",
                    $update["kode_khs_detail"],
                )->update([
                    "nilai_harian" => $update["nilai_harian"],
                    "nilai_uts" => $update["nilai_uts"],
                    "nilai_uas" => $update["nilai_uas"],
                    "nilai_akhir" => $update["nilai_akhir"],
                ]);
            }
        });

        return response()->json(
            [
                "status" => true,
                "message" => "Semua penilaian updated successfully",
            ],
            200,
        );
    }

    public function obe_penilaian(Request $request)
    {
        $validated = $request->validate([
            "code_kelas" => "required|string",
            "nim" => "nullable|string|regex:/^\d{11,13}$/",
        ]);

        preg_match("/[A-Za-z](\d+)-/", $validated["code_kelas"], $matches);

        if (!isset($matches[1])) {
            return response()->json(
                [
                    "status" => false,
                    "message" => "Invalid code_kelas",
                ],
                422,
            );
        }

        $kode_kelas = $matches[1];

        $query = KelasMahasiswa::select(
            "khs_detail.kode_krs_detail as id",
            "khs_detail.kode_khs_detail as code_penilaian",
            "mahasiswa.nim",
            "mahasiswa.nama_mahasiswa",
            "khs_detail.nilai_harian",
            "khs_detail.nilai_uts",
            "khs_detail.nilai_uas",
            "khs_detail.nilai_akhir",
            "sistem_penilaian_detail.grade",
        )
            ->join(
                "krs_detail",
                "kelas_mahasiswa.kode_krs_detail",
                "=",
                "krs_detail.kode_krs_detail",
            )
            ->join(
                "khs_detail",
                "krs_detail.kode_krs_detail",
                "=",
                "khs_detail.kode_krs_detail",
            )
            ->join("krs", "krs_detail.kode_krs", "=", "krs.kode_krs")
            ->join("mahasiswa", "krs.nim", "=", "mahasiswa.nim")
            ->leftJoin('sistem_penilaian_detail', function ($join) {
                $join->where('sistem_penilaian_detail.kode_sistem_penilaian', 1)
                    ->whereColumn('khs_detail.nilai_akhir', '>=', 'sistem_penilaian_detail.nilai_minimum')
                    ->whereColumn('khs_detail.nilai_akhir', '<=', 'sistem_penilaian_detail.nilai_maksimum');
            })
            ->where("kelas_id", $kode_kelas);

        if (!empty($validated["nim"])) {
            $query->where("mahasiswa.nim", $validated["nim"]);
        }

        $data = $query->get()->map(function ($item) {
            $item->code_penilaian = Crypt::encryptString(
                $item->code_penilaian,
            );

            return $item;
        });

        return response()->json(
            [
                "status" => true,
                "data" => $data,
            ],
            200,
        );
    }
}
