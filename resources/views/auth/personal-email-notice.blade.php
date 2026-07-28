<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email Pribadi - PKK Kabupaten Toba</title>

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

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        .header {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            padding: 2.5rem 2rem;
            text-align: center;
            color: #fff;
        }

        .header .mail-icon {
            width: 72px;
            height: 72px;
            background: rgba(255,255,255,0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
            animation: float 2.5s ease-in-out infinite;
        }

        .header h1 {
            font-size: 1.35rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .header p {
            font-size: 0.9rem;
            opacity: 0.9;
            line-height: 1.6;
        }

        .body { padding: 2rem; }

        .success-message {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            line-height: 1.6;
        }

        .info-card {
            background: #f8fafc;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
        }

        .info-card .email-display {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            background: #fff;
            border: 2px dashed var(--border);
            border-radius: 8px;
            margin: 0.75rem 0;
        }

        .info-card .email-display svg {
            flex-shrink: 0;
        }

        .info-card .email-display span {
            font-weight: 600;
            color: var(--text-dark);
            word-break: break-all;
        }

        .steps {
            list-style: none;
            padding: 0;
        }

        .steps li {
            display: flex;
            gap: 0.75rem;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--border);
            font-size: 0.9rem;
            color: var(--text-dark);
            line-height: 1.5;
        }

        .steps li:last-child {
            border-bottom: none;
        }

        .steps li .step-number {
            width: 26px;
            height: 26px;
            background: var(--primary);
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: 700;
            flex-shrink: 0;
            margin-top: 0.1rem;
        }

        .btn-group {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            margin-top: 1.5rem;
        }

        .btn-primary {
            width: 100%;
            padding: 0.875rem;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            border: none;
            border-radius: 10px;
            font-family: inherit;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            box-shadow: 0 4px 12px rgba(20, 184, 166, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(20, 184, 166, 0.4);
        }

        .btn-outline {
            width: 100%;
            padding: 0.75rem;
            background: #fff;
            color: var(--text-muted);
            border: 2px solid var(--border);
            border-radius: 10px;
            font-family: inherit;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-outline:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: #f0fdfa;
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
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="{{ $personal_email ?? Auth::user()->personal_email ? '#14b8a6' : '#94a3b8' }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                        <polyline points="22,6 12,13 2,6"/>
                    </svg>
                    <span>{{ $personal_email ?? Auth::user()->personal_email }}</span>
                </div>
            </div>

            <div class="info-card">
                <strong style="font-size:0.9rem; color:var(--text-dark);">Langkah selanjutnya:</strong>
                <ol class="steps">
                    <li>
                        <span class="step-number">1</span>
                        <span>Buka kotak masuk email <strong>{{ $personal_email ?? Auth::user()->personal_email }}</strong> Anda (Gmail, Yahoo, dll)</span>
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
