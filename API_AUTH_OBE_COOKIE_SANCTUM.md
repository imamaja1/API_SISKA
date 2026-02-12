# Auth OBE — Sanctum SPA Cookie (Session)

Dokumen ini menjelaskan cara login API OBE menggunakan **Sanctum SPA Cookie** (session-based), bukan bearer token.

## Konsep singkat

- **Bearer token**: client menyimpan token (mis. di localStorage) lalu kirim `Authorization: Bearer ...`.
- **Sanctum SPA Cookie**: client tidak menyimpan token akses. Setelah login, server mengirim cookie session (`laravel_session`) dan cookie CSRF (`XSRF-TOKEN`). Request berikutnya otomatis membawa cookie tersebut (jika `withCredentials` aktif).

Keuntungan utama cookie SPA:

- Tidak ada token bearer yang perlu disimpan di frontend.
- Proteksi CSRF lewat token `XSRF-TOKEN`.

## Endpoint yang dipakai

- `GET /sanctum/csrf-cookie` (WAJIB sebelum login dari browser)
- `POST /api/v1/obe/login`
- `GET /api/v1/obe/me`
- `POST /api/v1/obe/logout`

> Catatan: route OBE terproteksi memakai `middleware: auth:dosen_web` (session-cookie).

## Alur yang benar (SPA / Browser)

1. Ambil CSRF cookie

- Request:
    - `GET /sanctum/csrf-cookie`
    - Harus pakai `withCredentials: true`

Hasil:

- Browser menyimpan cookie `XSRF-TOKEN`.

2. Login

- Request:
    - `POST /api/v1/obe/login`
    - Body JSON: `{ "email": "...", "password": "..." }`
    - Harus `withCredentials: true`

Hasil:

- Browser menyimpan cookie session `laravel_session`.
- Response login **tidak mengembalikan token** (karena session sudah menjadi identitas).

3. Akses endpoint terproteksi

- Request:
    - `GET /api/v1/obe/me`
    - Harus `withCredentials: true`

Hasil:

- Server mengenali user dari cookie session.

4. Logout

- Request:
    - `POST /api/v1/obe/logout`
    - Harus `withCredentials: true`

Hasil:

- Session di server di-invalidate.

## Error umum

### 419 "CSRF token mismatch."

Ini terjadi ketika request dianggap **stateful** (cookie + session aktif), tetapi token CSRF yang dikirim tidak cocok.

Penyebab paling sering:

- Kamu **tidak memanggil** `GET /sanctum/csrf-cookie` sebelum `POST /login`.
- Cookie `XSRF-TOKEN` dan/atau cookie session tidak tersimpan / tidak ikut terkirim (karena CORS credentials / SameSite / domain).
- Frontend dan API beda **subdomain**, tetapi cookie dibuat host-only (mis. hanya untuk `api.example.com`), sehingga JavaScript di `app.example.com` **tidak bisa membaca** cookie `XSRF-TOKEN` untuk mengirim header `X-XSRF-TOKEN`.

Solusi ringkas:

- Pastikan `withCredentials: true`.
- Pastikan CORS mengizinkan origin frontend dan `supports_credentials=true`.
- Untuk kasus beda subdomain (mis. `app.ubg.ac.id` -> `api-siska-tester.ubg.ac.id`), set `SESSION_DOMAIN=.ubg.ac.id` agar cookie `XSRF-TOKEN` bisa dibaca oleh frontend.

### "Session store not set on request"

Artinya request kamu **tidak masuk mode stateful (SPA)**, sehingga middleware session tidak berjalan (tidak ada `$request->session()`).

Penyebab paling sering:

- Request dilakukan dari Postman/cURL tanpa cookie jar / tanpa context browser.
- Frontend tidak mengaktifkan `withCredentials: true` (Axios) / `credentials: "include"` (fetch).
- Domain frontend belum dimasukkan ke `SANCTUM_STATEFUL_DOMAINS`.

Solusi:

- Panggil `GET /sanctum/csrf-cookie` dulu dari browser/SPA dengan credentials.
- Pastikan env sudah benar (lihat bagian _Stateful domains_ dan _CORS_).

## Contoh frontend

### Axios

```js
import axios from "axios";

const api = axios.create({
    baseURL: "http://localhost:8000",
    withCredentials: true,
});

export async function login(email, password) {
    // 1) ambil CSRF cookie dulu
    await api.get("/sanctum/csrf-cookie");

    // 2) login
    return api.post("/api/v1/obe/login", { email, password });
}

export async function me() {
    return api.get("/api/v1/obe/me");
}

export async function logout() {
    return api.post("/api/v1/obe/logout");
}
```

