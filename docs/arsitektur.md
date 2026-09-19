# Arsitektur — Sistem Informasi PKK Kabupaten Toba

> Dokumen ini menjelaskan arsitektur teknis aplikasi. Proses bisnis ada di
> [`proses-bisnis.md`](proses-bisnis.md); diagram Mermaid (konteks, deployment,
> ERD, sequence) ada di [`diagram.md`](diagram.md).

## 1. Ringkasan Teknologi

| Komponen | Teknologi |
|---|---|
| Framework | Laravel 12 (PHP 8.2+), konfigurasi modern `bootstrap/app.php` |
| Database | MySQL — **2 koneksi**: utama (Admin Panel & SIDONGAN) + `sieda` (read/maintenance data SIEDA) |
| Frontend | Blade + vanilla JS + CSS terpisah per halaman (tanpa SPA framework); Tailwind/PostCSS & Vite untuk build tertentu |
| Rich text | TinyMCE (vendor lokal di `public/vendor/tinymce`) |
| Mail | Laravel Mail (SMTP) — OTP, verifikasi email pribadi, reset password |
| Queue/Scheduler | Laravel Scheduler (pembersihan notifikasi harian) |
| Testing | PHPUnit / Pest — test Feature untuk Admin, Auth, dan Sidongan |

## 2. Arsitektur Tingkat Tinggi

```
                        ┌──────────────────────────────┐
                        │        Pengguna (browser)    │
                        └──────┬───────────────┬───────┘
                               │               │
              tp-pkk.tobakab.go.id     sidongan.tobakab.go.id
                               │               │
                 ┌─────────────┴───────────────┴─────────────┐
                 │            SATU APLIKASI LARAVEL          │
                 │                                           │
                 │  Routing berbasis DOMAIN (Route::domain): │
                 │  • Grup landing_domain → Landing + Admin  │
                 │  • Grup sidongan_domain → SIDONGAN        │
                 │  • Onboarding & forgot-password: global   │
                 │  • API wilayah & sync SIEDA: global       │
                 └───────┬──────────────────────────┬────────┘
                         │                          │
              ┌──────────┴──────────┐    ┌──────────┴──────────┐
              │  MySQL (utama)      │    │  MySQL (db SIEDA)   │
              │  users, roles,      │    │  read-only/refresh  │
              │  sidongan_documents,│    │  ref_kecamatan,     │
              │  news, desas, ...   │    │  ref_desa, ...      │
              └─────────────────────┘    └──────────┬──────────┘
                         │                          │
                         │        HTTP + HMAC ┌─────┴────────┐
                         └────────────────────│ Aplikasi     │
                          SSO & sinkronisasi  │ SIEDA        │
                                              └──────────────┘
```

**Satu codebase, dua "aplikasi".** Pemisahan dilakukan lewat `Route::domain()`
(`config/app.php`: `landing_domain`, `sidongan_domain`), bukan lewat project
terpisah. Keduanya berbagi tabel `users` dan sesi autentikasi yang sama.

Khusus `APP_ENV=local`, route SIDONGAN didaftarkan ulang **tanpa constraint
host** agar bisa dibuka lewat `127.0.0.1`/preview tanpa mengubah DNS. Fallback
ini tidak aktif di produksi.

## 3. Struktur Direktori

```
app/
├── Console/Commands/          Artisan (sync kecamatan, cleanup notifikasi)
├── Http/
│   ├── Controllers/
│   │   ├── Admin/             Dashboard, Berita, Desa, Struktur, Aplikasi,
│   │   │                      HeroSlider, SK/Dokumen, Template, Tentang,
│   │   │                      SiteSetting, UserManagement, SidonganData,
│   │   │                      SiedaData
│   │   ├── Auth/              Login/registrasi, SSO, PersonalEmail
│   │   ├── Sidongan/          Auth, AdminDocument, Verification,
│   │   │                      ActivityReport, Profile, ForgotPassword
│   │   ├── Landing/           Halaman publik (detail berita)
│   │   ├── Api/               SiedaAvatarSync (server-to-server)
│   │   ├── OnboardingController.php
│   │   └── UnifiedForgotPasswordController.php
│   ├── Middleware/            SidonganAuthenticate, RedirectIfAuthenticatedSidongan,
│   │                          SidonganEnsureProfileComplete, VerifySiedaSyncKey, ...
│   └── View/Composers/        FooterComposer, SidonganNotificationComposer
├── Models/                    User, Role, Permission, Document, ActivityReport,
│                              News, Desa, Application, SiteSetting, ...
│   └── Sieda/                 Model dengan $connection = 'sieda'
├── Notifications/             OTP, verifikasi email pribadi, reset password (branded)
├── Services/                  SsoTokenService, SiedaSyncService,
│                              WilayahIndonesiaService, DocumentPreviewService, ...
└── Support/                   ProfileFields, ImageUploadSanitizer, ...
```

