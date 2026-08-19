{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Forbidden</title>
        <link rel="stylesheet" href="{{ asset('assets/shared/css/errors-403.css') }}">

</head>
<body>
    <div class="cloud c1"></div>
    <div class="cloud c2"></div>
    <div class="cloud c3"></div>
    <div class="cloud c4"></div>
    
    <div class="container">
        <div class="forbidden-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
        </div>
        
        <div class="error-code">403</div>
        
        <div class="message">
            <span class="dynamic-msg">Akses Ditolak - Forbidden</span>
            Anda tidak memiliki izin untuk mengakses halaman ini.<br>
            Hubungi administrator jika Anda memerlukan akses.
        </div>
        
        <button data-error-action="back" class="btn">
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
    
        <script src="{{ asset('assets/shared/js/errors-403.js') }}"></script>

</body>
</html>
{{-- Dikembangkan oleh Institut Teknologi Del --}}
