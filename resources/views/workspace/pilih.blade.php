{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     Launcher "Pilih Ruang Kerja" — pilihan AREA KERJA ADMIN PANEL
     sesuai role/assignment user:
       - Admin Panel PKK (beranda panel)
       - Admin Panel Desa (desa di-assign di Manajemen Akun, via SSO SIEDA)
       - Manajemen Akun (permission manage-users)
       - Manajemen Data SIEDA & SIDONGAN (super admin)

     Aplikasi SIDONGAN (surat-menyurat) BUKAN pilihan ruang kerja —
     aksesnya murni mengikuti sidongan_role dari Manajemen Akun.
     Pilihan disimpan sebagai default login berikutnya. Gaya visual
     mengikuti onboarding Admin Panel (ob-split, aksen ungu #6d28d9).
     ============================================================ --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Ruang Kerja — Admin Panel PKK</title>

    <link rel="icon" type="image/svg+xml" id="favicon" href="{{ asset('assets/admin/images/Logo_Admin-Panel.svg') }}">

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Tema 3-mode: sama dengan onboarding Admin Panel (kunci admin-dark-mode:
         system/light/dark), diterapkan sebelum render agar tidak flicker. --}}
    <script>
    (function(){
        var k='admin-dark-mode';
        var s=localStorage.getItem(k);
        var mq=window.matchMedia?window.matchMedia('(prefers-color-scheme:dark)'):null;
        var dark=(s==='true')||(s!=='false'&&mq&&mq.matches);
        if(dark){document.documentElement.classList.add('dark-mode');}
        if(mq&&mq.addEventListener){
            mq.addEventListener('change',function(e){
                if(localStorage.getItem(k)===null){
                    document.documentElement.classList.toggle('dark-mode',e.matches);
                }
            });
        }
        window.addEventListener('DOMContentLoaded',function(){
            var fav=document.getElementById('favicon');
            if(!fav)return;
            var LIGHT='{{ asset('assets/admin/images/Logo_Admin-Panel.svg') }}';
            var DARK='{{ asset('assets/admin/images/Logo_Admin-Panel-White.svg') }}';
            var apply=function(){fav.href=document.documentElement.classList.contains('dark-mode')?DARK:LIGHT;};
            apply();
            if(mq&&mq.addEventListener){mq.addEventListener('change',apply);}
        });
    })();
    </script>

    <style>
        :root {
            --gradient-start: #6d28d9;
            --gradient-mid: #7c3aed;
            --gradient-end: #a855f7;
            --text-dark: #1e293b;
            --text-muted: #64748b;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text-dark);
            -webkit-font-smoothing: antialiased;
        }
        .ws-split {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        /* ===== PANEL KIRI: sapaan & identitas ===== */
        .ws-left {
            flex: 0 0 42%;
            max-width: 42%;
            background: linear-gradient(160deg, var(--gradient-start) 0%, var(--gradient-mid) 40%, var(--gradient-end) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem;
            position: relative;
            overflow: hidden;
        }
        .ws-left::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url('/assets/admin/images/batik-pkk.svg');
            background-size: 300px;
            background-repeat: repeat;
            opacity: 0.06;
            pointer-events: none;
        }
        .ws-left-content {
            position: relative;
            z-index: 1;
            max-width: 380px;
            width: 100%;
            animation: slideLeft 0.6s ease;
        }
        @keyframes slideLeft {
            from { opacity: 0; transform: translateX(-30px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .ws-logo { margin-bottom: 2rem; }
        .ws-logo img { width: 56px; height: 56px; filter: brightness(0) invert(1) drop-shadow(0 4px 20px rgba(0,0,0,0.2)); }
        .ws-welcome {
            font-size: 1.9rem;
            font-weight: 800;
            color: #fff;
            line-height: 1.25;
            margin-bottom: 0.75rem;
            text-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .ws-subtitle {
            font-size: 0.95rem;
            color: rgba(255,255,255,0.85);
            line-height: 1.6;
            margin-bottom: 2rem;
        }
        .ws-left-footer { font-size: 0.75rem; color: rgba(255,255,255,0.55); }

        /* ===== PANEL KANAN: kartu area kerja ===== */
        .ws-right {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem;
            overflow-y: auto;
            background: #fff;
        }
        .ws-right-content {
            max-width: 560px;
            width: 100%;
            animation: slideRight 0.6s ease;
        }
        @keyframes slideRight {
            from { opacity: 0; transform: translateX(30px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .ws-heading { font-size: 1.35rem; font-weight: 800; margin-bottom: 0.4rem; }
        .ws-heading-sub { font-size: 0.875rem; color: var(--text-muted); margin-bottom: 1.75rem; }

        .ws-cards { display: flex; flex-direction: column; gap: 0.9rem; }
        .ws-card {
            display: flex;
            align-items: center;
            gap: 1.1rem;
            width: 100%;
            text-align: left;
            background: #fff;
            border: 1.5px solid #e2e8f0;
            border-radius: 16px;
            padding: 1.15rem 1.3rem;
            cursor: pointer;
            font-family: inherit;
            text-decoration: none;
            color: inherit;
            transition: border-color 0.18s ease, box-shadow 0.18s ease, transform 0.18s ease;
        }
        .ws-card:hover {
            border-color: var(--gradient-mid);
            box-shadow: 0 10px 28px rgba(124, 58, 237, 0.14);
            transform: translateY(-2px);
        }
        .ws-card-icon {
            flex: 0 0 auto;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 54px;
            height: 54px;
            border-radius: 14px;
        }
        .ws-card-body { flex: 1; min-width: 0; }
        .ws-card-title { display: flex; align-items: center; gap: 0.5rem; font-size: 1rem; font-weight: 700; }
        .ws-card-desc { font-size: 0.8rem; color: var(--text-muted); margin-top: 0.3rem; line-height: 1.55; }
        .ws-card-go {
            flex: 0 0 auto;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: #f1f5f9;
            color: var(--text-muted);
            transition: background 0.18s ease, color 0.18s ease;
        }
        .ws-card:hover .ws-card-go { background: var(--gradient-mid); color: #fff; }
        .ws-tag {
            font-size: 0.6rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            padding: 0.14rem 0.5rem;
            border-radius: 99px;
            background: #f5f3ff;
            color: #6d28d9;
            border: 1px dashed #c4b5fd;
        }
        .ws-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            margin-top: 0.45rem;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 0.16rem 0.55rem;
            border-radius: 99px;
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
        }

        /* ===== Dropdown pemilih desa (kartu Admin Panel Desa) ===== */
        .ws-desa-picker { margin-top: 0.7rem; position: relative; }
        .ws-desa-select {
            width: 100%;
            appearance: none;
            -webkit-appearance: none;
            font-family: inherit;
            font-size: 0.82rem;
            font-weight: 600;
            color: #1e293b;
            background: #f8fafc url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E") no-repeat right 0.75rem center;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            padding: 0.55rem 2.2rem 0.55rem 0.8rem;
            cursor: pointer;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .ws-desa-select:hover { border-color: #c4b5fd; }
        .ws-desa-select:focus {
            outline: none;
            border-color: #7c3aed;
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.15);
        }
        .ws-desa-select optgroup { font-size: 0.78rem; color: #6d28d9; }
        .ws-desa-select option { font-weight: 500; color: #1e293b; }

        html.dark-mode .ws-desa-select {
            background-color: #1a2436;
            color: #e2e8f0;
            border-color: #334155;
        }
        html.dark-mode .ws-desa-select:hover { border-color: #8b5cf6; }
        html.dark-mode .ws-desa-select:focus { border-color: #8b5cf6; box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.2); }
        html.dark-mode .ws-desa-select option { background: #1e293b; color: #f1f5f9; }
        html.dark-mode .ws-desa-select optgroup { color: #c4b5fd; }

        .ws-footnote {
            margin-top: 1.6rem;
            font-size: 0.78rem;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 0.45rem;
        }

        /* ===== DARK MODE (paritas onboarding Admin Panel) ===== */
        html.dark-mode body { background: #0f172a; }
        html.dark-mode .ws-right { background: #1e293b; }
        html.dark-mode .ws-heading { color: #f1f5f9; }
        html.dark-mode .ws-heading-sub,
        html.dark-mode .ws-card-desc,
        html.dark-mode .ws-footnote { color: #94a3b8; }
        html.dark-mode .ws-card { background: #273449; border-color: #334155; }
        html.dark-mode .ws-card:hover { border-color: #8b5cf6; }
        html.dark-mode .ws-card-go { background: #334155; color: #cbd5e1; }
        html.dark-mode .ws-tag { background: rgba(109, 40, 217, 0.2); color: #c4b5fd; border-color: #7c3aed; }
        html.dark-mode .ws-chip { background: rgba(4, 120, 87, 0.18); color: #6ee7b7; border-color: #065f46; }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 900px) {
            .ws-split { flex-direction: column; }
            .ws-left {
                flex: none;
                max-width: none;
                padding: 2.25rem 1.75rem;
            }
            .ws-left-content { max-width: 100%; }
            .ws-right { padding: 2rem 1.25rem; align-items: flex-start; }
            .ws-welcome { font-size: 1.5rem; }
        }
    </style>
</head>
<body>
    <div class="ws-split">
        {{-- ===== PANEL KIRI ===== --}}
        <div class="ws-left">
            <div class="ws-left-content">
                <div class="ws-logo">
                    <img src="{{ asset('assets/admin/images/Logo_Admin-Panel-White.svg') }}" alt="Logo Admin Panel PKK" width="56" height="56">
                </div>
                <h1 class="ws-welcome">Selamat datang,<br>{{ $user->name }}</h1>
                <p class="ws-subtitle">
                    Satu akun untuk seluruh area kerja PKK Kabupaten Toba.
                    Pilih area yang ingin dikelola — pilihan ini menjadi default
                    saat Anda login berikutnya.
                </p>
                <div class="ws-left-footer">PKK Kabupaten Toba &middot; Move Together Move Forward</div>
            </div>
        </div>

        {{-- ===== PANEL KANAN: kartu sesuai role/assignment ===== --}}
        <div class="ws-right">
            <div class="ws-right-content">
                <h2 class="ws-heading">Pilih Ruang Kerja</h2>
                <p class="ws-heading-sub">Ruang kerja bisa diganti kapan saja dari menu akun.</p>

                <div class="ws-cards">
                    @if ($bisaPkk)
                        {{-- Admin Panel PKK — beranda panel --}}
                        <form method="POST" action="{{ route('workspace.simpan') }}">
                            @csrf
                            <input type="hidden" name="workspace" value="pkk">
                            <button type="submit" class="ws-card">
                                <span class="ws-card-icon" style="background:#f5f3ff;color:#6d28d9;">
                                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M5 21V7l7-4 7 4v14"/><path d="M9 21v-4h6v4"/><path d="M9 11h.01M12 11h.01M15 11h.01M9 14h.01M12 14h.01M15 14h.01"/></svg>
                                </span>
                                <span class="ws-card-body">
                                    <span class="ws-card-title">Admin Panel PKK <span class="ws-tag">Utama</span></span>
                                    <span class="ws-card-desc d-block">Kelola landing page TP-PKK Kabupaten Toba: berita, pengurus, slider, SK &amp; dokumen, aplikasi, dan pengaturan situs.</span>
                                </span>
                                <span class="ws-card-go">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                                </span>
                            </button>
                        </form>
                    @endif

                    @if ($bisaDesa)
                        {{-- Admin Panel Desa — bentuk kartu mengikuti jumlah desa
                             yang di-assign di Manajemen Akun:
                               - tepat 1 desa → kartu utuh satu tombol, langsung masuk;
                               - lebih dari 1 (termasuk lintas desa) → langkah
                                 "Pilih Desa" di halaman tersendiri. --}}
                        @if ($multiDesa)
                            <a class="ws-card" href="{{ route('workspace.pilih-desa') }}">
                                <span class="ws-card-icon" style="background:#eff6ff;color:#2563eb;">
                                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                </span>
                                <span class="ws-card-body">
                                    <span class="ws-card-title">Admin Panel Desa</span>
                                    <span class="ws-card-desc d-block">Kelola landing page desa: berita, slider, potensi, profil, struktur, dan pengaturan website desa.</span>
                                    <span class="ws-chip">
                                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                        {{ $desaAssign === null ? 'Pilih desa dulu' : 'Beberapa desa' }}
                                    </span>
                                </span>
                                <span class="ws-card-go">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                                </span>
                            </a>
                        @else
                            {{-- Satu desa di assignment — masuk langsung tanpa pilihan. --}}
                            <form method="POST" action="{{ route('workspace.simpan') }}">
                                @csrf
                                <input type="hidden" name="workspace" value="desa">
                                <button type="submit" class="ws-card">
                                    <span class="ws-card-icon" style="background:#eff6ff;color:#2563eb;">
                                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                    </span>
                                    <span class="ws-card-body">
                                        <span class="ws-card-title">Admin Panel Desa</span>
                                        <span class="ws-card-desc d-block">Kelola landing page desa: berita, slider, potensi, profil, struktur, dan pengaturan website desa.</span>
                                        @if ($desaAssign)
                                            <span class="ws-chip">
                                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                                Desa {{ $desaAssign }}
                                            </span>
                                        @endif
                                    </span>
                                    <span class="ws-card-go">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                                    </span>
                                </button>
                            </form>
                        @endif
                    @endif

                    @if ($bisaAkun)
                        {{-- Manajemen Akun --}}
                        <form method="POST" action="{{ route('workspace.simpan') }}">
                            @csrf
                            <input type="hidden" name="workspace" value="akun">
                            <button type="submit" class="ws-card">
                                <span class="ws-card-icon" style="background:#fef9ec;color:#b45309;">
                                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                </span>
                                <span class="ws-card-body">
                                    <span class="ws-card-title">Manajemen Akun</span>
                                    <span class="ws-card-desc d-block">Kelola pengguna: buat akun, atur role &amp; permission, assignment desa/kecamatan, reset password, dan status akses.</span>
                                </span>
                                <span class="ws-card-go">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                                </span>
                            </button>
                        </form>
                    @endif

                    @if ($bisaDataSieda)
                        {{-- Manajemen Data SIEDA --}}
                        <form method="POST" action="{{ route('workspace.simpan') }}">
                            @csrf
                            <input type="hidden" name="workspace" value="data-sieda">
                            <button type="submit" class="ws-card">
                                <span class="ws-card-icon" style="background:#ecfdf5;color:#059669;">
                                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14a9 3 0 0 0 18 0V5"/><path d="M3 12a9 3 0 0 0 18 0"/></svg>
                                </span>
                                <span class="ws-card-body">
                                    <span class="ws-card-title">Manajemen Data SIEDA</span>
                                    <span class="ws-card-desc d-block">Inspeksi &amp; kelola data database SIEDA: warga, keluarga, dasawisma, dan modul pendataan lainnya.</span>
                                </span>
                                <span class="ws-card-go">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                                </span>
                            </button>
                        </form>
                    @endif

                    @if ($bisaDataSieda)
                        {{-- Manajemen Data SIDONGAN --}}
                        <form method="POST" action="{{ route('workspace.simpan') }}">
                            @csrf
                            <input type="hidden" name="workspace" value="data-sidongan">
                            <button type="submit" class="ws-card">
                                <span class="ws-card-icon" style="background:#fef2f2;color:#dc2626;">
                                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14a9 3 0 0 0 18 0V5"/><path d="M3 12a9 3 0 0 0 18 0"/></svg>
                                </span>
                                <span class="ws-card-body">
                                    <span class="ws-card-title">Manajemen Data SIDONGAN</span>
                                    <span class="ws-card-desc d-block">Inspeksi &amp; kelola arsip surat database SIDONGAN: dokumen masuk/keluar, kategori, dan laporan disposisi.</span>
                                </span>
                                <span class="ws-card-go">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                                </span>
                            </button>
                        </form>
                    @endif

                    @if (!$bisaPkk && !$bisaDesa && !$bisaAkun && !$bisaDataSieda)
                        <div class="ws-card" style="cursor:default;">
                            <span class="ws-card-icon" style="background:#fef2f2;color:#dc2626;">
                                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v4"/><path d="M12 16h.01"/></svg>
                            </span>
                            <span class="ws-card-body">
                                <span class="ws-card-title">Belum ada akses area kerja</span>
                                <span class="ws-card-desc d-block">Akun Anda belum diberi peran di area mana pun. Hubungi Super Admin untuk pengaturan akses.</span>
                            </span>
                        </div>
                    @endif
                </div>

                <div class="ws-footnote">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                    Pilihan Anda tersimpan dan dipakai otomatis pada login berikutnya.
                </div>
            </div>
        </div>
    </div>
</body>
</html>
{{-- Dikembangkan oleh Institut Teknologi Del --}}
