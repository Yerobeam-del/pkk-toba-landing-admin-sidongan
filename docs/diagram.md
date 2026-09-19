# Diagram — Sistem Informasi PKK Kabupaten Toba

> Semua diagram di bawah memakai **Mermaid** dan bisa dirender langsung di
> GitHub/GitLab dan VS Code (extension Markdown Preview Mermaid Support).
> Konteks lengkap: [`arsitektur.md`](arsitektur.md) dan [`proses-bisnis.md`](proses-bisnis.md).

---

## 1. Diagram Konteks Sistem

Satu codebase Laravel melayani Landing Page + Admin Panel dan SIDONGAN,
terhubung ke dua database dan aplikasi SIEDA.

```mermaid
flowchart LR
    subgraph PENGGUNA["Pengguna"]
        PUB["Pengunjung publik"]
        ADM["Admin / Super Admin"]
        SID["Pengurus PKK"]
    end

    subgraph APP["Satu Aplikasi Laravel"]
        LAND["Landing Page<br/>tp-pkk.tobakab.go.id"]
        ADMIN["Admin Panel<br/>tp-pkk.tobakab.go.id/admin"]
        SIDO["SIDONGAN<br/>sidongan.tobakab.go.id"]
        ONB["Onboarding & Lupa Password<br/>(tersedia di semua domain)"]
    end

    subgraph DATA["Penyimpanan"]
        DB1[("MySQL utama")]
        DB2[("MySQL SIEDA<br/>read / maintenance")]
    end

    SIEDA["Aplikasi SIEDA<br/>(host terpisah)"]

    PUB --> LAND
    ADM --> ADMIN
    ADM --> ONB
    SID --> SIDO
    SID --> ONB

    LAND --> DB1
    ADMIN --> DB1
    SIDO --> DB1
    ADMIN -.->|"baca data wilayah/desa"| DB2
    SIEDA -.->|"SSO login + sync avatar"| ADMIN
    ADMIN -.->|"SSO back + sinkron user"| SIEDA
    SIEDA --> DB2
```

---

## 2. Arsitektur Internal (Lapisan)

```mermaid
flowchart TB
    subgraph HTTP["Lapisan HTTP"]
        R["routes/web.php + routes/auth.php<br/>Routing per-domain (Route::domain)"]
    end

    subgraph MW["Middleware"]
        M1["auth + permission:manage-*"]
        M2["sidongan.auth / sidongan.guest / sidongan.profile"]
        M3["sieda.sync (HMAC header)"]
        M4["throttle / signed / CSRF"]
    end

    subgraph CTRL["Controllers"]
        C1["Admin: Berita, Desa, Struktur,<br/>Aplikasi, SK, UserManagement, ..."]
        C2["Sidongan: AdminDocument, Verification,<br/>ActivityReport, Profile, Auth"]
        C3["Auth: Login, SSO, PersonalEmail,<br/>Onboarding, ForgotPassword"]
        C4["Closure API publik: wilayah,<br/>berita, struktur, aplikasi, desa"]
    end

    subgraph SVC["Services"]
        S1["SsoTokenService"]
        S2["SiedaSyncService"]
        S3["WilayahIndonesiaService"]
    end

    subgraph MODEL["Models (Eloquent)"]
        MO1["User, Role, Permission"]
        MO2["Document, ActivityReport, Notification"]
        MO3["News, Desa, Application, SiteSetting, ..."]
    end

    DB1[("MySQL utama")]
    DB2[("MySQL SIEDA")]

    R --> MW
    MW --> CTRL
    C1 --> MO1
    C1 --> MO3
    C2 --> MO2
    C3 --> SVC
    C4 --> MO3
    S1 --> MO1
    S2 --> MO1
    MO1 --> DB1
    MO2 --> DB1
    MO3 --> DB1
    S2 -.->|"koneksi sieda"| DB2
```

---

## 3. Diagram Penyebaran (Deployment)

