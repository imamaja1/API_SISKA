<?php

namespace App\Http\Controllers\Api\Divisi;

use App\Http\Controllers\Api\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Dosen;
use Exception;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class DosenController extends Controller
{
    use ApiResponse;

    /**
     * Helper untuk mendekripsi atau mengonversi kode_dosen (mendukung string terenkripsi atau integer).
     */
    private function resolveKodeDosen($input): ?int
    {
        if ($input === null || $input === '') {
            return null;
        }

        if (is_numeric($input)) {
            return (int) $input;
        }

        if (is_string($input)) {
            try {
                $decrypted = Crypt::decryptString($input);
                if (is_numeric($decrypted)) {
                    return (int) $decrypted;
                }
            } catch (DecryptException $e) {
                return null;
            }
        }

        return null;
    }

    /**
     * Search & Filter Dosen (Semua parameter dikirim di Request Body).
     * POST /api/v1/divisi/dosen/search
     * POST /api/v1/divisi/get-dosen
     */
    public function search(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'search'       => 'nullable|string|max:100',
            'nik'          => 'nullable|string|max:50',
            'nama'         => 'nullable|string|max:255',
            'nama_dosen'   => 'nullable|string|max:255',
            'homebase'     => 'nullable|integer',
            'status_dosen' => 'nullable|string|max:10',
            'aktif'        => 'nullable|string|in:A,N',
            'page'         => 'nullable|integer|min:1',
            'per_page'     => 'nullable|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation Error', 422, $validator->errors()->toArray());
        }

        $perPage = (int) ($request->input('per_page') ?: $request->query('per_page', 15));
        if ($perPage < 1) {
            $perPage = 15;
        }
        if ($perPage > 100) {
            $perPage = 100;
        }

        $query = Dosen::query()
            ->with(['programStudi:kode_program_studi,nama_program_studi,singkatan_program_studi'])
            ->select([
                'kode_dosen',
                'nama_dosen',
                'nik',
                'field_studi',
                'alumni',
                'homebase',
                'status_dosen',
                'alamat_email',
                'no_telp',
                'status_login',
                'aktif',
            ]);

        // Filter NIK spesifik
        if ($request->filled('nik')) {
            $nik = trim((string) $request->input('nik'));
            $query->where('nik', 'like', '%' . $nik . '%');
        }

        // Filter Nama spesifik
        $nama = $request->input('nama') ?: $request->input('nama_dosen');
        if (! empty($nama)) {
            $query->where('nama_dosen', 'like', '%' . trim((string) $nama) . '%');
        }

        // Pencarian global (mencocokkan nama_dosen ATAU nik)
        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('nama_dosen', 'like', '%' . $search . '%')
                  ->orWhere('nik', 'like', '%' . $search . '%');
            });
        }

        // Filter Homebase / Prodi
        if ($request->filled('homebase')) {
            $query->where('homebase', (int) $request->input('homebase'));
        }

        // Filter Status Dosen
        if ($request->filled('status_dosen')) {
            $query->where('status_dosen', trim((string) $request->input('status_dosen')));
        }

        // Filter Status Aktif
        if ($request->filled('aktif')) {
            $query->where('aktif', trim((string) $request->input('aktif')));
        }

        $paginator = $query->orderBy('nama_dosen', 'asc')->paginate($perPage);

        // Transform collection agar kode_dosen dienkripsi secara aman
        $transformed = $paginator->getCollection()->map(function ($item) {
            return [
                'kode_dosen'         => Crypt::encryptString((string) $item->kode_dosen),
                'nama_dosen'         => $item->nama_dosen,
                'nik'                => $item->nik,
                'field_studi'        => $item->field_studi,
                'alumni'             => $item->alumni,
                'homebase'           => $item->homebase,
                'nama_program_studi' => $item->programStudi->nama_program_studi ?? null,
                'status_dosen'       => $item->status_dosen,
                'alamat_email'       => $item->alamat_email,
                'no_telp'            => $item->no_telp,
                'status_login'       => $item->status_login,
                'aktif'              => $item->aktif,
            ];
        });

        $paginator->setCollection($transformed);

        return $this->success(
            ['data' => $paginator],
            'Data Dosen retrieved successfully'
        );
    }

    /**
     * Ambil Detail 1 Dosen via Request Body.
     * POST /api/v1/divisi/dosen/show
     * POST /api/v1/divisi/dosen-show
     */
    public function show(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'kode_dosen' => 'nullable',
            'nik'        => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation Error', 422, $validator->errors()->toArray());
        }

        $kodeDosen = $this->resolveKodeDosen($request->input('kode_dosen'));
        $nik = $request->input('nik');

        if ($kodeDosen === null && empty($nik)) {
            return $this->error('Parameter kode_dosen atau nik wajib diisi di body request.', 422);
        }

        $query = Dosen::query()
            ->with(['programStudi:kode_program_studi,nama_program_studi,singkatan_program_studi'])
            ->select([
                'kode_dosen',
                'nama_dosen',
                'nik',
                'field_studi',
                'alumni',
                'homebase',
                'status_dosen',
                'alamat_email',
                'no_telp',
                'status_login',
                'aktif',
                'created_at',
                'updated_at',
            ]);

        if ($kodeDosen !== null) {
            $query->where('kode_dosen', $kodeDosen);
        } elseif (! empty($nik)) {
            $query->where('nik', trim((string) $nik));
        }

        $dosen = $query->first();

        if ($dosen === null) {
            return $this->error('Dosen tidak ditemukan', 404);
        }

        $responseData = [
            'kode_dosen'         => Crypt::encryptString((string) $dosen->kode_dosen),
            'nama_dosen'         => $dosen->nama_dosen,
            'nik'                => $dosen->nik,
            'field_studi'        => $dosen->field_studi,
            'alumni'             => $dosen->alumni,
            'homebase'           => $dosen->homebase,
            'nama_program_studi' => $dosen->programStudi->nama_program_studi ?? null,
            'status_dosen'       => $dosen->status_dosen,
            'alamat_email'       => $dosen->alamat_email,
            'no_telp'            => $dosen->no_telp,
            'status_login'       => $dosen->status_login,
            'aktif'              => $dosen->aktif,
            'created_at'         => $dosen->created_at,
            'updated_at'         => $dosen->updated_at,
        ];

        return $this->success(
            ['data' => $responseData],
            'Detail Dosen retrieved successfully'
        );
    }

    /**
     * Update Data Dosen via Request Body.
     * PUT  /api/v1/divisi/dosen/update
     * PUT  /api/v1/divisi/update-dosen
     * POST /api/v1/divisi/update-dosen
     */
    public function update(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'kode_dosen'   => 'required',
            'nama_dosen'   => 'sometimes|required|string|max:255',
            'nik'          => 'sometimes|nullable|string|max:50',
            'field_studi'  => 'sometimes|nullable|string|max:100',
            'alumni'       => 'sometimes|nullable|string|max:100',
            'homebase'     => 'sometimes|nullable|integer',
            'status_dosen' => 'sometimes|nullable|string|max:10',
            'alamat_email' => 'sometimes|nullable|email|max:100',
            'no_telp'      => 'sometimes|nullable|string|max:30',
            'aktif'        => 'sometimes|nullable|in:A,N',
            'status_login' => 'sometimes|nullable|in:Y,N',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation Error', 422, $validator->errors()->toArray());
        }

        $kodeDosen = $this->resolveKodeDosen($request->input('kode_dosen'));
        if ($kodeDosen === null) {
            return $this->error('Parameter kode_dosen tidak valid (gagal dekripsi).', 422);
        }

        $dosen = Dosen::where('kode_dosen', $kodeDosen)->first();
        if ($dosen === null) {
            return $this->error('Dosen tidak ditemukan', 404);
        }

        $updatableFields = [
            'nama_dosen',
            'nik',
            'field_studi',
            'alumni',
            'homebase',
            'status_dosen',
            'alamat_email',
            'no_telp',
            'aktif',
            'status_login',
        ];

        $updateData = [];
        foreach ($updatableFields as $field) {
            if ($request->has($field)) {
                $updateData[$field] = $request->input($field);
            }
        }

        if (empty($updateData)) {
            return $this->error('Tidak ada field yang dikirim untuk diperbarui.', 422);
        }

        try {
            $dosen->update($updateData);
            $dosen->refresh();
        } catch (QueryException $e) {
            return $this->error('Database Error saat memperbarui dosen.', 500, ['exception' => $e->getMessage()]);
        } catch (Exception $e) {
            return $this->error('Server Error saat memperbarui dosen.', 500, ['exception' => $e->getMessage()]);
        }

        $responseData = [
            'kode_dosen'   => Crypt::encryptString((string) $dosen->kode_dosen),
            'nama_dosen'   => $dosen->nama_dosen,
            'nik'          => $dosen->nik,
            'field_studi'  => $dosen->field_studi,
            'alumni'       => $dosen->alumni,
            'homebase'     => $dosen->homebase,
            'status_dosen' => $dosen->status_dosen,
            'alamat_email' => $dosen->alamat_email,
            'no_telp'      => $dosen->no_telp,
            'aktif'        => $dosen->aktif,
            'status_login' => $dosen->status_login,
            'updated_at'   => $dosen->updated_at,
        ];

        return $this->success(
            ['data' => $responseData],
            'Data Dosen berhasil diperbarui'
        );
    }
}
