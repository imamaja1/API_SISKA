# API Authentication dengan ApiUser - Dokumentasi

## Setup Selesai ✓

Sistem autentikasi Laravel Sanctum untuk `ApiUser` sudah dikonfigurasi dengan lengkap.

## Endpoints yang Tersedia

### 1. Register User Baru
**POST** `/api/register`

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Response (201):**
```json
{
  "message": "User registered successfully",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "status": "active",
    "created_at": "2025-12-15T10:00:00.000000Z",
    "updated_at": "2025-12-15T10:00:00.000000Z"
  },
  "token": "1|aBcDeFgHiJkLmNoPqRsTuVwXyZ..."
}
```

### 2. Login
**POST** `/api/login`

**Request Body:**
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

**Response (200):**
```json
{
  "message": "Login successful",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "status": "active"
  },
  "token": "2|xYzAbCdEfGhIjKlMnOpQrStUv..."
}
```

### 3. Get User Info (Protected)
**GET** `/api/me`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "status": "active"
  }
}
```

### 4. Logout (Protected)
**POST** `/api/logout`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "Logged out successfully"
}
```

### 5. Logout dari Semua Device (Protected)
**POST** `/api/logout-all`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "All tokens revoked successfully"
}
```

## Cara Menggunakan di Code

### Mendapatkan User yang Terautentikasi

```php
// Di Controller atau Route
$user = auth('api_users')->user();

// Atau menggunakan Request
$user = $request->user('api_users');
```

### Membuat Route yang Dilindungi

```php
Route::middleware('auth:api_users')->group(function () {
    Route::get('protected-endpoint', function (Request $request) {
        $user = $request->user('api_users');
        return response()->json(['user' => $user]);
    });
});
```

### Membuat Token dengan Abilities Khusus

```php
$token = $apiUser->createToken('token-name', ['manage-mahasiswa', 'view-data'])->plainTextToken;
```

### Mengecek Abilities di Route

```php
Route::post('mahasiswa', function (Request $request) {
    if (!$request->user('api_users')->tokenCan('manage-mahasiswa')) {
        return response()->json(['message' => 'Forbidden'], 403);
    }
    
    // Lakukan operasi...
})->middleware('auth:api_users');
```

## Test dengan cURL

### Register
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

### Login
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }'
```

### Get User (gunakan token dari response login)
```bash
curl -X GET http://localhost:8000/api/me \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### Logout
```bash
curl -X POST http://localhost:8000/api/logout \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

## Konfigurasi yang Ditambahkan

### 1. `config/auth.php`
- Guard baru: `api_users` menggunakan driver `sanctum`
- Provider baru: `api_users` menggunakan model `ApiUser`

### 2. Model `ApiUser`
- Sudah menggunakan trait `HasApiTokens` dari Laravel Sanctum
- Status field untuk mengaktifkan/menonaktifkan user

### 3. Controller
- `ApiAuthController` di `app/Http/Controllers/Api/`
- Methods: register, login, logout, logoutAll, me

### 4. Routes
- Semua route di `routes/web.php` dengan prefix `/api`
- Public: register, login
- Protected: me, logout, logout-all

## Keamanan

1. Password di-hash menggunakan `Hash::make()`
2. Status user dicek saat login (harus 'active')
3. Token dapat dibatasi dengan abilities
4. Gunakan HTTPS di production
5. Set rate limiting untuk endpoint login/register

## Tips

- Simpan token di sisi client (localStorage, cookies httpOnly, dll)
- Kirim token di header `Authorization: Bearer {token}`
- Revoke token lama saat user ganti password
- Monitor access_logs untuk tracking aktivitas user