```mermaid
flowchart LR
    U["Browser pengguna"] --> NG["Web server<br/>Nginx + TLS"]
    NG --> PHP["PHP-FPM<br/>Laravel (satu codebase)"]
    PHP --> DB1[("MySQL utama")]
    PHP -.->|"koneksi sieda"| DB2[("MySQL SIEDA")]
    PHP -.->|"SMTP: OTP, verifikasi,<br/>reset password"| MAIL["Mail server"]
    PHP -.->|"sinkronisasi + SSO"| SIEDA["Aplikasi SIEDA"]
    PHP -.->|"seed/fallback kecamatan"| API["API wilayah.id"]
    PHP -.->|"scheduler 00:05"| CRON["Cron: notifications:cleanup"]
```

---

## 4. ERD — Database Utama (subset penting)

```mermaid
erDiagram
    USERS ||--o| ROLES : "role_id"
    ROLES ||--o{ ROLE_PERMISSION : "role_id"
    PERMISSIONS ||--o{ ROLE_PERMISSION : "permission_id"
    USERS ||--o{ PERMISSION_USER : "izin pribadi"
    PERMISSIONS ||--o{ PERMISSION_USER : "permission_id"

    USERS ||--o{ SIDONGAN_DOCUMENTS : "created_by"
    SIDONGAN_CATEGORIES ||--o{ SIDONGAN_DOCUMENTS : "category_id"
    SIDONGAN_DOCUMENTS ||--o{ SIDONGAN_DOCUMENT_TAG : "document_id"
    SIDONGAN_TAGS ||--o{ SIDONGAN_DOCUMENT_TAG : "tag_id"
    SIDONGAN_DOCUMENTS ||--o{ ACTIVITY_REPORTS : "document_id"
    USERS ||--o{ ACTIVITY_REPORTS : "created_by"
    USERS ||--o{ NOTIFICATIONS : "user_id"

    CATEGORIES ||--o{ NEWS : "category_id"
    NEWS ||--o{ NEWS_IMAGES : "galeri berita"
    POKJA ||--o{ STRUKTUR_MEMBERS : "pokja_id"
    KECAMATANS ||--o{ DESAS : "kecamatan_id"

    USERS {
        bigint id PK
        string email
        string personal_email
        datetime personal_email_verified_at
        string sidongan_role
        string sieda_role
        bigint role_id FK
        datetime onboarding_skipped_at
    }
    SIDONGAN_DOCUMENTS {
        bigint id PK
        string title
        string agenda_number
        string status
        json disposisi_data
        bigint category_id FK
        bigint created_by FK
    }
    ACTIVITY_REPORTS {
        bigint id PK
        bigint document_id FK
        bigint created_by FK
        string status
        text content
    }
```

> Tabel `sidongan_documents`, `sidongan_categories`, `sidongan_tags`,
> `sidongan_document_tag`, dan `activity_reports` menjadi inti aplikasi
> SIDONGAN; sisanya melayani Admin Panel dan landing page.

---

## 5. ERD — Database SIEDA (ringkas, koneksi `sieda`)

```mermaid
erDiagram
    WARGA ||--o| KELUARGA : "kepala keluarga"
    WARGA ||--o{ ANGGOTA_KELUARGA : "nik"
    KELUARGA ||--o{ ANGGOTA_KELUARGA : "no_kk"
    KELOMPOK_DASAWISMA ||--o{ KELUARGA : "id_kelompok_dasawisma"
    REF_AGAMA ||--o{ WARGA : "id_agama"
    REF_DUSUN ||--o{ KELOMPOK_DASAWISMA : "id_dusun"
```

> Database ini diakses terbatas (data desa untuk modul Desa, pemeliharaan
> oleh Super Admin). Admin Panel tidak pernah menulis data milik SIEDA
> kecuali lewat endpoint yang disepakati.

---

## 6. Alur Autentikasi & SSO (PB-01)

