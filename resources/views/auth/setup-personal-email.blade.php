<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Email Pribadi - PKK Kabupaten Toba</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/admin/images/Logo_Admin-Panel.svg') }}">
    <link rel="alternate icon" type="image/svg+xml" href="{{ asset('assets/admin/images/Logo_Admin-Panel.svg') }}">

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/shared/css/auth-setup-personal-email.css') }}">
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
                <h1>Setup Email Pribadi</h1>
                <p>Daftarkan email aktif Anda untuk fitur lupa password</p>
            </div>

            {{-- Alert Messages --}}
            @if(isset($needs_verification) && $needs_verification && isset($existing_email))
                <div class="alert alert-warning">
                    <div class="alert-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                    </div>
                    <div class="alert-content">
                        <strong>Email pribadi sudah didaftarkan!</strong>
                        <span>Email <strong>{{ $existing_email }}</strong> sudah disimpan, namun <strong>belum diverifikasi</strong>.</span>
                        <a href="{{ route('personal-email.notice') }}">Klik di sini untuk kirim ulang verifikasi →</a>
                        <span class="alert-sub">Atau ganti dengan email lain di bawah.</span>
                    </div>
                </div>
            @else
                <div class="alert alert-info">
                    <div class="alert-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="16" x2="12" y2="12"/>
                            <line x1="12" y1="8" x2="12.01" y2="8"/>
                        </svg>
                    </div>
                    <div class="alert-content">
                        <strong>Selamat datang, {{ Auth::user()->name }}!</strong>
                        <span>Untuk keamanan akun, silakan daftarkan <strong>email pribadi</strong> Anda (Gmail, Yahoo, dll). Email ini akan digunakan untuk mengirim link <strong>reset password</strong> jika Anda lupa password.</span>
                        <span class="alert-sub">Email login <strong>{{ Auth::user()->email }}</strong> tidak memiliki kotak surat, sehingga tidak bisa menerima link reset password.</span>
                    </div>
                </div>
            @endif

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
                        @foreach($errors->all() as $error)
                            <span>{{ $error }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('personal-email.store') }}" class="form-card">
                @csrf

                <div class="form-group">
                    <label for="personal_email">Alamat Email Pribadi</label>
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
                            value="{{ old('personal_email') }}"
                            required
                            autofocus
                            placeholder="namaketua@gmail.com"
                        >
                    </div>
                    <span class="input-hint">Gunakan email aktif yang bisa Anda akses kapan saja</span>
                </div>

                <button type="submit" class="btn-primary">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="22" y1="2" x2="11" y2="13"/>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                    </svg>
                    Kirim Verifikasi
                </button>
            </form>

            {{-- Skip --}}
            <a href="{{ route('personal-email.skip') }}" class="btn-skip">
                Lewati — nanti saja
            </a>

            {{-- Footer --}}
            <div class="footer">
                <span>Butuh bantuan? Hubungi administrator</span>
                <span class="footer-divider">•</span>
                <span>&copy; {{ date('Y') }} TP-PKK Kabupaten Toba</span>
            </div>
        </div>
    </div>
</body>
</html>
