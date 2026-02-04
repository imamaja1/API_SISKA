# OBE API (Sanctum SPA Cookie) — Cara Testing di Postman

Dokumen ini untuk testing endpoint OBE yang **menggunakan Sanctum SPA cookie (session-based)**, bukan Bearer token.

> Intinya: Postman harus mengirim **Cookie + header `Origin`/`Referer`** supaya Laravel Sanctum menganggap request kamu **stateful** dan middleware session aktif. Tanpa `Origin`/`Referer`, request sering dianggap “non-frontend”, sehingga session tidak jalan dan muncul error `Session not available`.

---

## 1) Pastikan URL host konsisten

Pilih salah satu dan pakai **konsisten** untuk semua request:

- `http://localhost:8000` **atau**
- `http://127.0.0.1:8000`

Cookie **tidak** akan ikut terkirim jika kamu ganti-ganti host (misalnya `localhost` ↔ `127.0.0.1`).

Kalau kamu pakai backend `http://127.0.0.1:8000`, maka request `/sanctum/csrf-cookie` juga harus ke `127.0.0.1:8000` (bukan `localhost:8000`).

---

## 2) Pastikan `.env` sesuai (backend)

Cek nilai berikut (contoh sesuai project kamu):

- `SANCTUM_STATEFUL_DOMAINS=localhost:5173,localhost:3000,127.0.0.1:5173,127.0.0.1:3000`
- `CORS_ALLOWED_ORIGINS=http://localhost:5173,http://localhost:3000,http://127.0.0.1:5173,http://127.0.0.1:3000`
- `SESSION_DOMAIN=null` (untuk dev biasanya OK)

Jika sudah ubah `.env`, jalankan (di terminal):

- `php artisan config:clear`
- `php artisan cache:clear`

---

## 3) Flow Postman yang benar (Step-by-step)

### Step A — Ambil CSRF cookie

Request:

- Method: `GET`
- URL: `http://localhost:8000/sanctum/csrf-cookie` (atau `http://127.0.0.1:8000/sanctum/csrf-cookie` sesuai host yang kamu pilih)
- Headers:
    - `Accept: application/json`
        - `Origin: http://localhost:3000`  
           (atau `http://localhost:5173` / `http://127.0.0.1:3000` / `http://127.0.0.1:5173` — harus ada di `SANCTUM_STATEFUL_DOMAINS`)

WAJIB: header `Origin` (atau `Referer`) harus ada. Tanpa ini, Postman biasanya dianggap **non-stateful** dan session tidak akan aktif.

Expected:

- Response `204 No Content` (atau 200)
- Cookie jar Postman berisi minimal:
    - `XSRF-TOKEN`
    - (kadang) `laravel_session` (tergantung config)

Di Postman, klik **Cookies** (dekat tombol Send) → pilih domain `localhost` → pastikan cookie-cookienya a
da.

### Step B — Login (menghasilkan session)

Request:

- Method: `POST`
- URL: `http://localhost:8000/api/v1/obe/login` (atau `http://127.0.0.1:8000/api/v1/obe/login` sesuai host)
- Headers (wajib):
    - `Accept: application/json`
    - `Content-Type: application/json`
    - `Origin: http://localhost:3000`
    - `X-XSRF-TOKEN: <isi cookie XSRF-TOKEN>`

Tips Postman: jangan set header `Cookie` manual. Pastikan cookie tersimpan lewat tombol **Cookies**.

Opsional (biar otomatis): di tab **Pre-request Script** untuk request login, kamu bisa set:

```javascript
pm.environment.set(
    "csrf_token",
    decodeURIComponent(pm.cookies.get("XSRF-TOKEN") || ""),
);
```

Lalu header `X-XSRF-TOKEN` isi `{{csrf_token}}`.

Body (raw JSON):

```json
{
    "email": "...",
    "password": "..."
}
```

Cara isi `X-XSRF-TOKEN`:

1. Buka cookie `XSRF-TOKEN` di Postman cookie jar.
2. Copy **value**-nya.
3. Paste ke header `X-XSRF-TOKEN`.

Catatan penting:

- Value cookie kadang URL-encoded. Kalau request masih 419/CSRF, coba decode dulu (misalnya `%3D` jadi `=`).

Expected:

- Response `200` dengan `{ "status": true, "message": "Login successful" }`
- Cookie `laravel_session` harus terset (atau ter-update) untuk domain itu.

### Step C — Call endpoint protected (me)

Request:

- Method: `GET`
- URL: `http://localhost:8000/api/v1/obe/me` (atau `http://127.0.0.1:8000/api/v1/obe/me` sesuai host)
- Headers:
    - `Accept: application/json`
    - `Origin: http://localhost:3000`

Expected:

- Response `200` dan data dosen.

### Step D — Logout

Request:

- Method: `POST`
- URL: `http://localhost:8000/api/v1/obe/logout`
- Headers:
    - `Accept: application/json`
    - `Origin: http://localhost:3000`
    - `X-XSRF-TOKEN: <isi cookie XSRF-TOKEN>`

---

## 4) Kenapa Postman sering dapat “Session not available” walau cookie sudah ada?

Penyebab paling umum:

1. **Tidak ada header `Origin` atau `Referer`**
    - Sanctum menentukan “stateful frontend request” dari `Origin`/`Referer`.
    - Solusi: selalu kirim `Origin: http://localhost:3000` (atau origin frontend yang kamu set di `.env`).

2. **Host tidak konsisten** (`localhost` vs `127.0.0.1`)
    - Cookie tersimpan untuk domain tertentu.
    - Solusi: pakai satu host saja.

3. **Cookie tidak terkirim dari Postman**
    - Cek di Postman: Cookies untuk domain itu ada dan tidak “expired”.

4. **CSRF header belum dikirim untuk request POST/PUT/DELETE**
    - Solusi: tambahkan `X-XSRF-TOKEN`.

---

## 5) Checklist cepat (kalau masih error)

- [ ] Request ke `/sanctum/csrf-cookie` dilakukan dulu
- [ ] Cookie `XSRF-TOKEN` tersimpan di Postman (domain yang benar)
- [ ] Saat `POST /api/v1/obe/login`, header `Origin` ada
- [ ] Saat `POST`, header `X-XSRF-TOKEN` ada dan isinya sesuai cookie
- [ ] Semua request pakai host yang sama (`localhost:8000` saja atau `127.0.0.1:8000` saja)

---

## Contoh set headers minimal (Postman)

Untuk `GET /sanctum/csrf-cookie`:

- `Accept: application/json`
- `Origin: http://localhost:3000`

Untuk `POST /api/v1/obe/login`:

- `Accept: application/json`
- `Content-Type: application/json`
- `Origin: http://localhost:3000`
- `X-XSRF-TOKEN: <value cookie XSRF-TOKEN>`
