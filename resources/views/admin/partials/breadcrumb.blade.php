{{-- Breadcrumb Navigation --}}
@php
    $segments = request()->segments();
    $breadcrumbs = [];
    $cumulativePath = '';

    // Map route segments to labels
    $labelMap = [
        'admin' => ['label' => 'Beranda', 'url' => route('admin.dashboard')],
        'berita' => ['label' => 'Berita', 'url' => route('admin.berita.index')],
        'struktur' => ['label' => 'Struktur', 'url' => route('admin.struktur.index')],
        'aplikasi' => ['label' => 'Aplikasi', 'url' => route('admin.aplikasi.index')],
        'sk' => ['label' => 'SK & Dokumen', 'url' => route('admin.sk.index')],
        'template' => ['label' => 'Template', 'url' => route('admin.template.index')],
        'tentang' => ['label' => 'Tentang', 'url' => route('admin.tentang.index')],
        'hero-sliders' => ['label' => 'Hero Sliders', 'url' => route('admin.hero-sliders.index')],
        'user-management' => ['label' => 'Manajemen Akun', 'url' => route('admin.user-management.index')],
        'sidongan-data' => ['label' => 'Data SIDONGAN', 'url' => route('admin.sidongan-data.index')],
        'sieda-data' => ['label' => 'Manajemen SIEDA', 'url' => route('admin.sieda-data.index')],
        'profile' => ['label' => 'Profil', 'url' => route('admin.profile.edit')],
        'create' => ['label' => 'Tambah Baru'],
        'edit' => ['label' => 'Edit'],
    ];

    $actionLabels = [
        'create' => 'Tambah Baru',
        'edit' => 'Edit',
    ];

    foreach ($segments as $segment) {
        if (isset($labelMap[$segment])) {
            $breadcrumbs[] = $labelMap[$segment];
        } elseif (isset($actionLabels[$segment])) {
            $breadcrumbs[] = ['label' => $actionLabels[$segment]];
        } elseif (is_numeric($segment)) {
            // Skip numeric IDs in breadcrumb
            continue;
        } else {
            $breadcrumbs[] = ['label' => ucfirst(str_replace('-', ' ', $segment))];
        }
    }
@endphp

@if(count($breadcrumbs) > 1)
<nav class="admin-breadcrumb" aria-label="Breadcrumb">
    <ol>
        @foreach($breadcrumbs as $index => $crumb)
            <li class="{{ $index === count($breadcrumbs) - 1 ? 'active' : '' }}">
                @if($index < count($breadcrumbs) - 1 && isset($crumb['url']))
                    <a href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</a>
                @else
                    <span>{{ $crumb['label'] }}</span>
                @endif
                @if($index < count($breadcrumbs) - 1)
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="breadcrumb-sep">
                        <polyline points="9 18 15 12 9 6"/>
                    </svg>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
@endif
