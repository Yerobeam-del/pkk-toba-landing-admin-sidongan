{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Internal Server Error</title>
        <link rel="stylesheet" href="{{ asset('assets/shared/css/errors-500.css') }}">

</head>
<body>
    <div class="cloud c1"></div>
    <div class="cloud c2"></div>
    <div class="cloud c3"></div>
    <div class="cloud c4"></div>
    
    <div class="container">
        <div class="server-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect>
                <rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect>
                <line x1="6" y1="6" x2="6.01" y2="6"></line>
                <line x1="6" y1="18" x2="6.01" y2="18"></line>
            </svg>
        </div>
        
        <div class="error-code">500</div>
        
        <div class="message">
            <span class="dynamic-msg">Internal Server Error</span>
            Terjadi kesalahan pada server. Tim teknis kami sedang menangani masalah ini.<br>
            Silakan coba lagi beberapa saat atau hubungi administrator.
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
    
        <script src="{{ asset('assets/shared/js/errors-500.js') }}"></script>

</body>
</html>
{{-- Dikembangkan oleh Institut Teknologi Del --}}
