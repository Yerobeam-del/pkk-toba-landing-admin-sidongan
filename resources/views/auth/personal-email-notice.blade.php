{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email Pribadi - PKK Kabupaten Toba</title>

    {{-- Favicon untuk Tab Browser --}}
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/admin/images/Logo_Admin-Panel.svg') }}">
    <link rel="alternate icon" type="image/svg+xml" href="{{ asset('assets/admin/images/Logo_Admin-Panel.svg') }}">

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <link rel="stylesheet" href="{{ asset('assets/shared/css/auth-personal-email-notice.css') }}">

</head>
<body>
    <div class="container">
        <div class="header">
            <div class="mail-icon">
                <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                    <polyline points="22,6 12,13 2,6"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
            </div>
            <h1>Cek Email Anda</h1>
            <p>Kami telah mengirimkan link verifikasi ke email pribadi Anda</p>
        </div>

        <div class="body">
            @if(session('success'))
                <div class="success-message">
                    {!! session('success') !!}
                </div>
            @endif

            @if(session('error'))
                <div class="error-message" style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626;padding:1rem;border-radius:10px;margin-bottom:1.5rem;font-size:0.9rem;line-height:1.6;">
                    {{ session('error') }}
                </div>
            @endif

            <div class="info-card">
                <strong style="font-size:0.9rem; color:var(--text-dark);">Dikirim ke:</strong>
                <div class="email-display">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#14b8a6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                        <polyline points="22,6 12,13 2,6"/>
                    </svg>
                    <span>{{ $personal_email }}</span>
                </div>
            </div>

            <div class="info-card">
                <strong style="font-size:0.9rem; color:var(--text-dark);">Langkah selanjutnya:</strong>
                <ol class="steps">
                    <li>
                        <span class="step-number">1</span>
                        <span>Buka kotak masuk email <strong>{{ $personal_email }}</strong> Anda (Gmail, Yahoo, dll)</span>
                    </li>
                    <li>
                        <span class="step-number">2</span>
                        <span>Cari email dari <strong>PKK Kabupaten Toba</strong> dengan subjek <strong>"Verifikasi Email Pribadi"</strong></span>
                    </li>
                    <li>
                        <span class="step-number">3</span>
                        <span>Klik tombol <strong>"Verifikasi Email"</strong> di dalam email tersebut</span>
                    </li>
                    <li>
                        <span class="step-number">4</span>
                        <span>Setelah diverifikasi, Anda akan otomatis diarahkan ke Dashboard!</span>
                    </li>
                </ol>
            </div>

            <div class="btn-group">
                <form method="POST" action="{{ route('personal-email.resend') }}" style="width:100%;">
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

                <a href="{{ route('personal-email.skip') }}" class="btn-outline" style="border-color:transparent; background:transparent;">
                    Lewati — nanti saja
                </a>
            </div>
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} IT Del x PKK Toba. All rights reserved.
        </div>
    </div>
</body>
</html>
{{-- Dikembangkan oleh Institut Teknologi Del --}}