```mermaid
sequenceDiagram
    autonumber
    actor U as Pengguna
    participant SI as Aplikasi SIEDA
    participant AP as Admin Panel
    participant DB as MySQL

    Note over U,DB: Alur A - Login normal
    U->>AP: POST /login (email, password)
    AP->>DB: Verifikasi kredensial
    alt Profil belum lengkap (HP / email pribadi kosong)
        AP-->>U: Redirect ke /onboarding
    else Profil lengkap atau onboarding pernah dilewati
        AP-->>U: Redirect ke dashboard
    end

    Note over U,DB: Alur B - SSO dari SIEDA (token HMAC)
    U->>SI: Klik masuk ke Admin Panel
    SI->>AP: GET /sso/login?token=HMAC
    AP->>AP: Verifikasi signature + masa berlaku 5 menit
    AP->>DB: Cek token belum dipakai + akun terdaftar
    alt Token sah
        AP->>DB: Buat sesi (guard web)
        AP-->>U: Redirect ke tujuan (whitelist)
    else Token tidak sah / dipakai ulang / akun tak ada
        AP-->>U: Redirect ke /login + pesan kesalahan
    end

    Note over U,DB: Alur C - Kembali ke SIEDA
    U->>AP: Klik "Kembali ke SIEDA"
    AP->>SI: Redirect ke callback dengan token baru
    SI-->>U: Sesi SIEDA dipulihkan
```

---

## 7. Alur Onboarding & Verifikasi Email Pribadi (PB-02)

```mermaid
flowchart TD
    A["Login berhasil"] --> B{"Profil lengkap?<br/>HP + email pribadi"}
    B -- "Ya" --> Z["Masuk dashboard"]
    B -- "Tidak" --> C{"Pernah memilih Lewati?"}
    C -- "Ya" --> Z
    C -- "Tidak" --> D["Halaman Onboarding"]
    D --> E["Isi nomor HP + email pribadi"]
    E --> F["Sistem mengirim OTP 6 digit"]
    F --> G{"OTP benar?"}
    G -- "Ya" --> H["Email tersimpan + ditandai terverifikasi"]
    H --> Z
    G -- "Tidak" --> I["Tampilkan kesalahan,<br/>tawarkan coba lagi / kirim ulang"]
    I --> F
    D --> K["Pilih Lewati"]
    K --> L["Tersimpan onboarding_skipped_at"]
    L --> Z
    H -.-> M["Verifikasi ulang kapan pun<br/>via link signed dari halaman profil"]
```

> Mengganti email pribadi akan **mereset** status verifikasi dan mengirim
> verifikasi baru ke alamat yang diubah.

---

## 8. Alur Lupa Password Terpadu (PB-03)

```mermaid
flowchart TD
    A["Buka /forgot-password"] --> B["Masukkan email"]
    B --> C{"Akun ditemukan?"}
    C -- "Tidak" --> G["Pesan generik<br/>(tidak membocorkan keberadaan akun)"]
    C -- "Ya" --> D{"Email pribadi terverifikasi?"}
    D -- "Ya" --> E["Kirim link reset ke email pribadi"]
    D -- "Tidak" --> F["Sarankan verifikasi email<br/>atau hubungi admin"]
    E --> H["Buka link, atur password baru"]
    H --> I["Login ulang"]
```

---

## 9. Alur Siklus Surat SIDONGAN (PB-06 s.d. PB-10)

```mermaid
flowchart TD
    subgraph SEK["Sekretaris"]
        A["Catat surat masuk:<br/>agenda, asal, perihal, lampiran"] --> B["Buat disposisi:<br/>penerima, instruksi, tenggat"]
    end

    subgraph SYS["Sistem"]
        C["Status: menunggu_disposisi"] --> D["Status: berjalan<br/>notifikasi ke penerima"]
        D --> P["Lembar disposisi siap cetak"]
    end

    subgraph PGN["Penerima: Pokja / Pengurus / Staf Ahli / Bendahara"]
        E["Daftar Lapor Kegiatan<br/>(urut prioritas status)"] --> F["Buat laporan + lampiran"]
        F --> G["Kirim: menunggu_verifikasi"]
        G --> H{"Ditolak?"}
        H -- "Ya, revisi" --> F
    end

    subgraph KT["Ketua"]
        I["Halaman Verifikasi"] --> J{"Keputusan"}
        J -- "Setujui" --> K["Laporan disetujui,<br/>surat: selesai"]
        J -- "Tolak + catatan" --> L["Laporan ditolak,<br/>surat: kembali berjalan"]
    end

    K --> M["Arsipkan (satuan / massal)"]
    M --> N["Status: diarsipkan"]

    B --> C
    D --> E
    G --> I
    L --> H
```

### Status surat (state machine)

