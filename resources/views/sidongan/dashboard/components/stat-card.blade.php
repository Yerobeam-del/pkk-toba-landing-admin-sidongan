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

<style>
    .stat-card {
        border-radius: 0.75rem;
        padding: 1.25rem;
        color: white;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        transition: all 0.3s;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
    }

    .stat-card-decoration {
        position: absolute;
        top: -10px;
        right: -10px;
        width: 80px;
        height: 80px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
    }

    .stat-card-content {
        display: flex;
        align-items: center;
        gap: 1rem;
        position: relative;
        z-index: 1;
    }

    .stat-card-icon {
        width: 3rem;
        height: 3rem;
        background: rgba(255,255,255,0.25);
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(4px);
    }

    .stat-card-icon i {
        font-size: 1.25rem;
    }

    .stat-card-title {
        font-size: 0.875rem;
        opacity: 0.95;
        margin: 0;
    }

    .stat-card-value {
        font-size: 1.875rem;
        font-weight: 800;
        margin: 0.25rem 0 0 0;
    }

    /* Color Variants */
    .stat-card-blue {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);
    }

    .stat-card-blue:hover {
        box-shadow: 0 8px 16px rgba(59, 130, 246, 0.4);
    }

    .stat-card-orange {
        background: linear-gradient(135deg, #f97316, #ea580c);
        box-shadow: 0 4px 6px -1px rgba(249, 115, 22, 0.3);
    }

    .stat-card-orange:hover {
        box-shadow: 0 8px 16px rgba(249, 115, 22, 0.4);
    }

    .stat-card-yellow {
        background: linear-gradient(135deg, #eab308, #ca8a04);
        box-shadow: 0 4px 6px -1px rgba(234, 179, 8, 0.3);
    }

    .stat-card-yellow:hover {
        box-shadow: 0 8px 16px rgba(234, 179, 8, 0.4);
    }

    .stat-card-green {
        background: linear-gradient(135deg, #22c55e, #16a34a);
        box-shadow: 0 4px 6px -1px rgba(34, 197, 94, 0.3);
    }

    .stat-card-green:hover {
        box-shadow: 0 8px 16px rgba(34, 197, 94, 0.4);
    }

    .stat-card-purple {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        box-shadow: 0 4px 6px -1px rgba(139, 92, 246, 0.3);
    }

    .stat-card-purple:hover {
        box-shadow: 0 8px 16px rgba(139, 92, 246, 0.4);
    }

    
</style>