@if ($paginator->hasPages())
<nav role="navigation" aria-label="Pagination Navigation" class="pagination-wrapper">
    <div class="pagination-container">
        {{-- Previous Button --}}
        @if ($paginator->onFirstPage())
            <button class="pagination-btn" disabled>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px"><polyline points="15 18 9 12 15 6"/></svg>
                <span class="desktop-only">Previous</span>
            </button>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="pagination-btn" rel="prev">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px"><polyline points="15 18 9 12 15 6"/></svg>
                <span class="desktop-only">Previous</span>
            </a>
        @endif

        {{-- Pagination Elements --}}
        @php
            $currentPage = $paginator->currentPage();
            $lastPage = $paginator->lastPage();
            if ($lastPage <= 5) {
                $pages = range(1, $lastPage);
            } else {
                if ($currentPage <= 3) {
                    $pages = [1, 2, 3, 4, '...', $lastPage];
                } elseif ($currentPage >= $lastPage - 2) {
                    $pages = [1, '...', $lastPage - 3, $lastPage - 2, $lastPage - 1, $lastPage];
                } else {
                    $pages = [1, '...', $currentPage - 1, $currentPage, $currentPage + 1, '...', $lastPage];
                }
            }
        @endphp

        @foreach($pages as $page)
            @if($page === '...')
                <span style="padding: 0.5rem 0.25rem; color: var(--text-muted); font-size: 0.875rem;">...</span>
            @elseif($page == $currentPage)
                <button class="pagination-btn active" aria-current="page">{{ $page }}</button>
            @else
                <a href="{{ $paginator->url($page) }}" class="pagination-btn">{{ $page }}</a>
            @endif
        @endforeach

        {{-- Next Button --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="pagination-btn" rel="next">
                <span class="desktop-only">Next</span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
        @else
            <button class="pagination-btn" disabled>
                <span class="desktop-only">Next</span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
        @endif
    </div>

    {{-- Pagination Info --}}
    <div class="pagination-info">
        Menampilkan <strong>{{ $paginator->firstItem() }}</strong> - <strong>{{ $paginator->lastItem() }}</strong> dari <strong>{{ $paginator->total() }}</strong> data
    </div>
</nav>

<style>
/* Pagination Styles (Matching Kelola Beranda) */
.pagination-wrapper {
    padding: 1rem 1.5rem;
    border-top: 1px solid rgba(0,0,0,0.06);
}

.pagination-container {
    display: flex;
    gap: 0.4rem;
    align-items: center;
    flex-wrap: wrap;
    justify-content: center;
    margin-bottom: 0.75rem;
}

.pagination-btn {
    padding: 0.5rem 0.9rem;
    background: #fff;
    color: var(--text-dark);
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 500;
    min-width: 40px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.25rem;
    transition: all 0.2s;
    text-decoration: none;
    cursor: pointer;
}

.pagination-btn:hover:not(:disabled) {
    background: #f8fafc;
    border-color: var(--primary);
    color: var(--primary);
}

.pagination-btn.active {
    background: linear-gradient(135deg, var(--primary), #0d9488);
    color: #fff;
    border-color: var(--primary);
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(20,184,166,0.3);
    cursor: default;
}

.pagination-btn:disabled {
    color: var(--text-muted);
    cursor: default;
    opacity: 0.5;
}

.pagination-info {
    text-align: center;
    color: var(--text-muted);
    font-size: 0.85rem;
}

/* Mobile adjustments for pagination */
@media (max-width: 768px) {
    .desktop-only {
        display: none !important;
    }

    .pagination-container {
        gap: 0.25rem;
    }

    .pagination-btn {
        padding: 0.5rem 0.75rem;
        font-size: 0.8rem;
        min-width: 36px;
    }
}
</style>
@endif
