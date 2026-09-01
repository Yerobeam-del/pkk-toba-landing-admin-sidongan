{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lengkapi Profil - SIDONGAN PKK Kabupaten Toba</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/sidongan/images/Logo-SIDONGAN-white.svg') }}">
    <link rel="alternate icon" type="image/svg+xml" href="{{ asset('assets/sidongan/images/Logo-SIDONGAN-white.svg') }}">

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/sidongan-auth/css/auth-onboarding.css') }}">
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
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
            </div>
            <h1>Selamat Datang, {{ $user->name }}! 👋</h1>
            <p>Sebelum mulai, silakan lengkapi profil Anda agar bisa menggunakan SIDONGAN secara optimal.</p>
        </div>

        {{-- Progress Bar --}}
        <div class="progress-section">
            <div class="progress-header">
                <span class="progress-label">Kelengkapan Profil</span>
                <span class="progress-value">{{ $completionPercentage }}%</span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" style="width: {{ $completionPercentage }}%"></div>
            </div>
        </div>

        {{-- Status Message --}}
        @if(session('status'))
            <div class="alert alert-success">
                <div class="alert-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                </div>
                <div class="alert-content">
                    <span>{{ session('status') }}</span>
                </div>
            </div>
        @endif

        {{-- Validation Errors --}}
        @if($errors->any())
            <div class="alert alert-error">
                <div class="alert-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="15" y1="9" x2="9" y2="15"/>
                        <line x1="9" y1="9" x2="15" y2="15"/>
                    </svg>
                </div>
                <div class="alert-content">
                    <strong>Terjadi kesalahan:</strong>
                    <ul class="error-list">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- What's Missing Info --}}
        <div class="info-box">
            <div class="info-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="16" x2="12" y2="12"/>
                    <line x1="12" y1="8" x2="12.01" y2="8"/>
                </svg>
            </div>
            <div class="info-content">
                <strong>Yang perlu dilengkapi:</strong>
                <ul class="checklist">
                    @if(in_array('phone_number', $missingFields))
                        <li class="missing">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="15" y1="9" x2="9" y2="15"/>
                                <line x1="9" y1="9" x2="15" y2="15"/>
                            </svg>
                            Nomor Telepon — untuk notifikasi WhatsApp
                        </li>
                    @else
                        <li class="completed">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                <polyline points="22 4 12 14.01 9 11.01"/>
                            </svg>
                            Nomor Telepon
                        </li>
                    @endif

                    @if(in_array('personal_email', $missingFields))
                        <li class="missing">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="15" y1="9" x2="9" y2="15"/>
                                <line x1="9" y1="9" x2="15" y2="15"/>
                            </svg>
                            Email Pribadi — untuk reset password
                        </li>
                    @else
                        <li class="completed">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                <polyline points="22 4 12 14.01 9 11.01"/>
                            </svg>
                            Email Pribadi
                        </li>
                    @endif
                </ul>
            </div>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('sidongan.onboarding.store') }}" class="form-card">
            @csrf

            {{-- Phone Number (only if missing) --}}
            @if(in_array('phone_number', $missingFields))
                <div class="form-group">
                    <label for="phone_number">
                        Nomor Telepon
                        <span class="required">*</span>
                    </label>
                    <div class="input-wrapper">
                        <div class="input-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>
                            </svg>
                        </div>
                        <input
                            type="tel"
                            id="phone_number"
                            name="phone_number"
                            value="{{ old('phone_number', $user->phone_number) }}"
                            required
                            placeholder="0812 3456 7890"
                            pattern="[0-9+\-\s()]+"
                            minlength="10"
                            maxlength="15"
                        >
                    </div>
                    <span class="input-hint">Nomor WhatsApp aktif untuk notifikasi</span>
                </div>
            @endif

            {{-- Personal Email (only if missing) --}}
            @if(in_array('personal_email', $missingFields))
                <div class="form-group">
                    <label for="personal_email">
                        Email Pribadi
                        <span class="required">*</span>
                    </label>
                    <div class="input-wrapper">
                        <div class="input-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                <polyline points="22,6 12,13 2,6"/>
                            </svg>
                        </div>
                        <input
                            type="email"
                            id="personal_email"
                            name="personal_email"
                            value="{{ old('personal_email', $user->personal_email) }}"
                            required
                            placeholder="namaketua@gmail.com"
                        >
                    </div>
                    <span class="input-hint">Email aktif untuk reset password (Gmail, Yahoo, dll)</span>
                </div>
            @endif

            <button type="submit" class="btn-primary">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
                Simpan & Lanjutkan
            </button>
        </form>

        {{-- Skip --}}
        <a href="{{ route('sidongan.onboarding.skip') }}" class="btn-skip">
            Lewati — nanti saja
        </a>

        {{-- Why important --}}
        <div class="why-box">
            <div class="why-title">Mengapa ini penting?</div>
            <div class="why-items">
                @if(in_array('phone_number', $missingFields))
                    <div class="why-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>
                        </svg>
                        <span>Notifikasi WhatsApp untuk surat masuk/keluar</span>
                    </div>
                @endif
                @if(in_array('personal_email', $missingFields))
                    <div class="why-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                        <span>Reset password jika lupa</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Footer --}}
        <div class="footer">
            <span>Butuh bantuan? Hubungi administrator</span>
            <span class="footer-divider">•</span>
            <span>&copy; {{ date('Y') }} IT Del x PKK Toba</span>
        </div>
    </div>
</body>
</html>
{{-- Dikembangkan oleh Institut Teknologi Del --}}
