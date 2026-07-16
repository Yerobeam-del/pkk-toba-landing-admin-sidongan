<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIDONGAN</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            /* Tema Ungu */
            --primary: #8b5cf6;
            --primary-dark: #6d28d9;
            --primary-light: #a78bfa;
            --text-dark: #4c1d95;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --bg-light: #f5f3ff;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

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

        .login-wrapper {
            display: grid;
            grid-template-columns: 1fr 1fr;
            max-width: 850px;
            width: 100%;
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(76, 29, 149, 0.2);
            animation: slideUp 0.5s ease-out;
            position: relative;
            z-index: 1;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-branding {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            padding: 2.5rem 2rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .login-branding::before {
            content: '';
            position: absolute;
            top: 65%;
            left: 35%;
            transform: translate(-50%, -50%);
            width: 600px;
            height: 600px;
            background-image: url("{{ asset('assets/sidongan/images/Logo-SIDONGAN.svg') }}");
            background-size: contain;
            background-position: center;
            background-repeat: no-repeat;
            opacity: 0.12;
            z-index: 0;
            pointer-events: none;
            filter: brightness(0) invert(1);
        }

        .login-branding::after {
            content: '';
            position: absolute;
            top: -50%; left: -50%;
            width: 200%; height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.08) 1px, transparent 1px);
            background-size: 30px 30px;
            opacity: 0.4;
            z-index: 0;
        }

        .logos-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1.25rem;
            margin-bottom: 1.75rem;
            position: relative;
            z-index: 1;
        }

        .logo-circle {
            width: 95px;
            height: 95px;
            background: white;
            border-radius: 50%;
            padding: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 24px rgba(76, 29, 149, 0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .logo-circle:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 12px 32px rgba(139, 92, 246, 0.4);
        }

        .logo-circle img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .branding-content {
            position: relative;
            z-index: 1;
        }

        .branding-title {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 0.4rem;
            line-height: 1.3;
            color: white;
        }

        .branding-subtitle {
            font-size: 0.9rem;
            opacity: 0.95;
            margin-bottom: 0.3rem;
            color: rgba(255,255,255,0.95);
        }

        .branding-tagline {
            font-size: 0.8rem;
            opacity: 0.9;
            font-weight: 500;
            color: rgba(255,255,255,0.9);
        }

        .login-form-wrapper {
            padding: 2.5rem 2rem;
            background: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-header {
            text-align: center;
            margin-bottom: 1.75rem;
        }

        .login-header h2 {
            color: var(--text-dark);
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.4rem;
        }

        .login-header p {
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        .form-group {
            margin-bottom: 1.125rem;
        }

        .form-label {
            display: block;
            color: var(--text-dark);
            font-weight: 600;
            margin-bottom: 0.4rem;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 0.875rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 16px;
            pointer-events: none;
            transition: color 0.3s ease;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 0.875rem 0.75rem 2.5rem;
            border: 2px solid var(--border);
            border-radius: 8px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            background: var(--bg-light);
            font-family: inherit;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            background: white;
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.2);
        }

        .form-control:focus ~ .input-icon {
            color: var(--primary-dark);
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.25rem;
            font-size: 0.85rem;
        }

        .custom-checkbox {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            user-select: none;
            color: var(--text-muted);
        }

        .custom-checkbox input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            width: 0;
            height: 0;
        }

        .checkmark {
            width: 18px;
            height: 18px;
            background-color: white;
            border: 2px solid var(--border);
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }

        .checkmark svg {
            width: 11px;
            height: 11px;
            color: white;
            opacity: 0;
            transform: scale(0.5);
            transition: all 0.2s ease;
        }

        .custom-checkbox:hover .checkmark {
            border-color: var(--primary-dark);
        }

        .custom-checkbox input:checked ~ .checkmark {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        .custom-checkbox input:checked ~ .checkmark svg {
            opacity: 1;
            transform: scale(1);
        }

        .forgot-password {
            color: var(--primary-dark);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
            font-size: 0.85rem;
        }

        .forgot-password:hover {
            color: var(--text-dark);
            text-decoration: underline;
        }

        .btn-login {
            width: 100%;
            padding: 0.75rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(139, 92, 246, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px -5px rgba(139, 92, 246, 0.5);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 1.25rem 0;
            color: var(--text-muted);
            font-size: 0.75rem;
            font-weight: 500;
        }

        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        .divider span { padding: 0 0.75rem; }

        .footer-security {
            text-align: center;
            color: var(--text-muted);
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
        }

        .footer-security svg {
            color: var(--primary-dark);
            width: 14px;
            height: 14px;
        }

        .alert {
            padding: 0.75rem 1rem;
            border-radius: 8px;
            font-size: 0.85rem;
            margin-bottom: 1.125rem;
            border-left: 4px solid;
        }

        .alert-danger {
            background: #fef2f2;
            color: #dc2626;
            border-left-color: #dc2626;
        }

        .alert ul { margin: 0; padding-left: 1.25rem; }

        /* Responsive */
        @media (max-width: 768px) {
            .login-wrapper { grid-template-columns: 1fr; max-width: 420px; }
            .login-branding { padding: 2rem 1.5rem; }
            .logos-container { gap: 1rem; margin-bottom: 1.5rem; }
            .logo-circle { width: 100px; height: 100px; padding: 12px; }
            .branding-title { font-size: 1.15rem; }
            .branding-subtitle { font-size: 0.85rem; }
            .branding-tagline { font-size: 0.75rem; }
            .login-form-wrapper { padding: 2rem 1.5rem; }
            .login-header h2 { font-size: 1.35rem; }
        }

        @media (max-width: 480px) {
            body { padding: 0.5rem; }
            .login-wrapper { border-radius: 12px; max-width: 100%; }
            .login-branding { padding: 1.75rem 1.25rem; }
            .logos-container { gap: 0.75rem; }
            .logo-circle { width: 100px; height: 100px; padding: 10px; }
            .branding-title { font-size: 1rem; }
            .login-form-wrapper { padding: 1.75rem 1.25rem; }
            .form-control { padding: 0.7rem 0.875rem 0.7rem 2.375rem; font-size: 0.875rem; }
            .btn-login { padding: 0.7rem; font-size: 0.9rem; }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        {{-- Left Side - Branding --}}
        <div class="login-branding">
            <div class="logos-container">
                <div class="logo-circle">
                    <img src="{{ asset('assets/admin/images/Logo-Kabupaten-Toba-Transparent.png') }}" alt="Logo Kabupaten Toba">
                </div>
                <div class="logo-circle">
                    <img src="{{ asset('assets/admin/images/Logo-PKK-Transparent.png') }}" alt="Logo PKK">
                </div>
            </div>
            
            <div class="branding-content">
                <h1 class="branding-title">SIDONGAN</h1>
                <p class="branding-subtitle">Sistem Informasi Dokumen Organisasi Agenda dan Naskah</p>
                <p class="branding-tagline">PKK Kabupaten Toba</p>
            </div>
        </div>

        {{-- Right Side - Login Form --}}
        <div class="login-form-wrapper">
            <div class="login-header">
                <h2>Selamat Datang</h2>
                <p>Silahkan login untuk melanjutkan</p>
            </div>

            <form method="POST" action="{{ route('sidongan.login') }}">
                @csrf

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                @if (session('status'))
                    <div class="alert alert-danger">{{ session('status') }}</div>
                @endif

                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <div class="input-wrapper">
                        <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="nama@email.com" required autofocus autocomplete="email">
                        <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-wrapper">
                        <!-- Tambahkan style padding-right agar teks tidak menabrak ikon mata -->
                        <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required autocomplete="current-password" style="padding-right: 2.5rem;">
                        
                        <!-- Ikon Gembok (Kiri) -->
                        <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>

                        <!-- Tombol Toggle Password (Kanan) -->
                        <button type="button" id="togglePassword" style="position: absolute; right: 0.875rem; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--text-muted); display: flex; align-items: center; justify-content: center; padding: 0; transition: color 0.2s ease;">
                            <!-- Ikon Mata Terbuka (Default) -->
                            <svg id="eyeOpen" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <!-- Ikon Mata Tertutup (Hidden) -->
                            <svg id="eyeClosed" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                <line x1="1" y1="1" x2="23" y2="23"></line>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="form-options">
                    <label class="custom-checkbox">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span class="checkmark">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </span>
                        <span class="check-text">Ingat saya</span>
                    </label>
                    @if (Route::has('sidongan.password.request'))
                        <a class="forgot-password" href="{{ route('sidongan.password.request') }}">Lupa password?</a>
                    @endif
                </div>

                <button type="submit" class="btn-login">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                        <polyline points="10 17 15 12 10 7"></polyline>
                        <line x1="15" y1="12" x2="3" y2="12"></line>
                    </svg>
                    <span>MASUK</span>
                </button>

                <div class="divider"><span>Secure Login</span></div>

                <div class="footer-security">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    </svg>
                    <span>Sistem Resmi Pemkab Toba</span>
                </div>
            </form>
        </div>
    </div>
    
<script>
document.addEventListener('DOMContentLoaded', function() {
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const eyeOpen = document.getElementById('eyeOpen');
    const eyeClosed = document.getElementById('eyeClosed');

    togglePassword.addEventListener('click', function() {
        // Ganti tipe input antara 'password' dan 'text'
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        // Ganti ikon mata
        if (type === 'text') {
            eyeOpen.style.display = 'none';
            eyeClosed.style.display = 'block';
            togglePassword.style.color = 'var(--primary-dark)'; // Ubah warna jadi ungu saat aktif
        } else {
            eyeOpen.style.display = 'block';
            eyeClosed.style.display = 'none';
            togglePassword.style.color = 'var(--text-muted)'; // Kembali ke warna default
        }
    });
});
</script>
</body>
</html>