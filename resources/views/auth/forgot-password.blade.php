{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
<!DOCTYPE html>
<html lang="id">
<head>
    
    <link rel="stylesheet" href="{{ asset('assets/shared/css/utilities.css') }}">
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Admin Panel PKK Kabupaten Toba</title>
    
    {{-- Favicon untuk Tab Browser --}}
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/admin/images/Logo_Admin-Panel.svg') }}">
    <link rel="alternate icon" type="image/svg+xml" href="{{ asset('assets/admin/images/Logo_Admin-Panel.svg') }}">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
        <link rel="stylesheet" href="{{ asset('assets/auth/css/auth-forgot-password.css') }}">

</head>
<body>
    <div class="login-container">
        {{-- Header --}}
        <div class="login-header">
            <div class="logo">
                <img src="{{ asset('assets/admin/images/Logo-PKK-Transparent.png') }}" alt="Logo PKK Kabupaten Toba">
            </div>
            <h1>Admin Panel</h1>
            <p>PKK Kabupaten Toba</p>
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
                        <ul class="u-list-indent-sm">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            {{-- Info Box --}}
            <div class="info-box">
                Lupa password? Masukkan email Anda dan kami akan mengirimkan tautan untuk mereset password.
            </div>

            {{-- Forgot Password Form --}}
            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                {{-- Email --}}
                <div class="form-group">
                    <label for="email">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="{{ old('email') }}" 
                        required 
                        autofocus 
                        autocomplete="email"
                        placeholder="nama@email.com"
                    >
                </div>

                {{-- Submit Button --}}
                <button type="submit" class="btn-login">
                    KIRIM LINK RESET
                </button>
            </form>

            {{-- Back to Login Link --}}
            <a href="{{ route('login') }}" class="link-back">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
                Kembali ke Login
            </a>

            {{-- Footer --}}
            <div class="login-footer">
                &copy; {{ date('Y') }} TP-PKK Kabupaten Toba. All rights reserved.
            </div>
        </div>
    </div>
</body>
</html>
{{-- Dikembangkan oleh Institut Teknologi Del --}}
