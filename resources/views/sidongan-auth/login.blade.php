{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
<!DOCTYPE html>
<html lang="id">
<head>
    
    <link rel="stylesheet" href="{{ asset('assets/shared/css/utilities.css') }}">
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIDONGAN</title>

    {{-- Favicon untuk Tab Browser (Format SVG) - Theme Aware --}}
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/sidongan/images/Logo-SIDONGAN-ThemeAware.svg') }}">

    {{-- Fallback untuk browser lama yang tidak mendukung SVG --}}
    <link rel="alternate icon" type="image/png" href="{{ asset('assets/admin/images/Logo-PKK-Transparent.png') }}">

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <link rel="stylesheet" href="{{ asset('assets/sidongan-auth/css/auth-login.css') }}">

</head>
<body>
    <div class="login-wrapper">
        {{-- Left Side - Branding --}}
        <div class="login-branding">
            <div class="logos-container">
                <div class="logo-circle">
                    <img src="{{ asset('assets/admin/images/Logo-Kabupaten-Toba-Transparent.png') }}" alt="Logo Kabupaten Toba">
                </div>
                <div class="logo-circle">
                    <img src="{{ asset('assets/admin/images/Logo-PKK-Transparent.png') }}" alt="Logo PKK">
                </div>
            </div>

            <div class="branding-content">
                <h1 class="branding-title">SIDONGAN</h1>
                <p class="branding-subtitle">Sistem Informasi Dokumen Organisasi Agenda dan Naskah</p>
                <p class="branding-tagline">PKK Kabupaten Toba</p>
            </div>
        </div>

        {{-- Right Side - Login Form --}}
        <div class="login-form-wrapper">
            <div class="login-header">
                <h2>Selamat Datang</h2>
                <p>Silahkan login untuk melanjutkan</p>
            </div>

            <form method="POST" action="{{ route('sidongan.login') }}">
                @csrf

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                @if (session('status'))
                    <div class="alert alert-success" style="background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;border-radius:8px;padding:0.75rem 1rem;font-size:0.85rem;margin-bottom:1.125rem">
                        <div class="u-flex-center-gap-2">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                <polyline points="22 4 12 14.01 9 11.01"/>
                            </svg>
                            {{ session('status') }}
                        </div>
                    </div>
                @endif

                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <div class="input-wrapper">
                        <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="nama@email.com" required autofocus autocomplete="email">
                        <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-wrapper">
                        <!-- Tambahkan style padding-right agar teks tidak menabrak ikon mata -->
                        <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required autocomplete="current-password" style="padding-right: 2.5rem;">

                        <!-- Ikon Gembok (Kiri) -->
                        <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>

                        <!-- Tombol Toggle Password (Kanan) -->
                        <button type="button" id="togglePassword" style="position: absolute; right: 0.875rem; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--text-muted); display: flex; align-items: center; justify-content: center; padding: 0; transition: color 0.2s ease;">
                            <!-- Ikon Mata Terbuka (Default) -->
                            <svg id="eyeOpen" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <!-- Ikon Mata Tertutup (Hidden) -->
                            <svg class="u-hidden" id="eyeClosed" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                <line x1="1" y1="1" x2="23" y2="23"></line>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="form-options">
                    <label class="custom-checkbox">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span class="checkmark">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </span>
                        <span class="check-text">Ingat saya</span>
                    </label>
                    <a class="forgot-password" href="https://{{ config('app.landing_domain') ?: 'tp-pkk.tobakab.go.id' }}/forgot-password" target="_blank" rel="noopener noreferrer">
                        Lupa password?
                    </a>
                </div>

                <button type="submit" class="btn-login">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                        <polyline points="10 17 15 12 10 7"></polyline>
                        <line x1="15" y1="12" x2="3" y2="12"></line>
                    </svg>
                    <span>MASUK</span>
                </button>

                <div class="divider"><span>Secure Login</span></div>

                <div class="footer-security">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    </svg>
                    <span>Sistem Resmi Pemkab Toba</span>
                </div>
            </form>
        </div>
    </div>

    <script src="{{ asset('assets/sidongan/js/sidongan-auth-login.js') }}"></script>
    <script src="{{ asset('assets/shared/js/email-typo-checker.js') }}"></script>
    <script>
    EmailTypoChecker.attach('#email');
    </script>

</body>
</html>
{{-- Dikembangkan oleh Institut Teknologi Del --}}
