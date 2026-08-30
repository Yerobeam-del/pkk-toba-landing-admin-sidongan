{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
@extends('modules.landing.layouts.app')

@section('title', $news->title . ' - Berita')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/landing/css/modules-landing-news-detail.css') }}">

@endpush

@php
    $ogTitle = $news->title;
    $ogDescription = $news->excerpt ?: Str::limit(strip_tags($news->content), 160);
    $ogUrl = request()->url();
    $ogImage = $news->image_path ? asset('storage/' . $news->image_path) : asset('assets/landing/images/Logo-PKK-Transparent.png');
    $ogType = 'article';
    $ogSiteName = 'PKK Kabupaten Toba';
    $publishedTime = $news->published_at?->toIso8601String();
    $modifiedTime = $news->updated_at?->toIso8601String();
@endphp

@push('meta')
    {{-- Standard SEO --}}
    <meta name="description" content="{{ $ogDescription }}">
    <link rel="canonical" href="{{ $ogUrl }}">

    {{-- Open Graph / Facebook --}}
    <meta property="og:type" content="{{ $ogType }}">
    <meta property="og:url" content="{{ $ogUrl }}">
    <meta property="og:title" content="{{ $ogTitle }}">
    <meta property="og:description" content="{{ $ogDescription }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="{{ $ogSiteName }}">
    <meta property="og:locale" content="id_ID">
    @if($publishedTime)
        <meta property="article:published_time" content="{{ $publishedTime }}">
    @endif
    @if($modifiedTime)
        <meta property="article:modified_time" content="{{ $modifiedTime }}">
    @endif
    @if($news->category)
        <meta property="article:section" content="{{ $news->category }}">
    @endif
    @if($news->author)
        <meta property="article:author" content="{{ $news->author }}">
    @endif

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $ogTitle }}">
    <meta name="twitter:description" content="{{ $ogDescription }}">
    <meta name="twitter:image" content="{{ $ogImage }}">
@endpush

@section('content')
<div class="news-progress-bar" id="newsProgressBar"></div>
<button type="button" class="news-back-to-top" id="newsBackToTop" aria-label="Kembali ke atas">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <polyline points="18 15 12 9 6 15"/>
    </svg>
</button>
<div class="news-detail-wrapper">
    <header class="news-page-header">
        <div class="news-page-header-content">
            <h1>Berita</h1>
            <p>Informasi terkini dari PKK Kabupaten Toba</p>
            <nav class="news-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('landing.home') }}">Beranda</a>
                <span>/</span>
                <a href="{{ url('/#berita') }}">Berita</a>
                <span>/</span>
                <span class="current">{{ Str::limit($news->title, 40) }}</span>
            </nav>
        </div>
    </header>

    <div class="news-detail-container">
        <div class="news-detail-article">
            <a href="{{ url('/#berita') }}" class="news-back-link">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
                Kembali ke Daftar Berita
            </a>

            <header>
                <h1 class="news-detail-title">{{ $news->title }}</h1>

                <div class="news-detail-meta">
                    @if($news->category)
                        <span class="news-detail-category">{{ $news->category }}</span>
                    @endif
                    <time class="news-detail-date" datetime="{{ $news->published_at?->toIso8601String() ?? $news->created_at->toIso8601String() }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        {{ $news->published_at?->locale('id')->translatedFormat('d F Y') ?? $news->created_at->locale('id')->translatedFormat('d F Y') }}
                    </time>
                    @php
                        $wordCount = str_word_count(strip_tags($news->content));
                        $readingTime = max(1, (int) ceil($wordCount / 200));
                    @endphp
                    <span class="news-detail-reading-time">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                        {{ $readingTime }} menit membaca
                    </span>
                    @if($news->author)
                    <span class="news-detail-author">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                        {{ $news->author }}
                    </span>
                    @endif
                </div>
            </header>

            @if($news->image_path)
                <figure class="news-detail-image">
                    <img src="{{ asset('storage/' . $news->image_path) }}"
                         alt="{{ $news->title }}"
                         loading="lazy"
                         onerror="this.src='{{ asset('assets/landing/images/berita/default.jpg') }}'">
                </figure>
            @endif

            @if($news->images->count() > 0)
            <div class="news-detail-gallery">
                @foreach($news->images as $img)
                <figure class="news-gallery-item">
                    <img src="{{ asset('storage/' . $img->image_path) }}"
                         alt="{{ $img->caption ?? $news->title }}"
                         loading="lazy"
                         onclick="window.open(this.src, '_blank')"
                         style="cursor:pointer;">
                    @if($img->caption)
                        <figcaption>{{ $img->caption }}</figcaption>
                    @endif
                </figure>
                @endforeach
            </div>
            @endif

            @if($news->excerpt)
                <p class="news-detail-excerpt">{{ $news->excerpt }}</p>
            @endif

            <div class="news-detail-content">
                {!! $news->content !!}
            </div>

            <div class="news-detail-actions">
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

                <button type="button" class="news-print-btn" id="newsPrintBtn" aria-label="Cetak artikel ini">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6 9 6 2 18 2 18 9"/>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                        <rect x="6" y="14" width="12" height="8"/>
                    </svg>
                    Cetak Artikel
                </button>
            </div>

            @if(isset($relatedNews) && $relatedNews->count() > 0)
            <div class="news-detail-related">
                <h2 class="news-detail-related-title">Berita Terkait</h2>
                <div class="news-related-grid">
                    @foreach($relatedNews as $item)
                        <a href="{{ route('news.show', $item->slug) }}" class="news-related-card">                                @if($item->image_path)
                                <img src="{{ asset('storage/' . $item->image_path) }}"
                                     alt="{{ $item->title }}"
                                     class="news-related-image"
                                     loading="lazy"
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
                                    {{ $item->published_at?->locale('id')->translatedFormat('d F Y') ?? $item->created_at->locale('id')->translatedFormat('d F Y') }}
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
    <script src="{{ asset('assets/landing/js/modules-landing-news-detail.js') }}"></script>

@endpush
@endsection
{{-- Dikembangkan oleh Institut Teknologi Del --}}
