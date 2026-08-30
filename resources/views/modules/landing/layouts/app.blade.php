{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
<!DOCTYPE html>
<html lang="id">
<head>
    
    <link rel="stylesheet" href="{{ asset('assets/shared/css/utilities.css') }}">
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="google-site-verification" content="I0f3Dx6hcE_3I0pMKYB26tDyNuw0oTGzAzOTrk-sI7o" />
    @stack('meta')
    <title>@yield('title', 'Beranda - PKK Kabupaten Toba')</title>

    {{-- Favicon --}}
        <link rel="icon" type="image/png" href="{{ asset('assets/landing/images/Logo-PKK-Transparent.png') }}">
        <link rel="shortcut icon" type="image/png" href="{{ asset('assets/landing/images/Logo-PKK-Transparent.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('assets/landing/css/style.css') }}">

    @stack('styles')
</head>
<body>
    {{-- Header / Navbar --}}
    @include('modules.landing.partials.header')

    {{-- Floating Button --}}
    @include('modules.landing.partials.floating-btn')

    {{-- Main Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('modules.landing.partials.footer')

    {{-- Global Config & Init --}}
    

    {{-- JavaScript Modular --}}
    <script src="{{ asset('assets/landing/js/main.js') }}"></script>
    <script src="{{ asset('assets/landing/js/navigation.js') }}"></script>
    <script src="{{ asset('assets/landing/js/hero-slider.js') }}"></script>
    {{-- <script src="{{ asset('assets/landing/js/news-handler.js') }}"></script> --}}
    <script src="{{ asset('assets/landing/js/desa-handler.js') }}"></script>
    <script src="{{ asset('assets/landing/js/sk-handler.js') }}"></script>
    <script src="{{ asset('assets/landing/js/template-handler.js') }}"></script>
    <script src="{{ asset('assets/landing/js/struktur-handler.js') }}"></script>
    <script src="{{ asset('assets/landing/js/animations.js') }}"></script>

        {{-- Router hybrid (SPA + Laravel route) --}}
    <script src="{{ asset('assets/landing/js/router.js') }}"></script>
    <script src="{{ asset('assets/landing/js/delegation.js') }}"></script>

    @stack('scripts')
</body>
</html>
{{-- Dikembangkan oleh Institut Teknologi Del --}}
