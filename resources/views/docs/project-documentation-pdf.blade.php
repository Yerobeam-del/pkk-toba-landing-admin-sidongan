<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dokumentasi Sistem Informasi PKK Kabupaten Toba</title>
    <style>
        @page { margin: 18mm 16mm 17mm 16mm; }
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; color: #253247; font-size: 9.5pt; line-height: 1.48; margin: 0; }
        h1, h2, h3 { color: #123b69; margin: 0 0 9px; }
        h1 { font-size: 24pt; line-height: 1.2; }
        h2 { font-size: 16pt; border-bottom: 2px solid #2c7be5; padding-bottom: 5px; margin-top: 20px; }
        h3 { font-size: 11.5pt; margin-top: 14px; }
        p { margin: 0 0 7px; }
        ul, ol { margin: 4px 0 9px 19px; padding: 0; }
        li { margin: 2px 0; }
        code { font-family: DejaVu Sans Mono, monospace; font-size: 8.2pt; color: #174a7e; background: #eef4fb; padding: 1px 3px; }
        .cover { height: 235mm; position: relative; padding: 30mm 17mm 20mm; background: #f4f8fd; border: 1px solid #c9d9ec; }
        .cover-band { height: 9mm; background: #123b69; position: absolute; top: 0; left: 0; right: 0; }
        .cover-accent { width: 38mm; height: 4mm; background: #2c7be5; margin: 24mm 0 20mm; }
        .cover-subtitle { font-size: 13pt; color: #52657b; margin-top: 14px; }
        .cover-meta { position: absolute; bottom: 22mm; left: 17mm; right: 17mm; border-top: 1px solid #b9cce1; padding-top: 10px; color: #52657b; }
        .small { font-size: 8pt; color: #62758c; }
        .page-break { page-break-before: always; }
        .toc { border: 1px solid #c9d9ec; background: #f7faff; padding: 12px 15px; }
        .toc-row { padding: 4px 0; border-bottom: 1px dotted #b8c7d9; }
        .toc-row:last-child { border-bottom: 0; }
        .section-note { border-left: 4px solid #2c7be5; background: #eef5fd; padding: 8px 10px; margin: 8px 0 12px; }
        table { width: 100%; border-collapse: collapse; margin: 8px 0 12px; font-size: 8.3pt; }
        th { background: #123b69; color: #fff; font-weight: bold; text-align: left; }
        th, td { border: 1px solid #c6d2df; padding: 5px 6px; vertical-align: top; }
        tr:nth-child(even) td { background: #f6f9fc; }
        .card-grid { width: 100%; }
        .card { display: inline-block; width: 31.8%; min-height: 48px; vertical-align: top; border: 1px solid #cbd9e8; background: #f8fbff; padding: 8px; margin: 0 1.1% 8px 0; }
        .card:nth-child(3n) { margin-right: 0; }
        .card-title { font-weight: bold; color: #123b69; margin-bottom: 3px; }
        .diagram { border: 1px solid #cbd9e8; background: #fff; padding: 8px; margin: 8px 0 14px; text-align: center; page-break-inside: avoid; }
        .diagram svg { max-width: 100%; height: auto; }
        .caption { text-align: left; font-size: 8pt; color: #62758c; margin-top: 4px; }
        .status { display: inline-block; border: 1px solid #2c7be5; background: #eef5fd; color: #174a7e; padding: 5px 8px; margin: 3px; font-size: 8pt; }
        .footer-note { margin-top: 18px; padding-top: 7px; border-top: 1px solid #cbd9e8; color: #62758c; font-size: 8pt; }
    </style>
</head>
<body>
    <section class="cover">
        <div class="cover-band"></div>
        <div class="cover-accent"></div>
        <h1>Dokumentasi Sistem Informasi<br>PKK Kabupaten Toba</h1>
        <div class="cover-subtitle">Proses Bisnis, Arsitektur Sistem, dan Integrasi Mobile</div>
        <p style="margin-top:28px; max-width:125mm; color:#52657b;">Dokumen ini menjelaskan proses kerja, aktor, modul, arsitektur teknis, integrasi, keamanan, dan alur utama aplikasi Landing Page, Admin Panel, SIDONGAN, serta SIEDA.</p>
        <div class="cover-meta">
            <strong>Versi dokumen:</strong> 1.0<br>
            <strong>Dibuat pada:</strong> {{ $generatedAt }}<br>
            <span class="small">Dokumen teknis dan fungsional untuk referensi pengembangan, operasional, dan presentasi.</span>
        </div>
    </section>

    <section class="page-break">
        <h2>Daftar Isi</h2>
        <div class="toc">
            <div class="toc-row">1. Ringkasan Eksekutif</div>
            <div class="toc-row">2. Ruang Lingkup dan Aktor</div>
            <div class="toc-row">3. Proses Bisnis Utama</div>
            <div class="toc-row">3A. Rincian Proses PB-01 sampai PB-14</div>
            <div class="toc-row">4. Arsitektur Sistem</div>
            <div class="toc-row">5. Arsitektur Data</div>
            <div class="toc-row">6. Keamanan dan Integrasi</div>
            <div class="toc-row">7. Diagram Alur dan State</div>
            <div class="toc-row">8. SIEDA Web dan SIEDA Mobile</div>
            <div class="toc-row">9. Kesimpulan</div>
        </div>
        <div class="section-note" style="margin-top:20px;"><strong>Catatan pembacaan:</strong> Dokumen ini adalah versi PDF yang disusun untuk dibaca dan dicetak. Diagram dibuat sebagai SVG inline agar tetap tajam pada hasil cetak.</div>
    </section>

    <section class="page-break">
        <h2>1. Ringkasan Eksekutif</h2>
        <p>Sistem Informasi PKK Kabupaten Toba merupakan satu aplikasi Laravel yang melayani beberapa kebutuhan organisasi melalui pemisahan domain dan modul. Sistem menyediakan website publik, Admin Panel untuk pengelolaan konten dan akun, serta aplikasi SIDONGAN untuk pengelolaan surat dan pelaporan kegiatan.</p>
        <div class="card-grid">
            <div class="card"><div class="card-title">Landing Page</div>Website publik untuk berita, struktur, aplikasi, data desa, SK, template, dan informasi organisasi.</div>
            <div class="card"><div class="card-title">Admin Panel</div>Pengelolaan konten, akun pengguna, role, permission, slider, pengaturan situs, dan data operasional.</div>
            <div class="card"><div class="card-title">SIDONGAN</div>Pengelolaan surat masuk, disposisi, laporan kegiatan, verifikasi, notifikasi, dan arsip.</div>
            <div class="card"><div class="card-title">SIEDA Web</div>Backend Laravel dan REST API untuk data penduduk, keluarga, Dasawisma, rekapitulasi, dan integrasi akun.</div>
            <div class="card"><div class="card-title">SIEDA Mobile</div>Aplikasi Flutter offline-first dengan database SQLCipher, sinkronisasi otomatis, retry/backoff, dan notifikasi.</div>
            <div class="card"><div class="card-title">Keamanan</div>RBAC, permission, HMAC, signed URL, OTP, CSRF, rate limiting, dan validasi upload.</div>
            <div class="card"><div class="card-title">Teknologi</div>Laravel, PHP, MySQL, Blade, vanilla JavaScript, CSS modular, SMTP, scheduler, dan testing Feature.</div>
        </div>
        <h3>Tujuan Sistem</h3>
        <ul>
            <li>Menyediakan pusat informasi publik PKK Kabupaten Toba.</li>
            <li>Menyederhanakan pengelolaan akun, konten, dan data organisasi.</li>
            <li>Mendigitalisasi alur surat dari pencatatan sampai arsip.</li>
            <li>Menghubungkan Admin Panel dengan SIEDA tanpa duplikasi autentikasi.</li>
        </ul>
    </section>

    <section class="page-break">
        <h2>2. Ruang Lingkup dan Aktor</h2>
        <table>
            <tr><th>Aktor</th><th>Peran dan akses utama</th></tr>
            <tr><td>Pengunjung publik</td><td>Membaca informasi, berita, struktur, aplikasi, data desa, serta mengunduh SK dan template.</td></tr>
            <tr><td>Super Admin</td><td>Akses penuh Admin Panel, SIDONGAN, manajemen role/permission, dan fungsi pemeliharaan SIEDA.</td></tr>
            <tr><td>Administrator</td><td>Mengelola modul Admin Panel berdasarkan permission yang diberikan.</td></tr>
            <tr><td>Sekretaris PKK</td><td>Mencatat surat masuk, membuat agenda, dan membuat disposisi.</td></tr>
            <tr><td>Ketua PKK</td><td>Memverifikasi laporan, memberi keputusan, dan mengarsipkan surat.</td></tr>
            <tr><td>Pengurus, Staf Ahli, Bendahara</td><td>Menerima disposisi dan membuat laporan kegiatan.</td></tr>
            <tr><td>Sistem SIEDA</td><td>Berkomunikasi dengan Admin Panel melalui endpoint server-to-server dan SSO.</td></tr>
        </table>
        <h3>Domain Aplikasi</h3>
        <table>
            <tr><th>Domain / area</th><th>Fungsi</th></tr>
            <tr><td><code>tp-pkk.tobakab.go.id</code></td><td>Landing Page dan Admin Panel.</td></tr>
            <tr><td><code>sidongan.tobakab.go.id</code></td><td>Operasional surat dan laporan kegiatan SIDONGAN.</td></tr>
            <tr><td>Integrasi SIEDA</td><td>SSO, sinkronisasi pengguna/avatar, dan data wilayah/desa.</td></tr>
        </table>
    </section>

    <section class="page-break">
        <h2>3. Proses Bisnis Utama</h2>
        <h3>3.1 Autentikasi, Onboarding, dan SSO</h3>
        <ol>
            <li>Pengguna memasukkan kredensial pada Admin Panel atau SIDONGAN.</li>
            <li>Sistem memvalidasi akun dan memeriksa role serta kelengkapan profil.</li>
            <li>Pengguna yang belum memiliki nomor HP atau email pribadi diarahkan ke onboarding.</li>
            <li>OTP dikirim ke email pribadi; setelah benar, email ditandai terverifikasi.</li>
            <li>SSO dari SIEDA menggunakan token HMAC berumur lima menit dan hanya dapat dipakai satu kali.</li>
        </ol>
        <h3>3.2 Pengelolaan Konten Publik</h3>
        <p>Admin dengan permission yang sesuai membuat atau mengubah konten. Data yang berstatus aktif/terbit dibaca oleh Landing Page melalui API publik yang dilindungi rate limiting.</p>
        <table>
            <tr><th>Modul</th><th>Output publik</th></tr>
            <tr><td>Berita</td><td>Daftar dan detail berita beserta kategori dan gambar.</td></tr>
            <tr><td>Struktur</td><td>Pengurus inti, Pokja, anggota, jabatan, dan foto.</td></tr>
            <tr><td>Aplikasi dan layanan</td><td>Kartu aplikasi dengan status active, maintenance, atau development.</td></tr>
            <tr><td>Desa dan wilayah</td><td>Daftar kecamatan/desa beserta informasi demografis.</td></tr>
            <tr><td>SK dan template</td><td>Dokumen yang dapat dicari dan diunduh publik.</td></tr>
        </table>
        <h3>3.3 Siklus Surat SIDONGAN</h3>
        <p>Surat dicatat oleh Sekretaris, didisposisikan kepada penerima, dilaporkan oleh pelaksana, diverifikasi oleh Ketua, lalu diarsipkan setelah selesai.</p>
        <div style="text-align:center; margin:10px 0;">
            <span class="status">Menunggu Disposisi</span><span class="status">Berjalan</span><span class="status">Menunggu Verifikasi</span><span class="status">Selesai</span><span class="status">Diarsipkan</span>
        </div>
        <h3>3.4 Laporan Kegiatan</h3>
        <ol>
            <li>Penerima disposisi membuka daftar surat yang ditugaskan kepada role-nya.</li>
            <li>Penerima membuat satu laporan per surat beserta lampiran.</li>
            <li>Laporan dikirim untuk verifikasi.</li>
            <li>Ketua menyetujui atau menolak dengan catatan.</li>
            <li>Laporan yang ditolak dapat direvisi dan dikirim ulang.</li>
        </ol>
    </section>

    <section class="page-break">
        <h2>3A. Rincian Proses Bisnis PB-01 sampai PB-14</h2>
        <h3>PB-01. Autentikasi dan SSO</h3>
        <ol><li>Login normal memvalidasi email dan password pada guard aplikasi.</li><li>Login SSO dari SIEDA memakai token HMAC berisi email, tujuan, dan waktu kedaluwarsa.</li><li>Admin Panel memeriksa signature, TTL lima menit, akun, whitelist tujuan, dan replay token.</li><li>Token valid membuat sesi tanpa login ulang; token invalid diarahkan ke login.</li><li>Pengguna dapat kembali ke SIEDA dengan token callback baru.</li></ol>
        <h3>PB-02. Onboarding dan Email Pribadi</h3>
        <ol><li>Sistem memeriksa nomor HP dan email pribadi setelah login.</li><li>Data yang belum lengkap mengarahkan pengguna ke onboarding.</li><li>OTP enam digit dikirim ke email pribadi.</li><li>OTP benar menandai email terverifikasi; OTP salah mengikuti batas percobaan dan cooldown.</li><li>Pengguna boleh melewati onboarding dan dapat melakukan verifikasi ulang melalui signed URL.</li></ol>
        <h3>PB-03. Lupa Password</h3>
        <p>Sistem menggunakan respons generik agar keberadaan akun tidak terbuka. Jika email pribadi sudah terverifikasi, link reset dikirim ke email tersebut; jika belum, pengguna diarahkan untuk menghubungi admin atau melakukan verifikasi.</p>
        <h3>PB-04. Manajemen Pengguna</h3>
        <p>Admin dengan <code>manage-users</code> dapat membuat, melihat, mengubah, mengaktifkan, menonaktifkan, menghapus, mereset password, mengirim ulang verifikasi email, mengekspor, dan menjalankan aksi massal. Perubahan role SIEDA dan akses lintas aplikasi dikendalikan dari Admin Panel PKK.</p>
        <h3>PB-05. Pengelolaan Konten Publik</h3>
        <p>Admin mengelola slider, struktur, berita, aplikasi, desa, SK, template, halaman tentang, dan pengaturan situs. Data yang aktif/terbit disajikan melalui API publik dengan rate limit.</p>
        <h3>PB-06 sampai PB-10. Siklus Surat SIDONGAN</h3>
        <table><tr><th>Proses</th><th>Aktor</th><th>Hasil</th></tr><tr><td>PB-06 Pencatatan</td><td>Sekretaris</td><td>Surat masuk tercatat dengan agenda, perihal, asal, tanggal, dan lampiran.</td></tr><tr><td>PB-07 Disposisi</td><td>Sekretaris/Ketua</td><td>Penerima, instruksi, tenggat, notifikasi, dan lembar disposisi.</td></tr><tr><td>PB-08 Pelaporan</td><td>Pengurus/Staf Ahli/Bendahara</td><td>Satu laporan per surat dengan lampiran dan status menunggu verifikasi.</td></tr><tr><td>PB-09 Verifikasi</td><td>Ketua</td><td>Laporan disetujui atau ditolak dengan catatan revisi.</td></tr><tr><td>PB-10 Arsip</td><td>Ketua/Super Admin</td><td>Surat selesai dipindahkan ke arsip, satuan atau massal.</td></tr></table>
        <h3>PB-11. Notifikasi dan Pemeliharaan</h3>
        <p>Disposisi, laporan, dan hasil verifikasi membuat notifikasi. Notifikasi yang sudah dibaca dibersihkan oleh scheduler harian pada pukul 00.05.</p>
        <h3>PB-12. Integrasi Admin Panel dengan SIEDA</h3>
        <p>Admin Panel mengelola akun dan role, SIEDA memverifikasi akun sesuai kontrak, dan sinkronisasi avatar dilakukan melalui endpoint server-to-server dengan shared secret.</p>
        <h3>PB-13. Pendataan melalui SIEDA Mobile</h3>
        <ol><li>Petugas login dan memilih tahun kerja serta wilayah tugas.</li><li>Petugas mengelola penduduk, keluarga, Dasawisma, kesehatan, kegiatan, dan catatan.</li><li>Data disimpan lokal saat offline.</li><li>Data pending disinkronkan otomatis saat online.</li><li>Data yang bergantung pada NIK atau nomor KK ditunda sampai data induk berhasil terkirim.</li></ol>
        <h3>PB-14. API SIEDA Web</h3>
        <p>API menyediakan endpoint publik untuk homepage, berita, slider, profil, potensi, dan referensi; serta endpoint protected untuk auth, dashboard, penduduk, keluarga, Dasawisma, rekapitulasi, catatan, kegiatan, arsip surat, dan referensi.</p>
    </section>

    <section class="page-break">
        <h2>4. Arsitektur Sistem</h2>
        <p>Arsitektur menggunakan satu codebase Laravel dengan pemisahan aplikasi berbasis domain. Landing Page, Admin Panel, dan SIDONGAN berbagi model pengguna utama, tetapi memiliki routing, guard, middleware, dan modul yang berbeda.</p>
        <div class="diagram">
            <svg viewBox="0 0 760 350" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Arsitektur tingkat tinggi">
                <defs><marker id="arrow1" markerWidth="8" markerHeight="8" refX="7" refY="4" orient="auto"><path d="M0,0 L8,4 L0,8 z" fill="#2c7be5"/></marker></defs>
                <rect x="260" y="25" width="245" height="72" rx="8" fill="#123b69"/><text x="382" y="56" text-anchor="middle" fill="#fff" font-size="17" font-family="DejaVu Sans">Aplikasi Laravel</text><text x="382" y="78" text-anchor="middle" fill="#dcecff" font-size="11" font-family="DejaVu Sans">Routing berbasis domain</text>
                <rect x="35" y="142" width="190" height="72" rx="8" fill="#eef5fd" stroke="#2c7be5"/><text x="130" y="171" text-anchor="middle" fill="#123b69" font-size="13" font-family="DejaVu Sans">Landing + Admin</text><text x="130" y="191" text-anchor="middle" fill="#52657b" font-size="10" font-family="DejaVu Sans">tp-pkk.tobakab.go.id</text>
                <rect x="285" y="142" width="190" height="72" rx="8" fill="#eef5fd" stroke="#2c7be5"/><text x="380" y="171" text-anchor="middle" fill="#123b69" font-size="13" font-family="DejaVu Sans">SIDONGAN</text><text x="380" y="191" text-anchor="middle" fill="#52657b" font-size="10" font-family="DejaVu Sans">sidongan.tobakab.go.id</text>
                <rect x="535" y="142" width="190" height="72" rx="8" fill="#eef5fd" stroke="#2c7be5"/><text x="630" y="171" text-anchor="middle" fill="#123b69" font-size="13" font-family="DejaVu Sans">SIEDA</text><text x="630" y="191" text-anchor="middle" fill="#52657b" font-size="10" font-family="DejaVu Sans">Aplikasi eksternal</text>
                <rect x="150" y="270" width="205" height="55" rx="8" fill="#f7f9fb" stroke="#8fa5bd"/><text x="252" y="302" text-anchor="middle" fill="#253247" font-size="13" font-family="DejaVu Sans">MySQL Utama</text>
                <rect x="405" y="270" width="205" height="55" rx="8" fill="#f7f9fb" stroke="#8fa5bd"/><text x="507" y="302" text-anchor="middle" fill="#253247" font-size="13" font-family="DejaVu Sans">Database SIEDA</text>
                <path d="M320 97 L160 142" stroke="#2c7be5" stroke-width="2" marker-end="url(#arrow1)"/><path d="M382 97 L382 142" stroke="#2c7be5" stroke-width="2" marker-end="url(#arrow1)"/><path d="M445 97 L600 142" stroke="#2c7be5" stroke-width="2" marker-end="url(#arrow1)"/>
                <path d="M130 214 L220 270" stroke="#2c7be5" stroke-width="2" marker-end="url(#arrow1)"/><path d="M380 214 L280 270" stroke="#2c7be5" stroke-width="2" marker-end="url(#arrow1)"/><path d="M630 214 L540 270" stroke="#2c7be5" stroke-width="2" marker-end="url(#arrow1)"/>
            </svg>
            <div class="caption">Gambar 1. Satu codebase Laravel melayani dua area aplikasi dan berintegrasi dengan SIEDA.</div>
        </div>
        <h3>Lapisan Arsitektur</h3>
        <table>
            <tr><th>Lapisan</th><th>Komponen</th></tr>
            <tr><td>Routing</td><td><code>routes/web.php</code>, <code>routes/auth.php</code>, route domain, API publik.</td></tr>
            <tr><td>Middleware</td><td>Auth, role/permission, profil SIDONGAN, signed URL, CSRF, HMAC, dan throttle.</td></tr>
            <tr><td>Controller</td><td>Admin, Auth, SIDONGAN, Landing, API, Onboarding.</td></tr>
            <tr><td>Service</td><td>SSO token, sinkronisasi SIEDA, wilayah, sanitasi upload, dan preview dokumen.</td></tr>
            <tr><td>Model</td><td>Eloquent model untuk pengguna, konten, surat, laporan, notifikasi, dan data SIEDA.</td></tr>
            <tr><td>Storage</td><td>MySQL utama, koneksi SIEDA, filesystem, cache, mail server, dan scheduler.</td></tr>
        </table>
    </section>

    <section class="page-break">
        <h2>5. Arsitektur Data</h2>
        <h3>5.1 Data Pengguna dan Otorisasi</h3>
        <p>Pengguna disimpan pada tabel <code>users</code>. Role Admin Panel terhubung melalui <code>roles</code>, sedangkan permission terhubung melalui <code>role_permission</code> dan <code>permission_user</code>. Role SIDONGAN disimpan pada atribut role khusus di pengguna.</p>
        <h3>5.2 Data Surat</h3>
        <p>Data surat berada pada <code>sidongan_documents</code>. Kategori dan tag digunakan untuk klasifikasi. Setiap surat dapat memiliki banyak <code>activity_reports</code>. Status surat dihitung berdasarkan laporan terakhir sehingga daftar dan statistik tetap konsisten.</p>
        <h3>5.3 Data Konten</h3>
        <p>Konten Landing Page terdiri dari berita, gambar berita, struktur, Pokja, aplikasi, desa, kecamatan, dokumen SK, template, hero slider, pengaturan situs, dan informasi tentang organisasi.</p>
        <div class="diagram">
            <svg viewBox="0 0 760 230" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Relasi inti data surat">
                <defs><marker id="arrow2" markerWidth="8" markerHeight="8" refX="7" refY="4" orient="auto"><path d="M0,0 L8,4 L0,8 z" fill="#2c7be5"/></marker></defs>
                <rect x="25" y="75" width="145" height="70" rx="7" fill="#123b69"/><text x="97" y="104" text-anchor="middle" fill="#fff" font-size="13" font-family="DejaVu Sans">USERS</text><text x="97" y="125" text-anchor="middle" fill="#dcecff" font-size="10" font-family="DejaVu Sans">created_by</text>
                <rect x="225" y="75" width="175" height="70" rx="7" fill="#eef5fd" stroke="#2c7be5"/><text x="312" y="104" text-anchor="middle" fill="#123b69" font-size="13" font-family="DejaVu Sans">SIDONGAN_DOCUMENTS</text><text x="312" y="125" text-anchor="middle" fill="#52657b" font-size="10" font-family="DejaVu Sans">status, disposisi, lampiran</text>
                <rect x="455" y="25" width="150" height="60" rx="7" fill="#f7f9fb" stroke="#8fa5bd"/><text x="530" y="52" text-anchor="middle" fill="#253247" font-size="12" font-family="DejaVu Sans">CATEGORIES</text><text x="530" y="69" text-anchor="middle" fill="#62758c" font-size="9" font-family="DejaVu Sans">category_id</text>
                <rect x="455" y="105" width="150" height="60" rx="7" fill="#f7f9fb" stroke="#8fa5bd"/><text x="530" y="132" text-anchor="middle" fill="#253247" font-size="12" font-family="DejaVu Sans">ACTIVITY_REPORTS</text><text x="530" y="149" text-anchor="middle" fill="#62758c" font-size="9" font-family="DejaVu Sans">document_id</text>
                <rect x="650" y="105" width="90" height="60" rx="7" fill="#f7f9fb" stroke="#8fa5bd"/><text x="695" y="132" text-anchor="middle" fill="#253247" font-size="11" font-family="DejaVu Sans">NOTIF</text><text x="695" y="149" text-anchor="middle" fill="#62758c" font-size="9" font-family="DejaVu Sans">user_id</text>
                <path d="M170 110 L225 110" stroke="#2c7be5" stroke-width="2" marker-end="url(#arrow2)"/><path d="M400 95 L455 65" stroke="#2c7be5" stroke-width="2" marker-end="url(#arrow2)"/><path d="M400 125 L455 135" stroke="#2c7be5" stroke-width="2" marker-end="url(#arrow2)"/><path d="M605 135 L650 135" stroke="#2c7be5" stroke-width="2" marker-end="url(#arrow2)"/>
            </svg>
            <div class="caption">Gambar 2. Relasi inti modul SIDONGAN.</div>
        </div>
    </section>

    <section class="page-break">
        <h2>6. Keamanan dan Integrasi</h2>
        <table>
            <tr><th>Aspek</th><th>Implementasi</th></tr>
            <tr><td>Otorisasi</td><td>Role dan permission pada route serta validasi ulang di controller untuk fungsi sensitif.</td></tr>
            <tr><td>SSO</td><td>Token HMAC-SHA256 dengan shared secret, TTL lima menit, sekali pakai, whitelist tujuan.</td></tr>
            <tr><td>Email pribadi</td><td>OTP onboarding dan signed URL untuk verifikasi ulang.</td></tr>
            <tr><td>Rate limiting</td><td>Login, OTP, resend, API publik, dan SSO dibatasi berdasarkan kebutuhan.</td></tr>
            <tr><td>CSRF</td><td>Aktif untuk request browser; endpoint server-to-server SIEDA dikecualikan secara eksplisit.</td></tr>
            <tr><td>Upload</td><td>Validasi MIME dan sanitasi nama/path; file SVG tidak diizinkan pada alur upload gambar.</td></tr>
            <tr><td>Anti enumeration</td><td>Alur lupa password menggunakan pesan generik agar keberadaan akun tidak terbuka.</td></tr>
        </table>
        <h3>Integrasi SIEDA</h3>
        <ol>
            <li>Admin Panel dan SIEDA menggunakan shared secret yang sama untuk autentikasi server-to-server.</li>
            <li>SSO memungkinkan perpindahan aplikasi tanpa mengetik ulang kredensial.</li>
            <li>Sinkronisasi avatar menjaga konsistensi foto profil pada kedua aplikasi.</li>
            <li>Data wilayah/desa dapat digunakan oleh modul Admin Panel sesuai koneksi dan endpoint yang disepakati.</li>
        </ol>
    </section>

    <section class="page-break">
        <h2>7. Diagram Alur dan State</h2>
        <h3>7.1 Alur Surat</h3>
        <div class="diagram">
            <svg viewBox="0 0 760 155" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Alur surat SIDONGAN">
                <defs><marker id="arrow3" markerWidth="8" markerHeight="8" refX="7" refY="4" orient="auto"><path d="M0,0 L8,4 L0,8 z" fill="#2c7be5"/></marker></defs>
                <g font-family="DejaVu Sans" font-size="11" text-anchor="middle"><rect x="10" y="48" width="132" height="55" rx="7" fill="#eef5fd" stroke="#2c7be5"/><text x="76" y="71" fill="#123b69">Catat Surat</text><text x="76" y="88" fill="#62758c" font-size="9">Sekretaris</text><rect x="178" y="48" width="132" height="55" rx="7" fill="#eef5fd" stroke="#2c7be5"/><text x="244" y="71" fill="#123b69">Disposisi</text><text x="244" y="88" fill="#62758c" font-size="9">Penerima + instruksi</text><rect x="346" y="48" width="132" height="55" rx="7" fill="#eef5fd" stroke="#2c7be5"/><text x="412" y="71" fill="#123b69">Laporan</text><text x="412" y="88" fill="#62758c" font-size="9">Pelaksana</text><rect x="514" y="48" width="108" height="55" rx="7" fill="#eef5fd" stroke="#2c7be5"/><text x="568" y="71" fill="#123b69">Verifikasi</text><text x="568" y="88" fill="#62758c" font-size="9">Ketua</text><rect x="658" y="48" width="92" height="55" rx="7" fill="#123b69"/><text x="704" y="80" fill="#fff">Arsip</text></g><path d="M142 75 L178 75 M310 75 L346 75 M478 75 L514 75 M622 75 L658 75" stroke="#2c7be5" stroke-width="2" marker-end="url(#arrow3)"/>
            </svg>
            <div class="caption">Gambar 3. Alur utama surat dari pencatatan sampai arsip.</div>
        </div>
        <h3>7.2 Jalur Revisi</h3>
        <p>Jika Ketua menolak laporan, sistem menyimpan catatan penolakan, mengembalikan status surat ke <code>berjalan</code>, dan pelaksana dapat memperbaiki laporan sebelum mengirimkannya kembali.</p>
        <h3>7.3 Notifikasi</h3>
        <p>Disposisi, pengiriman laporan, dan hasil verifikasi menghasilkan notifikasi untuk pihak yang relevan. Notifikasi yang sudah dibaca dibersihkan oleh scheduler harian.</p>
    </section>

    <section class="page-break">
        <h2>8. SIEDA Web dan SIEDA Mobile</h2>
        <h3>8.1 SIEDA Web / API</h3>
        <p>Project <code>sieda-pkk-toba-app</code> adalah backend Laravel terpisah yang menyediakan web admin, API REST untuk Flutter, endpoint publik, endpoint protected, serta endpoint internal untuk sinkronisasi dari Admin Panel PKK.</p>
        <table>
            <tr><th>Area API</th><th>Fungsi</th></tr>
            <tr><td>Auth dan profil</td><td>Login, logout, profil, token Sanctum, dan pengelolaan sesi perangkat.</td></tr>
            <tr><td>Penduduk dan keluarga</td><td>Data warga, keluarga, anggota keluarga, pencarian, filter, dan status KK.</td></tr>
            <tr><td>Dasawisma</td><td>Kelompok, kesehatan, sanitasi, ringkasan per dusun, dan rekap kesehatan.</td></tr>
            <tr><td>Rekapitulasi</td><td>Data umum, Pokja I-IV, kesehatan, kelahiran, dan kematian.</td></tr>
            <tr><td>Operasional tambahan</td><td>Kegiatan warga, catatan kelahiran/kematian, arsip surat, referensi, berita, slider, profil, dan potensi.</td></tr>
        </table>
        <h3>8.2 SIEDA Mobile Flutter</h3>
        <p>Project <code>sieda</code> adalah aplikasi Flutter untuk petugas lapangan. Aplikasi menggunakan Provider untuk state management, Dio untuk HTTP, Secure Storage untuk token, SQLCipher untuk database offline terenkripsi, Firebase Messaging untuk notifikasi, Crashlytics untuk pemantauan error, serta autentikasi biometrik.</p>
        <ul>
            <li>Login dan splash screen.</li>
            <li>Dashboard dan detail dusun.</li>
            <li>Penduduk dan keluarga.</li>
            <li>Dasawisma dan data kesehatan.</li>
            <li>Kegiatan warga dan catatan kelahiran/kematian.</li>
            <li>Rekapitulasi dan profil.</li>
        </ul>
        <h3>8.3 Offline-First dan Sinkronisasi</h3>
        <div class="diagram">
            <svg viewBox="0 0 760 155" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Alur offline first SIEDA Mobile">
                <defs><marker id="arrow4" markerWidth="8" markerHeight="8" refX="7" refY="4" orient="auto"><path d="M0,0 L8,4 L0,8 z" fill="#2c7be5"/></marker></defs>
                <g font-family="DejaVu Sans" font-size="11" text-anchor="middle"><rect x="10" y="48" width="135" height="55" rx="7" fill="#eef5fd" stroke="#2c7be5"/><text x="77" y="72" fill="#123b69">Input Petugas</text><text x="77" y="89" fill="#62758c" font-size="9">Mobile Flutter</text><rect x="180" y="48" width="145" height="55" rx="7" fill="#f7f9fb" stroke="#8fa5bd"/><text x="252" y="72" fill="#253247">SQLCipher Lokal</text><text x="252" y="89" fill="#62758c" font-size="9">Cache + pending queue</text><rect x="360" y="48" width="145" height="55" rx="7" fill="#eef5fd" stroke="#2c7be5"/><text x="432" y="72" fill="#123b69">SyncService</text><text x="432" y="89" fill="#62758c" font-size="9">Retry dan dependency</text><rect x="540" y="48" width="205" height="55" rx="7" fill="#123b69"/><text x="642" y="72" fill="#fff">SIEDA Web API</text><text x="642" y="89" fill="#dcecff" font-size="9">Sanctum + Database SIEDA</text></g><path d="M145 75 L180 75 M325 75 L360 75 M505 75 L540 75" stroke="#2c7be5" stroke-width="2" marker-end="url(#arrow4)"/>
            </svg>
            <div class="caption">Gambar 4. Pola offline-first: data lokal dikirim ketika koneksi tersedia.</div>
        </div>
        <p>Data dikirim berdasarkan urutan ketergantungan: penduduk dan keluarga terlebih dahulu, kemudian anggota keluarga, Dasawisma, catatan, dan kegiatan. Kegagalan jaringan menggunakan retry/backoff; kegagalan validasi permanen ditandai agar tidak menyebabkan pengiriman berulang.</p>
        <h3>8.4 Kontrak Endpoint Mobile</h3>
        <table><tr><th>Kelompok endpoint</th><th>Contoh fungsi</th><th>Auth</th></tr><tr><td><code>/auth</code></td><td>Login, logout, logout semua perangkat, profil</td><td>Login publik; lainnya token</td></tr><tr><td><code>/dashboard</code></td><td>Statistik, konfigurasi tahun, detail dusun</td><td>Token</td></tr><tr><td><code>/penduduk</code></td><td>CRUD warga, pencarian, filter, pagination</td><td>Token</td></tr><tr><td><code>/keluarga</code></td><td>CRUD keluarga dan anggota</td><td>Token</td></tr><tr><td><code>/dasawisma</code></td><td>Kelompok, data kesehatan, sanitasi, rekap dusun</td><td>Token</td></tr><tr><td><code>/rekapitulasi</code></td><td>Data umum, Pokja I-IV, kesehatan, kelahiran/kematian</td><td>Token</td></tr><tr><td><code>/catatan-kelahiran-kematian</code></td><td>CRUD catatan</td><td>Token</td></tr><tr><td><code>/kegiatan-warga</code></td><td>Daftar dan sinkron kegiatan</td><td>Token</td></tr><tr><td><code>/letter-archive</code></td><td>Arsip, upload, download, hapus</td><td>Token</td></tr><tr><td><code>/public</code></td><td>Homepage, berita, slider, profil, potensi, referensi</td><td>Publik</td></tr></table>
        <h3>8.5 Matriks Akses Ringkas</h3>
        <table><tr><th>Fungsi</th><th>Super Admin / Admin</th><th>Petugas Mobile</th><th>Publik</th></tr><tr><td>Kelola akun dan role</td><td>Ya</td><td>Tidak</td><td>Tidak</td></tr><tr><td>Kelola data warga dan keluarga</td><td>Sesuai role</td><td>Ya, sesuai wilayah</td><td>Tidak</td></tr><tr><td>Kelola Dasawisma dan kesehatan</td><td>Sesuai role</td><td>Ya, sesuai wilayah</td><td>Tidak</td></tr><tr><td>Lihat rekapitulasi</td><td>Ya</td><td>Ya</td><td>Tidak</td></tr><tr><td>Lihat berita/profil/potensi publik</td><td>Ya</td><td>Ya</td><td>Ya</td></tr><tr><td>Kelola surat SIDONGAN</td><td>Ya</td><td>Tidak</td><td>Tidak</td></tr></table>
    </section>

    <section class="page-break">
        <h2>9. Kesimpulan</h2>
        <p>Project ini mencakup ekosistem terpadu yang terdiri dari PKK Toba, SIDONGAN, SIEDA Web, dan SIEDA Mobile. PKK Toba dan SIDONGAN menangani informasi organisasi serta administrasi surat, sedangkan SIEDA Web dan SIEDA Mobile menangani pendataan warga, keluarga, Dasawisma, kegiatan, kesehatan, dan rekapitulasi.</p>
        <p>Integrasi antarproject menggunakan kombinasi REST API, Sanctum token, shared secret HMAC, sinkronisasi akun, serta pola offline-first pada aplikasi mobile. Dengan demikian, petugas tetap dapat bekerja di lapangan saat offline dan data dapat dikirim kembali secara aman ketika koneksi tersedia.</p>
        <div class="section-note"><strong>Dokumen sumber:</strong> detail proses bisnis tersedia pada <code>docs/proses-bisnis.md</code>, detail teknis pada <code>docs/arsitektur.md</code>, dan kumpulan diagram Mermaid pada <code>docs/diagram.md</code>.</div>
        <div class="footer-note">Dokumentasi Ekosistem PKK Toba, SIDONGAN, SIEDA Web, dan SIEDA Mobile | Versi 2.0 | {{ $generatedAt }}</div>
    </section>
</body>
</html>
