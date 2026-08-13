<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - SIDONGAN PKK Kabupaten Toba</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #8b5cf6;
            --primary-dark: #6d28d9;
            --text-dark: #4c1d95;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --bg: #f1f5f9;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #4c1d95 0%, #6d28d9 50%, #8b5cf6 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background-image: url("{{ asset('assets/admin/images/batik-pkk.svg') }}");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            opacity: 0.15;
            z-index: 0;
            pointer-events: none;
        }

        .login-container {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(76, 29, 149, 0.3);
            overflow: hidden;
            width: 100%;
            max-width: 420px;
            animation: slideUp 0.5s ease;
            position: relative;
            z-index: 1;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            padding: 2.5rem 2rem;
            text-align: center;
            color: #fff;
        }

        .logo {
            width: 100px;
            height: 100px;
            background: rgba(255,255,255,0.15);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            backdrop-filter: blur(10px);
            padding: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .logo:hover { transform: scale(1.05); }

        .logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
        }

        .login-header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0 0 0.25rem 0;
        }

        .login-header p {
            font-size: 0.9rem;
            opacity: 0.9;
            margin: 0;
        }

        .login-body { padding: 2rem; }

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
            background: #fff;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.15);
        }

        .btn-login {
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
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(139, 92, 246, 0.4);
        }

        .btn-login:active { transform: translateY(0); }

        .error-message {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 0.875rem 1rem;
            border-radius: 8px;
            margin-bottom: 1.25rem;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .status-message {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
            padding: 0.875rem 1rem;
            border-radius: 8px;
            margin-bottom: 1.25rem;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-box {
            background: #f5f3ff;
            border: 1px solid #ddd6fe;
            color: #5b21b6;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            line-height: 1.6;
            text-align: center;
        }

        .link-back {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            margin-top: 1.5rem;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            transition: color 0.2s;
        }

        .link-back:hover {
            color: var(--primary);
            text-decoration: underline;
        }

        .login-footer {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border);
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        @media (max-width: 480px) {
            .login-container { max-width: 100%; }
            .login-header { padding: 2rem 1.5rem; }
            .login-body { padding: 1.5rem; }
        }
    </style>
</head>
<body>
    <div class="login-container">
        {{-- Header --}}
        <div class="login-header">
            <div class="logo">
                <img src="{{ asset('assets/sidongan/images/Logo-SIDONGAN-white.svg') }}" alt="Logo SIDONGAN">
            </div>
            <h1>SIDONGAN</h1>
            <p>Sistem Informasi Dokumen & Arsip PKK Kabupaten Toba</p>
        </div>

        {{-- Body --}}
        <div class="login-body">
            {{-- Status Message --}}
            @if(session('status'))
                <div class="status-message">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                    {{ session('status') }}
                </div>
            @endif

            {{-- Validation Errors --}}
            @if($errors->any())
                <div class="error-message">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="15" y1="9" x2="9" y2="15"/>
                        <line x1="9" y1="9" x2="15" y2="15"/>
                    </svg>
                    <div>
                        <strong>Terjadi kesalahan:</strong>
                        <ul style="margin: 0.25rem 0 0 0; padding-left: 1.25rem;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            {{-- Info Box --}}
            <div class="info-box">
                Lupa password? Fitur reset password kini terpusat di Portal Akun PKK Kabupaten Toba.
            </div>

            {{-- Unified Portal Info --}}
            <div class="info-box">
                <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:0.75rem">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                        <polyline points="10 17 15 12 10 7"/>
                        <line x1="15" y1="12" x2="3" y2="12"/>
                    </svg>
                    <strong style="font-size:1rem;">Reset Password Terpusat</strong>
                </div>
                <p style="margin:0 0 1rem 0;line-height:1.6;">
                    Fitur lupa password kini telah terpusat di <strong>Portal Akun PKK Kabupaten Toba</strong>.
                    Klik tombol di bawah untuk melanjutkan.
                </p>
                <a href="https://{{ config('app.landing_domain', 'tp-pkk.tobakab.go.id') }}/forgot-password"
                   class="btn-login"
                   style="display:inline-flex;align-items:center;justify-content:center;gap:0.5rem;text-decoration:none;font-size:0.9rem;padding:0.75rem 1.25rem;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                        <polyline points="10 17 15 12 10 7"/>
                        <line x1="15" y1="12" x2="3" y2="12"/>
                    </svg>
                    BUKA PORTAL RESET PASSWORD
                </a>
            </div>

            {{-- Back to Login Link --}}
            <a href="{{ route('sidongan.login') }}" class="link-back">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
                Kembali ke Login SIDONGAN
            </a>

            {{-- Footer --}}
            <div class="login-footer">
                &copy; {{ date('Y') }} IT Del x PKK Toba. All rights reserved.
            </div>
        </div>
    </div>
</body>
</html>
