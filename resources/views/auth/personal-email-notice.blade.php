<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email Pribadi - PKK Kabupaten Toba</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/admin/images/Logo_Admin-Panel.svg') }}">
    <link rel="alternate icon" type="image/svg+xml" href="{{ asset('assets/admin/images/Logo_Admin-Panel.svg') }}">

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/shared/css/auth-personal-email-notice.css') }}">
</head>
<body>
    <div class="page-wrapper">
        {{-- Background Pattern --}}
        <div class="bg-pattern"></div>

        {{-- Main Content --}}
        <div class="content">
            {{-- Logo --}}
            <div class="logo">
                <img src="{{ asset('assets/admin/images/Logo_Admin-Panel.svg') }}" alt="PKK Logo" width="72" height="72">
            </div>

            {{-- Header --}}
            <div class="header">
                <div class="icon-circle">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                        <polyline points="22,6 12,13 2,6"/>
                    </svg>
                </div>
                <h1>Cek Email Anda</h1>
                <p>Kami telah mengirimkan link verifikasi ke email pribadi Anda</p>
            </div>

            {{-- Alert Messages --}}
            @if(session('success'))
                <div class="alert alert-success">
                    <div class="alert-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                    </div>
                    <div class="alert-content">
                        {!! session('success') !!}
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error">
                    <div class="alert-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="15" y1="9" x2="9" y2="15"/>
                            <line x1="9" y1="9" x2="15" y2="15"/>
                        </svg>
                    </div>
                    <div class="alert-content">
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            {{-- Email Info Card --}}
            <div class="info-card">
                <div class="info-card-header">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                        <polyline points="22,6 12,13 2,6"/>
                    </svg>
                    <span>Dikirim ke:</span>
                </div>
                <div class="email-display">
                    {{ $personal_email }}
                </div>
            </div>

            {{-- Steps Card --}}
            <div class="steps-card">
                <div class="steps-header">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="16" x2="12" y2="12"/>
                        <line x1="12" y1="8" x2="12.01" y2="8"/>
                    </svg>
                    <span>Langkah Selanjutnya:</span>
                </div>
                <ol class="steps-list">
                    <li>
                        <div class="step-num">1</div>
                        <div class="step-text">
                            <strong>Buka kotak masuk email</strong>
                            <span>Gmail, Yahoo, atau penyedia email lainnya</span>
                        </div>
                    </li>
                    <li>
                        <div class="step-num">2</div>
                        <div class="step-text">
                            <strong>Cari email dari PKK</strong>
                            <span>Subjek: "Verifikasi Email Pribadi"</span>
                        </div>
                    </li>
                    <li>
                        <div class="step-num">3</div>
                        <div class="step-text">
                            <strong>Klik tombol verifikasi</strong>
                            <span>Tombol "Verifikasi Email" di dalam email</span>
                        </div>
                    </li>
                    <li>
                        <div class="step-num">4</div>
                        <div class="step-text">
                            <strong>Selesai!</strong>
                            <span>Otomatis diarahkan ke Dashboard</span>
                        </div>
                    </li>
                </ol>
            </div>

            {{-- Action Buttons --}}
            <div class="btn-group">
                <form method="POST" action="{{ route('personal-email.resend') }}">
                    @csrf
                    <button type="submit" class="btn-primary">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="23 4 23 10 17 10"/>
                            <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>
                        </svg>
                        Kirim Ulang Verifikasi
                    </button>
                </form>

                <a href="{{ route('personal-email.setup') }}" class="btn-outline">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                    Ganti Email Pribadi
                </a>
            </div>

            {{-- Skip --}}
            <a href="{{ route('personal-email.skip') }}" class="btn-skip">
                Lewati — nanti saja
            </a>

            {{-- Footer --}}
            <div class="footer">
                <span>&copy; {{ date('Y') }} TP-PKK Kabupaten Toba</span>
            </div>
        </div>
    </div>
</body>
</html>
