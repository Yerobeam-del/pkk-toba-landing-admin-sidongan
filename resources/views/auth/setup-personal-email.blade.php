<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Email Pribadi - PKK Kabupaten Toba</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #14b8a6;
            --primary-dark: #0d9488;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f1f5f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .container {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            overflow: hidden;
            width: 100%;
            max-width: 480px;
            animation: slideUp 0.5s ease;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .header {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            padding: 2.5rem 2rem;
            text-align: center;
            color: #fff;
        }

        .header .icon {
            width: 64px;
            height: 64px;
            background: rgba(255,255,255,0.15);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }

        .header h1 {
            font-size: 1.35rem;
            font-weight: 700;
            margin-bottom: 0.35rem;
        }

        .header p {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .body { padding: 2rem; }

        .info-box {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            color: #0369a1;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            line-height: 1.6;
        }

        .form-group { margin-bottom: 1.25rem; }

        .form-group label {
            display: block;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .form-group input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid var(--border);
            border-radius: 10px;
            font-family: inherit;
            font-size: 0.95rem;
            transition: all 0.2s;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(20, 184, 166, 0.1);
        }

        .btn-primary {
            width: 100%;
            padding: 0.875rem;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            border: none;
            border-radius: 10px;
            font-family: inherit;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 4px 12px rgba(20, 184, 166, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(20, 184, 166, 0.4);
        }

        .btn-skip {
            display: block;
            text-align: center;
            margin-top: 1.25rem;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            transition: color 0.2s;
        }

        .btn-skip:hover {
            color: var(--primary);
            text-decoration: underline;
        }

        .error-message {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 0.875rem 1rem;
            border-radius: 8px;
            margin-bottom: 1.25rem;
            font-size: 0.9rem;
        }

        .footer {
            text-align: center;
            padding: 1.25rem 2rem;
            border-top: 1px solid var(--border);
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        @media (max-width: 480px) {
            .container { max-width: 100%; }
            .header { padding: 2rem 1.5rem; }
            .body { padding: 1.5rem; }
        }
    </style>
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