```mermaid
stateDiagram-v2
    [*] --> menunggu_disposisi : surat dicatat sekretaris
    menunggu_disposisi --> berjalan : disposisi dikirim
    berjalan --> menunggu_verifikasi : laporan dikirim
    menunggu_verifikasi --> selesai : laporan disetujui
    menunggu_verifikasi --> berjalan : laporan ditolak, perlu revisi
    selesai --> diarsipkan : diarsipkan ketua
    diarsipkan --> [*]
```

### Status laporan kegiatan (state machine)

```mermaid
stateDiagram-v2
    [*] --> perlu_dilaporkan : surat didisposisi ke role
    perlu_dilaporkan --> menunggu_verifikasi : laporan dibuat
    menunggu_verifikasi --> disetujui : ketua menyetujui
    menunggu_verifikasi --> ditolak : ketua menolak + catatan
    ditolak --> menunggu_verifikasi : revisi + kirim ulang
    disetujui --> [*]
```

> Status surat **dihitung otomatis** dari laporan terakhirnya (tidak di-update
> manual), sehingga daftar, statistik, dan detail selalu konsisten.

---

## 10. Arsitektur Terpadu PKK Toba, SIEDA Web, dan SIEDA Mobile

```mermaid
flowchart LR
    U["Pengguna web"] --> PKK["PKK Toba<br/>Landing + Admin + SIDONGAN"]
    M["Petugas lapangan"] --> MOB["SIEDA Mobile<br/>Flutter"]
    PKK -->|"SSO + HMAC + sinkron akun/avatar"| WEB["SIEDA Web / API<br/>Laravel"]
    MOB -->|"HTTPS + Sanctum Bearer"| WEB
    MOB -->|"offline-first"| LOCAL[("SQLCipher<br/>database lokal")]
    WEB --> DB[("Database SIEDA")]
    PKK --> PKKDB[("Database PKK Toba")]
    WEB -.->|"baca verifikasi akun"| PKKDB
```

## 11. Alur SIEDA Mobile Offline-First

```mermaid
sequenceDiagram
    autonumber
    actor P as Petugas
    participant APP as SIEDA Mobile
    participant LOCAL as SQLCipher Lokal
    participant API as SIEDA Web API
    participant DB as Database SIEDA

    P->>APP: Isi atau ubah data warga
    APP->>LOCAL: Simpan cache + pending queue
    alt Online
        APP->>API: POST/PUT data dengan Bearer token
        API->>DB: Validasi dan simpan
        DB-->>API: Data tersimpan
        API-->>APP: Respons sukses
        APP->>LOCAL: Tandai synced
    else Offline
        APP-->>P: Data tersimpan lokal
        Note over APP,LOCAL: Menunggu koneksi tersedia
        APP->>API: Sinkron otomatis saat online
        API->>DB: Kirim sesuai dependency order
        API-->>APP: Sukses / gagal / ditunda
        APP->>LOCAL: Update status retry dan pending
    end
```

## 12. Alur Data Mobile dan Dependency Ordering

```mermaid
flowchart TD
    A["Penduduk"] --> B["Keluarga"]
    A --> C["Anggota Keluarga"]
    B --> C
    B --> D["Dasawisma Keluarga"]
    A --> E["Kegiatan Warga"]
    B --> F["Catatan Kelahiran/Kematian"]
    C --> G["Rekapitulasi"]
    D --> G
    E --> G
    F --> G
    H["Data gagal jaringan"] --> I["Retry dengan backoff"]
    I --> H
    J["Error validasi permanen"] --> K["Ditandai gagal permanen"]
```

## 13. Alur Sinkronisasi dengan SIEDA (PB-12)

```mermaid
sequenceDiagram
    autonumber
    participant AP as Admin Panel
    participant SI as Aplikasi SIEDA

    Note over AP,SI: Shared secret HMAC (SIEDA_SYNC_SECRET)
    AP->>SI: Sinkronisasi akun + perubahan role
    AP->>SI: Ambil data kecamatan/desa (penduduk, KK)
    SI->>AP: POST /api/sieda/sync-avatar (foto profil)
    AP->>AP: Verifikasi header X-Sieda-Key/Timestamp/Signature
    Note over AP: Foto disalin ke akun Admin Panel<br/>agar konsisten di kedua aplikasi
```
