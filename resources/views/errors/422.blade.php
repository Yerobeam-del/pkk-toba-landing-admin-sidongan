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
<body data-error-theme="warning">
    <script>(function(){var t=localStorage.getItem('admin-dark-mode');if(t==='true'||(t===null&&matchMedia('(prefers-color-scheme:dark)').matches))document.documentElement.classList.add('dark-mode')})()</script>
    <div class="cloud c1"></div>
    <div class="cloud c2"></div>
    <div class="cloud c3"></div>
    <div class="cloud c4"></div>

    <div class="container">
        <div class="err-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
        </div>

        <div class="error-code">422</div>

        <div class="message">
            <span class="dynamic-msg">Data yang dikirim tidak valid</span>
            Ada kesalahan pada input yang Anda kirim.<br>
            Silahkan periksa kembali data Anda dan coba lagi.
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
