{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Email Pribadi - PKK Kabupaten Toba</title>

    {{-- Favicon untuk Tab Browser --}}
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/admin/images/Logo_Admin-Panel.svg') }}">
    <link rel="alternate icon" type="image/svg+xml" href="{{ asset('assets/admin/images/Logo_Admin-Panel.svg') }}">

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <link rel="stylesheet" href="{{ asset('assets/shared/css/auth-setup-personal-email.css') }}">

</head>
<body>
    <div class="container">
        <div class="header">
            <div class="icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                    <polyline points="22,6 12,13 2,6"/>
                </svg>
            </div>
            <h1>Setup Email Pribadi</h1>
            <p>Daftarkan email pribadi untuk fitur lupa password</p>
        </div>

        <div class="body">
            @if(isset($needs_verification) && $needs_verification && isset($existing_email))
                <div class="info-box" style="background:#fefce8;border-color:#fde68a;color:#92400e;">
                    <strong>Email pribadi sudah didaftarkan!</strong>
                    <br><br>
                    Email <strong>{{ $existing_email }}</strong> sudah disimpan, namun <strong>belum diverifikasi</strong>.
                    <br><br>
                    <a href="{{ route('personal-email.notice') }}" style="color:#14b8a6;font-weight:600;">
                        Klik di sini untuk kirim ulang verifikasi →
                    </a>
                    <br><br>
                    Atau ganti dengan email lain di bawah.
                </div>
            @else
                <div class="info-box">
                    <strong>Selamat datang, {{ Auth::user()->name }}!</strong>
                    <br><br>
                    Untuk keamanan akun, silakan daftarkan <strong>email pribadi</strong> Anda (Gmail, Yahoo, dll).
                    Email ini akan digunakan untuk mengirim link <strong>reset password</strong> jika Anda lupa password.
                    <br><br>
                    Email login <strong>{{ Auth::user()->email }}</strong> tidak memiliki kotak surat,
                    sehingga tidak bisa menerima link reset password.
                </div>
            @endif

            @if($errors->any())
                <div class="error-message">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('personal-email.store') }}">
                @csrf

                <div class="form-group">
                    <label for="personal_email">Alamat Email Pribadi</label>
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

                <button type="submit" class="btn-primary">
                    KIRIM VERIFIKASI
                </button>
            </form>

            <a href="{{ route('personal-email.skip') }}" class="btn-skip">
                Lewati — nanti saja
            </a>
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} IT Del x PKK Toba. All rights reserved.
        </div>
    </div>
</body>
</html>
{{-- Dikembangkan oleh Institut Teknologi Del --}}
