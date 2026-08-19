{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>429 - Too Many Requests</title>
        <link rel="stylesheet" href="{{ asset('assets/shared/css/errors-429.css') }}">

</head>
<body>
    <div class="cloud c1"></div>
    <div class="cloud c2"></div>
    <div class="cloud c3"></div>
    <div class="cloud c4"></div>
    
    <div class="container">
        <div class="rate-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"></path>
            </svg>
        </div>
        
        <div class="error-code">429</div>
        
        <div class="message">
            <span class="dynamic-msg">Terlalu Banyak Permintaan</span>
            Anda melakukan terlalu banyak permintaan. Silakan tunggu beberapa saat sebelum mencoba lagi.<br>
            Ini adalah batasan keamanan untuk melindungi sistem.
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
    
        <script src="{{ asset('assets/shared/js/errors-429.js') }}"></script>

</body>
</html>
{{-- Dikembangkan oleh Institut Teknologi Del --}}
