{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     Reset Password (SIDONGAN) — layout split-panel ob-*,
     selaras dengan halaman onboarding & lupa password.
     ============================================================ --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="stylesheet" href="{{ asset('assets/shared/css/utilities.css') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - SIDONGAN PKK Kabupaten Toba</title>

    {{-- Favicon theme-aware — pasangan logo yang sama dengan aplikasi SIDONGAN --}}
    <link rel="icon" type="image/svg+xml" id="favicon" href="{{ asset('assets/sidongan/images/Logo-SIDONGAN-ThemeAware.svg') }}">

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Tema 3-mode (system/light/dark, ala SIEDA) bersama aplikasi SIDONGAN —
         class dipasang SEBELUM CSS render agar tidak flicker. --}}
    <script>
    (function(){
        var k='sidongan-theme',s=localStorage.getItem(k);
        var mq=window.matchMedia?window.matchMedia('(prefers-color-scheme:dark)'):null;
        var dark=(s==='dark')||(s!=='light'&&s!=='dark'&&mq&&mq.matches);
        if(dark){document.documentElement.classList.add('dark-mode');}
        if(mq&&mq.addEventListener){
            mq.addEventListener('change',function(e){
                if((localStorage.getItem(k)!=='light'&&localStorage.getItem(k)!=='dark')){
                    document.documentElement.classList.toggle('dark-mode',e.matches);
                    document.dispatchEvent(new CustomEvent('sidongan-theme-changed'));
                }
            });
        }
        // Favicon mengikuti tema aktif (bukan hanya preferensi OS)
        window.addEventListener('DOMContentLoaded',function(){
            var fav=document.getElementById('favicon');
            if(!fav)return;
            var LIGHT='{{ asset('assets/sidongan/images/Logo-SIDONGAN-ThemeAware.svg') }}';
            var DARK='{{ asset('assets/sidongan/images/Logo-SIDONGAN-white.svg') }}';
            var apply=function(){fav.href=document.documentElement.classList.contains('dark-mode')?DARK:LIGHT;};
            apply();
            document.addEventListener('sidongan-theme-changed',apply);
            if(mq&&mq.addEventListener){mq.addEventListener('change',apply);}
        });
    })();
    </script>

    <link rel="stylesheet" href="{{ asset('assets/sidongan-auth/css/auth-onboarding.css') }}">

    <style>
        :root {
            --gradient-start: #14b8a6;
            --gradient-mid: #0ea5e9;
            --gradient-end: #38bdf8;
            --primary: #14b8a6;
            --primary-dark: #0d9488;
            --primary-light: #5eead4;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
        }

        /* ===== Elemen spesifik halaman reset (di luar set ob-*) ===== */
        .ob-form { display: flex; flex-direction: column; gap: 1.1rem; }

        .ob-input-wrap { position: relative; }
        .ob-input-wrap .ob-input { padding-right: 2.75rem; }
        .ob-input-wrap .toggle-password {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-muted);
            display: flex;
            padding: 0.25rem;
            transition: color 0.2s ease;
        }
        .ob-input-wrap .toggle-password:hover { color: var(--primary); }

        .ob-strength { margin-top: 0.5rem; display: flex; gap: 4px; }
        .ob-strength .bar {
            height: 4px;
            flex: 1;
            border-radius: 4px;
            background: var(--border);
            transition: all 0.3s ease;
        }
        .ob-strength .bar.active.weak { background: #ef4444; }
        .ob-strength .bar.active.medium { background: #f59e0b; }
        .ob-strength .bar.active.strong { background: #22c55e; }
        .ob-hint { font-size: 0.8rem; color: var(--text-muted); margin-top: 0.375rem; }

        {{-- Email terkunci: terkirim otomatis dari link reset --}}
        .ob-email-locked {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.75rem 1rem;
            border-radius: 12px;
            background: rgba(74, 222, 128, 0.08);
            border: 1px solid rgba(74, 222, 128, 0.25);
        }
        .ob-email-locked svg { color: #4ade80; flex-shrink: 0; }
        .ob-email-locked div { display: flex; flex-direction: column; gap: 0.1rem; min-width: 0; }
        .ob-email-locked strong { font-size: 0.92rem; color: var(--text-dark); word-break: break-all; }
        .ob-email-locked small { font-size: 0.75rem; color: var(--text-muted); }

        html.dark-mode .ob-strength .bar { background: #334155; }
        html.dark-mode .ob-hint { color: #94a3b8; }
        html.dark-mode .ob-input-wrap .toggle-password { color: #94a3b8; }
        html.dark-mode .ob-input-wrap .toggle-password:hover { color: var(--primary-light); }
        html.dark-mode .ob-email-locked strong { color: #e2e8f0; }
    </style>
</head>
<body>
    <div class="ob-split">
        {{-- ===== LEFT PANEL ===== --}}
        <div class="ob-left">
            <div class="ob-left-content">
                <div class="ob-logo">
                    <img src="{{ asset('assets/sidongan/images/Logo-SIDONGAN-white.svg') }}" alt="Logo SIDONGAN" width="56" height="56">
                </div>

                <h1 class="ob-welcome">Buat Password Baru</h1>
                <p class="ob-subtitle">Anda sedang mengganti password akun SIDONGAN. Buat password yang kuat dan mudah Anda ingat.</p>

                <div class="ob-steps">
                    <div class="ob-step">
                        <div class="ob-step-num">1</div>
                        <div class="ob-step-info">
                            <span class="ob-step-title">Masukkan Password Baru</span>
                            <span class="ob-step-desc">Minimal 8 karakter, kombinasi kuat</span>
                        </div>
                    </div>
                    <div class="ob-step">
                        <div class="ob-step-num">2</div>
                        <div class="ob-step-info">
                            <span class="ob-step-title">Konfirmasi Password</span>
                            <span class="ob-step-desc">Ketik ulang untuk memastikan sama</span>
                        </div>
                    </div>
                    <div class="ob-step">
                        <div class="ob-step-num">3</div>
                        <div class="ob-step-info">
                            <span class="ob-step-title">Masuk Kembali</span>
                            <span class="ob-step-desc">Gunakan password baru Anda</span>
                        </div>
                    </div>
                </div>

                <div class="ob-tips">
                    <div class="ob-tips-title">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a7 7 0 0 1 7 7c0 2.38-1.19 4.47-3 5.74V17a2 2 0 0 1-2 2H10a2 2 0 0 1-2-2v-2.26C6.19 13.47 5 11.38 5 9a7 7 0 0 1 7-7z"/><line x1="9" y1="21" x2="15" y2="21"/></svg>
                        Tips
                    </div>
                    <p>Gunakan kombinasi <strong>huruf besar-kecil, angka, dan simbol</strong> untuk password yang kuat.</p>
                    <p style="margin-top:0.5rem">Jangan bagikan password kepada siapa pun, termasuk administrator.</p>
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
                        <h2>Reset Password SIDONGAN</h2>
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

                @if(session('status'))
                    <div class="ob-alert ob-alert--success">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('sidongan.password.store') }}" class="ob-form" data-validate>
                    @csrf
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">
                    <input type="hidden" name="email" value="{{ old('email', $request->email) }}">

                    {{-- Email terkunci — dibawa otomatis dari link reset --}}
                    <div class="ob-field">
                        <label class="ob-field-label">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            Email Akun
                        </label>
                        <div class="ob-email-locked">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            <div>
                                <strong>{{ old('email', $request->email) }}</strong>
                                <small>Email terkunci — terverifikasi dari link reset</small>
                            </div>
                        </div>
                    </div>

                    {{-- Password Baru --}}
                    <div class="ob-field">
                        <label class="ob-field-label" for="password">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            Password Baru
                        </label>
                        <div class="ob-input-wrap">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                required
                                autocomplete="new-password"
                                placeholder="Minimal 8 karakter"
                                class="ob-input"
                                data-password-strength=""
                                data-confirm-password-source
                            >
                            <button type="button" class="toggle-password" data-toggle-password="password" tabindex="-1" aria-label="Tampilkan atau sembunyikan password">
                                <svg id="eyeOpenPassword" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg class="u-hidden" id="eyeClosedPassword" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                            </button>
                        </div>
                        <div class="ob-strength" id="passwordStrength">
                            <div class="bar" data-index="0"></div>
                            <div class="bar" data-index="1"></div>
                            <div class="bar" data-index="2"></div>
                            <div class="bar" data-index="3"></div>
                        </div>
                        <div class="ob-hint" id="passwordHint"></div>
                    </div>

                    {{-- Konfirmasi Password --}}
                    <div class="ob-field">
                        <label class="ob-field-label" for="password_confirmation">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            Konfirmasi Password
                        </label>
                        <div class="ob-input-wrap">
                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                required
                                autocomplete="new-password"
                                placeholder="Ulangi password baru"
                                class="ob-input"
                                data-confirm-password="[data-confirm-password-source]"
                            >
                            <button type="button" class="toggle-password" data-toggle-password="password_confirmation" tabindex="-1" aria-label="Tampilkan atau sembunyikan konfirmasi password">
                                <svg id="eyeOpenConfirm" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg class="u-hidden" id="eyeClosedConfirm" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                            </button>
                        </div>
                    </div>

                    <div class="ob-actions">
                        <button type="submit" class="ob-btn-primary">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            Reset Password
                        </button>
                        <a href="{{ route('sidongan.login') }}" class="ob-btn-skip">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                            Kembali ke Login SIDONGAN
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/sidongan/js/sidongan-auth-reset-password.js') }}"></script>
    <script src="{{ asset('assets/shared/js/auth-form-validation.js') }}"></script>
</body>
</html>
{{-- Dikembangkan oleh Institut Teknologi Del --}}
