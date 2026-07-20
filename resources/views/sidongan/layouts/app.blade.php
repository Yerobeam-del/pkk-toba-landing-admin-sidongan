<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/sidongan/images/Logo-SIDONGAN-white.svg') }}">
    <title>@yield('title', 'SIDONGAN - PKK Kabupaten Toba')</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- SIDONGAN CSS -->
    <link rel="stylesheet" href="{{ asset('assets/sidongan/css/style.css') }}">

    @stack('styles')
</head>
<body>

    <x-loading-screen />

    @php
        $currentUser = auth()->guard('sidongan')->user();

        if (!$currentUser && !request()->routeIs('sidongan.login*')) {
            if (!request()->ajax() && !request()->wantsJson()) {
                echo '<script>window.location.href="' . route('sidongan.login') . '";</script>';
                exit;
            }
        }
    @endphp

    <div class="admin-layout" id="adminLayout">
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        {{-- Sidebar --}}
        @include('sidongan.partials.sidebar')

        <div class="main-wrapper">
            {{-- Top Header --}}
            @include('sidongan.partials.top-header')

            <main class="content-area">
                @yield('content')
            </main>

            {{-- Footer - STICKY BOTTOM --}}
            @include('sidongan.partials.footer')
        </div>
    </div>

    <!-- SIDONGAN JS -->
    <script src="{{ asset('assets/sidongan/js/app.js') }}"></script>

    {{-- Toast Notifications --}}
    @include('sidongan.partials.toast')

    @stack('scripts')
</body>
</html>
