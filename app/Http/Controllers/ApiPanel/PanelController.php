<?php

namespace App\Http\Controllers\ApiPanel;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PanelController extends Controller
{
    public function index(Request $request)
    {
        $baseUrl = request()->root() ?: 'http://127.0.0.1:8000';
        $data = $this->buildDocumentationData($baseUrl);

        return view('api_panel.index', [
            'categories' => $data['categories'],
            'totalRoutes' => $data['totalRoutes'],
            'initialEndpoint' => $data['initialEndpoint'],
            'initialTester' => $data['initialTester'],
            'baseUrl' => $baseUrl,
            'user' => auth('api_users_web')->user(),
        ]);
    }

    public function proxy(Request $request)
    {
        $method = strtoupper($request->input('method', 'GET'));
        $url = $request->input('url');
        $headers = $request->input('headers', []);
        $body = $request->input('body');
        $params = $request->input('params', []);

        if (! $url) {
            return response()->json(['error' => 'URL is required'], 400);
        }

        $startTime = microtime(true);

        try {
            $client = Http::withHeaders($headers ?: ['Accept' => 'application/json'])
                ->timeout(20)
                ->withoutVerifying();

            if ($method === 'GET') {
                $response = $client->get($url, $params ?: []);
            } elseif ($method === 'POST') {
                $response = $client->withBody(is_array($body) ? json_encode($body, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : (string) $body, 'application/json')->post($url);
            } elseif ($method === 'PUT') {
                $response = $client->withBody(is_array($body) ? json_encode($body, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : (string) $body, 'application/json')->put($url);
            } elseif ($method === 'PATCH') {
                $response = $client->withBody(is_array($body) ? json_encode($body, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : (string) $body, 'application/json')->patch($url);
            } elseif ($method === 'DELETE') {
                $response = $client->delete($url, $params ?: []);
            } else {
                $response = $client->send($method, $url, ['query' => $params, 'body' => $body]);
            }

            $elapsedMs = round((microtime(true) - $startTime) * 1000);

            $bodyData = $response->json();
            if ($bodyData === null) {
                $bodyData = $response->body();
            }

            return response()->json([
                'status' => $response->status(),
                'statusText' => $response->reason() ?: ($response->successful() ? 'OK' : 'Error'),
                'headers' => $response->headers(),
                'body' => $bodyData,
                'raw_body' => $response->body(),
                'time_ms' => $elapsedMs,
                'size_bytes' => strlen($response->body()),
            ]);
        } catch (\Throwable $e) {
            $elapsedMs = round((microtime(true) - $startTime) * 1000);

            return response()->json([
                'status' => 500,
                'statusText' => 'Request Failed',
                'error' => $e->getMessage(),
                'time_ms' => $elapsedMs,
            ], 500);
        }
    }

    private function buildDocumentationData(string $baseUrl = 'http://127.0.0.1:8000'): array
    {
        $categories = [];

        // 1. Auth - Mahasiswa (7)
        $categories[] = [
            'id' => 'auth-mahasiswa',
            'name' => 'Auth - Mahasiswa',
            'icon' => 'folder',
            'count' => 7,
            'endpoints' => [
                [
                    'id' => 'ep-auth-mhs-csrf',
                    'method' => 'GET',
                    'path' => '/api/auth/csrf-cookie',
                    'full_url' => $baseUrl . '/api/auth/csrf-cookie',
                    'status' => 'AKTIF',
                    'category' => 'Auth - Mahasiswa',
                    'breadcrumb' => 'AUTH - MAHASISWA • API GATEWAY',
                    'judul' => 'CSRF Cookie',
                    'description' => 'Ambil XSRF-TOKEN cookie untuk autentikasi Sanctum SPA. Wajib dipanggil sebelum login.',
                    'is_sanctum' => true,
                    'sanctum_note' => 'Autentikasi & Proteksi Sanctum: Endpoint terlindungi memerlukan session cookie (laravel_session) dan token CSRF (X-XSRF-TOKEN). Pada pengujian via API Tester, token dan session akan otomatis disertakan.',
                    'headers' => [
                        ['key' => 'Accept', 'type' => 'string', 'status' => 'Wajib', 'keterangan' => 'Harus bernilai application/json'],
                    ],
                    'params_schema' => '// Endpoint ini tidak memerlukan parameter URL.',
                    'default_params' => null,
                    'default_body' => null,
                    'responses' => [
                        '200' => json_encode(['message' => 'XSRF-TOKEN cookie set'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                        '401' => json_encode(['message' => 'Unauthenticated.'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                        '422' => json_encode(['message' => 'The given data was invalid.'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                    ],
                ],
                [
                    'id' => 'ep-auth-mhs-login',
                    'method' => 'POST',
                    'path' => '/api/auth/mhs/login',
                    'full_url' => $baseUrl . '/api/auth/mhs/login',
                    'status' => 'AKTIF',
                    'category' => 'Auth - Mahasiswa',
                    'breadcrumb' => 'AUTH - MAHASISWA • API GATEWAY',
                    'judul' => 'Login Mahasiswa',
                    'description' => 'Autentikasi mahasiswa menggunakan NIM dan password untuk mendapatkan sesi Sanctum SPA.',
                    'is_sanctum' => true,
                    'sanctum_note' => 'Endpoint terlindungi memerlukan token CSRF (X-XSRF-TOKEN) yang didapatkan dari pemanggilan /api/auth/csrf-cookie.',
                    'headers' => [
                        ['key' => 'Accept', 'type' => 'string', 'status' => 'Wajib', 'keterangan' => 'Harus bernilai application/json'],
                        ['key' => 'Content-Type', 'type' => 'string', 'status' => 'Wajib', 'keterangan' => 'Harus bernilai application/json'],
                        ['key' => 'X-XSRF-TOKEN', 'type' => 'string', 'status' => 'Wajib', 'keterangan' => 'Token CSRF Sanctum'],
                    ],
                    'params_schema' => '// Endpoint ini tidak memerlukan parameter URL.',
                    'default_params' => null,
                    'default_body' => json_encode(['nim' => '2201010167', 'password' => 'password123'], JSON_PRETTY_PRINT),
                    'responses' => [
                        '200' => json_encode(['status' => true, 'message' => 'Login berhasil', 'token' => '1|token...', 'user' => ['nim' => '2201010167', 'nama_mahasiswa' => 'MUHAMMAD ADRIYAN MAZKUR', 'prodi' => 'S1 Ilmu Komputer']], JSON_PRETTY_PRINT),
                        '401' => json_encode(['status' => false, 'message' => 'NIM atau password salah'], JSON_PRETTY_PRINT),
                        '422' => json_encode(['message' => 'The given data was invalid.', 'errors' => ['nim' => ['Field nim wajib diisi.']]], JSON_PRETTY_PRINT),
                    ],
                ],
                [
                    'id' => 'ep-auth-mhs-logout',
                    'method' => 'POST',
                    'path' => '/api/mhs/logout',
                    'full_url' => $baseUrl . '/api/mhs/logout',
                    'status' => 'AKTIF',
                    'category' => 'Auth - Mahasiswa',
                    'breadcrumb' => 'AUTH - MAHASISWA • API GATEWAY',
                    'judul' => 'Logout Mahasiswa',
                    'description' => 'Mengakhiri sesi aktif mahasiswa dan menghapus session cookie.',
                    'is_sanctum' => true,
                    'sanctum_note' => 'Endpoint ini memerlukan session cookie dan CSRF token aktif.',
                    'headers' => [
                        ['key' => 'Accept', 'type' => 'string', 'status' => 'Wajib', 'keterangan' => 'Harus bernilai application/json'],
                        ['key' => 'X-XSRF-TOKEN', 'type' => 'string', 'status' => 'Wajib', 'keterangan' => 'Token CSRF Sanctum'],
                    ],
                    'params_schema' => '// Endpoint ini tidak memerlukan parameter URL.',
                    'default_params' => null,
                    'default_body' => '{}',
                    'responses' => [
                        '200' => json_encode(['status' => true, 'message' => 'Logout berhasil'], JSON_PRETTY_PRINT),
                        '401' => json_encode(['message' => 'Unauthenticated.'], JSON_PRETTY_PRINT),
                        '422' => json_encode(['message' => 'Unprocessable Entity'], JSON_PRETTY_PRINT),
                    ],
                ],
                [
                    'id' => 'ep-auth-mhs-me',
                    'method' => 'GET',
                    'path' => '/api/mhs/me',
                    'full_url' => $baseUrl . '/api/mhs/me',
                    'status' => 'AKTIF',
                    'category' => 'Auth - Mahasiswa',
                    'breadcrumb' => 'AUTH - MAHASISWA • API GATEWAY',
                    'judul' => 'Profil Mahasiswa Aktif',
                    'description' => 'Mendapatkan profil dan ringkasan data mahasiswa yang sedang login.',
                    'is_sanctum' => true,
                    'sanctum_note' => 'Endpoint terlindungi session Sanctum.',
                    'headers' => [
                        ['key' => 'Accept', 'type' => 'string', 'status' => 'Wajib', 'keterangan' => 'Harus bernilai application/json'],
                    ],
                    'params_schema' => '// Endpoint ini tidak memerlukan parameter URL.',
                    'default_params' => null,
                    'default_body' => null,
                    'responses' => [
                        '200' => json_encode(['status' => true, 'data' => ['nim' => '2201010167', 'nama' => 'MUHAMMAD ADRIYAN MAZKUR', 'prodi' => 'S1 Ilmu Komputer', 'semester' => 8, 'ipk' => 3.75]], JSON_PRETTY_PRINT),
                        '401' => json_encode(['message' => 'Unauthenticated.'], JSON_PRETTY_PRINT),
                        '422' => json_encode(['message' => 'Invalid request'], JSON_PRETTY_PRINT),
                    ],
                ],
                [
                    'id' => 'ep-auth-mhs-profile',
                    'method' => 'GET',
                    'path' => '/api/mhs/profile',
                    'full_url' => $baseUrl . '/api/mhs/profile',
                    'status' => 'AKTIF',
                    'category' => 'Auth - Mahasiswa',
                    'breadcrumb' => 'AUTH - MAHASISWA • API GATEWAY',
                    'judul' => 'Detail Profil Mahasiswa',
                    'description' => 'Mendapatkan data lengkap biodata, alamat, orang tua, dan status akademik mahasiswa.',
                    'is_sanctum' => true,
                    'sanctum_note' => 'Endpoint terlindungi session Sanctum.',
                    'headers' => [
                        ['key' => 'Accept', 'type' => 'string', 'status' => 'Wajib', 'keterangan' => 'Harus bernilai application/json'],
                    ],
                    'params_schema' => '// Endpoint ini tidak memerlukan parameter URL.',
                    'default_params' => null,
                    'default_body' => null,
                    'responses' => [
                        '200' => json_encode(['status' => true, 'data' => ['nim' => '2201010167', 'nama' => 'MUHAMMAD ADRIYAN MAZKUR', 'nik' => '5203051610030002', 'tempat_lahir' => 'DANGER', 'tanggal_lahir' => '2003-10-16', 'jenis_kelamin' => 'L', 'agama' => 'Islam', 'telepon' => '085238022264', 'email' => '2201010167@universitasbumigora.ac.id']], JSON_PRETTY_PRINT),
                        '401' => json_encode(['message' => 'Unauthenticated.'], JSON_PRETTY_PRINT),
                        '422' => json_encode(['message' => 'Invalid request'], JSON_PRETTY_PRINT),
                    ],
                ],
                [
                    'id' => 'ep-auth-mhs-profile-update',
                    'method' => 'PUT',
                    'path' => '/api/mhs/profile/update',
                    'full_url' => $baseUrl . '/api/mhs/profile/update',
                    'status' => 'AKTIF',
                    'category' => 'Auth - Mahasiswa',
                    'breadcrumb' => 'AUTH - MAHASISWA • API GATEWAY',
                    'judul' => 'Update Profil Mahasiswa',
                    'description' => 'Memperbarui informasi kontak nomor telepon, email, dan alamat domisili mahasiswa.',
                    'is_sanctum' => true,
                    'sanctum_note' => 'Endpoint terlindungi session Sanctum dan token CSRF.',
                    'headers' => [
                        ['key' => 'Accept', 'type' => 'string', 'status' => 'Wajib', 'keterangan' => 'Harus bernilai application/json'],
                        ['key' => 'Content-Type', 'type' => 'string', 'status' => 'Wajib', 'keterangan' => 'Harus bernilai application/json'],
                        ['key' => 'X-XSRF-TOKEN', 'type' => 'string', 'status' => 'Wajib', 'keterangan' => 'Token CSRF Sanctum'],
                    ],
                    'params_schema' => '// Endpoint ini tidak memerlukan parameter URL.',
                    'default_params' => null,
                    'default_body' => json_encode(['telepon' => '085238022264', 'email' => '2201010167@universitasbumigora.ac.id', 'alamat' => 'KAMPUNG WARTEL'], JSON_PRETTY_PRINT),
                    'responses' => [
                        '200' => json_encode(['status' => true, 'message' => 'Profil berhasil diperbarui'], JSON_PRETTY_PRINT),
                        '401' => json_encode(['message' => 'Unauthenticated.'], JSON_PRETTY_PRINT),
                        '422' => json_encode(['message' => 'The given data was invalid.', 'errors' => ['telepon' => ['Format nomor telepon tidak valid.']]], JSON_PRETTY_PRINT),
                    ],
                ],
                [
                    'id' => 'ep-auth-mhs-profile-foto',
                    'method' => 'PUT',
                    'path' => '/api/mhs/profile/foto',
                    'full_url' => $baseUrl . '/api/mhs/profile/foto',
                    'status' => 'AKTIF',
                    'category' => 'Auth - Mahasiswa',
                    'breadcrumb' => 'AUTH - MAHASISWA • API GATEWAY',
                    'judul' => 'Update Foto Profil',
                    'description' => 'Mengunggah dan memperbarui foto profil mahasiswa (format base64 image).',
                    'is_sanctum' => true,
                    'sanctum_note' => 'Endpoint terlindungi session Sanctum dan token CSRF.',
                    'headers' => [
                        ['key' => 'Accept', 'type' => 'string', 'status' => 'Wajib', 'keterangan' => 'Harus bernilai application/json'],
                        ['key' => 'Content-Type', 'type' => 'string', 'status' => 'Wajib', 'keterangan' => 'Harus bernilai application/json'],
                        ['key' => 'X-XSRF-TOKEN', 'type' => 'string', 'status' => 'Wajib', 'keterangan' => 'Token CSRF Sanctum'],
                    ],
                    'params_schema' => '// Endpoint ini tidak memerlukan parameter URL.',
                    'default_params' => null,
                    'default_body' => json_encode(['foto' => 'data:image/jpeg;base64,/9j/4AAQSkZJRg...'], JSON_PRETTY_PRINT),
                    'responses' => [
                        '200' => json_encode(['status' => true, 'message' => 'Foto profil berhasil diperbarui', 'foto_url' => $baseUrl . '/storage/foto/2201010167.jpg'], JSON_PRETTY_PRINT),
                        '401' => json_encode(['message' => 'Unauthenticated.'], JSON_PRETTY_PRINT),
                        '422' => json_encode(['message' => 'File gambar tidak valid.'], JSON_PRETTY_PRINT),
                    ],
                ],
            ],
        ];

        // 2. Auth - Staff (3)
        $categories[] = [
            'id' => 'auth-staff',
            'name' => 'Auth - Staff',
            'icon' => 'folder',
            'count' => 3,
            'endpoints' => [
                [
                    'id' => 'ep-auth-staff-login',
                    'method' => 'POST',
                    'path' => '/api/auth/staff/login',
                    'full_url' => $baseUrl . '/api/auth/staff/login',
                    'status' => 'AKTIF',
                    'category' => 'Auth - Staff',
                    'breadcrumb' => 'AUTH - STAFF • API GATEWAY',
                    'judul' => 'Login Staff',
                    'description' => 'Autentikasi staff/divisi akademik.',
                    'is_sanctum' => true,
                    'sanctum_note' => 'Mendukung login via Sanctum SPA session cookie atau Bearer token API.',
                    'headers' => [
                        ['key' => 'Accept', 'type' => 'string', 'status' => 'Wajib', 'keterangan' => 'Harus bernilai application/json'],
                        ['key' => 'Content-Type', 'type' => 'string', 'status' => 'Wajib', 'keterangan' => 'Harus bernilai application/json'],
                    ],
                    'params_schema' => '// Endpoint ini tidak memerlukan parameter URL.',
                    'default_params' => null,
                    'default_body' => json_encode(['username' => 'akademik', 'password' => 'password123'], JSON_PRETTY_PRINT),
                    'responses' => [
                        '200' => json_encode(['status' => true, 'message' => 'Login successful', 'token' => '671|staffBearerToken...', 'user' => ['kode_pengguna' => 38, 'nama_login' => 'akademik', 'nama_pengguna' => 'akademik']], JSON_PRETTY_PRINT),
                        '401' => json_encode(['status' => false, 'message' => 'Username atau password salah'], JSON_PRETTY_PRINT),
                        '422' => json_encode(['message' => 'The given data was invalid.'], JSON_PRETTY_PRINT),
                    ],
                ],
                [
                    'id' => 'ep-auth-staff-logout',
                    'method' => 'POST',
                    'path' => '/api/auth/logout',
                    'full_url' => $baseUrl . '/api/auth/logout',
                    'status' => 'AKTIF',
                    'category' => 'Auth - Staff',
                    'breadcrumb' => 'AUTH - STAFF • API GATEWAY',
                    'judul' => 'Logout Staff',
                    'description' => 'Mengakhiri sesi staff dan mencabut token otorisasi.',
                    'is_sanctum' => true,
                    'headers' => [
                        ['key' => 'Accept', 'type' => 'string', 'status' => 'Wajib', 'keterangan' => 'Harus bernilai application/json'],
                        ['key' => 'X-XSRF-TOKEN', 'type' => 'string', 'status' => 'Wajib', 'keterangan' => 'Token CSRF Sanctum'],
                    ],
                    'params_schema' => '// Endpoint ini tidak memerlukan parameter URL.',
                    'default_body' => '{}',
                    'responses' => [
                        '200' => json_encode(['status' => true, 'message' => 'Logout berhasil'], JSON_PRETTY_PRINT),
                        '401' => json_encode(['message' => 'Unauthenticated.'], JSON_PRETTY_PRINT),
                    ],
                ],
                [
                    'id' => 'ep-auth-staff-me',
                    'method' => 'GET',
                    'path' => '/api/staff/me',
                    'full_url' => $baseUrl . '/api/staff/me',
                    'status' => 'AKTIF',
                    'category' => 'Auth - Staff',
                    'breadcrumb' => 'AUTH - STAFF • API GATEWAY',
                    'judul' => 'Profil Staff Aktif',
                    'description' => 'Mendapatkan data staff yang sedang terautentikasi dan hak akses divisi.',
                    'is_sanctum' => true,
                    'headers' => [
                        ['key' => 'Accept', 'type' => 'string', 'status' => 'Wajib', 'keterangan' => 'Harus bernilai application/json'],
                    ],
                    'params_schema' => '// Endpoint ini tidak memerlukan parameter URL.',
                    'responses' => [
                        '200' => json_encode(['status' => true, 'data' => ['kode_pengguna' => 38, 'nama_login' => 'akademik', 'nama_pengguna' => 'akademik', 'id_role' => 2]], JSON_PRETTY_PRINT),
                        '401' => json_encode(['message' => 'Unauthenticated.'], JSON_PRETTY_PRINT),
                    ],
                ],
            ],
        ];

        // 3. Auth - Dosen (5)
        $categories[] = [
            'id' => 'auth-dosen',
            'name' => 'Auth - Dosen',
            'icon' => 'folder',
            'count' => 5,
            'endpoints' => [
                [
                    'id' => 'ep-auth-dosen-login',
                    'method' => 'POST',
                    'path' => '/api/auth/dosen/login',
                    'full_url' => $baseUrl . '/api/auth/dosen/login',
                    'status' => 'AKTIF',
                    'category' => 'Auth - Dosen',
                    'breadcrumb' => 'AUTH - DOSEN • API GATEWAY',
                    'judul' => 'Login Dosen',
                    'description' => 'Autentikasi dosen pengajar menggunakan email / NIK dan password.',
                    'is_sanctum' => true,
                    'headers' => [
                        ['key' => 'Accept', 'type' => 'string', 'status' => 'Wajib', 'keterangan' => 'Harus bernilai application/json'],
                        ['key' => 'Content-Type', 'type' => 'string', 'status' => 'Wajib', 'keterangan' => 'Harus bernilai application/json'],
                    ],
                    'params_schema' => '// Endpoint ini tidak memerlukan parameter URL.',
                    'default_body' => json_encode(['email' => 'dyah.susilowati@universitasbumigora.ac.id', 'password' => 'password123'], JSON_PRETTY_PRINT),
                    'responses' => [
                        '200' => json_encode(['status' => true, 'message' => 'Login successful', 'token' => '672|dosenBearerToken...', 'user' => ['kode_dosen' => 2, 'nik' => '196804262005012001', 'nama_dosen' => 'Dr. Dyah Susilowati, M.Kom', 'alamat_email' => 'dyah.susilowati@universitasbumigora.ac.id']], JSON_PRETTY_PRINT),
                        '401' => json_encode(['status' => false, 'message' => 'Kredensial tidak sesuai'], JSON_PRETTY_PRINT),
                    ],
                ],
                [
                    'id' => 'ep-auth-dosen-logout',
                    'method' => 'POST',
                    'path' => '/api/dosen/logout',
                    'full_url' => $baseUrl . '/api/dosen/logout',
                    'status' => 'AKTIF',
                    'category' => 'Auth - Dosen',
                    'breadcrumb' => 'AUTH - DOSEN • API GATEWAY',
                    'judul' => 'Logout Dosen',
                    'description' => 'Mencabut sesi login dan token dosen.',
                    'is_sanctum' => true,
                    'headers' => [
                        ['key' => 'Accept', 'type' => 'string', 'status' => 'Wajib', 'keterangan' => 'Harus bernilai application/json'],
                    ],
                    'params_schema' => '// Endpoint ini tidak memerlukan parameter URL.',
                    'default_body' => '{}',
                    'responses' => [
                        '200' => json_encode(['status' => true, 'message' => 'Logout berhasil'], JSON_PRETTY_PRINT),
                        '401' => json_encode(['message' => 'Unauthenticated.'], JSON_PRETTY_PRINT),
                    ],
                ],
                [
                    'id' => 'ep-auth-dosen-me',
                    'method' => 'GET',
                    'path' => '/api/dosen/me',
                    'full_url' => $baseUrl . '/api/dosen/me',
                    'status' => 'AKTIF',
                    'category' => 'Auth - Dosen',
                    'breadcrumb' => 'AUTH - DOSEN • API GATEWAY',
                    'judul' => 'Profil Dosen Aktif',
                    'description' => 'Mendapatkan rincian data dosen yang sedang login, jabatan fungsional, dan status mengajar.',
                    'is_sanctum' => true,
                    'headers' => [
                        ['key' => 'Accept', 'type' => 'string', 'status' => 'Wajib', 'keterangan' => 'Harus bernilai application/json'],
                    ],
                    'params_schema' => '// Endpoint ini tidak memerlukan parameter URL.',
                    'responses' => [
                        '200' => json_encode(['status' => true, 'data' => ['kode_dosen' => 2, 'nik' => '196804262005012001', 'nama_dosen' => 'Dr. Dyah Susilowati, M.Kom', 'field_studi' => 'Teknik Informatika', 'alamat_email' => 'dyah.susilowati@universitasbumigora.ac.id', 'no_telp' => '081915788838']], JSON_PRETTY_PRINT),
                        '401' => json_encode(['message' => 'Unauthenticated.'], JSON_PRETTY_PRINT),
                    ],
                ],
                [
                    'id' => 'ep-auth-dosen-profile-update',
                    'method' => 'PUT',
                    'path' => '/api/dosen/profile/update',
                    'full_url' => $baseUrl . '/api/dosen/profile/update',
                    'status' => 'AKTIF',
                    'category' => 'Auth - Dosen',
                    'breadcrumb' => 'AUTH - DOSEN • API GATEWAY',
                    'judul' => 'Update Profil Dosen',
                    'description' => 'Memperbarui nomor kontak dan bidang keahlian dosen.',
                    'is_sanctum' => true,
                    'headers' => [
                        ['key' => 'Accept', 'type' => 'string', 'status' => 'Wajib', 'keterangan' => 'Harus bernilai application/json'],
                        ['key' => 'Content-Type', 'type' => 'string', 'status' => 'Wajib', 'keterangan' => 'Harus bernilai application/json'],
                    ],
                    'params_schema' => '// Endpoint ini tidak memerlukan parameter URL.',
                    'default_body' => json_encode(['telepon' => '081915788838', 'keahlian' => 'Teknik Informatika'], JSON_PRETTY_PRINT),
                    'responses' => [
                        '200' => json_encode(['status' => true, 'message' => 'Data dosen berhasil diperbarui'], JSON_PRETTY_PRINT),
                        '401' => json_encode(['message' => 'Unauthenticated.'], JSON_PRETTY_PRINT),
                    ],
                ],
                [
                    'id' => 'ep-auth-dosen-profile-foto',
                    'method' => 'PUT',
                    'path' => '/api/dosen/profile/foto',
                    'full_url' => $baseUrl . '/api/dosen/profile/foto',
                    'status' => 'AKTIF',
                    'category' => 'Auth - Dosen',
                    'breadcrumb' => 'AUTH - DOSEN • API GATEWAY',
                    'judul' => 'Update Foto Dosen',
                    'description' => 'Memperbarui pas foto resmi pengajar.',
                    'is_sanctum' => true,
                    'headers' => [
                        ['key' => 'Accept', 'type' => 'string', 'status' => 'Wajib', 'keterangan' => 'Harus bernilai application/json'],
                        ['key' => 'Content-Type', 'type' => 'string', 'status' => 'Wajib', 'keterangan' => 'Harus bernilai application/json'],
                    ],
                    'params_schema' => '// Endpoint ini tidak memerlukan parameter URL.',
                    'default_body' => json_encode(['foto' => 'data:image/jpeg;base64,...'], JSON_PRETTY_PRINT),
                    'responses' => [
                        '200' => json_encode(['status' => true, 'message' => 'Foto dosen berhasil diunggah'], JSON_PRETTY_PRINT),
                        '401' => json_encode(['message' => 'Unauthenticated.'], JSON_PRETTY_PRINT),
                    ],
                ],
            ],
        ];

        // 4. Mahasiswa - Akademik (12)
        $mhsAkademikEndpoints = [
            ['method' => 'GET', 'path' => '/api/mhs/semester', 'judul' => 'Riwayat Semester', 'desc' => 'Mendapatkan riwayat semester aktif mahasiswa.'],
            ['method' => 'GET', 'path' => '/api/mhs/kurikulum', 'judul' => 'Kurikulum Mahasiswa', 'desc' => 'Daftar kurikulum dan total sks program studi.'],
            ['method' => 'GET', 'path' => '/api/mhs/krs', 'judul' => 'KRS Mahasiswa', 'desc' => 'Daftar mata kuliah Kartu Rencana Studi semester aktif.'],
            ['method' => 'GET', 'path' => '/api/mhs/khs', 'judul' => 'KHS & Indeks Prestasi', 'desc' => 'Kartu Hasil Studi per semester beserta nilai dan bobot.'],
            ['method' => 'GET', 'path' => '/api/mhs/jadwal', 'judul' => 'Jadwal Perkuliahan', 'desc' => 'Jadwal mingguan perkuliahan, praktikum, dan ruang kelas.'],
            ['method' => 'GET', 'path' => '/api/mhs/presensi', 'judul' => 'Presensi Kehadiran', 'desc' => 'Rekapitulasi kehadiran kuliah dan syarat ujian.'],
            ['method' => 'GET', 'path' => '/api/mhs/tagihan', 'judul' => 'Tagihan Pembayaran SPP', 'desc' => 'Informasi pembayaran SPP dan status lunas registrasi.'],
            ['method' => 'GET', 'path' => '/api/mhs/transkrip', 'judul' => 'Transkrip Nilai Sementara', 'desc' => 'Rekapitulasi seluruh mata kuliah yang telah ditempuh dan IPK kumulatif.'],
            ['method' => 'GET', 'path' => '/api/mhs/dosen-wali', 'judul' => 'Dosen Pembimbing Akademik', 'desc' => 'Informasi Dosen Wali/PA dan jadwal bimbingan.'],
            ['method' => 'GET', 'path' => '/api/mhs/pengumuman', 'judul' => 'Pengumuman Akademik', 'desc' => 'Daftar pengumuman dan edaran resmi dekanat.'],
            ['method' => 'GET', 'path' => '/api/mhs/kalender-akademik', 'judul' => 'Kalender Akademik', 'desc' => 'Agenda jadwal perkuliahan, UTS, UAS, dan registrasi.'],
            ['method' => 'GET', 'path' => '/api/mhs/kuesioner', 'judul' => 'Kuesioner Evaluasi Dosen', 'desc' => 'Daftar kuesioner evaluasi dosen semester berjalan.'],
        ];

        $categories[] = [
            'id' => 'mhs-akademik',
            'name' => 'Mahasiswa - Akademik',
            'icon' => 'folder',
            'count' => count($mhsAkademikEndpoints),
            'endpoints' => array_map(function ($item, $idx) use ($baseUrl) {
                return [
                    'id' => 'ep-mhs-akd-'.$idx,
                    'method' => $item['method'],
                    'path' => $item['path'],
                    'full_url' => $baseUrl . $item['path'],
                    'status' => 'AKTIF',
                    'category' => 'Mahasiswa - Akademik',
                    'breadcrumb' => 'MAHASISWA - AKADEMIK • API GATEWAY',
                    'judul' => $item['judul'],
                    'description' => $item['desc'],
                    'is_sanctum' => true,
                    'headers' => [
                        ['key' => 'Accept', 'type' => 'string', 'status' => 'Wajib', 'keterangan' => 'Harus bernilai application/json'],
                    ],
                    'params_schema' => '// Endpoint ini tidak memerlukan parameter URL.',
                    'default_params' => null,
                    'default_body' => null,
                    'responses' => [
                        '200' => json_encode(['status' => true, 'message' => 'Success', 'data' => []], JSON_PRETTY_PRINT),
                        '401' => json_encode(['message' => 'Unauthenticated.'], JSON_PRETTY_PRINT),
                    ],
                ];
            }, $mhsAkademikEndpoints, array_keys($mhsAkademikEndpoints)),
        ];

        // 5. Staff - Akademik (12)
        $staffAkademikEndpoints = [
            [
                'method' => 'GET',
                'path' => '/api/staff/akademik/krs-matakuliah-tahun-akademik',
                'judul' => 'KRS Matakuliah per Tahun Akademik',
                'desc' => 'Mengambil daftar matakuliah KRS yang ditawarkan berdasarkan tahun akademik tertentu.',
                'params' => ['tahun_akademik' => '2026/2027'],
                'params_schema' => "{\n  \"tahun_akademik\": \"2026/2027\" // string (wajib)\n}",
                'response' => [
                    'status' => true,
                    'tahun_akademik' => '2026/2027',
                    'data' => [
                        ['kode_mk' => 'ISPK210001', 'nama_mk' => 'PENDIDIKAN AGAMA**', 'sks' => 2, 'semester' => 1],
                        ['kode_mk' => 'TSBB240023', 'nama_mk' => 'Kecakapan Antar Personal', 'sks' => 2, 'semester' => 1],
                    ],
                ],
            ],
            ['method' => 'GET', 'path' => '/api/staff/akademik/mahasiswa-aktif', 'judul' => 'Daftar Mahasiswa Aktif', 'desc' => 'Rekap daftar mahasiswa aktif semester berjalan.', 'params' => null],
            ['method' => 'GET', 'path' => '/api/staff/akademik/kelas-perkuliahan', 'judul' => 'Daftar Kelas Perkuliahan', 'desc' => 'Master data kelas perkuliahan program studi.', 'params' => null],
            ['method' => 'GET', 'path' => '/api/staff/akademik/status-perkuliahan', 'judul' => 'Status Perkuliahan Mahasiswa', 'desc' => 'Monitoring status aktif, cuti, non-aktif mahasiswa.', 'params' => null],
            ['method' => 'GET', 'path' => '/api/staff/akademik/rekap-krs', 'judul' => 'Rekap Pengumpulan KRS', 'desc' => 'Statistik pengumpulan KRS mahasiswa.', 'params' => null],
            ['method' => 'PUT', 'path' => '/api/staff/akademik/verifikasi-krs', 'judul' => 'Verifikasi KRS Mahasiswa', 'desc' => 'Approval dan validasi KRS oleh bagian akademik.', 'params' => null],
            ['method' => 'GET', 'path' => '/api/staff/akademik/jadwal-kuliah', 'judul' => 'Master Jadwal Kuliah', 'desc' => 'Pengaturan jadwal kuliah dan alokasi ruangan.', 'params' => null],
            ['method' => 'POST', 'path' => '/api/staff/akademik/sync-feeder', 'judul' => 'Trigger Sinkronisasi Feeder', 'desc' => 'Inisiasi sinkronisasi batch data ke Feeder PDDIKTI.', 'params' => null],
            ['method' => 'GET', 'path' => '/api/staff/akademik/dosen-mengajar', 'judul' => 'Penugasan Dosen Mengajar', 'desc' => 'Daftar penugasan dosen per mata kuliah dan kelas.', 'params' => null],
            ['method' => 'GET', 'path' => '/api/staff/akademik/peserta-kelas', 'judul' => 'Daftar Peserta Kelas', 'desc' => 'Daftar mahasiswa yang terdaftar dalam suatu kelas.', 'params' => null],
            ['method' => 'GET', 'path' => '/api/staff/akademik/rekap-presensi', 'judul' => 'Rekapitulasi Presensi Kuliah', 'desc' => 'Laporan persentase kehadiran kuliah per kelas.', 'params' => null],
            ['method' => 'GET', 'path' => '/api/staff/akademik/nilai-akhir', 'judul' => 'Rekap Nilai Akhir Kelas', 'desc' => 'Laporan nilai akhir dan transkrip mahasiswa per kelas.', 'params' => null],
        ];

        $categories[] = [
            'id' => 'staff-akademik',
            'name' => 'Staff - Akademik',
            'icon' => 'folder',
            'count' => count($staffAkademikEndpoints),
            'endpoints' => array_map(function ($item, $idx) use ($baseUrl) {
                return [
                    'id' => 'ep-staff-akd-'.$idx,
                    'method' => $item['method'],
                    'path' => $item['path'],
                    'full_url' => $baseUrl . $item['path'],
                    'status' => 'AKTIF',
                    'category' => 'Staff - Akademik',
                    'breadcrumb' => 'STAFF - AKADEMIK • API GATEWAY',
                    'judul' => $item['judul'],
                    'description' => $item['desc'],
                    'is_sanctum' => true,
                    'headers' => [
                        ['key' => 'Accept', 'type' => 'string', 'status' => 'Wajib', 'keterangan' => 'Harus bernilai application/json'],
                    ],
                    'params_schema' => $item['params_schema'] ?? '// Endpoint ini tidak memerlukan parameter URL.',
                    'default_params' => $item['params'] ?? null,
                    'default_body' => null,
                    'responses' => [
                        '200' => json_encode($item['response'] ?? ['status' => true, 'message' => 'Success', 'data' => []], JSON_PRETTY_PRINT),
                        '401' => json_encode(['message' => 'Unauthenticated.'], JSON_PRETTY_PRINT),
                    ],
                ];
            }, $staffAkademikEndpoints, array_keys($staffAkademikEndpoints)),
        ];

        // 6. Integrate all database categories and apiDocs
        $dbCategories = Category::with('apiDocs')->get();
        foreach ($dbCategories as $dbCat) {
            $catEndpoints = [];
            foreach ($dbCat->apiDocs as $doc) {
                $parsed = $this->parseDbEndpointDoc($doc, $dbCat->name, $baseUrl);
                $catEndpoints[] = $parsed;
            }

            if (! empty($catEndpoints)) {
                $categories[] = [
                    'id' => 'db-cat-'.$dbCat->id,
                    'name' => $dbCat->name,
                    'icon' => 'folder',
                    'count' => count($catEndpoints),
                    'endpoints' => $catEndpoints,
                ];
            }
        }

        // Count total routes
        $totalRoutes = 0;
        foreach ($categories as $cat) {
            $totalRoutes += count($cat['endpoints']);
        }

        // First initial endpoint is /api/auth/mhs/login (or fallback to index 0)
        $initialEndpoint = $categories[0]['endpoints'][1] ?? $categories[0]['endpoints'][0];

        // Default Live API Tester state with real working student credentials
        $initialTester = [
            'method' => 'POST',
            'url' => $baseUrl . '/api/auth/mhs/login',
            'tab' => 'body',
            'params' => '{}',
            'body' => json_encode(['nim' => '2201010167', 'password' => 'password123'], JSON_PRETTY_PRINT),
            'headers' => json_encode(['Accept' => 'application/json', 'Content-Type' => 'application/json'], JSON_PRETTY_PRINT),
        ];

        return [
            'categories' => $categories,
            'totalRoutes' => $totalRoutes,
            'initialEndpoint' => $initialEndpoint,
            'initialTester' => $initialTester,
        ];
    }

    private function parseDbEndpointDoc($doc, string $categoryName, string $baseUrl): array
    {
        $raw = (string) $doc->endpoint;

        // Extract method and path
        $method = 'GET';
        $path = '/api/v1';
        if (preg_match('/##\s+([A-Z]+)\s+([^\s\r\n]+)/', $raw, $m)) {
            $method = strtoupper($m[1]);
            $path = $m[2];
        }

        // Separate query params from path if any, or fallback to example request line
        $defaultParams = null;
        if (str_contains($path, '?')) {
            $parts = explode('?', $path, 2);
            $path = $parts[0];
            parse_str($parts[1], $parsedParams);
            $defaultParams = $parsedParams;
        } elseif (preg_match('/(?:GET|POST|PUT|DELETE)\s+[^\s?]+\?([^\s\r\n`]+)/i', $raw, $qm)) {
            parse_str($qm[1], $parsedParams);
            $defaultParams = $parsedParams;
        }

        // Extract Request Body
        $defaultBody = null;
        if (preg_match('/\*\*Request Body:\*\*\s*```(?:json)?\s*([\s\S]*?)```/i', $raw, $bm)) {
            $defaultBody = trim($bm[1]);
        } elseif (preg_match('/\*\*Contoh Request Body:\*\*\s*```(?:json)?\s*([\s\S]*?)```/i', $raw, $bm)) {
            $defaultBody = trim($bm[1]);
        }

        // Extract Response 200
        $resp200 = null;
        if (preg_match('/\*\*Response \(200\):\*\*\s*```(?:json)?\s*([\s\S]*?)```/i', $raw, $rm)) {
            $resp200 = trim($rm[1]);
        } elseif (! empty($doc->response)) {
            $resp200 = trim($doc->response);
        } else {
            $resp200 = json_encode(['status' => true, 'message' => 'Success'], JSON_PRETTY_PRINT);
        }

        // Extract Response 401/404/422
        $resp401 = json_encode(['status' => false, 'message' => 'Unauthenticated or unauthorized.'], JSON_PRETTY_PRINT);
        if (preg_match('/\*\*Response \((?:401|404|422)\):\*\*\s*```(?:json)?\s*([\s\S]*?)```/i', $raw, $em)) {
            $resp401 = trim($em[1]);
        }

        $headers = [
            ['key' => 'Accept', 'type' => 'string', 'status' => 'Wajib', 'keterangan' => 'Harus bernilai application/json'],
        ];
        if (str_contains($raw, 'Bearer')) {
            $headers[] = ['key' => 'Authorization', 'type' => 'string', 'status' => 'Wajib', 'keterangan' => 'Bearer {token}'];
        }
        if ($method !== 'GET') {
            $headers[] = ['key' => 'Content-Type', 'type' => 'string', 'status' => 'Wajib', 'keterangan' => 'Harus bernilai application/json'];
        }

        $paramsSchema = $defaultParams
            ? json_encode($defaultParams, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            : '// Endpoint ini tidak memerlukan parameter URL.';

        return [
            'id' => 'db-doc-'.$doc->id,
            'method' => $method,
            'path' => $path,
            'full_url' => $baseUrl . $path,
            'status' => 'AKTIF',
            'category' => $categoryName,
            'breadcrumb' => strtoupper($categoryName).' • API GATEWAY',
            'judul' => $doc->judul,
            'description' => $doc->description ?: 'Dokumentasi endpoint '.$doc->judul,
            'is_sanctum' => str_contains($raw, 'Sanctum') || str_contains($doc->description ?? '', 'Sanctum'),
            'sanctum_note' => 'Autentikasi & Proteksi Sanctum: Endpoint terlindungi memerlukan session cookie (laravel_session) dan token CSRF (X-XSRF-TOKEN). Pada pengujian via API Tester, token dan session akan otomatis disertakan.',
            'headers' => $headers,
            'params_schema' => $paramsSchema,
            'default_params' => $defaultParams,
            'default_body' => $defaultBody,
            'responses' => [
                '200' => $resp200,
                '401' => $resp401,
                '422' => json_encode(['status' => false, 'message' => 'The given data was invalid.'], JSON_PRETTY_PRINT),
            ],
        ];
    }
}