View dan aset mengikuti pola modul: `resources/views/{modules/landing, admin,
sidongan, sidongan-auth, auth, onboarding}` dan
`public/assets/{admin, sidongan, landing, shared}/...` — CSS/JS dipisah per
halaman (diekstrak otomatis oleh `scripts/extract_*.php` pada saat dev).

## 4. Desain Routing & Domain

| Blok | Registrasi | Catatan |
|---|---|---|
| API wilayah (`/api/v1/wilayah/*`) | Global, di luar grup domain | Dipanggil relatif oleh SIDONGAN; sumbernya tabel `wilayah` lokal agar tidak bergantung API wilayah.id |
| Sinkron avatar SIEDA (`POST /api/sieda/sync-avatar`) | Global | Middleware `sieda.sync` (HMAC header X-Sieda-Key/Timestamp/Signature), tanpa CSRF (server-to-server) |
| SIDONGAN | Closure `$registerSidonganRoutes` dipanggil 2×: subdomain asli + fallback local | Guest: landing, login, forgot password. Auth: `sidongan.auth` + `sidongan.profile` |
| Landing + API publik landing | Grup `landing_domain` | API berita/struktur/aplikasi/desa/SK/template/hero-slider (rate-limited `public-api`) |
| Admin Panel | Grup `landing_domain` + `auth` + `permission:manage-*` | Setiap modul dijaga permission tersendiri |
| Onboarding & lupa password | Global (semua host) | Dipakai kedua aplikasi |
| Auth bawaan (`login`, `register`, `verify-email`, dst.) | `routes/auth.php`, di-require paling bawah | Agar tidak menimpa route khusus di atasnya |

**Alias middleware** (didaftarkan di `bootstrap/app.php`):
`sieda.sync`, `sidongan.auth`, `sidongan.guest`, `sidongan.profile`,
`permission`.

## 5. Autentikasi & Otorisasi

### 5.1 Dua guard, satu tabel users
- Guard `web` → Admin Panel; guard `sidongan` → aplikasi SIDONGAN.
- Akses SIDONGAN ditentukan `users.sidongan_role` (ketua, sekretaris,
  bendahara, staf_ahli_1/2, pengurus_1–4). Middleware `sidongan.auth` menolak
  akun tanpa role SIDONGAN.
- Helper di model `User`: `isSidonganKetua()`, `isSidonganSekretaris()`, dst. —
  Super Admin selalu termasuk semua role SIDONGAN.

### 5.2 Admin Panel: role + permission
- RBAC: `users.role_id → roles → role_permission → permissions`, plus izin
  tambahan per akun lewat `permission_user`.
- `User::hasPermission()`: administrator/super_admin selalu true → izin role →
  izin pribadi. Route admin memakai middleware `permission:manage-*`;
  controller sensitif memverifikasi ulang di dalam (defense-in-depth).

### 5.3 SSO SIEDA (HMAC, sekali pakai)
- Token: `base64url(JSON{email, exp, return}) . '.' . HMAC-SHA256(payload, SIEDA_SYNC_SECRET)`.
- TTL 5 menit; verifikasi constant-time (`hash_equals`); token dipakai sekali
  (cache `sso_token_used_*`); whitelist tujuan; rate limit gagal per-IP +
  global (anti rotasi `X-Forwarded-For` dengan `trustProxies: '*'`).

### 5.4 Verifikasi email pribadi
- Link **signed URL** (tidak perlu login) untuk verifikasi via klik email;
  onboarding memakai **OTP 6 digit** dengan cooldown dan batas percobaan.
- Ganti email pribadi → status verifikasi otomatis direset.
- `routeNotificationForMail()` di model User memusatkan aturan tujuan email:
  notifikasi pengguna → email pribadi terverifikasi, fallback email login.

## 6. Modul Data Utama

