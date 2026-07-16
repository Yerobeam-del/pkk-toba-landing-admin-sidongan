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

<style>
    .empty-state {
        padding: 3rem 2rem;
        text-align: center;
    }

    .empty-state-icon {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
    }

    .empty-state-icon i {
        font-size: 1.5rem;
    }

    .empty-state-title {
        font-size: 0.9rem;
        color: #1e293b;
        margin: 0;
        font-weight: 600;
    }

    .empty-state-message {
        font-size: 0.875rem;
        color: #64748b;
        margin: 0.25rem 0 0 0;
    }
</style>