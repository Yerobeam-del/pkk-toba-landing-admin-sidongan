{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="stylesheet" href="{{ asset('assets/shared/css/utilities.css') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Admin Panel PKK Kabupaten Toba</title>

    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/admin/images/Logo_Admin-Panel.svg') }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/sidongan-auth/css/auth-onboarding.css') }}">

    <style>
        :root {
            --gradient-start: #7c2d12;
            --gradient-mid: #c2410c;
            --gradient-end: #ea580c;
        }
    </style>
</head>
<body>
    <div class="ob-split">
        {{-- ===== LEFT PANEL ===== --}}
        <div class="ob-left">
            <div class="ob-left-content">
                <div class="ob-logo">
                    <img src="{{ asset('assets/admin/images/Logo-PKK-Toba-White.png') }}" alt="Logo PKK" width="56" height="56">
                </div>

                <h1 class="ob-welcome">Lupa Password?</h1>
                <p class="ob-subtitle">Jangan khawatir! Masukkan email akun admin Anda dan kami akan mengirimkan tautan untuk mereset password.</p>

                <div class="ob-steps">
                    <div class="ob-step">
                        <div class="ob-step-num">1</div>
                        <div class="ob-step-info">
                            <span class="ob-step-title">Masukkan Email</span>
                            <span class="ob-step-desc">Email yang terdaftar di akun admin</span>
                        </div>
                    </div>
                    <div class="ob-step">
                        <div class="ob-step-num">2</div>
                        <div class="ob-step-info">
                            <span class="ob-step-title">Cek Email</span>
                            <span class="ob-step-desc">Link reset akan dikirim ke email Anda</span>
                        </div>
                    </div>
                    <div class="ob-step">
                        <div class="ob-step-num">3</div>
                        <div class="ob-step-info">
                            <span class="ob-step-title">Buat Password Baru</span>
                            <span class="ob-step-desc">Klik link dan masukkan password baru</span>
                        </div>
                    </div>
                </div>

                <div class="ob-tips">
                    <div class="ob-tips-title">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a7 7 0 0 1 7 7c0 2.38-1.19 4.47-3 5.74V17a2 2 0 0 1-2 2H10a2 2 0 0 1-2-2v-2.26C6.19 13.47 5 11.38 5 9a7 7 0 0 1 7-7z"/><line x1="9" y1="21" x2="15" y2="21"/></svg>
                        Tips
                    </div>
                    <p>Pastikan email yang dimasukkan adalah email yang terdaftar di akun admin Anda. Link reset berlaku selama 60 menit.</p>
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
                        <h2>Reset Password</h2>
                    </div>
                </div>

                {{-- Success --}}
                @if(session('status'))
                    <div class="ob-alert ob-alert--success">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                {{-- Errors --}}
                @if($errors->any())
                    <div class="ob-alert ob-alert--error">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                        <div>
                            @foreach($errors->all() as $error)
                                <span>{{ $error }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Form --}}
                <form method="POST" action="{{ route('password.email') }}" class="ob-form" id="forgotForm">
                    @csrf

                    <div class="ob-field">
                        <label class="ob-field-label" for="email">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            Email Akun Admin
                        </label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="email"
                            placeholder="nama@email.com"
                            class="ob-input"
                        >
                        <span class="ob-field-hint">Email yang terdaftar di akun admin panel</span>
                    </div>

                    <div class="ob-actions">
                        <button type="submit" class="ob-btn-primary" id="submitBtn">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            Kirim Link Reset
                        </button>
                        <a href="{{ route('login') }}" class="ob-btn-skip">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                            Kembali ke Login
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    (function() {
        var form = document.getElementById('forgotForm');
        var btn = document.getElementById('submitBtn');
        if (form && btn) {
            form.addEventListener('submit', function() {
                btn.disabled = true;
                btn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation:spin 1s linear infinite"><circle cx="12" cy="12" r="10" stroke-dasharray="30 60"/></svg> Memproses...';
            });
        }
    })();
    </script>
    <style>@keyframes spin{from{transform:rotate(0deg)}to{transform:rotate(360deg)}}</style>
</body>
</html>
{{-- Dikembangkan oleh Institut Teknologi Del --}}
