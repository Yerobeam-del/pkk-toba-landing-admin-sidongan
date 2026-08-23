{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>422 - Data Tidak Valid</title>
    <link rel="stylesheet" href="{{ asset('assets/shared/css/errors-shared.css') }}">
</head>
<body data-error-theme="info">
    <div class="cloud c1"></div>
    <div class="cloud c2"></div>
    <div class="cloud c3"></div>
    <div class="cloud c4"></div>

    <div class="container">
        <div class="err-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                <line x1="12" y1="9" x2="12" y2="13"></line>
                <line x1="12" y1="17" x2="12.01" y2="17"></line>
            </svg>
        </div>

        <div class="error-code">422</div>

        <div class="message">
            <span class="dynamic-msg">Data Tidak Valid - Validation Error</span>
            Data yang Anda kirim tidak memenuhi syarat yang dibutuhkan.<br>
            Silakan periksa kembali form yang Anda isi dan pastikan semua field sudah benar.
        </div>

        <button data-error-action="back" class="err-btn">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
            Kembali & Perbaiki
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
