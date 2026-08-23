{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan</title>
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
                <path d="M9 10h.01"/><path d="M15 10h.01"/><path d="M12 2a8 8 0 0 0-8 8v12l3-3 2.5 2.5L12 19l2.5 2.5L17 19l3 3V10a8 8 0 0 0-8-8z"/>
            </svg>
        </div>

        <div class="error-code">404</div>

        <div class="message">
            <span class="dynamic-msg">Oops! Halaman yang Anda cari tidak ditemukan</span>
            Mungkin tautannya sudah kadaluarsa atau halaman tersebut telah dihapus.<br>
            Silahkan kembali ke halaman sebelumnya untuk melanjutkan.
        </div>

        <button data-error-action="back" class="err-btn">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
            Kembali
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