| Domain | Tabel/Model | Keterangan |
|---|---|---|
| Pengguna & akses | `users`, `roles`, `permissions`, `role_permission`, `permission_user` | Guard `web` + `sidongan`; kolom `sidongan_role`, `sieda_role` |
| Profil & keamanan | kolom `phone_number`, `personal_email`, `personal_email_verified_at`, `personal_email_otp_*`, `onboarding_skipped_at` | Dasar onboarding & reset password |
| Surat SIDONGAN | `sidongan_documents`, `sidongan_categories`, `sidongan_tags`, `activity_reports`, `notifications` | `disposisi_data` (JSON) memuat penerima & instruksi; status surat berjalan otomatis via `Document::updateCorrectStatus()` |
| Konten landing | `news`, `news_images`, `struktur_members`, `pokjas`, `applications`, `desas`, `kecamatans`, `dokumens`, `templates`, `hero_sliders`, `tentang_kami`, `site_settings` | Semua tampil di landing page lewat API publik |
| Wilayah | `wilayah` (utama), `ref_kecamatan`/`ref_desa` (koneksi `sieda`) | Dropdown onboarding memakai `wilayah` lokal; modul Desa membaca data SIEDA |
| Log | `admin_activity_logs` | Jejak perubahan penting oleh admin |

## 7. Alur Domain Surat (Inti SIDONGAN)

```
Sekretaris        Surat                    Disposisi               Laporan
   │                │                         │                      │
   │ catat surat    │ status:                 │ kirim disposisi      │ buat laporan
   ├───────────────►│ menunggu_disposisi      ├─────────────────────►│ (menunggu_
   │                │                         │  status: berjalan    │  verifikasi)
   │                │                         │                      │
   │                │      Ketua verifikasi ──┤                      │
   │                │      setujui ──────────►│ status: selesai      │ disetujui
   │                │      tolak ────────────►│ status: berjalan     │ ditolak
   │                │                         │  (revisi laporan)    │
   │                │      Ketua arsip ──────►│ status: diarsipkan   │
```

Status surat **tidak pernah di-update manual oleh user**: dihitung dari kondisi
laporan terakhir (COALESCE/subquery), sehingga konsisten antara daftar, statistik,
dan detail.

## 8. Arsitektur Keamanan

| Lapisan | Implementasi |
|---|---|
| Rate limiting | `throttle` pada login SIDONGAN (5/menit), OTP/resend (3/30 menit), API publik (`public-api`), SSO gagal (per-IP + global) |
| Shared secret HMAC | Sinkronisasi user & avatar SIEDA, token SSO — header `X-Sieda-Key/Timestamp/Signature`, verifikasi `hash_equals` |
| Signed URL | Verifikasi email pribadi & email verifikasi bawaan Laravel |
| Upload aman | `ImageUploadSanitizer` — whitelist MIME (fileinfo), SVG ditolak, fail-closed bila ekstensi tidak tersedia |
| CSRF | Aktif global; hanya endpoint server-to-server SIEDA yang dikecualikan |
| Proxy trust | `trustProxies(at: '*')` untuk HTTPS di belakang tunnel/proxy |
| Prinsip least privilege | Middleware `permission:manage-*` + cek ulang di controller; hapus data SIEDA hanya Super Admin |
| Anti enumeration | Lupa password selalu membalas pesan generik |

## 9. Notifikasi & Tugas Terjadwal

- **Mail class** di `app/Notifications/`: OTP onboarding, verifikasi email
  pribadi, reset password (branded per aplikasi, parameter guard).
- **Notifikasi in-app** SIDONGAN tersimpan di tabel `notifications`; composer
  `SidonganNotificationComposer` menyuntikkan jumlah belum dibaca ke layout.
- **Scheduler**: `notifications:cleanup` tiap hari 00.05 — membersihkan
  notifikasi yang sudah dibaca; output ke `storage/logs/notification-cleanup.log`.

## 10. Frontend & Aset

- Tanpa framework SPA: tiap halaman punya CSS/JS sendiri
  (`public/assets/<area>/<tipe>/*`), memuat lebih cepat dan mudah diaudit.
- Ikon UI memakai **inline SVG** (bukan emoji/ikon font) demi konsistensi
  render lintas perangkat; toast & modal memakai utilitas bersama di
  `public/assets/shared/js/`.
- Dark mode + light mode didukung lewat variabel CSS global.
- Landing page mengonsumsi API JSON publik miliknya sendiri (pola headless
  ringan), sehingga konten bisa berubah tanpa menyentuh template.

## 11. Konfigurasi Lingkungan (Kunci)

| Variabel | Fungsi |
|---|---|
| `LANDING_DOMAIN` / `SIDONGAN_DOMAIN` | Pemetaan domain → modul aplikasi |
| `DB_DATABASE` / `DB_SIEDA_DATABASE` | Koneksi database utama & SIEDA |
| `SIEDA_SYNC_SECRET` (`services.sieda.sync_secret`) | Shared secret HMAC untuk SSO + sinkronisasi; kosong = fitur integrasi mati dengan aman |
| `services.sieda.base_url` | Alamat callback "Kembali ke SIEDA" (default `http://127.0.0.1:8004`) |
| Mail (`MAIL_*`) | Pengiriman OTP/verifikasi/reset password |

