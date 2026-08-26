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

    {{-- Favicon - Theme Aware --}}
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/sidongan/images/Logo-SIDONGAN-ThemeAware.svg') }}">
    <title>@yield('title', 'SIDONGAN - PKK Kabupaten Toba')</title>

    {{-- Dark Mode: apply class SEBELUM CSS render agar tidak flicker --}}
    <script>
    (function(){
        var k='sidongan-theme',s=localStorage.getItem(k);
        if(s==='dark'||(s===null&&window.matchMedia&&window.matchMedia('(prefers-color-scheme:dark)').matches)){document.documentElement.classList.add('dark-mode');}
    })();
    </script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- SIDONGAN CSS -->
    <link rel="stylesheet" href="{{ asset('assets/sidongan/css/style.css') }}">

    @stack('styles')
</head>
<body data-success="{{ session('success') }}" data-error="{{ session('error') }}" data-warning="{{ session('warning') }}" data-info="{{ session('info') }}">

    @php
        $currentUser = auth()->guard('sidongan')->user();

        if (!$currentUser && !request()->routeIs('sidongan.login*')) {
            if (!request()->ajax() && !request()->wantsJson()) {
                echo "    <script src=\"" . asset('assets/sidongan/js/sidongan-layouts-app.js') . "\" data-login-url=\"" . route('sidongan.login') . "\"></script>\n";
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
                {{-- Breadcrumb Navigation --}}
                @php
                    $segments = collect(explode('/', trim(request()->path(), '/')))->filter();
                    $breadcrumbMap = [
                        'sidongan' => 'Dashboard',
                        'dashboard' => 'Dashboard',
                        'documents' => 'Daftar Surat',
                        'create' => 'Buat Surat Baru',
                        'edit' => 'Edit Surat',
                        'disposisi' => 'Disposisi',
                        'form' => 'Formulir',
                        'verifikasi' => 'Verifikasi',
                        'lapor-kegiatan' => 'Lapor Kegiatan',
                        'arsip' => 'Arsip Surat',
                        'notifications' => 'Notifikasi',
                        'admin' => 'Admin',
                        'tags' => 'Tag',
                        'categories' => 'Kategori',
                    ];
                @endphp
                @if($segments->count() > 1)
                <nav class="sd-breadcrumb" aria-label="Breadcrumb">
                    <ol class="sd-breadcrumb-list">
                        @foreach($segments as $i => $segment)
                            @php
                                $label = $breadcrumbMap[$segment] ?? ucfirst(str_replace('-', ' ', $segment));
                                $isNumeric = is_numeric($segment);
                                $isLast = $loop->last;
                            @endphp
                            @if($isNumeric)
                                <li class="sd-breadcrumb-item sd-breadcrumb-current">Detail</li>
                            @elseif(!$isLast)
                                <li class="sd-breadcrumb-item"><a class="sd-breadcrumb-link" href="#">{{ $label }}</a><span class="sd-breadcrumb-sep">/</span></li>
                            @else
                                <li class="sd-breadcrumb-item sd-breadcrumb-current">{{ $label }}</li>
                            @endif
                        @endforeach
                    </ol>
                </nav>
                @endif

                @yield('content')
            </main>

            {{-- Footer - STICKY BOTTOM --}}
            @include('sidongan.partials.footer')
        </div>
    </div>

    {{-- Toast Notifications.
         Dimuat SEBELUM app.js dan skrip halaman karena keduanya memanggil
         Toast untuk pesan validasi & konfirmasi (menggantikan alert/confirm bawaan). --}}
    @include('sidongan.partials.toast')

    <!-- SIDONGAN Shared JS -->
    <script src="{{ asset('assets/sidongan/js/sidongan-shared.js') }}"></script>
    <!-- SIDONGAN JS -->
    <script src="{{ asset('assets/sidongan/js/app.js') }}"></script>



    @stack('scripts')
</body>
</html>
{{-- Dikembangkan oleh Institut Teknologi Del --}}
