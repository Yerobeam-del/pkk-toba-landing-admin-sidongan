{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     Verifikasi Email (Admin Panel) — layout split-panel ob-*,
     selaras dengan onboarding & panel "Cek Email".
     ============================================================ --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="stylesheet" href="{{ asset('assets/shared/css/utilities.css') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email - Admin Panel PKK Kabupaten Toba</title>

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
        }
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

                <h1 class="ob-welcome">Satu Langkah Lagi</h1>
                <p class="ob-subtitle">Verifikasi email Anda untuk mengaktifkan semua fitur akun, termasuk reset password mandiri.</p>

                <div class="ob-steps">
                    <div class="ob-step ob-step--done">
                        <div class="ob-step-num">1</div>
                        <div class="ob-step-info">
                            <span class="ob-step-title">Akun Dibuat</span>
                            <span class="ob-step-desc">Data Anda tersimpan aman</span>
                        </div>
                    </div>
                    <div class="ob-step">
                        <div class="ob-step-num">2</div>
                        <div class="ob-step-info">
                            <span class="ob-step-title">Verifikasi Email</span>
                            <span class="ob-step-desc">Klik link yang kami kirim</span>
                        </div>
                    </div>
                    <div class="ob-step">
                        <div class="ob-step-num">3</div>
                        <div class="ob-step-info">
                            <span class="ob-step-title">Akun Aktif</span>
                            <span class="ob-step-desc">Semua fitur terbuka</span>
                        </div>
                    </div>
                </div>

                <div class="ob-tips">
                    <div class="ob-tips-title">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a7 7 0 0 1 7 7c0 2.38-1.19 4.47-3 5.74V17a2 2 0 0 1-2 2H10a2 2 0 0 1-2-2v-2.26C6.19 13.47 5 11.38 5 9a7 7 0 0 1 7-7z"/><line x1="9" y1="21" x2="15" y2="21"/></svg>
                        Tips
                    </div>
                    <p>Tidak menemukan email? Periksa folder <strong>spam/promosi</strong>, atau kirim ulang link di samping.</p>
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
                        <h2>Cek Email Anda</h2>
                    </div>
                </div>

                @if(session('status') == 'verification-link-sent')
                    <div class="ob-alert ob-alert--success">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        <span>Link verifikasi baru telah dikirim ke email Anda.</span>
                    </div>
                @endif

                <div class="ob-alert ob-alert--warning">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <span>Link verifikasi telah dikirim ke <strong>{{ Auth::user()->email }}</strong>. Klik link di email untuk memverifikasi akun Anda.</span>
                </div>

                <div class="ob-actions">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" class="ob-btn-primary">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                            Kirim Ulang Link Verifikasi
                        </button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="#" onclick="this.closest('form').submit();return false" class="ob-btn-skip">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                            Keluar
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
{{-- Dikembangkan oleh Institut Teknologi Del --}}