## 12. Arsitektur SIEDA Web dan SIEDA Mobile

### 12.1 SIEDA Web / API

Project `sieda-pkk-toba-app` adalah backend Laravel terpisah yang menyediakan:

- Web admin dan front-end data SIEDA.
- REST API `/api/v1` untuk aplikasi Flutter.
- API publik untuk homepage, berita, slider, profil, potensi, dan referensi.
- API terlindungi untuk dashboard, penduduk, keluarga, Dasawisma, rekapitulasi, catatan kelahiran/kematian, kegiatan warga, arsip surat, dan profil.
- Endpoint internal `/api/sieda/*` untuk sinkronisasi dari Admin Panel PKK.

Autentikasi mobile memakai **Laravel Sanctum personal access token**. Akun dapat berasal dari database lokal SIEDA atau diverifikasi terhadap database Admin Panel PKK melalui `LoginPkkService`. Akun yang memiliki `sieda_role` dikelola oleh Admin Panel PKK sehingga email login dan password tidak dapat diubah melalui API mobile.

### 12.2 SIEDA Mobile

Project `sieda` adalah aplikasi Flutter untuk Android/iOS/web dengan komponen utama:

- `provider` untuk state management.
- `dio` untuk komunikasi HTTP API.
- `flutter_secure_storage` untuk token dan passphrase database.
- `sqflite_sqlcipher` untuk database offline terenkripsi.
- `connectivity_plus` untuk deteksi koneksi.
- `firebase_messaging` dan Crashlytics untuk notifikasi dan pemantauan crash.
- `local_auth` untuk pengamanan biometrik.
- `flutter_map` dan geolokasi untuk kebutuhan lokasi.

Modul mobile terdapat pada `lib/screens`: auth, dashboard, penduduk, keluarga, Dasawisma, kegiatan, catatan, rekapitulasi, profile, dan admin.

### 12.3 Pola Offline-First dan Sinkronisasi

```text
Input pengguna
     |
     v
Provider Flutter
     |
     +--> Database lokal SQLCipher --> Antrian pending
     |                                  |
     |                                  v
     |                         SyncService + retry/backoff
     |                                  |
     +-------------------------------> API SIEDA Web
                                        |
                                        v
                                  Database SIEDA
```

Aplikasi mobile menyimpan data pending pada tabel lokal seperti `pending_penduduk`, `pending_keluarga`, `pending_catatan`, `pending_dasawisma`, `pending_anggota_keluarga`, dan `pending_kegiatan`. Sinkronisasi memakai dependency ordering: penduduk/keluarga dikirim lebih dahulu, lalu data yang merujuk NIK atau nomor KK. Item yang gagal karena jaringan memakai backoff; error validasi permanen ditandai agar tidak terus dicoba.

### 12.4 Kontrak API Mobile

- Base path: `/api/v1`.
- Login: `POST /auth/login`.
- Header protected: `Authorization: Bearer <sanctum-token>`.
- Respons standar: `success`, `message`, `data`, dan `meta` untuk pagination.
- Rate limit login: 5 request/menit per IP.
- Rate limit endpoint protected: 120 request/menit per token.
- Rate limit API publik: 60 request/menit per IP.
- Error validasi: HTTP 422; unauthorized: 401; forbidden: 403; rate limit: 429.

### 12.5 Batas Data dan Kepemilikan

- Database utama SIEDA Web menyimpan data operasional SIEDA.
- Database `pkk` pada SIEDA Web dibaca untuk memverifikasi akun Admin Panel PKK; SIEDA Web tidak menulis ke database tersebut.
- Database lokal SIEDA Mobile adalah cache dan antrian offline, bukan sumber kebenaran akhir.
- SIEDA Web menjadi sumber kebenaran setelah data mobile berhasil disinkronkan.

## 13. Strategi Pengujian

- `tests/Feature/Admin` — konten landing, manajemen akun, izin/otorisasi,
  pengaturan situs, sinkronisasi SIEDA (termasuk kasus secret salah/kosong).
- `tests/Feature/Auth` — SSO round-trip (terbit → verifikasi → replay ditolak,
  kedaluwarsa, rate limit), onboarding & OTP email.
- `tests/Feature/Sidongan` — otorisasi role SIDONGAN, alur surat/laporan.
- Konvensi: setiap middleware/permission baru wajib punya test negatif
  (akses tanpa izin harus ditolak).
