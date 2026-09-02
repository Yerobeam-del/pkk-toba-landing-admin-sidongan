{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - SIDONGAN PKK Kabupaten Toba</title>

    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/sidongan/images/Logo-SIDONGAN-white.svg') }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/sidongan-auth/css/auth-onboarding.css') }}">
</head>
<body>
    <div class="ob-split">
        {{-- ===== LEFT PANEL ===== --}}
        <div class="ob-left">
            <div class="ob-left-content">
                <div class="ob-logo">
                    <img src="{{ asset('assets/sidongan/images/Logo-SIDONGAN-white.svg') }}" alt="Logo SIDONGAN" width="56" height="56">
                </div>

                <h1 class="ob-welcome">Lupa Password?</h1>
                <p class="ob-subtitle">Jangan khawatir! Masukkan email akun Anda dan kami akan membantu mereset password.</p>

                <div class="ob-steps">
                    <div class="ob-step">
                        <div class="ob-step-num">1</div>
                        <div class="ob-step-info">
                            <span class="ob-step-title">Masukkan Email Akun</span>
                            <span class="ob-step-desc">Email yang digunakan untuk login</span>
                        </div>
                    </div>
                    <div class="ob-step">
                        <div class="ob-step-num">2</div>
                        <div class="ob-step-info">
                            <span class="ob-step-title">Sistem Mendeteksi</span>
                            <span class="ob-step-desc">Apakah akun memiliki email pribadi</span>
                        </div>
                    </div>
                    <div class="ob-step">
                        <div class="ob-step-num">3</div>
                        <div class="ob-step-info">
                            <span class="ob-step-title">Reset Password</span>
                            <span class="ob-step-desc">Link dikirim ke email pribadi Anda</span>
                        </div>
                    </div>
                </div>

                <div class="ob-tips">
                    <div class="ob-tips-title">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a7 7 0 0 1 7 7c0 2.38-1.19 4.47-3 5.74V17a2 2 0 0 1-2 2H10a2 2 0 0 1-2-2v-2.26C6.19 13.47 5 11.38 5 9a7 7 0 0 1 7-7z"/><line x1="9" y1="21" x2="15" y2="21"/></svg>
                        Tips
                    </div>
                    <p>Email pribadi (Gmail, Yahoo, dll) diperlukan untuk menerima link reset password. Jika belum punya, hubungi administrator.</p>
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
                @if(session('status') === 'success')
                    <div class="ob-alert ob-alert--success">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        <div>
                            <strong>Berhasil!</strong>
                            <span>{{ session('status_message') }}</span>
                            <span style="display:block;font-size:0.8rem;opacity:0.8;margin-top:0.25rem">Cek inbox atau folder spam email Anda.</span>
                        </div>
                    </div>
                @endif

                {{-- No personal email --}}
                @if(session('status') === 'no_personal_email')
                    <div class="ob-alert ob-alert--warning">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M16 16s-1.5-2-4-2-4 2-4 2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>
                        <div>
                            <strong>Belum ada email pribadi</strong>
                            <span>{{ session('status_message') }}</span>
                            <div style="margin-top:0.75rem;padding:0.75rem;background:rgba(251,191,36,0.1);border-radius:8px;border:1px solid rgba(251,191,36,0.3)">
                                <span style="display:flex;align-items:center;gap:0.5rem;font-size:0.85rem">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                    Hubungi <strong>Administrator</strong> untuk mereset password Anda
                                </span>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Need verification --}}
                @if(session('status') === 'need_verification')
                    <div class="ob-alert ob-alert--warning">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <div>
                            <strong>Email pribadi belum diverifikasi</strong>
                            <span>{{ session('status_message') }}</span>
                            <div style="margin-top:0.75rem;padding:0.75rem;background:rgba(251,191,36,0.1);border-radius:8px;border:1px solid rgba(251,191,36,0.3)">
                                <span style="display:flex;align-items:center;gap:0.5rem;font-size:0.85rem">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                    Verifikasi email pribadi Anda terlebih dahulu, atau hubungi administrator
                                </span>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Error --}}
                @if($errors->has('email'))
                    <div class="ob-alert ob-alert--error">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                        <span>{{ $errors->first('email') }}</span>
                    </div>
                @endif

                {{-- Form --}}
                <form method="POST" action="{{ route('sidongan.password.email') }}" class="ob-form" id="forgotForm">
                    @csrf

                    <div class="ob-field">
                        <label class="ob-field-label" for="email">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            Email Akun SIDONGAN
                        </label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            placeholder="nama@pkk-toba.id"
                            class="ob-input"
                        >
                        <span class="ob-field-hint">Masukkan email kantor yang digunakan untuk login SIDONGAN</span>
                    </div>

                    <div class="ob-actions">
                        <button type="submit" class="ob-btn-primary" id="submitBtn">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            Cek & Kirim Link Reset
                        </button>
                        <a href="{{ route('sidongan.login') }}" class="ob-btn-skip">
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
