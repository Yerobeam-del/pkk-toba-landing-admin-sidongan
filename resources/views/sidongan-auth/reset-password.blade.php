<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - SIDONGAN PKK Kabupaten Toba</title>

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

        .reset-container {
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

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-4px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .reset-header {
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

        .reset-header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0 0 0.25rem 0;
        }

        .reset-header p {
            font-size: 0.9rem;
            opacity: 0.9;
            margin: 0;
        }

        .reset-body { padding: 2rem; }

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

        .form-group .input-with-icon {
            position: relative;
        }

        .form-group .input-with-icon input {
            padding-right: 2.5rem;
        }

        .form-group .toggle-password {
            position: absolute;
            right: 0.875rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            padding: 0;
            transition: color 0.2s ease;
        }

        .form-group .toggle-password:hover {
            color: var(--primary);
        }

        .btn-reset {
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

        .btn-reset:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(139, 92, 246, 0.4);
        }

        .btn-reset:active { transform: translateY(0); }
        .btn-reset:disabled { opacity: 0.7; cursor: not-allowed; transform: none; }

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
            animation: fadeIn 0.3s ease;
        }

        .success-message {
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
            animation: fadeIn 0.3s ease;
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

        .reset-footer {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border);
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        .password-strength {
            margin-top: 0.5rem;
            display: flex;
            gap: 4px;
        }

        .password-strength .bar {
            height: 4px;
            flex: 1;
            border-radius: 4px;
            background: var(--border);
            transition: all 0.3s ease;
        }

        .password-strength .bar.active.weak { background: #ef4444; }
        .password-strength .bar.active.medium { background: #f59e0b; }
        .password-strength .bar.active.strong { background: #22c55e; }

        .password-hint {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-top: 0.375rem;
        }

        @media (max-width: 480px) {
            .reset-container { max-width: 100%; }
            .reset-header { padding: 2rem 1.5rem; }
            .reset-body { padding: 1.5rem; }
        }
    </style>
</head>
<body>
    <div class="reset-container">
        {{-- Header --}}
        <div class="reset-header">
            <div class="logo">
                <img src="{{ asset('assets/sidongan/images/Logo-SIDONGAN-white.svg') }}" alt="Logo SIDONGAN">
            </div>
            <h1>Reset Password SIDONGAN</h1>
            <p>Sistem Informasi Dokumen & Arsip PKK Kabupaten Toba</p>
        </div>

        {{-- Body --}}
        <div class="reset-body">
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

            {{-- Success Message --}}
            @if(session('status'))
                <div class="success-message">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                    {{ session('status') }}
                </div>
            @endif

            {{-- Info Box --}}
            <div class="info-box">
                Buat password baru untuk akun SIDONGAN Anda. Pastikan password kuat dan mudah diingat.
            </div>

            {{-- Reset Password Form --}}
            <form method="POST" action="{{ route('sidongan.password.store') }}">
                @csrf

                {{-- Token --}}
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                {{-- Email --}}
                <div class="form-group">
                    <label for="email">Email SIDONGAN</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email', $request->email) }}"
                        required
                        autofocus
                        autocomplete="username"
                        readonly
                        style="background: #f8fafc; cursor: not-allowed;"
                    >
                </div>

                {{-- Password Baru --}}
                <div class="form-group">
                    <label for="password">Password Baru</label>
                    <div class="input-with-icon">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            required
                            autocomplete="new-password"
                            placeholder="Minimal 8 karakter"
                            oninput="checkPasswordStrength(this.value)"
                        >
                        <button type="button" class="toggle-password" onclick="togglePassword('password', this)" tabindex="-1">
                            <svg id="eyeOpenPassword" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <svg id="eyeClosedPassword" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                <line x1="1" y1="1" x2="23" y2="23"></line>
                            </svg>
                        </button>
                    </div>
                    <div class="password-strength" id="passwordStrength">
                        <div class="bar" data-index="0"></div>
                        <div class="bar" data-index="1"></div>
                        <div class="bar" data-index="2"></div>
                        <div class="bar" data-index="3"></div>
                    </div>
                    <div class="password-hint" id="passwordHint"></div>
                </div>

                {{-- Konfirmasi Password --}}
                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Password</label>
                    <div class="input-with-icon">
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            required
                            autocomplete="new-password"
                            placeholder="Ulangi password baru"
                        >
                        <button type="button" class="toggle-password" onclick="togglePassword('password_confirmation', this)" tabindex="-1">
                            <svg id="eyeOpenConfirm" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <svg id="eyeClosedConfirm" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 2.16-3.19m6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                <line x1="1" y1="1" x2="23" y2="23"></line>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Submit Button --}}
                <button type="submit" class="btn-reset" id="submitBtn">
                    RESET PASSWORD SIDONGAN
                </button>
            </form>

            {{-- Back to Login Link --}}
            <a href="{{ route('sidongan.login') }}" class="link-back">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
                Kembali ke Login SIDONGAN
            </a>

            {{-- Footer --}}
            <div class="reset-footer">
                &copy; {{ date('Y') }} IT Del x PKK Toba. All rights reserved.
            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId, btn) {
            const input = document.getElementById(inputId);
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';

            const openIcon = btn.querySelector('[id^="eyeOpen"]');
            const closedIcon = btn.querySelector('[id^="eyeClosed"]');

            if (openIcon && closedIcon) {
                openIcon.style.display = isPassword ? 'none' : 'block';
                closedIcon.style.display = isPassword ? 'block' : 'none';
            }
        }

        function checkPasswordStrength(password) {
            const bars = document.querySelectorAll('#passwordStrength .bar');
            const hint = document.getElementById('passwordHint');
            let strength = 0;

            bars.forEach(bar => { bar.className = 'bar'; });

            if (password.length === 0) {
                hint.textContent = '';
                return;
            }

            if (password.length >= 8) strength++;
            if (password.length >= 12) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;

            strength = Math.min(strength, 4);

            for (let i = 0; i < bars.length; i++) {
                if (i < strength) {
                    let level = 'weak';
                    if (strength >= 4) level = 'strong';
                    else if (strength >= 2) level = 'medium';
                    bars[i].className = 'bar active ' + level;
                }
            }

            if (strength <= 1) hint.textContent = 'Password lemah';
            else if (strength <= 2) hint.textContent = 'Password cukup';
            else if (strength <= 3) hint.textContent = 'Password baik';
            else hint.textContent = '✓ Password kuat';
        }
    </script>
</body>
</html>
