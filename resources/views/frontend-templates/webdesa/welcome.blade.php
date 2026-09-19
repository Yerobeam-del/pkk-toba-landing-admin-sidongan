{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     Beranda template Web Desa klasik — recreation mandiri dari
     pola website desa resmi. Data seluruhnya dari SIEDA.
     ============================================================ --}}
@extends('frontend-templates.webdesa.layout')
@section('title', 'Home Desa — ' . ($settings['nama_desa'] ?? 'Website Desa'))

@section('content')
    @php
        $desaName = $settings['nama_desa'] ?? 'Desa';
        $profilDesa = $profile->firstWhere('title', 'Profil Desa');
        $kepalaNama = $settings['kepala_desa_nama'] ?? null;
    @endphp

    {{-- ================= INTRO + PENCARIAN ================= --}}
    <section class="wd-intro">
        <div class="wd-container wd-intro-inner">
            <div class="wd-intro-copy">
                <h1>{{ $settings['judul_beranda'] ?? 'Selamat Datang di ' . $desaName }}</h1>
                <p>{{ $settings['deskripsi_beranda'] ?? 'Pusat informasi desa, pelayanan publik, dan potensi masyarakat.' }}</p>
                <form class="wd-home-search" action="{{ route('frontend.news.index') }}" method="get">
                    <label class="sr-only" for="wd-home-search-input">Cari informasi desa</label>
                    <input id="wd-home-search-input" type="search" name="q" placeholder="Cari informasi ..." value="{{ request('q') }}">
                    <button type="submit" aria-label="Cari informasi">
                        <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </section>

    {{-- ================= NAVIGASI IKON ================= --}}
    <nav class="wd-icon-nav" aria-label="Akses informasi desa">
        <div class="wd-container wd-icon-nav-list">
            <a href="{{ $profilDesa ? route('frontend.profile.view', ['id' => $profilDesa->id]) : route('frontend.index') }}" class="wd-icon-nav-item">
                <span class="wd-icon-circle"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v16H6.5A2.5 2.5 0 0 0 4 21.5z"/><path d="M4 5.5v16M8 7h8M8 11h8"/></svg></span><span>Profil Desa</span>
            </a>
            <a href="{{ route('frontend.apbd-desa.index') }}" class="wd-icon-nav-item">
                <span class="wd-icon-circle"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19V5M4 19h17"/><path d="m7 15 4-4 3 2 5-6"/></svg></span><span>Infografis</span>
            </a>
            <a href="{{ route('frontend.potential.index') }}" class="wd-icon-nav-item">
                <span class="wd-icon-circle"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 4 7v10l8 4 8-4V7z"/><path d="m4 7 8 4 8-4M12 11v10"/></svg></span><span>Potensi Desa</span>
            </a>
            <a href="{{ route('frontend.layanan.index') }}" class="wd-icon-nav-item">
                <span class="wd-icon-circle"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12h16M13 5l7 7-7 7"/><path d="M4 5v14"/></svg></span><span>Layanan Desa</span>
            </a>
            <a href="{{ route('frontend.news.index') }}" class="wd-icon-nav-item">
                <span class="wd-icon-circle"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M5 4h14v16H5zM8 8h8M8 12h8M8 16h5"/></svg></span><span>Berita</span>
            </a>
        </div>
    </nav>

    {{-- ================= SELAMAT DATANG ================= --}}
    <section class="wd-section wd-welcome-section" id="selamat-datang">
        <div class="wd-container wd-welcome">
            <div class="wd-welcome-copy">
                <h1>Selamat Datang</h1>
                <h2>Tentang {{ $desaName }}.</h2>
                <p>{{ $profilDesa ? strip_tags($profilDesa->description) : 'Selamat datang di portal informasi resmi desa. Temukan informasi pemerintahan, pelayanan, berita, dan potensi desa.' }}</p>
                @if ($profilDesa)
                    <a href="{{ route('frontend.profile.view', ['id' => $profilDesa->id]) }}" class="wd-button">Lihat Profil Desa
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </a>
                @endif
            </div>
            <div class="wd-welcome-image">
                @if ($profilDesa && !empty($profilDesa->image))
                    <img src="{{ asset(Storage::url($profilDesa->image)) }}" alt="Profil {{ $desaName }}" loading="lazy">
                @else
                    <img src="{{ asset('assets/landing/images/Background_1.jpg') }}" alt="Pemandangan {{ $desaName }}" loading="lazy">
                @endif
            </div>
        </div>
    </section>

    {{-- ================= BERITA + SIDEBAR ================= --}}
    <section class="wd-section wd-news-section" id="berita">
        <div class="wd-container wd-content-layout">
            <div class="wd-news-column">
                <div class="wd-section-heading">
                    <h2>Berita Desa</h2>
                    @if ($news->count())
                        <a class="wd-heading-link" href="{{ route('frontend.news.index') }}">Lihat Semua
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                        </a>
                    @endif
                </div>
                <div class="wd-news-grid">
                    @forelse ($news->take(6) as $item)
                        <article class="wd-news-card">
                            <a href="{{ route('frontend.news.view', ['id' => $item->id]) }}">
                                <img class="wd-news-image" src="{{ asset(Storage::url($item->image)) }}" alt="{{ $item->title }}" loading="lazy">
                            </a>
                            <div class="wd-news-body">
                                <div class="wd-news-meta">
                                    <span class="wd-news-category"><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a3 3 0 0 0-3 3v1.1A5.99 5.99 0 0 0 6 11.3V16l-2 2v1h16v-1l-2-2v-4.7a5.99 5.99 0 0 0-3-5.2V5a3 3 0 0 0-3-3Zm0 20a2.5 2.5 0 0 0 2.35-1.65h-4.7A2.5 2.5 0 0 0 12 22Z"/></svg> Berita Desa</span>
                                    <span aria-hidden="true">·</span>
                                    <time datetime="{{ $item->updated_at }}">{{ \Carbon\Carbon::parse($item->updated_at)->locale('id')->translatedFormat('d F Y') }}</time>
                                </div>
                                <h3 class="wd-news-title"><a href="{{ route('frontend.news.view', ['id' => $item->id]) }}">{{ \Illuminate\Support\Str::limit($item->title, 75) }}</a></h3>
                                <p class="wd-news-excerpt">{{ \Illuminate\Support\Str::limit(strip_tags($item->description), 115) }}</p>
                            </div>
                        </article>
                    @empty
                        <div class="wd-empty">Belum ada berita yang dipublikasikan.</div>
                    @endforelse
                </div>
            </div>

            <aside class="wd-sidebar">
                @if ($kepalaNama)
                    <section class="wd-sidebar-card wd-official-card" id="kepala-desa">
                        <h3>Kepala Desa</h3>
                        <div class="wd-official-photo">
                            @if (!empty($settings['kepala_desa_foto']))
                                <img src="{{ asset(Storage::url($settings['kepala_desa_foto'])) }}" alt="{{ $kepalaNama }}">
                            @else
                                <div class="wd-official-placeholder"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div>
                            @endif
                        </div>
                        <h4>{{ $kepalaNama }}</h4>
                        <p class="wd-official-role">Kepala Desa {{ $desaName }}</p>
                        @if ($settings['kepala_desa_sambutan'] ?? null)
                            <p class="wd-official-speech">{{ $settings['kepala_desa_sambutan'] }}</p>
                        @endif
                    </section>
                @endif

                @if ($potensi->count())
                    <section class="wd-sidebar-card wd-potential-sidebar" id="potensi">
                        <div class="wd-sidebar-heading"><h3>Potensi Desa</h3></div>
                        <div class="wd-potential-list">
                            @foreach ($potensi->take(5) as $item)
                                <a href="{{ route('frontend.potential.view', ['id' => $item->id]) }}" class="wd-potential-row">
                                    <img src="{{ asset(Storage::url($item->image)) }}" alt="{{ $item->title }}" loading="lazy">
                                    <span>{{ \Illuminate\Support\Str::limit($item->title, 46) }}</span>
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                                </a>
                            @endforeach
                        </div>
                        <a href="{{ route('frontend.potential.index') }}" class="wd-sidebar-more">Lihat Semua
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                        </a>
                    </section>
                @endif
            </aside>
        </div>
    </section>
@endsection
{{-- Dikembangkan oleh Institut Teknologi Del --}}
