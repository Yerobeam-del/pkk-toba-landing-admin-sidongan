{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
@if ($paginator->hasPages())
<nav role="navigation" aria-label="Pagination Navigation" class="pagination-wrapper">
    <div class="pagination-container">
        {{-- Previous Button --}}
        @if ($paginator->onFirstPage())
            <button class="pagination-btn" disabled>
                <svg class="u-icon-sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                <span class="desktop-only">Previous</span>
            </button>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="pagination-btn" rel="prev">
                <svg class="u-icon-sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                <span class="desktop-only">Previous</span>
            </a>
        @endif

        {{-- Pagination Elements (Tombol Nomor Halaman) --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span class="pagination-dots">{{ $element }}</span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <button class="pagination-btn active" aria-current="page">{{ $page }}</button>
                    @else
                        <a href="{{ $url }}" class="pagination-btn">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Button --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="pagination-btn" rel="next">
                <span class="desktop-only">Next</span>
                <svg class="u-icon-sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
        @else
            <button class="pagination-btn" disabled>
                <span class="desktop-only">Next</span>
                <svg class="u-icon-sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
        @endif
    </div>

    {{-- Pagination Info --}}
    <div class="pagination-info">
        Menampilkan <strong>{{ $paginator->firstItem() }}</strong> - <strong>{{ $paginator->lastItem() }}</strong> dari <strong>{{ $paginator->total() }}</strong> data
    </div>
</nav>

    @once
    @push('styles')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/admin-partials-pagination.css') }}">
    @endpush
    @endonce

@endif
{{-- Dikembangkan oleh Institut Teknologi Del --}}
