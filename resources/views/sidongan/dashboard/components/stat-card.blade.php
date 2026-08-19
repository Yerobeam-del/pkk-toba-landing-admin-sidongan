{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
<div class="stat-card stat-card-{{ $color }}">
    <div class="stat-card-decoration"></div>
    <div class="stat-card-content">
        <div class="stat-card-icon">
            <i class="fas {{ $icon }}"></i>
        </div>
        <div class="stat-card-info">
            <p class="stat-card-title">{{ $title }}</p>
            <p class="stat-card-value">{{ $value }}</p>
        </div>
    </div>
</div>

    @once
    @push('styles')
    <link rel="stylesheet" href="{{ asset('assets/sidongan/css/sidongan-dashboard-components-stat-card.css') }}">
    @endpush
    @endonce

{{-- Dikembangkan oleh Institut Teknologi Del --}}
