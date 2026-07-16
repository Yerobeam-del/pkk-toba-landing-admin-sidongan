@extends('modules.landing.layouts.app')

@section('title', $news->title . ' - Berita')

@push('styles')
<style>
    body:has(.news-detail-wrapper) #newsModal,
    body:has(.news-detail-wrapper) .news-modal-overlay {
        display: none !important;
    }
</style>
@endpush

@section('content')
<div class="news-detail-wrapper">
    <header class="news-page-header">
        <div class="news-page-header-content">
            <h1>Berita</h1>
            <p>Informasi terkini dari PKK Kabupaten Toba</p>
            <nav class="news-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('landing.home') }}">Beranda</a>
                <span>/</span>
                <a href="{{ url('/#berita') }}" onclick="window.location.href='{{ url('/#berita') }}'; return false;">Berita</a>
                <span>/</span>
                <span class="current">{{ Str::limit($news->title, 40) }}</span>
            </nav>
        </div>
    </header>

    <div class="news-detail-container">
        <div class="news-detail-article">
            <header>
                <h1 class="news-detail-title">{{ $news->title }}</h1>
                
                <div class="news-detail-meta">
                    @if($news->category)
                        <span class="news-detail-category">{{ $news->category }}</span>
                    @endif
                    <span class="news-detail-date">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        {{ $news->published_at?->format('d F Y') ?? $news->created_at->format('d F Y') }}
                    </span>
                </div>
            </header>

            @if($news->image_path)
                <figure class="news-detail-image">
                    <img src="{{ asset('storage/' . $news->image_path) }}" 
                         alt="{{ $news->title }}"
                         onerror="this.src='{{ asset('assets/landing/images/berita/default.jpg') }}'">
                </figure>
            @endif

            @if($news->excerpt)
                <p class="news-detail-excerpt">{{ $news->excerpt }}</p>
            @endif

            <div class="news-detail-content">
                {!! $news->content !!}
            </div>

            <div class="news-detail-share">
                <p class="news-detail-share-title">Bagikan Artikel Ini:</p>
                <div class="news-share-buttons">
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" 
                       target="_blank" 
                       class="news-share-btn facebook"
                       rel="noopener noreferrer">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                        Facebook
                    </a>
                    <a href="https://twitter.com/intent/tweet?text={{ urlencode($news->title) }}&url={{ urlencode(request()->url()) }}" 
                       target="_blank" 
                       class="news-share-btn twitter"
                       rel="noopener noreferrer">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        X
                    </a>
                    <a href="https://wa.me/?text={{ urlencode($news->title . ' ' . request()->url()) }}" 
                       target="_blank" 
                       class="news-share-btn whatsapp"
                       rel="noopener noreferrer">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>
                        WhatsApp
                    </a>
                </div>
            </div>

            @if(isset($relatedNews) && $relatedNews->count() > 0)
            <div class="news-detail-related">
                <h2 class="news-detail-related-title">Berita Terkait</h2>
                <div class="news-related-grid">
                    @foreach($relatedNews as $item)
                        <a href="{{ route('news.show', $item->slug) }}" class="news-related-card">
                            @if($item->image_path)
                                <img src="{{ asset('storage/' . $item->image_path) }}" 
                                     alt="{{ $item->title }}"
                                     class="news-related-image"
                                     onerror="this.src='{{ asset('assets/landing/images/berita/default.jpg') }}'">
                            @else
                                <div class="news-related-image-placeholder">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                                        <circle cx="8.5" cy="8.5" r="1.5"/>
                                        <path d="M21 15l-5-5L5 21"/>
                                    </svg>
                                </div>
                            @endif
                            <div class="news-related-body">
                                @if($item->category)
                                    <span class="news-related-category">{{ $item->category }}</span>
                                @endif
                                <h3 class="news-related-title">{{ Str::limit($item->title, 60) }}</h3>
                                @if($item->excerpt)
                                    <p class="news-related-excerpt">{{ Str::limit($item->excerpt, 80) }}</p>
                                @endif
                                <span class="news-related-date">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="4" width="18" height="18" rx="2"/>
                                        <line x1="16" y1="2" x2="16" y2="6"/>
                                        <line x1="8" y1="2" x2="8" y2="6"/>
                                        <line x1="3" y1="10" x2="21" y2="10"/>
                                    </svg>
                                    {{ $item->published_at?->format('d M Y') ?? $item->created_at->format('d M Y') }}
                                </span>
                                <span class="news-related-link">
                                    Baca Selengkapnya
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                        <path d="M5 12h14M12 5l7 7-7 7"/>
                                    </svg>
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Hapus modal dari DOM
        var modal = document.getElementById('newsModal');
        if (modal && modal.parentNode) {
            modal.parentNode.removeChild(modal);
        }
        
        // Reset body
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
        
        // Lazy load images
        var images = document.querySelectorAll('.news-detail-content img');
        images.forEach(function(img) {
            img.setAttribute('loading', 'lazy');
        });
        
        // Set active navbar
        document.querySelectorAll('.navbar-links a').forEach(function(link) {
            link.classList.remove('active-link');
            var text = link.textContent.trim().toLowerCase();
            var href = link.getAttribute('href') || '';
            if (text === 'berita' || href.includes('/berita')) {
                link.classList.add('active-link');
            }
        });
    });
</script>
@endpush
@endsection