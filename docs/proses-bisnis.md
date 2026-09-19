# Proses Bisnis — Sistem Informasi PKK Kabupaten Toba

> Dokumen ini menjelaskan proses bisnis (bukan detail teknis) dari aplikasi.
> Ringkasan teknis/arsitektur ada di [`arsitektur.md`](arsitektur.md); diagram
> alur tiap proses ada di [`diagram.md`](diagram.md).

## 1. Gambaran Umum

Satu codebase Laravel melayani **dua aplikasi utama** yang diakses publik lewat domain berbeda, ditambah **integrasi dengan aplikasi eksternal SIEDA**:

| Aplikasi | Domain | Pengguna | Fungsi |
|---|---|---|---|
| **Landing Page + Admin Panel** | `tp-pkk.tobakab.go.id` | Publik + Pengurus PKK | Website informasi PKK & panel kelola konten/akun |
| **SIDONGAN** | `sidongan.tobakab.go.id` | Pengurus PKK (role khusus) | Manajemen surat masuk, disposisi, laporan kegiatan, arsip |
| **SIEDA** (eksternal) | `services.sieda.base_url` | Tim web & data desa | Aplikasi data desa terpisah; terintegrasi SSO & sinkronisasi data |

## 2. Aktor

| Aktor | Deskripsi | Akses |
|---|---|---|
| **Pengunjung (publik)** | Masyarakat umum | Landing page, berita, unduh SK/template, API publik |
| **Super Admin** | Administrator sistem | Semua modul Admin Panel + manajemen data SIEDA (hapus permanen) |
| **Administrator** | Admin konten | Modul sesuai izin (permission) yang diberikan |
| **Sekretaris PKK** | Role SIDONGAN | Membuat agenda surat, disposisi surat |
| **Ketua PKK** | Role SIDONGAN | Verifikasi laporan kegiatan, arsip surat |
| **Pengurus I–IV / Staf Ahli I–II / Bendahara** | Role SIDONGAN | Menerima disposisi, membuat laporan kegiatan |
| **Sistem SIEDA** | Aplikasi server-ke-server | Sinkronisasi avatar, SSO dua arah |

## 3. Daftar Proses Bisnis

