{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - SIDONGAN PKK Kabupaten Toba</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/sidongan/images/Logo-SIDONGAN-white.svg') }}">
    <link rel="alternate icon" type="image/svg+xml" href="{{ asset('assets/sidongan/images/Logo-SIDONGAN-white.svg') }}">

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/sidongan-auth/css/auth-forgot-password.css') }}">
</head>
<body>
    {{-- Background Pattern --}}
    <div class="bg-pattern"></div>

    {{-- Main Content --}}
    <div class="content">
        {{-- Logo --}}
        <div class="logo">
            <img src="{{ asset('assets/sidongan/images/Logo-SIDONGAN-white.svg') }}" alt="Logo SIDONGAN" width="80" height="80">
        </div>

        {{-- Header --}}
        <div class="header">
            <div class="icon-circle">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
            </div>
            <h1>Lupa Password?</h1>
            <p>Masukkan email akun SIDONGAN Anda. Kami akan mendeteksi apakah akun sudah memiliki email pribadi untuk reset password.</p>
        </div>

        {{-- Success: Reset link sent --}}
        @if(session('status') === 'success')
            <div class="alert alert-success">
                <div class="alert-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                </div>
                <div class="alert-content">
                    <strong>Berhasil!</strong>
                    <span>{{ session('status_message') }}</span>
                    <span class="alert-sub">Cek inbox atau folder spam email Anda.</span>
                </div>
            </div>
        @endif

        {{-- No personal email --}}
        @if(session('status') === 'no_personal_email')
            <div class="alert alert-danger">
                <div class="alert-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M16 16s-1.5-2-4-2-4 2-4 2"/>
                        <line x1="9" y1="9" x2="9.01" y2="9"/>
                        <line x1="15" y1="9" x2="15.01" y2="9"/>
                    </svg>
                </div>
                <div class="alert-content">
                    <strong>Belum ada email pribadi</strong>
                    <span>{{ session('status_message') }}</span>
                    <span class="alert-sub">Email pribadi diperlukan agar Anda bisa menerima link reset password secara otomatis.</span>
                    <div class="contact-admin-box">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>
                        </svg>
                        <span>Hubungi <strong>Administrator</strong> untuk mereset password Anda</span>
                    </div>
                </div>
            </div>
        @endif

        {{-- Need verification --}}
        @if(session('status') === 'need_verification')
            <div class="alert alert-warning">
                <div class="alert-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                </div>
                <div class="alert-content">
                    <strong>Email pribadi belum diverifikasi</strong>
                    <span>{{ session('status_message') }}</span>
                    <span class="alert-sub">Verifikasi email pribadi Anda terlebih dahulu, atau hubungi administrator.</span>
                    <div class="contact-admin-box">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>
                        </svg>
                        <span>Hubungi <strong>Administrator</strong> untuk mereset password Anda</span>
                    </div>
                </div>
            </div>
        @endif

        {{-- Generic security message --}}
        @if($errors->has('email'))
            <div class="alert alert-info">
                <div class="alert-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="16" x2="12" y2="12"/>
                        <line x1="12" y1="8" x2="12.01" y2="8"/>
                    </svg>
                </div>
                <div class="alert-content">
                    <span>{{ $errors->first('email') }}</span>
                </div>
            </div>
        @endif

        {{-- Form --}}
        <form method="POST" action="{{ route('sidongan.password.email') }}" class="form-card" id="forgotForm">
            @csrf

            <div class="form-group">
                <label for="email">Email Akun SIDONGAN</label>
                <div class="input-wrapper">
                    <div class="input-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    </div>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        placeholder="nama@pkk-toba.id"
                    >
                </div>
                <span class="input-hint">Masukkan email kantor yang digunakan untuk login SIDONGAN</span>
            </div>

            <button type="submit" class="btn-primary" id="submitBtn">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                Cek &amp; Kirim Link Reset
            </button>
        </form>

        {{-- How it works --}}
        <div class="how-it-works">
            <div class="how-title">Bagaimana cara kerjanya?</div>
            <div class="how-steps">
                <div class="how-step">
                    <div class="step-number">1</div>
                    <div class="step-text">Masukkan email akun SIDONGAN Anda</div>
                </div>
                <div class="how-step">
                    <div class="step-number">2</div>
                    <div class="step-text">Sistem mendeteksi apakah akun memiliki email pribadi</div>
                </div>
                <div class="how-step">
                    <div class="step-number">3</div>
                    <div class="step-text">Link reset dikirim ke email pribadi, atau Anda diarahkan ke admin</div>
                </div>
            </div>
        </div>

        {{-- Back to Login --}}
        <a href="{{ route('sidongan.login') }}" class="link-back">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6"/>
            </svg>
            Kembali ke Login SIDONGAN
        </a>

        {{-- Footer --}}
        <div class="footer">
            <span>Butuh bantuan? Hubungi administrator</span>
            <span class="footer-divider">•</span>
            <span>&copy; {{ date('Y') }} IT Del x PKK Toba</span>
        </div>
    </div>

    <script>
    (function() {
        const form = document.getElementById('forgotForm');
        const btn = document.getElementById('submitBtn');

        if (form && btn) {
            form.addEventListener('submit', function() {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner"></span> Memproses...';
            });
        }
    })();
    </script>
</body>
</html>
{{-- Dikembangkan oleh Institut Teknologi Del --}}
