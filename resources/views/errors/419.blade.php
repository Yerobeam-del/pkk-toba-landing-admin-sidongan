{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>419 - Page Expired</title>
    <link rel="stylesheet" href="{{ asset('assets/shared/css/errors-shared.css') }}">
</head>
<body data-error-theme="warning">
    <div class="cloud c1"></div>
    <div class="cloud c2"></div>
    <div class="cloud c3"></div>
    <div class="cloud c4"></div>

    <div class="container">
        <div class="err-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
            </svg>
        </div>

        <div class="error-code">419</div>

        <div class="message">
            <span class="dynamic-msg">Halaman Kedaluwarsa</span>
            Sesi Anda telah berakhir. Silakan refresh halaman dan coba lagi.<br>
            Ini biasanya terjadi karena token keamanan sudah kadaluarsa.
        </div>

        <button data-error-action="reload" class="err-btn">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="23 4 23 10 17 10"></polyline>
                <polyline points="1 20 1 14 7 14"></polyline>
                <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
            </svg>
            Refresh Halaman
        </button>

        @if(config('app.debug'))
        <div class="url-info">
            <strong>URL yang diminta:</strong> {{ request()->fullUrl() }}
        </div>
        @endif
    </div>

    <script src="{{ asset('assets/shared/js/errors-shared.js') }}"></script>
</body>
</html>
{{-- Dikembangkan oleh Institut Teknologi Del --}}
