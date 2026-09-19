{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     Konfirmasi Password (Admin Panel) — layout split-panel ob-*,
     selaras dengan onboarding & halaman auth lainnya.
     ============================================================ --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="stylesheet" href="{{ asset('assets/shared/css/utilities.css') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Password - Admin Panel PKK Kabupaten Toba</title>

    <link rel="icon" type="image/svg+xml" id="favicon" href="{{ asset('assets/admin/images/Logo_Admin-Panel.svg') }}">

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Tema 3-mode (system/light/dark) bersama panel admin --}}
    <script>
    (function(){
        var k='admin-dark-mode',s=localStorage.getItem(k);
        var mq=window.matchMedia?window.matchMedia('(prefers-color-scheme:dark)'):null;
        var dark=(s==='true')||(s!=='false'&&mq&&mq.matches);
        if(dark){document.documentElement.classList.add('dark-mode');}
        if(mq&&mq.addEventListener){
            mq.addEventListener('change',function(e){
                if(localStorage.getItem(k)!=='false'&&localStorage.getItem(k)!=='true'){
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

    <link rel="stylesheet" href="{{ asset('assets/sidongan-auth/css/auth-onboarding.css') }}">

    <style>
        :root {
            --gradient-start: #6d28d9;
            --gradient-mid: #7c3aed;
            --gradient-end: #a855f7;
            --primary: #7c3aed;
            --primary-dark: #6d28d9;
            --primary-light: #c4b5fd;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
        }

        .ob-input-wrap { position: relative; }
        .ob-input-wrap .ob-input { padding-right: 2.75rem; }
        .ob-input-wrap .toggle-password {
            position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%);
            background: none; border: none; cursor: pointer; color: var(--text-muted);
            display: flex; padding: 0.25rem; transition: color 0.2s ease;
        }
        .ob-input-wrap .toggle-password:hover { color: var(--primary); }
        html.dark-mode .ob-input-wrap .toggle-password { color: #94a3b8; }
        html.dark-mode .ob-input-wrap .toggle-password:hover { color: var(--primary-light); }
    </style>
</head>
<body>
    <div class="ob-split">
        {{-- ===== LEFT PANEL ===== --}}
        <div class="ob-left">
            <div class="ob-left-content">
                <div class="ob-logo">
                    <img src="{{ asset('assets/shared/images/Logo-Kabupaten-Toba-White.svg') }}" alt="Logo PKK" width="56" height="56">
                </div>

                <h1 class="ob-welcome">Area Terlindungi</h1>
                <p class="ob-subtitle">Halaman yang akan Anda buka bersifat sensitif. Konfirmasi password Anda untuk melanjutkan.</p>

                <div class="ob-steps">
                    <div class="ob-step">
                        <div class="ob-step-num">1</div>
                        <div class="ob-step-info">
                            <span class="ob-step-title">Konfirmasi Password</span>
                            <span class="ob-step-desc">Bukti bahwa ini benar Anda</span>
                        </div>
                    </div>
                    <div class="ob-step">
                        <div class="ob-step-num">2</div>
                        <div class="ob-step-info">
                            <span class="ob-step-title">Lanjut ke Tujuan</span>
                            <span class="ob-step-desc">Aksi sensitif dilindungi</span>
                        </div>
                    </div>
                </div>

                <div class="ob-tips">
                    <div class="ob-tips-title">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a7 7 0 0 1 7 7c0 2.38-1.19 4.47-3 5.74V17a2 2 0 0 1-2 2H10a2 2 0 0 1-2-2v-2.26C6.19 13.47 5 11.38 5 9a7 7 0 0 1 7-7z"/><line x1="9" y1="21" x2="15" y2="21"/></svg>
                        Tips
                    </div>
                    <p>Ini lapisan keamanan tambahan — konfirmasi berlaku sementara, tidak perlu tiap kali.</p>
                </div>

                <div class="ob-left-footer">
                    <span>&copy; {{ date('Y') }} TP-PKK Kabupaten Toba</span>
                </div>
            </div>
        </div>

        {{-- ===== RIGHT PANEL ===== --}}
        <div class="ob-right">
            <div class="ob-right-content">
                <div class="ob-progress-header">
                    <div class="ob-progress-text">
                        <h2>Konfirmasi Password</h2>
                    </div>
                </div>

                @if($errors->any())
                    <div class="ob-alert ob-alert--error">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                        <div>
                            <strong>Terjadi kesalahan:</strong>
                            <ul class="u-list-indent-sm">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.confirm') }}" class="ob-form" style="display:flex;flex-direction:column;gap:1.1rem">
                    @csrf

                    <div class="ob-field">
                        <label class="ob-field-label" for="password">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            Password Anda
                        </label>
                        <div class="ob-input-wrap">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                required
                                autocomplete="current-password"
                                placeholder="Password saat ini"
                                class="ob-input"
                                autofocus
                            >
                            <button type="button" class="toggle-password" data-toggle-password="password" tabindex="-1" aria-label="Tampilkan atau sembunyikan password">
                                <svg id="eyeOpenPassword" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg class="u-hidden" id="eyeClosedPassword" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                            </button>
                        </div>
                    </div>

                    <div class="ob-actions">
                        <button type="submit" class="ob-btn-primary">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            Konfirmasi
                        </button>
                        <a href="{{ route('admin.dashboard') }}" class="ob-btn-skip">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                            Kembali ke Dashboard
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/shared/js/auth-reset-password.js') }}"></script>
</body>
</html>
{{-- Dikembangkan oleh Institut Teknologi Del --}}
