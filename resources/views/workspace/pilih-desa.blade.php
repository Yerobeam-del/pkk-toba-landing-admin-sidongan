{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     Langkah kedua kartu "Admin Panel Desa": pengelola multi-desa
     memilih desa mana yang landing page-nya mau diatur.
     Daftar desa = assignment akun di Manajemen Akun (dikelompokkan
     per kecamatan). Klik satu desa → langsung masuk ke sana.
     Gaya visual & tema mengikuti launcher pilih.blade.php.
     ============================================================ --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Desa — Admin Panel PKK</title>

    <link rel="icon" type="image/svg+xml" id="favicon" href="{{ asset('assets/admin/images/Logo_Admin-Panel.svg') }}">

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Tema 3-mode: sama dengan launcher (kunci admin-dark-mode:
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
        }
        .ws-brand {
            margin-top: 2.5rem;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.04em;
            color: rgba(255,255,255,0.7);
        }

        /* ===== PANEL KANAN: daftar desa ===== */
        .ws-right {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 2rem;
            background: #f8fafc;
        }
        .ws-panel {
            width: 100%;
            max-width: 560px;
        }
        .ws-heading {
            font-size: 1.45rem;
            font-weight: 800;
            margin-bottom: 0.35rem;
        }
        .ws-sub {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 1.5rem;
        }
        .ws-back {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-muted);
            text-decoration: none;
            margin-bottom: 1.25rem;
        }
        .ws-back:hover { color: var(--gradient-mid); }

        .ws-desa-group { margin-bottom: 1.4rem; }
        .ws-desa-group-title {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-muted);
            margin-bottom: 0.55rem;
        }
        .ws-desa-list {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.65rem;
        }
        .ws-desa-item { display: contents; }
        .ws-desa-btn {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.8rem 1rem;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            font-family: inherit;
            font-size: 0.92rem;
            font-weight: 600;
            color: var(--text-dark);
            text-align: left;
            cursor: pointer;
            transition: border-color 0.15s ease, box-shadow 0.15s ease, transform 0.15s ease;
        }
        .ws-desa-btn:hover {
            border-color: var(--gradient-mid);
            box-shadow: 0 4px 16px rgba(124,58,237,0.12);
            transform: translateY(-1px);
        }
        .ws-desa-btn.is-active {
            border-color: var(--gradient-mid);
            box-shadow: 0 0 0 2px rgba(124,58,237,0.15);
        }
        .ws-desa-btn svg { flex: 0 0 auto; color: var(--gradient-mid); }
        .ws-desa-btn span { flex: 1; }
        .ws-desa-check { color: #16a34a !important; }

        .ws-footnote {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 1.75rem;
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        /* ===== DARK MODE ===== */
        .dark-mode body,
        html.dark-mode body { background: #0f172a; }
        html.dark-mode .ws-right { background: #0f172a; }
        html.dark-mode .ws-heading { color: #f1f5f9; }
        html.dark-mode .ws-sub,
        html.dark-mode .ws-back { color: #94a3b8; }
        html.dark-mode .ws-back:hover { color: #c4b5fd; }
        html.dark-mode .ws-desa-group-title { color: #94a3b8; }
        html.dark-mode .ws-desa-btn {
            background: #1e293b;
            border-color: #334155;
            color: #e2e8f0;
        }
        html.dark-mode .ws-desa-btn:hover {
            border-color: #a78bfa;
            box-shadow: 0 4px 16px rgba(167,139,250,0.15);
        }
        html.dark-mode .ws-desa-btn.is-active { border-color: #a78bfa; box-shadow: 0 0 0 2px rgba(167,139,250,0.2); }
        html.dark-mode .ws-desa-btn svg { color: #c4b5fd; }
        html.dark-mode .ws-footnote { color: #94a3b8; }

        /* ===== MOBILE ===== */
        @media (max-width: 860px) {
            .ws-split { flex-direction: column; }
            .ws-left {
                flex: none;
                max-width: none;
                padding: 2rem 1.5rem;
            }
            .ws-left-content { max-width: none; }
            .ws-logo { margin-bottom: 1rem; }
            .ws-logo img { width: 42px; height: 42px; }
            .ws-welcome { font-size: 1.35rem; }
            .ws-brand { margin-top: 1.25rem; }
            .ws-right { padding: 1.75rem 1.25rem 2.5rem; }
            .ws-desa-list { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="ws-split">
        <aside class="ws-left">
            <div class="ws-left-content">
                <div class="ws-logo">
                    <img src="{{ asset('assets/admin/images/Logo_Admin-Panel.svg') }}" alt="Logo Admin Panel PKK">
                </div>
                <h1 class="ws-welcome">Pilih Desa</h1>
                <p class="ws-sub">Anda mengelola landing page lebih dari satu desa. Pilih desa yang ingin diatur — {{ $desas->flatten()->count() }} desa terdaftar pada akun Anda.</p>
                <p class="ws-brand">PKK Kabupaten Toba &middot; Move Together Move Forward</p>
            </div>
        </aside>

        <main class="ws-right">
            <div class="ws-panel">
                <a class="ws-back" href="{{ route('workspace.pilih') }}">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>
                    Kembali ke pilihan ruang kerja
                </a>

                <h2 class="ws-heading">Atur Landing Page Desa</h2>
                <p class="ws-sub">Daftar desa sesuai penugasan akun Anda di Manajemen Akun.</p>

                @foreach ($desas as $kecamatan => $daftarDesa)
                    <div class="ws-desa-group">
                        <div class="ws-desa-group-title">{{ $kecamatan !== '' ? $kecamatan : 'Lainnya' }}</div>
                        <div class="ws-desa-list">
                            @foreach ($daftarDesa as $d)
                                <form method="POST" action="{{ route('workspace.simpan') }}" class="ws-desa-item">
                                    @csrf
                                    <input type="hidden" name="workspace" value="desa">
                                    <input type="hidden" name="workspace_desa" value="{{ $d->kode }}">
                                    <button type="submit" class="ws-desa-btn {{ $desaTerpilih === $d->kode ? 'is-active' : '' }}">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                        <span>{{ $d->nama }}</span>
                                        @if ($desaTerpilih === $d->kode)
                                            <svg class="ws-desa-check" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                        @endif
                                    </button>
                                </form>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <div class="ws-footnote">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                    Pilihan Anda tersimpan dan dipakai otomatis pada login berikutnya.
                </div>
            </div>
        </main>
    </div>
</body>
</html>
