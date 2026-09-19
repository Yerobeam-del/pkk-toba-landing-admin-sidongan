{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     Setup Email Pribadi (Admin Panel — titik masuk user SIEDA
     via SSO) — layout split-panel ob-*, selaras dengan
     onboarding & halaman auth lainnya.
     ============================================================ --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="stylesheet" href="{{ asset('assets/shared/css/utilities.css') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Email Pribadi - Admin Panel PKK Kabupaten Toba</title>

    {{-- Favicon theme-aware — pasangan logo yang sama dengan panel admin --}}
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

                <h1 class="ob-welcome">Daftarkan Email Pribadi</h1>
                <p class="ob-subtitle">Email login Anda tidak memiliki kotak surat, sehingga tidak bisa menerima link reset password.</p>

                <div class="ob-steps">
                    <div class="ob-step">
                        <div class="ob-step-num">1</div>
                        <div class="ob-step-info">
                            <span class="ob-step-title">Masukkan Email Pribadi</span>
                            <span class="ob-step-desc">Gmail, Yahoo, atau lainnya</span>
                        </div>
                    </div>
                    <div class="ob-step">
                        <div class="ob-step-num">2</div>
                        <div class="ob-step-info">
                            <span class="ob-step-title">Cek Email Anda</span>
                            <span class="ob-step-desc">Klik link verifikasi yang dikirim</span>
                        </div>
                    </div>
                    <div class="ob-step">
                        <div class="ob-step-num">3</div>
                        <div class="ob-step-info">
                            <span class="ob-step-title">Reset Password Aktif</span>
                            <span class="ob-step-desc">Mandiri, tanpa hubungi admin</span>
                        </div>
                    </div>
                </div>

                <div class="ob-tips">
                    <div class="ob-tips-title">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a7 7 0 0 1 7 7c0 2.38-1.19 4.47-3 5.74V17a2 2 0 0 1-2 2H10a2 2 0 0 1-2-2v-2.26C6.19 13.47 5 11.38 5 9a7 7 0 0 1 7-7z"/><line x1="9" y1="21" x2="15" y2="21"/></svg>
                        Tips
                    </div>
                    <p>Gunakan <strong>email aktif</strong> yang bisa Anda akses kapan saja — email ini hanya untuk keamanan akun, bukan email login.</p>
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
                        <h2>Setup Email Pribadi</h2>
                    </div>
                </div>

                @if(isset($needs_verification) && $needs_verification && isset($existing_email))
                    <div class="ob-alert ob-alert--warning">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <div>
                            <strong>Email pribadi sudah didaftarkan!</strong>
                            <span>Email <strong>{{ $existing_email }}</strong> tersimpan namun <strong>belum diverifikasi</strong>.</span>
                            <a href="{{ route('personal-email.notice') }}" style="color:inherit;text-decoration:underline;display:inline-flex;align-items:center;gap:0.3rem">Klik di sini untuk kirim ulang verifikasi<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></a>
                            <span style="font-size:0.85rem;opacity:0.8">Atau ganti dengan email lain di bawah.</span>
                        </div>
                    </div>
                @else
                    <div class="ob-alert ob-alert--success">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                        <div>
                            <strong>Selamat datang, {{ Auth::user()->name }}!</strong>
                            <span>Daftarkan email pribadi Anda untuk mengaktifkan fitur reset password mandiri.</span>
                            <span style="font-size:0.85rem;opacity:0.8">Email login <strong>{{ Auth::user()->email }}</strong> tidak memiliki kotak surat.</span>
                        </div>
                    </div>
                @endif

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

                <form method="POST" action="{{ route('personal-email.store') }}" class="ob-form" style="display:flex;flex-direction:column;gap:1.1rem">
                    @csrf

                    <div class="ob-field">
                        <label class="ob-field-label" for="personal_email">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            Alamat Email Pribadi
                        </label>
                        <input
                            type="email"
                            id="personal_email"
                            name="personal_email"
                            value="{{ old('personal_email') }}"
                            required
                            autofocus
                            autocomplete="email"
                            placeholder="namaketua@gmail.com"
                            class="ob-input"
                        >
                        <span class="ob-field-hint">Gunakan email aktif yang bisa Anda akses kapan saja</span>
                    </div>

                    <div class="ob-actions">
                        <button type="submit" class="ob-btn-primary">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                            Kirim Verifikasi
                        </button>
                        <a href="{{ route('personal-email.skip') }}" class="ob-btn-skip">
                            Lewati — nanti saja
                        </a>
                    </div>
                </form>

                <div style="margin-top:1.5rem;padding-top:1rem;border-top:1px dashed rgba(0,0,0,0.08);font-size:0.8rem;color:var(--text-muted,#64748b);text-align:center">
                    Butuh bantuan? Hubungi administrator
                </div>
            </div>
        </div>
    </div>
</body>
</html>
{{-- Dikembangkan oleh Institut Teknologi Del --}}