### fetch

```js
const BASE = "http://localhost:8000";

await fetch(`${BASE}/sanctum/csrf-cookie`, {
    method: "GET",
    credentials: "include",
});

await fetch(`${BASE}/api/v1/obe/login`, {
    method: "POST",
    credentials: "include",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ email, password }),
});

const meRes = await fetch(`${BASE}/api/v1/obe/me`, {
    method: "GET",
    credentials: "include",
});
```

## Setting penting di backend

### 1) Stateful domains

Set di `.env` (contoh):

```
SANCTUM_STATEFUL_DOMAINS=localhost:5173,127.0.0.1:5173,localhost:3000
SESSION_DOMAIN=localhost
```

Untuk production beda subdomain (contoh):

```
APP_URL=https://api-siska-tester.ubg.ac.id

SANCTUM_STATEFUL_DOMAINS=app.ubg.ac.id,api-siska-tester.ubg.ac.id

# penting: share cookie lintas subdomain (agar XSRF-TOKEN terbaca di app.ubg.ac.id)
SESSION_DOMAIN=.ubg.ac.id

SESSION_SECURE_COOKIE=true

# Jika frontend & API masih satu "site" (sama eTLD+1: ubg.ac.id), biasanya aman pakai lax.
# Kalau frontend benar-benar beda domain (bukan *.ubg.ac.id), pakai none + secure.
SESSION_SAME_SITE=lax
```

### 2) CORS harus support credentials

Karena cookie dikirim lintas origin, CORS wajib:

- `Access-Control-Allow-Credentials: true`
- `Access-Control-Allow-Origin: <origin spesifik>` (tidak boleh `*`)

File config dibuat di: `config/cors.php`.
Tambahkan origin frontend ke env:

```
CORS_ALLOWED_ORIGINS=http://localhost:5173
```

Untuk production, pastikan pakai https dan origin harus persis (tanpa wildcard):

```
CORS_ALLOWED_ORIGINS=https://app.ubg.ac.id
```

### 3) HTTPS & SameSite (untuk production)

Jika frontend dan backend beda domain:

- `SESSION_SECURE_COOKIE=true`
- `SESSION_SAME_SITE=none`

Karena `SameSite=None` wajib `Secure`, maka production harus HTTPS.

## Set-Cookie otomatis oleh Laravel/Sanctum

**Anda TIDAK perlu manual set-Cookie di controller.** Laravel secara otomatis mengirim cookie berikut saat login berhasil:

1. **`laravel_session`** (atau `<APP_NAME>-session`)
    - Cookie session yang berisi ID session user
    - Dikirim otomatis oleh middleware `StartSession` setelah `Auth::login()`
    - HttpOnly, Secure (jika HTTPS), SameSite=lax (default)

2. **`XSRF-TOKEN`**
    - Cookie CSRF token (dibaca JavaScript untuk kirim header `X-XSRF-TOKEN`)
    - Dikirim otomatis oleh endpoint `/sanctum/csrf-cookie`
    - TIDAK HttpOnly (supaya JavaScript bisa baca)

### Response headers dari backend (contoh):

```
Set-Cookie: laravel_session=eyJpdiI6Ii...; expires=...; Max-Age=7200; path=/; httponly; samesite=lax
Set-Cookie: XSRF-TOKEN=eyJpdiI6Ii...; expires=...; Max-Age=7200; path=/; samesite=lax
Access-Control-Allow-Credentials: true
Access-Control-Allow-Origin: http://localhost:5173
```

### Catatan penting:

- Jika frontend beda domain dengan backend, set `SESSION_SAME_SITE=none` dan `SESSION_SECURE_COOKIE=true` (wajib HTTPS).
- `SESSION_DOMAIN=null` untuk same-domain, atau `SESSION_DOMAIN=.example.com` untuk subdomain sharing.

## Perubahan di project ini

- Login OBE sekarang menggunakan `Auth::guard('dosen_web')->login(...)` (session) dan tidak lagi membuat personal access token.
- Middleware route OBE menggunakan `auth:dosen_web` untuk autentikasi session-cookie.
- Controller tidak perlu manual set-Cookie; Laravel otomatis handle lewat middleware session.
