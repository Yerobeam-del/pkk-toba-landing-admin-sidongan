{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
@php
    $color = $color ?? 'gray';
    $bgColors = [
        'gray' => '#f1f5f9',
        'green' => '#f0fdf4',
    ];
    $iconColors = [
        'gray' => '#94a3b8',
        'green' => '#22c55e',
    ];
@endphp

<div class="empty-state">
    <div class="empty-state-icon" style="background: {{ $bgColors[$color] }};">
        <i class="fas {{ $icon }}" style="color: {{ $iconColors[$color] }};"></i>
    </div>
    @if(isset($title))
        <p class="empty-state-title">{{ $title }}</p>
    @endif
    <p class="empty-state-message">{{ $message }}</p>
</div>

    @once
    @push('styles')
    <link rel="stylesheet" href="{{ asset('assets/sidongan/css/sidongan-dashboard-components-empty-state.css') }}">
    @endpush
    @endonce

{{-- Dikembangkan oleh Institut Teknologi Del --}}