1. [PB-01 Autentikasi & SSO](#pb-01)
2. [PB-02 Onboarding & Verifikasi Email Pribadi](#pb-02)
3. [PB-03 Lupa Password Terpadu](#pb-03)
4. [PB-04 Manajemen Akun Pengguna (Admin)](#pb-04)
5. [PB-05 Kelola Konten Landing Page (Admin)](#pb-05)
6. [PB-06 Pencatatan Surat Masuk & Agenda (SIDONGAN)](#pb-06)
7. [PB-07 Disposisi Surat (SIDONGAN)](#pb-07)
8. [PB-08 Laporan Kegiatan (SIDONGAN)](#pb-08)
9. [PB-09 Verifikasi Laporan oleh Ketua (SIDONGAN)](#pb-09)
10. [PB-10 Arsip Surat (SIDONGAN)](#pb-10)
11. [PB-11 Notifikasi & Pemeliharaan Otomatis](#pb-11)
12. [PB-12 Sinkronisasi dengan SIEDA](#pb-12)

---

<a id="pb-01"></a>
## PB-01 — Autentikasi & SSO

### 3.1 Login Normal
1. Pengguna membuka halaman login Admin Panel atau halaman login SIDONGAN (dua halaman berbeda, satu tabel `users`).
2. Sistem memvalidasi kredensial; setelah login, sistem memeriksa kelengkapan profil (nomor HP & email pribadi):
   - Belum lengkap → diarahkan ke **Onboarding** (PB-02), kecuali pengguna pernah memilih "lewati".
   - Lengkap → masuk dashboard masing-masing aplikasi.
3. Pengguna hanya bisa masuk ke SIDONGAN bila akun memiliki **role SIDONGAN** (ketua, sekretaris, bendahara, staf ahli, pengurus, atau super admin).

### 3.2 SSO dari SIEDA ke Admin Panel
1. Pengguna menekan tombol di SIEDA → SIEDA mengirim pengguna ke `/sso/login?token=...`.
2. Token berisi email + waktu kedaluwarsa, ditandatangani HMAC dengan shared secret `SIEDA_SYNC_SECRET`.
3. Admin Panel memverifikasi: signature, masa berlaku (5 menit), token belum pernah dipakai (sekali pakai), akun terdaftar.
4. Berhasil → sesi dibuat, pengguna diteruskan ke halaman tujuan (whitelist terbatas).
5. Gagal → dikembalikan ke halaman login dengan pesan; percobaan gagal dibatasi (per-IP dan global) untuk mencegah brute-force.

### 3.3 Kembali ke SIEDA
1. Pengguna di Admin Panel menekan "Kembali ke SIEDA" (tombol hanya muncul bila sesi berasal dari SSO SIEDA).
2. Admin Panel menerbitkan token baru dan mengarahkan ke callback SIEDA; sesi SIEDA dipulihkan tanpa login ulang.

<a id="pb-02"></a>
## PB-02 — Onboarding & Verifikasi Email Pribadi

**Tujuan:** setiap akun memiliki nomor HP dan email pribadi yang terverifikasi, sebagai sarana reset password.

1. Setelah login pertama kali (atau saat data pemblokir kosong), sistem menampilkan halaman Onboarding.
2. Pengguna mengisi **nomor HP** dan **email pribadi**, lalu sistem mengirim **kode OTP** ke email tersebut.
3. Pengguna memasukkan OTP (ada batas percobaan dan cooldown kirim ulang):
   - Benar → email tersimpan dan ditandai **terverifikasi**, lanjut ke dashboard.
   - Salah/berhenti → pengguna dapat memilih **"Lewati"**; status "terlewat" tersimpan sehingga tidak dikejar ulang setiap login, namun fitur yang butuh email pribadi tetap terbatas.
4. Verifikasi ulang bisa dilakukan kapan pun dari halaman profil (Admin Panel maupun SIDONGAN) melalui **link bertanda tangan (signed URL)** yang dikirim ke email — link berlaku terbatas dan tidak memerlukan login.
5. Bila email pribadi diganti, status verifikasi otomatis **direset** dan verifikasi baru dikirim ke alamat baru.

**Aturan penting:** semua email notifikasi (reset password, OTP, verifikasi) dikirim ke **email pribadi yang sudah terverifikasi**; jika belum ada, fallback ke email login.

<a id="pb-03"></a>
## PB-03 — Lupa Password Terpadu

Satu alur untuk Admin Panel dan SIDONGAN (endpoint terpisah, logika sama):

1. Pengguna memasukkan email di halaman lupa password.
2. Sistem mengevaluasi kondisi akun **tanpa membocorkan keberadaan akun**:
   - Akun tidak ditemukan → pesan generik.
   - Akun ada + email pribadi terverifikasi → kirim link reset ke email pribadi.
   - Akun ada + email pribadi belum terverifikasi → sarankan menghubungi admin / verifikasi email dulu.
   - Akun ada tanpa akses sistem → pesan generik.
3. Pengguna membuka link reset, mengatur password baru, lalu login normal.

<a id="pb-04"></a>
## PB-04 — Manajemen Akun Pengguna (Admin Panel)

**Aktor:** pengguna dengan izin `manage-users` (menu "Manajemen Akun").

1. **Membuat akun** — admin mengisi data diri, email login, password awal, dan (opsional) role Admin Panel + role SIDONGAN + izin tambahan per akun.
2. **Mengelola akun** — daftar akun mendukung pencarian, filter, ekspor, dan aksi massal. Per akun tersedia:
   - Aktifkan/Nonaktifkan — akun nonaktif tidak bisa login.
   - Salin kredensial — untuk diserahkan ke pengguna baru.
   - Reset password — sistem membuat password baru dan mengirimkannya ke email pengguna.
   - Kirim ulang verifikasi email pribadi.
   - Edit / Lihat detail / Hapus.
3. **Konsistensi lintas aplikasi** — saat role SIEDA dicabut dari akun, aksesnya di SIEDA juga dicabut; perubahan penting tercatat di log aktivitas admin.
4. **Perlindungan akun** — hanya Super Admin boleh mengubah akun Super Admin lain.

<a id="pb-05"></a>
## PB-05 — Kelola Konten Landing Page (Admin Panel)

Setiap modul dijaga izin tersendiri (`manage-*`). Konten yang dikelola admin langsung tampil di landing page publik:

| Modul | Kelola | Tampil di |
|---|---|---|
| Hero Slider | Gambar slide + pengaturan autoplay/durasi | Beranda |
| Struktur | Pengurus inti & Pokja I–IV (nama, jabatan, foto) | Halaman Struktur |
| Berita | Artikel + kategori + gambar (status draf/terbit) | Halaman Berita |
| Aplikasi & Layanan | Kartu aplikasi (status aktif/maintenance/development) | Halaman Aplikasi |
| Desa | Data desa per kecamatan (foto, penduduk, KK diambil dari database SIEDA) | Halaman Desa |
| SK & Template | Unggah dokumen SK dan template surat untuk diunduh publik | Halaman SK / Template |
| Tentang Kami | Profil & kontak organisasi | Halaman Tentang |
| Pengaturan Situs | Judul footer, alamat, Instagram, logo, hak cipta | Footer seluruh halaman |

Alur umum: admin membuat/mengubah konten → konten berstatus terbit → landing page membacanya lewat API publik (rate-limited) → pengunjung melihat konten terbaru tanpa perlu deploy ulang.

<a id="pb-06"></a>
## PB-06 — Pencatatan Surat Masuk & Agenda (SIDONGAN)

**Aktor:** Sekretaris (dan Super Admin).

1. Surat masuk dicatat ke sistem: nomor agenda, asal surat, perihal, tanggal terima, dan **file lampiran**.
2. Status awal surat: `menunggu_disposisi`.
3. Kategori dan tag dokumen dikelola terpisah untuk pengelompokan arsip.
4. Surat bersifat privat internal; hanya surat berstatus terbit/published tertentu yang diekspos lewat API publik SIDONGAN.

**Status siklus surat:**

```
menunggu_disposisi → berjalan → menunggu_verifikasi → selesai → diarsipkan
                           (revisi dari verifikasi kembali ke berjalan)
```

<a id="pb-07"></a>
## PB-07 — Disposisi Surat (SIDONGAN)

**Aktor:** Sekretaris / Ketua (dan Super Admin).

1. Sekretaris membuka detail surat berstatus `menunggu_disposisi` dan membuat **lembar disposisi**: memilih penerima (satu atau lebih role: Ketua Pokja, Pengurus, Staf Ahli, Bendahara), instruksi, dan tenggat.
2. Lembar disposisi dapat **dicetak/diunduh** dalam format siap cetak untuk ditempel pada surat fisik.
3. Status surat berubah menjadi `berjalan`; semua penerima disposisi mendapat **notifikasi**.
4. Selama surat berjalan, penerima dapat melihat surat pada daftar "Lapor Kegiatan" miliknya (PB-08).

<a id="pb-08"></a>
## PB-08 — Laporan Kegiatan (SIDONGAN)

**Aktor:** penerima disposisi (Pengurus I–IV, Staf Ahli, Bendahara, Pokja).

1. Halaman "Lapor Kegiatan" menampilkan daftar surat yang didisposisi ke role pengguna, dikelompokkan otomatis: **Perlu Dilaporkan → Perlu Revisi → Menunggu Verifikasi → Disetujui**.
2. Pengguna membuat **satu laporan per surat**: isi kegiatan, tanggal, dan lampiran pendukung.
3. Laporan dikirim dengan status `menunggu_verifikasi` → pemberi disposisi/Ketua mendapat notifikasi.
4. Status surat ikut berubah menjadi `menunggu_verifikasi`.
5. Jika laporan **ditolak** (PB-09), laporan dapat **direvisi** dan dikirim ulang; riwayat laporan tetap tersimpan.

<a id="pb-09"></a>
## PB-09 — Verifikasi Laporan oleh Ketua (SIDONGAN)

**Aktor:** Ketua (dan Super Admin).

1. Halaman "Verifikasi" menampilkan laporan yang menunggu verifikasi.
2. Ketua meninjau isi laporan + lampiran, lalu memutuskan:
   - **Setujui** → laporan `disetujui`, surat menjadi `selesai`, pelapor mendapat notifikasi.
   - **Tolak** + catatan → laporan `ditolak`, surat kembali `berjalan`, pelapor diminta revisi.
3. Status surat diperbarui otomatis oleh sistem berdasarkan laporan terakhirnya (tidak ada status basi).

<a id="pb-10"></a>
## PB-10 — Arsip Surat (SIDONGAN)

**Aktor:** Ketua (dan Super Admin).

1. Surat berstatus `selesai` diarsipkan (satuan atau massal) → status `diarsipkan`.
2. Surat terarsip tidak dapat dilaporkan/disposisi ulang, tetapi tetap dapat dicari dan diunduh.
3. Halaman "Arsip" menyediakan filter kategori, tag, dan pencarian sebagai basis arsip digital organisasi.

<a id="pb-11"></a>
## PB-11 — Notifikasi & Pemeliharaan Otomatis

1. Setiap peristiwa penting SIDONGAN (disposisi baru, laporan baru, hasil verifikasi) membuat **notifikasi** bagi pihak terkait.
2. Notifikasi dapat ditandai dibaca (satu per satu atau semua).
3. **Jadwal otomatis (harian 00.05):** notifikasi yang sudah dibaca dari hari sebelumnya dibersihkan agar tabel tetap ramping (log: `storage/logs/notification-cleanup.log`).

<a id="pb-12"></a>
## PB-12 — Sinkronisasi dengan SIEDA

Integrasi dua arah dengan aplikasi SIEDA (database terpisah), berbasis shared secret HMAC:

| Arah | Proses |
|---|---|
| Admin Panel → SIEDA | Sinkronisasi akun pengguna & perubahan role; pengambilan data kecamatan/desa (nama, jumlah penduduk, KK) untuk modul Desa |
| SIEDA → Admin Panel | `POST /api/sieda/sync-avatar` — foto profil yang diunggah di SIEDA disalin ke akun Admin Panel agar konsisten di kedua aplikasi |
| SSO dua arah | PB-01 (login tanpa password antar aplikasi) |

Prinsip: Admin Panel **tidak pernah menulis data milik SIEDA** kecuali lewat endpoint yang disepakati; penghapusan data SIEDA dari Admin Panel hanya untuk Super Admin dan bersifat eksplisit.

## PB-13 — Pendataan Warga melalui SIEDA Mobile

**Aktor:** petugas/kader lapangan yang memiliki akun SIEDA.

1. Petugas login melalui aplikasi SIEDA Mobile menggunakan email dan password.
2. API SIEDA memeriksa akun lokal SIEDA atau akun yang dikelola Admin Panel PKK, lalu menerbitkan token Sanctum.
3. Petugas memilih tahun kerja dan wilayah tugas yang melekat pada akun.
4. Petugas mengelola data penduduk, keluarga, anggota keluarga, kelompok Dasawisma, data kesehatan, kegiatan warga, serta catatan kelahiran dan kematian.
5. Saat perangkat tidak memiliki koneksi, data disimpan pada database lokal terenkripsi dan dimasukkan ke antrian sinkronisasi.
6. Saat koneksi kembali tersedia, aplikasi mengirim data pending secara otomatis. Data yang gagal dikirim diberi retry/backoff; data yang bergantung pada data lain ditunda sampai data induknya berhasil dikirim.
7. Dashboard dan rekapitulasi menampilkan data terbaru dari server setelah sinkronisasi berhasil.

### Modul SIEDA Mobile

| Modul | Fungsi |
|---|---|
| Dashboard | Statistik penduduk, keluarga, wilayah, tahun kerja, dan detail dusun |
| Penduduk | Tambah, lihat, ubah, dan nonaktifkan data warga |
| Keluarga | Kelola keluarga dan anggota keluarga |
| Dasawisma | Kelompok Dasawisma, kesehatan, sanitasi, dan rekap per dusun |
| Kegiatan Warga | Pencatatan kegiatan yang diikuti warga |
| Catatan | Catatan kelahiran dan kematian |
| Rekapitulasi | Data umum, Pokja I-IV, kesehatan, dan kelahiran/kematian |
| Arsip Surat | Unggah, lihat, unduh, dan hapus arsip surat |

## PB-14 — Data SIEDA Web dan API

SIEDA Web berfungsi sebagai backend/API operasional untuk aplikasi mobile dan sebagai panel administrasi data. Endpoint publik menyediakan homepage, berita, slider, profil desa, potensi, dan referensi. Endpoint terlindungi menggunakan token Sanctum dan mencakup dashboard, penduduk, keluarga, Dasawisma, rekapitulasi, catatan, kegiatan warga, arsip surat, dan referensi.

### Sinkronisasi lintas sistem

- **Admin Panel PKK → SIEDA Web:** sinkronisasi akun, perubahan role, pencabutan akses, dan assignment wilayah.
- **SIEDA Mobile → SIEDA Web:** pengiriman data penduduk, keluarga, Dasawisma, catatan, kegiatan, dan arsip melalui API.
- **SIEDA Web → SIEDA Mobile:** data dashboard, referensi, profil, rekap, dan hasil perubahan.
- **Admin Panel PKK ↔ SIEDA Web:** autentikasi akun terkelola dan sinkronisasi avatar melalui shared secret.

## 4. Matriks Role × Proses (SIDONGAN)

| Proses | Sekretaris | Ketua | Bendahara | Pengurus I–IV | Staf Ahli | Super Admin |
|---|:-:|:-:|:-:|:-:|:-:|:-:|
| PB-06 Catat surat | Ya | — | — | — | — | Ya |
| PB-07 Disposisi | Ya | Ya | — | — | — | Ya |
| PB-08 Lapor kegiatan | — | — | Ya | Ya | Ya | Ya |
| PB-09 Verifikasi | — | Ya | — | — | — | Ya |
| PB-10 Arsip | — | Ya | — | — | — | Ya |
| Profil & email pribadi | Ya | Ya | Ya | Ya | Ya | Ya |

> Di Admin Panel, hak akses modul ditentukan **role + permission** (`manage-berita`, `manage-users`, dst.); Super Admin/Administrator selalu memiliki akses penuh.
