-- =====================================================================
-- SISKA API Documentation — Data Dump
-- Tabel: categories & api_docs
-- Hanya berisi data (VALUES), bukan struktur tabel.
-- =====================================================================

INSERT INTO `categories` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
('1', 'Autentikasi', 'Endpoint untuk login, logout, dan autentikasi pengguna pada semua modul API', '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('2', 'Mahasiswa', 'Endpoint untuk mengelola data mahasiswa', '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('3', 'Program Studi', 'Endpoint untuk mengelola data program studi', '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('4', 'Tahun Akademik', 'Endpoint untuk mengelola data tahun akademik', '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('5', 'KRS / KHS', 'Endpoint untuk Kartu Rencana Studi dan Kartu Hasil Studi', '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('6', 'Pembayaran', 'Endpoint untuk cek status pembayaran mahasiswa', '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('7', 'OBE (Dosen)', 'Endpoint untuk Outcome-Based Education bagi dosen', '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('8', 'Divisi', 'Endpoint untuk staff/divisi (Kedokteran, Akademik, Universal)', '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('9', 'Feeder PDDIKTI', 'Endpoint untuk sinkronisasi data dengan Feeder PDDIKTI (validasi & sync)', '2026-07-10 13:41:31', '2026-07-10 13:41:31');

INSERT INTO `api_docs` (`id`, `category_id`, `judul`, `description`, `endpoint`, `response`, `created_at`, `updated_at`) VALUES
('1', '1', 'Login API Utama', 'Melakukan autentikasi pengguna dan mendapatkan Bearer Token. Token digunakan untuk akses endpoint yang dilindungi pada modul API Utama.', '## POST /api/v1/login

**Request Body:**
```json
{
    "email": "user@example.com",
    "password": "secret123"
}
```

**Response (200):**
```json
{
    "status": "success",
    "message": "Login successful",
    "user": { "id": 1, "name": "Admin", "email": "user@example.com", "status": "active" },
    "token": "1|abc123def456..."
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('2', '1', 'Get Current User (API Utama)', 'Mendapatkan informasi pengguna yang sedang login menggunakan Bearer Token.', '## GET /api/v1/me

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "status": "success",
    "message": "Success",
    "user": { "id": 1, "name": "Admin", "email": "user@example.com", "status": "active" }
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('3', '1', 'Logout (API Utama)', 'Mencabut token saat ini dan mengakhiri sesi.', '## POST /api/v1/logout

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "status": "success",
    "message": "Logged out successfully"
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('4', '1', 'Logout All Devices (API Utama)', 'Mencabut semua token untuk pengguna saat ini. Berguna untuk keamanan jika akun terkompromi.', '## POST /api/v1/logout-all

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "status": "success",
    "message": "All tokens revoked successfully"
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('5', '1', 'Login SIMORTUA - Cek NIM', 'Langkah pertama autentikasi SIMORTUA. Memverifikasi apakah NIM terdaftar dalam sistem.', '## POST /api/v1/simortua/login-nim

**Request Body:**
```json
{
    "nim": "2301001"
}
```

**Response (200):**
```json
{
    "status": true,
    "message": "NIM ditemukan",
    "data": { "nim": "2301001", "nama": "Budi Santoso" }
}
```

**Response (404):**
```json
{
    "status": false,
    "message": "NIM tidak ditemukan"
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('6', '1', 'Login SIMORTUA - NIM + Tanggal Lahir', 'Autentikasi SIMORTUA menggunakan NIM dan tanggal lahir sebagai password.', '## POST /api/v1/simortua/login-password

**Request Body:**
```json
{
    "nim": "2301001",
    "password": "2003-05-15"
}
```

**Response (200):**
```json
{
    "status": "success",
    "message": "Login successful",
    "user": { "nim": "2301001", "nama_mahasiswa": "Budi Santoso", "program_studi_kode": 1 },
    "token": "1|abc123def456..."
}
```

**Response (401):**
```json
{
    "status": false,
    "message": "NIM atau tanggal lahir salah"
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('7', '1', 'Login SISKA Mahasiswa', 'Autentikasi mahasiswa menggunakan NIM dan password. Menggunakan Sanctum SPA Cookie.', '## POST /api/v1/siska/login-mhs

**Request Body:**
```json
{
    "nim": "2301001",
    "password": "secret123"
}
```

**Response (200):**
```json
{
    "status": true,
    "message": "Login successful"
}
```

**Response (401):**
```json
{
    "status": false,
    "message": "NIM atau Password Tidak Valid"
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('8', '1', 'Get Current User (SISKA Mahasiswa)', 'Mendapatkan informasi mahasiswa yang sedang login menggunakan session cookie.', '## GET /api/v1/siska/me

**Response (200):**
```json
{
    "status": true,
    "data": {
        "nim": "2301001",
        "nama_mahasiswa": "Budi Santoso",
        "email": "budi@student.ubg.ac.id",
        "status": "A"
    }
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('9', '1', 'Logout SISKA Mahasiswa', 'Mengakhiri sesi mahasiswa dan menginvalidasi session.', '## POST /api/v1/siska/logout

**Response (200):**
```json
{
    "status": true,
    "message": "Logout successful"
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('10', '1', 'Login SISKA Divisi', 'Autentikasi staff/divisi menggunakan username dan password. Menggunakan Sanctum SPA Cookie.', '## POST /api/v1/divisi/login

**Request Body:**
```json
{
    "username": "admin_akademik",
    "password": "secret123"
}
```

**Response (200):**
```json
{
    "status": true,
    "message": "Login successful"
}
```

**Response (401):**
```json
{
    "status": false,
    "message": "Username atau Password Tidak Valid"
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('11', '1', 'Get Current User (SISKA Divisi)', 'Mendapatkan informasi staff/divisi yang sedang login beserta role-nya.', '## GET /api/v1/divisi/me

**Response (200):**
```json
{
    "status": true,
    "data": {
        "kode_pengguna": "P001",
        "nama_login": "admin_akademik",
        "nama_pengguna": "Administrator Akademik",
        "role": "Akademik"
    }
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('12', '1', 'Logout SISKA Divisi', 'Mengakhiri sesi staff/divisi dan menginvalidasi session.', '## POST /api/v1/divisi/logout

**Response (200):**
```json
{
    "status": true,
    "message": "Logout successful"
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('13', '1', 'Login OBE (Dosen)', 'Autentikasi dosen menggunakan email dan password. Menggunakan Sanctum SPA Cookie.', '## POST /api/v1/obe/login

**Request Body:**
```json
{
    "email": "dosen@ubg.ac.id",
    "password": "secret123"
}
```

**Response (200):**
```json
{
    "status": true,
    "message": "Login successful"
}
```

**Response (401):**
```json
{
    "status": false,
    "message": "Email atau Password Tidak Valid"
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('14', '1', 'Get Current User (OBE / Dosen)', 'Mendapatkan informasi dosen yang sedang login beserta data profil lengkap.', '## GET /api/v1/obe/me

**Response (200):**
```json
{
    "status": true,
    "data": {
        "kode_dosen": "D001",
        "nama_dosen": "Dr. Ahmad Supriadi",
        "field_studi": "Artificial Intelligence",
        "nik": "3201234567890001",
        "no_telp": "081234567890",
        "alamat_email": "dosen@ubg.ac.id",
        "status_login": true
    }
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('15', '1', 'Logout OBE (Dosen)', 'Mengakhiri sesi dosen dan menginvalidasi session.', '## POST /api/v1/obe/logout

**Response (200):**
```json
{
    "status": true,
    "message": "Logout successful"
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('16', '2', 'List Mahasiswa (Paginated)', 'Mendapatkan daftar mahasiswa dengan paginasi. Mendukung filter NIM, prodi, status, dan angkatan.', '## GET /api/v1/mahasiswa

**Query Parameters:**
| Parameter | Tipe | Wajib | Default | Keterangan |
|-----------|------|-------|---------|------------|
| per_page | integer | Tidak | 15 | Data per halaman (1-100) |
| nim | string | Tidak | - | Filter NIM |
| program_studi_kode | integer | Tidak | - | Filter kode prodi |
| status_pendaftaran | string | Tidak | - | B/T/L |
| status | string | Tidak | - | A/N |
| angkatan | string | Tidak | - | Tahun angkatan |

**Response (200):**
```json
{
    "status": "success",
    "message": "Data Mahasiswa retrieved successfully",
    "data": {
        "current_page": 1,
        "data": [{
            "program_studi_kode": 1,
            "nim": "2301001",
            "nama_mahasiswa": "Budi Santoso",
            "status": "A",
            "nama_prodi": { "kode_program_studi": 1, "nama_program_studi": "Teknik Informatika" }
        }],
        "per_page": 15,
        "total": 50
    }
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('17', '2', 'Get Mahasiswa by NIM', 'Mendapatkan data lengkap satu mahasiswa berdasarkan NIM.', '## GET /api/v1/mahasiswa-nim?nim=2301001

**Response (200):**
```json
{
    "status": "success",
    "message": "Mahasiswa retrieved successfully",
    "data": {
        "nim": "2301001",
        "nama_mahasiswa": "Budi Santoso",
        "tempat_lahir": "Jakarta",
        "tanggal_lahir": "2003-05-15",
        "email": "budi@student.ubg.ac.id",
        "status": "A",
        "nama_prodi": { "kode_program_studi": 1, "nama_program_studi": "Teknik Informatika" }
    }
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('18', '2', 'Get List Mahasiswa', 'Mendapatkan daftar mahasiswa tanpa paginasi. Cocok untuk dropdown.', '## GET /api/v1/mahasiswa-get?status=A&angkatan=2023

**Response (200):**
```json
{
    "status": "success",
    "message": "Data Mahasiswa retrieved successfully",
    "data": [
        { "nim": "2301001", "nama_mahasiswa": "Budi Santoso", "program_studi_kode": 1, "status": "A" }
    ]
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('19', '2', 'Show Mahasiswa (POST)', 'Mendapatkan data mahasiswa berdasarkan NIM menggunakan request body POST.', '## POST /api/v1/mahasiswa-show

**Request Body:**
```json
{
    "nim": "2301001"
}
```

**Response (200):**
```json
{
    "status": "success",
    "message": "Mahasiswa retrieved successfully",
    "data": {
        "nim": "2301001",
        "nama_mahasiswa": "Budi Santoso",
        "status": "A"
    }
}
```

**Response (422):**
```json
{
    "status": "error",
    "message": "Validation Error",
    "errors": { "nim": ["The nim field is required."] }
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('20', '3', 'List Program Studi', 'Mendapatkan daftar program studi dengan paginasi.', '## GET /api/v1/program-studi?per_page=20

**Response (200):**
```json
{
    "status": "success",
    "message": "Data Program Studi retrieved successfully",
    "data": {
        "data": [
            { "kode_program_studi": 1, "nama_program_studi": "Teknik Informatika", "singkatan_program_studi": "TI" },
            { "kode_program_studi": 23, "nama_program_studi": "Kedokteran", "singkatan_program_studi": "FK" }
        ],
        "per_page": 20,
        "total": 4
    }
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('21', '3', 'List Program Studi (Tanpa Paginasi)', 'Mendapatkan seluruh daftar program studi tanpa paginasi.', '## GET /api/v1/program-studi

**Response (200):**
```json
{
    "status": "success",
    "message": "List of Program Studi retrieved successfully",
    "data": [
        { "kode_program_studi": 1, "nama_program_studi": "Teknik Informatika", "singkatan_program_studi": "TI" },
        { "kode_program_studi": 23, "nama_program_studi": "Kedokteran", "singkatan_program_studi": "FK" }
    ]
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('22', '3', 'Program Studi (Divisi)', 'Mendapatkan daftar program studi melalui modul Divisi.', '## GET /api/v1/divisi/program-studi

**Response (200):**
```json
{
    "status": true,
    "data": [
        { "kode_program_studi": 1, "nama_program_studi": "Teknik Informatika", "singkatan_program_studi": "TI" },
        { "kode_program_studi": 23, "nama_program_studi": "Kedokteran", "singkatan_program_studi": "FK" }
    ]
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('23', '4', 'List Tahun Akademik', 'Mendapatkan daftar seluruh tahun akademik. Status: A=Aktif, N=Non-aktif. Semester: 1=Ganjil, 2=Genap.', '## GET /api/v1/tahun-akademik

**Response (200):**
```json
{
    "status": "success",
    "message": "Data Tahun Akademik retrieved successfully",
    "data": [
        { "tahun_akademik": "2025/2026", "periode": "2025", "semester": "1", "status": "A" },
        { "tahun_akademik": "2024/2025", "periode": "2024", "semester": "2", "status": "N" }
    ]
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('24', '4', 'Create Tahun Akademik', 'Membuat tahun akademik baru. Status awal Non-aktif.', '## POST /api/v1/tahun-akademik

**Request Body:**
```json
{
    "tahun_akademik": "2026/2027",
    "semester": "1",
    "tanggal_mulai": "2026-08-01",
    "tanggal_berakhir": "2027-01-31"
}
```

**Response (201):**
```json
{
    "status": "success",
    "message": "Tahun Akademik created successfully",
    "data": { "tahun_akademik": "2026/2027", "semester": "1", "tanggal_mulai": "2026-08-01", "tanggal_berakhir": "2027-01-31" }
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('25', '4', 'Get Tahun Akademik by ID', 'Mendapatkan detail tahun akademik berdasarkan kode.', '## GET /api/v1/tahun-akademik/{id}/update

**Parameter:** id = Kode tahun akademik

**Response (200):**
```json
{
    "status": "success",
    "message": "Tahun Akademik retrieved successfully",
    "data": { "tahun_akademik": "2025/2026", "semester": "1", "tanggal_mulai": "2025-08-01", "tanggal_berakhir": "2026-01-31", "status": "A" }
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('26', '4', 'Update Tahun Akademik', 'Memperbarui data tahun akademik yang sudah ada.', '## PUT /api/v1/tahun-akademik/{id}/update

**Request Body:**
```json
{
    "tahun_akademik": "2025/2026",
    "semester": "1",
    "tanggal_mulai": "2025-08-01",
    "tanggal_berakhir": "2026-01-31"
}
```

**Response (200):**
```json
{
    "status": "success",
    "message": "Tahun Akademik updated successfully",
    "data": { "tahun_akademik": "2025/2026", "semester": "1", "tanggal_mulai": "2025-08-01", "tanggal_berakhir": "2026-01-31", "status": "A" }
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('27', '4', 'Delete Tahun Akademik', 'Menghapus tahun akademik. Status Aktif tidak bisa dihapus.', '## DELETE /api/v1/tahun-akademik/{id}/delete

**Response (200):**
```json
{
    "status": "success",
    "message": "Tahun Akademik deleted successfully"
}
```

**Response (404):**
```json
{
    "status": "error",
    "message": "Tahun Akademik status Aktif, can`t delete"
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('28', '4', 'Update Status Tahun Akademik', 'Mengubah status tahun akademik. Saat aktivasi, tahun lain otomatis dinonaktifkan.', '## PATCH /api/v1/tahun-akademik/{id}/status

**Request Body:**
```json
{
    "status": "A"
}
```

**Response (200):**
```json
{
    "status": "success",
    "message": "Tahun Akademik status updated successfully"
}
```

**Response (400):**
```json
{
    "status": "error",
    "message": "Cannot deactivate an active Tahun Akademik"
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('29', '4', 'Tahun Akademik (Divisi)', 'Mendapatkan daftar tahun akademik melalui modul Divisi.', '## GET /api/v1/divisi/tahun-akademik

**Response (200):**
```json
{
    "status": true,
    "data": [
        { "kode_tahun_akademik": 1, "tahun_akademik": "2025/2026", "semester": "1", "status": "A" }
    ]
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('30', '4', 'Tahun Akademik Aktif (Divisi)', 'Mendapatkan tahun akademik yang sedang aktif.', '## GET /api/v1/divisi/tahun-akademik/aktif

**Response (200):**
```json
{
    "status": true,
    "data": { "kode_tahun_akademik": 1, "tahun_akademik": "2025/2026", "semester": "1", "status": "A" }
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('31', '4', 'Cari Tahun Akademik (Divisi)', 'Mencari tahun akademik berdasarkan kode.', '## GET /api/v1/divisi/tahun-akademik/find?kode=20251

**Response (200):**
```json
{
    "status": true,
    "data": { "kode_tahun_akademik": 1, "tahun_akademik": "2025/2026", "semester": "1", "status": "A" }
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('32', '5', 'Cek KRS', 'Mendapatkan daftar KRS mahasiswa per tahun akademik.', '## GET /api/v1/simortua/krs?nim=2301001

**Response (200):**
```json
{
    "status": "success",
    "message": "KRS retrieved successfully",
    "data": [
        {
            "tahun_akademik": "2025/2026",
            "semester": "1",
            "kode_tahun_akademik": 1,
            "cek_krs": [{ "kode_krs": "KRS001", "nim": "2301001", "status": "1" }]
        }
    ]
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('33', '5', 'Detail KRS', 'Mendapatkan detail mata kuliah yang terdaftar pada KRS.', '## GET /api/v1/simortua/krs/detail?kode_krs=KRS001

**Response (200):**
```json
{
    "status": "success",
    "message": "KRS Detail retrieved successfully",
    "data": [
        {
            "status": "1",
            "id_matakuliah": "MK001",
            "matakuliah": { "id_matakuliah": "MK001", "nama_matakuliah": "Pemrograman Web", "sks": 3 }
        }
    ]
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('34', '5', 'Cek KHS', 'Mendapatkan daftar KHS mahasiswa per tahun akademik.', '## GET /api/v1/simortua/khs?nim=2301001

**Response (200):**
```json
{
    "status": "success",
    "message": "KRS retrieved successfully",
    "data": [
        {
            "tahun_akademik": "2024/2025",
            "semester": "2",
            "kode_tahun_akademik": 2,
            "cek_khs": [{ "kode_khs": "KHS001", "nim": "2301001" }]
        }
    ]
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('35', '5', 'Detail KHS', 'Mendapatkan detail nilai mata kuliah pada KHS, termasuk nilai akhir, grade, dan bobot.', '## GET /api/v1/simortua/khs/detail?kode_krs=KRS002

**Grade:**
| Grade | Bobot | Rentang |
|-------|-------|---------|
| A | 4.0 | 85-100 |
| B+ | 3.5 | 75-79 |
| B | 3.0 | 70-74 |
| C | 2.0 | 55-59 |
| D | 1.0 | 40-54 |
| E | 0.0 | 0-39 |

**Response (200):**
```json
{
    "status": "success",
    "message": "KRS Detail retrieved successfully",
    "data": [
        {
            "nilai_akhir": 85.5,
            "grade": "A",
            "bobot": 4.0,
            "matakuliah": { "id_matakuliah": "MK001", "nama_matakuliah": "Pemrograman Web", "sks": 3 }
        }
    ]
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('36', '5', 'Petikan Nilai', 'Mendapatkan transkrip nilai lengkap mahasiswa termasuk kurikulum, total SKS, dan IPK.', '## GET /api/v1/simortua/petikan-nilai?nim=2301001

**Response (200):**
```json
{
    "status": "success",
    "message": "Petikan Nilai URL retrieved successfully",
    "data": {
        "kurikulum": { "kode_nama_kurikulum": "KRK001", "nama_kurikulum": "Kurikulum 2023" },
        "mahasiswa": { "nim": "2301001", "nama_mahasiswa": "Budi Santoso" },
        "transkrip": [{ "semester": "1", "matakuliah": "Pemrograman Web", "sks": 3, "nilai": "A", "bobot": 4.0 }],
        "total_sks": 24,
        "ipk": 3.75
    }
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('37', '6', 'Cek Status Pembayaran', 'Mendapatkan informasi status pembayaran mahasiswa (SPP, SKS, Lab) per tahun akademik.', '## GET /api/v1/simortua/pembayaran?nim=2301001

**Response (200):**
```json
{
    "status": "success",
    "message": "Pembayaran retrieved successfully",
    "data": [
        {
            "tahun_akademik": "2025/2026",
            "semester": "1",
            "kode_tahun_akademik": 1,
            "status_perkuliahan": {
                "status_perkuliahan": true,
                "pembayaran_spp": true,
                "pembayaran_sks": true,
                "pembayaran_lab": false,
                "nim": "2301001"
            }
        }
    ]
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('38', '7', 'Get Kelas (OBE)', 'Mendapatkan daftar kelas yang diampu oleh dosen yang sedang login.', '## GET /api/v1/obe/kelas

**Response (200):**
```json
{
    "status": true,
    "data": [
        {
            "id": "KL001",
            "code_kelas": "encrypted_string",
            "nama_matakuliah": "Pemrograman Web",
            "nama_kelas": "Reguler",
            "id_matakuliah": "MK001",
            "jumlah_mahasiswa": 35
        }
    ]
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('39', '7', 'Get Penilaian (OBE)', 'Mendapatkan daftar penilaian mahasiswa berdasarkan kelas.', '## GET /api/v1/obe/penilaian?code_kelas=encrypted_string

**Response (200):**
```json
{
    "status": true,
    "data": [
        {
            "code_penilaian": "encrypted_penilaian",
            "nim": "2301001",
            "nama_mahasiswa": "Budi Santoso",
            "nilai_harian": 85.0,
            "nilai_uts": 80.0,
            "nilai_uas": 88.0,
            "nilai_akhir": 84.5
        }
    ]
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('40', '7', 'Update Penilaian Single (OBE)', 'Memperbarui nilai satu mahasiswa.', '## PUT /api/v1/obe/penilaian

**Request Body:**
```json
{
    "code_penilaian": "encrypted_penilaian_1",
    "nilai_harian": 88.0,
    "nilai_uts": 82.0,
    "nilai_uas": 90.0,
    "nilai_akhir": 86.5
}
```

**Response (200):**
```json
{
    "status": true,
    "message": "Penilaian updated successfully"
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('41', '7', 'Update Penilaian Batch (OBE)', 'Memperbarui nilai beberapa mahasiswa sekaligus dalam satu transaksi.', '## PUT /api/v1/obe/penilaian/batch

**Request Body:**
```json
[
    { "code_penilaian": "encrypted_1", "nilai_akhir": 86.5 },
    { "code_penilaian": "encrypted_2", "nilai_akhir": 91.5 }
]
```

**Response (200):**
```json
{
    "status": true,
    "message": "Semua penilaian updated successfully"
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('42', '7', 'OBE Penilaian Detail (Dosen)', 'Mendapatkan detail nilai mahasiswa per kelas dengan filter NIM opsional.', '## GET /api/v1/obe/obe-penilaian?code_kelas=TI001&nim=2301001

**Query Parameters:**
| Parameter | Tipe | Wajib | Keterangan |
|-----------|------|-------|------------|
| code_kelas | string | Ya | Kode kelas (plain, tidak di-encrypt) |
| nim | string | Tidak | Filter NIM mahasiswa |

**Response (200):**
```json
{
    "status": true,
    "data": [
        {
            "id": "KRSD001",
            "code_penilaian": "encrypted_string",
            "nim": "2301001",
            "nama_mahasiswa": "Budi Santoso",
            "nilai_harian": 85.0,
            "nilai_uts": 80.0,
            "nilai_uas": 88.0,
            "nilai_akhir": 84.5
        }
    ]
}
```

**Response (422):**
```json
{
    "status": false,
    "message": "Invalid code_kelas"
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('43', '8', 'Tahun Akademik (Universal)', 'Mendapatkan daftar tahun akademik (Universal).', '## GET /api/v1/divisi/tahun-akademik

**Response (200):**
```json
{
    "status": true,
    "data": [
        { "kode_tahun_akademik": 1, "tahun_akademik": "2025/2026", "semester": "1", "status": "A" }
    ]
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('44', '8', 'Tahun Akademik Aktif (Universal)', 'Mendapatkan tahun akademik aktif.', '## GET /api/v1/divisi/tahun-akademik/aktif

**Response (200):**
```json
{
    "status": true,
    "data": { "kode_tahun_akademik": 1, "tahun_akademik": "2025/2026", "semester": "1", "status": "A" }
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('45', '8', 'Cari Tahun Akademik (Universal)', 'Mencari tahun akademik berdasarkan kode.', '## GET /api/v1/divisi/tahun-akademik/find?kode=20251

**Response (200):**
```json
{
    "status": true,
    "data": { "kode_tahun_akademik": 1, "tahun_akademik": "2025/2026", "semester": "1", "status": "A" }
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('46', '8', 'Program Studi (Universal)', 'Mendapatkan daftar program studi.', '## GET /api/v1/divisi/program-studi

**Response (200):**
```json
{
    "status": true,
    "data": [
        { "kode_program_studi": 1, "nama_program_studi": "Teknik Informatika", "singkatan_program_studi": "TI" }
    ]
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('47', '8', 'Mahasiswa Kedokteran', 'Mendapatkan data mahasiswa Fakultas Kedokteran (kode_prodi=23).', '## GET /api/v1/divisi/get-mhs-kedokteran

**Response (200):**
```json
{
    "status": true,
    "data": [
        { "nim": "2301001", "nama_mahasiswa": "Budi Santoso", "program_studi_kode": 23, "status": "A" }
    ]
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('48', '8', 'Dosen Kedokteran', 'Mendapatkan data dosen Fakultas Kedokteran.', '## GET /api/v1/divisi/get-dosen-kedokteran

**Response (200):**
```json
{
    "status": true,
    "data": [
        { "kode_dosen": "D023", "nama_dosen": "Dr. Sp.PD", "homebase": 23, "status_dosen": "A" }
    ]
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('49', '8', 'Matakuliah Kedokteran', 'Mendapatkan mata kuliah Fakultas Kedokteran.', '## GET /api/v1/divisi/get-matakuliah

**Response (200):**
```json
{
    "status": true,
    "data": [
        { "id_matakuliah": "MK023", "nama_matakuliah": "Anatomi Tubuh Manusia", "sks": 4, "kode_program_studi": 23 }
    ]
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('50', '8', 'Kelas Kedokteran', 'Mendapatkan data kelas FK beserta dosen dan mahasiswa.', '## GET /api/v1/divisi/get-kelas

**Response (200):**
```json
{
    "status": true,
    "data": [
        {
            "kelas_id": "KL023",
            "nama_kelas_id": "FK-4A",
            "semester": "1",
            "kode_program_studi": 23,
            "kode_matakuliah": "MK023",
            "dosen_kedokteran": { "kode_dosen": "D023", "nama_dosen": "Dr. Sp.PD" },
            "mahasiswa_kedokteran": [{ "nim": "2301001", "nama_mahasiswa": "Budi Santoso" }]
        }
    ]
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('51', '8', 'Kurikulum Kedokteran', 'Mendapatkan data kurikulum FK.', '## GET /api/v1/divisi/get-kurikulum

**Response (200):**
```json
{
    "status": true,
    "data": {
        "nama_kurikulum": [{ "kode_nama_kurikulum": "KRK023", "nama_kurikulum": "Kurikulum Kedokteran 2023", "kode_program_studi": 23 }],
        "kurikulum": [{ "kode_kurikulum": "KR023", "id_matakuliah": "MK023", "semester": "1" }],
        "kurikulum_angkatan": [{ "kode_nama_kurikulum": "KRK023", "angkatan": "2023" }]
    }
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('52', '8', 'Status Perkuliahan', 'Mendapatkan status perkuliahan seluruh mahasiswa (role: akademik).', '## GET /api/v1/divisi/status-perkuliahan

**Response (200):**
```json
{
    "status": true,
    "message": "Status Perkuliahan Ditemukan",
    "data": [
        {
            "nim": "2301001",
            "nama_mahasiswa": "Budi Santoso",
            "nama_program_studi": "Teknik Informatika",
            "status_perkuliahan": true,
            "pembayaran_spp": true,
            "pengumpulan_krs": "1"
        }
    ]
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('53', '8', 'Status Perkuliahan - Sudah Kumpul KRS', 'Mahasiswa dengan status pengumpulan KRS=1.', '## GET /api/v1/divisi/status-perkuliahan-kumpul

**Response (200):**
```json
{
    "status": true,
    "data": [{ "nim": "2301001", "pengumpulan_krs": "1" }]
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('54', '8', 'Status Perkuliahan - Belum Kumpul KRS', 'Mahasiswa dengan status pengumpulan KRS=0.', '## GET /api/v1/divisi/status-perkuliahan-not-kumpul

**Response (200):**
```json
{
    "status": true,
    "data": [{ "nim": "2301002", "pengumpulan_krs": "0" }]
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('55', '8', 'Update Pengumpulan KRS', 'Toggle status pengumpulan KRS mahasiswa (role: akademik).', '## PUT /api/v1/divisi/update-pengumpulan-krs

**Request Body:**
```json
{
    "nim": "2301001"
}
```

**Response (200):**
```json
{
    "status": true,
    "message": "Pengumpulan KRS berhasil diperbarui Aktif"
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('56', '8', 'Chart Pengumpulan KRS', 'Statistik pengumpulan KRS keseluruhan.', '## GET /api/v1/divisi/chart-pengumpulan-krs

**Response (200):**
```json
{
    "status": true,
    "data": { "total": 500, "sudah_kumpul": 350, "belum_kumpul": 150 }
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('57', '8', 'Chart Pengumpulan KRS by Prodi', 'Statistik pengumpulan KRS per program studi.', '## GET /api/v1/divisi/chart-pengumpulan-krs-by-prodi

**Response (200):**
```json
{
    "status": true,
    "data": [
        { "nama_program_studi": "Teknik Informatika", "total": 150, "sudah_kumpul": 120, "belum_kumpul": 30 }
    ]
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('58', '8', 'Chart Pengumpulan KRS by Angkatan', 'Statistik pengumpulan KRS per tahun angkatan.', '## GET /api/v1/divisi/chart-pengumpulan-krs-by-tahun-angkatan

**Response (200):**
```json
{
    "status": true,
    "data": [
        { "angkatan": "2023", "total": 200, "sudah_kumpul": 180, "belum_kumpul": 20 }
    ]
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('59', '8', 'Chart Pengumpulan KRS by Prodi & Angkatan', 'Statistik pengumpulan KRS per prodi dan angkatan.', '## GET /api/v1/divisi/chart-pengumpulan-krs-by-prodi-and-tahun-angkatan

**Response (200):**
```json
{
    "status": true,
    "data": [
        { "nama_program_studi": "Teknik Informatika", "angkatan": "2023", "total": 100, "sudah_kumpul": 85, "belum_kumpul": 15 }
    ]
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('60', '9', 'Dropdown Program Studi (Feeder)', 'Mendapatkan daftar program studi untuk dropdown. Semua role. ID `kode_program_studi` dienkripsi (AES-256-CBC).', '## GET /api/v1/feeder/prodi

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "status": "success",
    "message": "Data berhasil diambil",
    "data": {
        "data": [
            {
                "kode_program_studi": "eyJpdiI6IkxZV3...",
                "nama_program_studi": "S1 Ilmu Komputer",
                "singkatan": "S1 Ilkom"
            },
            {
                "kode_program_studi": "eyJpdiI6IkxZV4...",
                "nama_program_studi": "D3 Sistem Informasi",
                "singkatan": "D3 SI"
            }
        ]
    }
}
```

> **Catatan:** `kode_program_studi` dienkripsi. Frontend tidak perlu tahu value aslinya.', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('61', '9', 'Dropdown Tahun Akademik (Feeder)', 'Mendapatkan daftar tahun akademik untuk dropdown. Semua role. ID `kode_tahun_akademik` dienkripsi.', '## GET /api/v1/feeder/ta

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "status": "success",
    "message": "Data berhasil diambil",
    "data": {
        "data": [
            {
                "kode_tahun_akademik": "eyJpdiI6IkxZV5...",
                "tahun_akademik": "2025/2026",
                "semester": "1",
                "semester_nama": "Ganjil"
            },
            {
                "kode_tahun_akademik": "eyJpdiI6IkxZV6...",
                "tahun_akademik": "2025/2026",
                "semester": "2",
                "semester_nama": "Genap"
            }
        ]
    }
}
```

> **Catatan:** Diurutkan berdasarkan `kode_tahun_akademik` descending (terbaru di atas).', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('62', '9', 'Dropdown Mata Kuliah (Feeder)', 'Mendapatkan mata kuliah berdasarkan kurikulum program studi dan tahun akademik. Semua role. ID `id_matakuliah` dienkripsi.', '## GET /api/v1/feeder/matakuliah

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
| Parameter | Tipe | Wajib | Keterangan |
|-----------|------|-------|------------|
| `kode_program_studi` | string | Ya | Kode prodi (terenkripsi dari endpoint `/prodi`) |
| `kode_tahun_akademik` | string | Ya | Kode ta (terenkripsi dari endpoint `/ta`) |

**Contoh Request:**
```
GET /api/v1/feeder/matakuliah?kode_program_studi=eyJpdiI6IkxZV3...&kode_tahun_akademik=eyJpdiI6IkxZV5...
```

**Alur Query:**
1. Decrypt `kode_program_studi` â†’ kodeProdi
2. Decrypt `kode_tahun_akademik` â†’ kodeTa
3. Dari kodeTa â†’ ambil `tahun_akademik` â†’ extract `angkatan` (contoh: "2022/2023" â†’ "2022")
4. Query: `Kurikulum` â†’ where `NamaKurikulum.kode_program_studi` = kodeProdi â†’ where `NamaKurikulum.KurikulumAngkatan.angkatan` = angkatan â†’ with(`matakuliah`)

**Response (200):**
```json
{
    "status": "success",
    "message": "Data berhasil diambil",
    "data": {
        "data": [
            {
                "id_matakuliah": "eyJpdiI6IkxZV7...",
                "kode_matakuliah": "ISKK310203",
                "nama_matakuliah": "ALGORITMA",
                "sks_teori": 3,
                "sks_praktek": 0,
                "sks_praktikum": 0
            },
            {
                "id_matakuliah": "eyJpdiI6IkxZV8...",
                "kode_matakuliah": "ISKB410205",
                "nama_matakuliah": "PEMROGRAMAN I",
                "sks_teori": 2,
                "sks_praktek": 0,
                "sks_praktikum": 0
            }
        ]
    }
}
```

> **Catatan:** `id_matakuliah` dienkripsi. Relation path: `ProgramStudi â†’ NamaKurikulum â†’ KurikulumAngkatan â†’ Kurikulum â†’ Matakuliah`.', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('63', '9', 'Dropdown Kelas (Feeder)', 'Mendapatkan kelas berdasarkan tahun akademik dan mata kuliah. Semua role. ID `kelas_id` dienkripsi.', '## GET /api/v1/feeder/kelas

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
| Parameter | Tipe | Wajib | Keterangan |
|-----------|------|-------|------------|
| `kode_tahun_akademik` | string | Ya | Kode ta (terenkripsi dari endpoint `/ta`) |
| `id_matakuliah` | string | Ya | ID matakuliah (terenkripsi dari endpoint `/matakuliah`) |

**Contoh Request:**
```
GET /api/v1/feeder/kelas?kode_tahun_akademik=eyJpdiI6IkxZV5...&id_matakuliah=eyJpdiI6IkxZV7...
```

**Response (200):**
```json
{
    "status": "success",
    "message": "Data berhasil diambil",
    "data": {
        "data": [
            {
                "kelas_id": "eyJpdiI6IkxZV9...",
                "nama_kelas": "A"
            },
            {
                "kelas_id": "eyJpdiI6IkxZWA...",
                "nama_kelas": "B"
            },
            {
                "kelas_id": "eyJpdiI6IkxZWB...",
                "nama_kelas": "C"
            }
        ]
    }
}
```

> **Catatan:** `kelas_id` dienkripsi. Hanya mengembalikan kelas yang unik (distinct).', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('64', '9', 'Validasi Per Mahasiswa (Feeder)', 'Membandingkan data nilai mahasiswa antara Feeder PDDIKTI dengan SISKA. Semua role. Response bersih tanpa duplikat.', '## GET /api/v1/feeder/validasi/mahasiswa

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
| Parameter | Tipe | Wajib | Keterangan |
|-----------|------|-------|------------|
| `nim` | string | Ya | NIM mahasiswa |
| `kode_tahun_akademik` | integer | Tidak | Kode tahun akademik |
| `semester` | string | Tidak | Semester: 1=Ganjil, 2=Genap |

**Contoh Request:**
```
GET /api/v1/feeder/validasi/mahasiswa?nim=2201010167
GET /api/v1/feeder/validasi/mahasiswa?nim=2201010167&kode_tahun_akademik=23&semester=1
```

**Response (200):**
```json
{
    "status": "success",
    "message": "Validasi nilai mahasiswa berhasil",
    "data": {
        "nim": "2201010167",
        "nama_mahasiswa": "MUHAMMAD ADRIYAN MAZKUR",
        "detail": [
            {
                "kode_krs_detail": "eyJpdiI6IkxZV3...",
                "kode_matakuliah": "ISKB320214",
                "nama_matakuliah": "PEMROGRAMAN II",
                "sks": 3,
                "data_feeder": {
                    "nilai": 90,
                    "nilai_huruf": "A"
                },
                "data_siska": {
                    "nilai_harian": 90,
                    "nilai_uts": 90,
                    "nilai_uas": 90,
                    "nilai_akhir": 90,
                    "grade": "A"
                },
                "status": "sudah_sync",
                "ket_grade": "sesuai",
                "ket_nilai": "sesuai"
            }
        ],
        "summary": {
            "total": 8,
            "sudah_sync": 6,
            "belum_sync": 2
        }
    }
}
```

> **Catatan:** `kode_krs_detail` dienkripsi. Digunakan untuk parameter sync.', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('65', '9', 'Validasi Per Kelas (Feeder)', 'Membandingkan data nilai seluruh mahasiswa dalam satu kelas antara Feeder PDDIKTI dengan SISKA. Semua role. Cek kelas di SISKA dulu, jika tidak ada return error.', '## GET /api/v1/feeder/validasi/kelas

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
| Parameter | Tipe | Wajib | Keterangan |
|-----------|------|-------|------------|
| `tahun_akademik` | string | Ya | Format: "YYYY/YYYY" (contoh: "2022/2023") |
| `semester` | string | Ya | "1" = Ganjil, "2" = Genap |
| `kode_matakuliah` | string | Ya | Kode mata kuliah (contoh: "ISKB320214") |
| `kelas` | string | Ya | Nama kelas SISKA: "A", "B", "C", "D", "E", "F", "G", "EX" |

**Contoh Request:**
```
GET /api/v1/feeder/validasi/kelas?tahun_akademik=2022/2023&semester=2&kode_matakuliah=ISKB320214&kelas=A
```

**Response (200):**
```json
{
    "status": "success",
    "message": "Validasi kelas berhasil",
    "data": {
        "tahun_akademik": "2022/2023",
        "semester": "Genap",
        "kode_matakuliah": "ISKB320214",
        "nama_matakuliah": "PEMROGRAMAN II",
        "kelas_siska": "A",
        "filter_feeder": "nama_kelas_kuliah LIKE \'%A\'",
        "detail": [
            {
                "nim": "2201010001",
                "kode_krs_detail": "eyJpdiI6IkxZV3...",
                "nama_mahasiswa": "IDA BAGUS SAMGITA DHARMA PUTRA",
                "data_feeder": {
                    "nilai": 37,
                    "nilai_huruf": "D"
                },
                "data_siska": {
                    "nilai_harian": 37,
                    "nilai_uts": 37,
                    "nilai_uas": 37,
                    "nilai_akhir": 37,
                    "grade": "D"
                },
                "status": "sudah_sync",
                "ket_grade": "sesuai",
                "ket_nilai": "sesuai"
            }
        ],
        "summary": {
            "total": 48,
            "sudah_sync": 30,
            "belum_sync": 18
        }
    }
}
```

> **Catatan Mapping Kelas:**
> - SISKA "A" â†’ Feeder filter: `LIKE \'%A\'` (match "IIA")
> - SISKA "B" â†’ Feeder filter: `LIKE \'%B\'` (match "IIB")
> - SISKA "E" â†’ Feeder filter: `LIKE \'%E\'` (match "IIE")
> - SISKA "EX" â†’ Feeder filter: `LIKE \'%Eks\'` (match "IIEks")
> - Jika kelas tidak ada di SISKA â†’ return error 404', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('66', '9', 'Sinkronisasi Data (Feeder)', 'Melakukan sinkronisasi data nilai dari Feeder PDDIKTI ke SISKA. Hanya role **Akademik**. Server TIDAK mengambil data dari Feeder â€” client mengirim data langsung.', '## POST /api/v1/feeder/sync

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
    "items": [
        {
            "kode_khs_detail": "eyJpdiI6IkxZV3...",
            "nilai_harian": 85,
            "nilai_uts": 85,
            "nilai_uas": 85,
            "nilai_akhir": 85
        },
        {
            "kode_khs_detail": "eyJpdiI6IkxZV4...",
            "nilai_harian": 90,
            "nilai_uts": 90,
            "nilai_uas": 90,
            "nilai_akhir": 90
        }
    ]
}
```

**Request Body Fields:**
| Field | Tipe | Wajib | Keterangan |
|-------|------|-------|------------|
| `items` | array | Ya | Array of nilai items |
| `items.*.kode_khs_detail` | string | Ya | Kode KHS Detail (terenkripsi dari validasi) |
| `items.*.nilai_harian` | numeric | Ya | Nilai harian (0-100) |
| `items.*.nilai_uts` | numeric | Ya | Nilai UTS (0-100) |
| `items.*.nilai_uas` | numeric | Ya | Nilai UAS (0-100) |
| `items.*.nilai_akhir` | numeric | Ya | Nilai akhir (0-100) |

**Response (200):**
```json
{
    "status": "success",
    "message": "Sync berhasil",
    "data": {
        "message": "Data berhasil diupdate",
        "total_updated": 2
    }
}
```

**Response (422):**
```json
{
    "status": "error",
    "message": "Validation Error",
    "errors": {
        "items": ["The items field is required."],
        "items.0.kode_khs_detail": ["The items.0.kode khs detail field is required."]
    }
}
```

**Response (500):**
```json
{
    "status": "error",
    "message": "Sync gagal: [error message]"
}
```

> **Catatan:** 
> - `kode_khs_detail` didencrypt untuk mencari record `khs_detail` di database
> - Server membandingkan nilai lama vs baru, hanya update jika berubah
> - Semua field nilai (`nilai_harian`, `nilai_uts`, `nilai_uas`, `nilai_akhir`) diisi dengan nilai yang sama dari Feeder (`nilai_angka`)
> - Throttle: 30 request per menit', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('67', '9', 'History Sinkronisasi (Feeder)', 'Mendapatkan riwayat sinkronisasi yang telah dilakukan. Hanya role **Akademik**.', '## GET /api/v1/feeder/history

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
| Parameter | Tipe | Wajib | Keterangan |
|-----------|------|-------|------------|
| `tipe_sync` | string | Tidak | Filter: `sync` |
| `referensi` | string | Tidak | Filter referensi |
| `per_page` | integer | Tidak | Data per halaman (default: 15, max: 100) |

**Contoh Request:**
```
GET /api/v1/feeder/history
GET /api/v1/feeder/history?tipe_sync=sync&per_page=30
```

**Response (200):**
```json
{
    "status": "success",
    "message": "History berhasil diambil",
    "data": {
        "current_page": 1,
        "per_page": 15,
        "total": 25,
        "data": [
            {
                "id": 1,
                "tipe": "sync",
                "tipe_sync": "sync",
                "referensi": "eyJpdiI6IkxZV3...",
                "jumlah_data_feeder": 2,
                "jumlah_data_siska": 2,
                "jumlah_sync": 2,
                "jumlah_gagal": 0,
                "status": "success",
                "synced_by": {
                    "id": 1,
                    "name": "Admin Akademik"
                },
                "created_at": "2026-07-08T10:30:00.000000Z"
            }
        ]
    }
}
```', NULL, '2026-07-10 13:41:31', '2026-07-10 13:41:31'),
('68', '2', 'Get Mahasiswa by NIM (SIMORTUA)', 'Mendapatkan detail data mahasiswa berdasarkan NIM melalui modul SIMORTUA.', '## GET /api/v1/simortua/mahasiswa?nim=2301001

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
| Parameter | Tipe | Wajib | Keterangan |
|-----------|------|-------|------------|
| `nim` | string | Ya | NIM mahasiswa |

**Contoh Request:**
```
GET /api/v1/simortua/mahasiswa?nim=2301001
```

**Response (200):**
```json
{
    "status": "success",
    "message": "Mahasiswa retrieved successfully",
    "data": {
        "nim": "2301001",
        "nama_mahasiswa": "Budi Santoso",
        "program_studi_kode": 2,
        "status": "A",
        "nama_prodi": {
            "kode_program_studi": 2,
            "nama_program_studi": "Teknik Informatika"
        }
    }
}
```

**Response (404):**
```json
{
    "status": "error",
    "message": "Mahasiswa not found"
}
```', NULL, '2026-09-23 05:34:21', '2026-09-23 05:34:21'),
('69', '8', 'Tahun Akademik Kedokteran', 'Mendapatkan seluruh data tahun akademik Fakultas Kedokteran (kode_prodi=23).', '## GET /api/v1/divisi/get-tahun-akademik

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "status": true,
    "data": [
        {
            "kode_tahun_akademik": 24,
            "tahun_akademik": "2025/2026",
            "semester": "1",
            "status": "A"
        }
    ]
}
```', NULL, '2026-09-23 05:34:21', '2026-09-23 05:34:21'),
('70', '8', 'KRS / KHS Kedokteran', 'Mendapatkan data KRS dan KHS seluruh mahasiswa Fakultas Kedokteran (kode_prodi=23) beserta relasinya.', '## GET /api/v1/divisi/get-krs-khs

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "status": true,
    "data": [
        {
            "nim": "2301001",
            "krs": [
                {
                    "kode_krs": "KRS001",
                    "kode_tahun_akademik": 24,
                    "nim": "2301001"
                }
            ],
            "krs_detail": [
                {
                    "kode_krs_detail": 1,
                    "kode_krs": "KRS001",
                    "kode_matakuliah": "TI101",
                    "nilai_akhir": 85
                }
            ]
        }
    ]
}
```', NULL, '2026-09-23 05:34:21', '2026-09-23 05:34:21'),
('71', '8', 'Status Perkuliahan by Prodi', 'Mendapatkan status perkuliahan mahasiswa per program studi (role: akademik).', '## GET /api/v1/divisi/status-perkuliahan-by-prodi?kode={encrypted}

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
| Parameter | Tipe | Wajib | Keterangan |
|-----------|------|-------|------------|
| `kode` | string | Ya | Kode program studi terenkripsi (dari /api/v1/divisi/program-studi) |

**Contoh Request:**
```
GET /api/v1/divisi/status-perkuliahan-by-prodi?kode=eyJpdiI6...
```

**Response (200):**
```json
{
    "status": true,
    "message": "Status Perkuliahan Ditemukan untuk Program Studi ini",
    "data": [
        {
            "id": 1,
            "kode": "eyJpdiI6...",
            "nim": "2301001",
            "nama_mahasiswa": "Budi Santoso",
            "nama_program_studi": "Teknik Informatika",
            "status_perkuliahan": true,
            "pembayaran_spp": true,
            "pengumpulan_krs": "1"
        }
    ]
}
```', NULL, '2026-09-23 05:34:21', '2026-09-23 05:34:21'),
('72', '8', 'Status Perkuliahan by Prodi - Sudah Kumpul KRS', 'Mendapatkan status perkuliahan mahasiswa per program studi yang sudah mengumpulkan KRS (role: akademik).', '## GET /api/v1/divisi/status-perkuliahan-by-prodi-kumpul?kode={encrypted}

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
| Parameter | Tipe | Wajib | Keterangan |
|-----------|------|-------|------------|
| `kode` | string | Ya | Kode program studi terenkripsi |

**Contoh Request:**
```
GET /api/v1/divisi/status-perkuliahan-by-prodi-kumpul?kode=eyJpdiI6...
```

**Response (200):**
```json
{
    "status": true,
    "message": "Status Perkuliahan Ditemukan untuk Program Studi ini",
    "data": [
        {
            "id": 1,
            "kode": "eyJpdiI6...",
            "nim": "2301001",
            "nama_mahasiswa": "Budi Santoso",
            "nama_program_studi": "Teknik Informatika",
            "status_perkuliahan": true,
            "pembayaran_spp": true,
            "pengumpulan_krs": "1"
        }
    ]
}
```', NULL, '2026-09-23 05:34:21', '2026-09-23 05:34:21'),
('73', '8', 'Status Perkuliahan by Prodi - Belum Kumpul KRS', 'Mendapatkan status perkuliahan mahasiswa per program studi yang belum mengumpulkan KRS (role: akademik).', '## GET /api/v1/divisi/status-perkuliahan-by-prodi-not-kumpul?kode={encrypted}

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
| Parameter | Tipe | Wajib | Keterangan |
|-----------|------|-------|------------|
| `kode` | string | Ya | Kode program studi terenkripsi |

**Contoh Request:**
```
GET /api/v1/divisi/status-perkuliahan-by-prodi-not-kumpul?kode=eyJpdiI6...
```

**Response (200):**
```json
{
    "status": true,
    "message": "Status Perkuliahan Ditemukan untuk Program Studi ini",
    "data": [
        {
            "id": 1,
            "kode": "eyJpdiI6...",
            "nim": "2301001",
            "nama_mahasiswa": "Budi Santoso",
            "nama_program_studi": "Teknik Informatika",
            "status_perkuliahan": true,
            "pembayaran_spp": true,
            "pengumpulan_krs": "0"
        }
    ]
}
```', NULL, '2026-09-23 05:34:21', '2026-09-23 05:34:21'),
('74', '9', 'Validasi Semua Mahasiswa (Feeder)', 'Membandingkan data nilai seluruh mahasiswa antara Feeder PDDIKTI dengan SISKA dalam satu panggilan. Wajib filter tahun_akademik. Bisa dipersempit dengan semester, prodi, atau detail=mismatch.', '## GET /api/v1/feeder/validasi/semua-mahasiswa?tahun_akademik=2025/2026

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
| Parameter | Tipe | Wajib | Keterangan |
|-----------|------|-------|------------|
| `tahun_akademik` | string | Ya | Format: "YYYY/YYYY" (contoh: "2025/2026") |
| `semester` | string | Tidak | "1" = Ganjil, "2" = Genap |
| `kode_program_studi` | integer | Tidak | Kode program studi (filter SISKA) |
| `detail` | string | Tidak | `mismatch` = hanya baris belum sync |

**Contoh Request:**
```
GET /api/v1/feeder/validasi/semua-mahasiswa?tahun_akademik=2025/2026&semester=1
GET /api/v1/feeder/validasi/semua-mahasiswa?tahun_akademik=2025/2026&detail=mismatch
```

**Response (200):**
```json
{
    "status": "success",
    "message": "Validasi semua mahasiswa berhasil",
    "data": {
        "detail": [
            {
                "nim": "2301001",
                "nama_mahasiswa": "Budi Santoso",
                "kode_matakuliah": "TI101",
                "nama_matakuliah": "Algoritma",
                "data_feeder": { "nilai": 85, "nilai_huruf": "A" },
                "data_siska": { "nilai_harian": 80, "nilai_uts": 85, "nilai_uas": 90, "nilai_akhir": 85, "grade": "A" },
                "status": "sudah_sync",
                "ket_grade": "sesuai",
                "ket_nilai": "sesuai"
            }
        ],
        "summary": { "total": 100, "sudah_sync": 90, "belum_sync": 10 },
        "summary_per_prodi": [
            { "kode_program_studi": "2", "nama_program_studi": "Teknik Informatika", "total": 50, "sudah_sync": 45, "belum_sync": 5 }
        ]
    }
}
```', NULL, '2026-09-23 05:34:21', '2026-09-23 05:34:21'),
('75', '9', 'Validasi Semua Kelas (Feeder)', 'Membandingkan data nilai seluruh kelas antara Feeder PDDIKTI dengan SISKA dalam satu panggilan. Wajib filter tahun_akademik. Bisa dipersempit dengan semester, prodi, matakuliah, atau detail=mismatch.', '## GET /api/v1/feeder/validasi/semua-kelas?tahun_akademik=2025/2026

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
| Parameter | Tipe | Wajib | Keterangan |
|-----------|------|-------|------------|
| `tahun_akademik` | string | Ya | Format: "YYYY/YYYY" (contoh: "2025/2026") |
| `semester` | string | Tidak | "1" = Ganjil, "2" = Genap |
| `kode_program_studi` | integer | Tidak | Kode program studi |
| `kode_matakuliah` | string | Tidak | Kode mata kuliah |
| `detail` | string | Tidak | `mismatch` = hanya baris belum sync |

**Contoh Request:**
```
GET /api/v1/feeder/validasi/semua-kelas?tahun_akademik=2025/2026&semester=1
GET /api/v1/feeder/validasi/semua-kelas?tahun_akademik=2025/2026&kode_matakuliah=TI101&detail=mismatch
```

**Response (200):**
```json
{
    "status": "success",
    "message": "Validasi semua kelas berhasil",
    "data": {
        "kelas": [
            {
                "tahun_akademik": "2025/2026",
                "semester": "Ganjil",
                "kode_program_studi": "2",
                "nama_program_studi": "Teknik Informatika",
                "kode_matakuliah": "TI101",
                "nama_matakuliah": "Algoritma",
                "nama_kelas": "A",
                "detail": [
                    {
                        "nim": "2301001",
                        "nama_mahasiswa": "Budi Santoso",
                        "data_feeder": { "nilai": 85, "nilai_huruf": "A" },
                        "data_siska": { "nilai_harian": 80, "nilai_uts": 85, "nilai_uas": 90, "nilai_akhir": 85, "grade": "A" },
                        "status": "sudah_sync",
                        "ket_grade": "sesuai",
                        "ket_nilai": "sesuai"
                    }
                ],
                "summary": { "total": 40, "sudah_sync": 38, "belum_sync": 2 }
            }
        ],
        "summary": {
            "total_kelas": 10,
            "kelas_sudah_sync": 8,
            "kelas_belum_sync": 2,
            "total_mahasiswa": 400,
            "sudah_sync": 380,
            "belum_sync": 20
        },
        "summary_per_prodi": []
    }
}
```', NULL, '2026-09-23 05:34:21', '2026-09-23 05:34:21');
