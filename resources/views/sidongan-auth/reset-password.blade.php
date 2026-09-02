{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
<!DOCTYPE html>
<html lang="id">
<head>
    
    <link rel="stylesheet" href="{{ asset('assets/shared/css/utilities.css') }}">
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - SIDONGAN PKK Kabupaten Toba</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <link rel="stylesheet" href="{{ asset('assets/sidongan-auth/css/auth-reset-password.css') }}">

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
                        <ul class="u-list-indent-sm">
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
                            
                        >
                        <button type="button" class="toggle-password" data-action="toggle-password" data-target="password" tabindex="-1">
                            <svg id="eyeOpenPassword" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <svg class="u-hidden" id="eyeClosedPassword" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
                        <button type="button" class="toggle-password" data-action="toggle-password" data-target="password_confirmation" tabindex="-1">
                            <svg id="eyeOpenConfirm" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <svg class="u-hidden" id="eyeClosedConfirm" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
                &copy; {{ date('Y') }} TP-PKK Kabupaten Toba. All rights reserved.
            </div>
        </div>
    </div>

        <script src="{{ asset('assets/sidongan/js/sidongan-auth-reset-password.js') }}"></script>

</body>
</html>
{{-- Dikembangkan oleh Institut Teknologi Del --}}
