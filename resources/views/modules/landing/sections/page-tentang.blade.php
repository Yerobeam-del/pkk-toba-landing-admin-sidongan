{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
    @once
    @push('styles')
    <link rel="stylesheet" href="{{ asset('assets/landing/css/modules-landing-sections-page-tentang.css') }}">
    @endpush
    @endonce


<div class="page u-hidden" id="page-tentang">
    <div class="page-header u-a75">
        <div class="page-header-content" style="padding: 4rem 2rem 2rem; text-align: center; color: #fff;">
            <h1 id="tentangJudul" style="font-size: 2.5rem; font-weight: 800; margin-bottom: 0.5rem;">Tentang Kami</h1>
            <p id="tentangSubjudul" style="font-size: 1.1rem; opacity: 0.9; margin-bottom: 1rem;">Informasi tentang PKK Kabupaten Toba</p>
            <div class="breadcrumb" style="display: flex; justify-content: center; gap: 0.5rem; font-size: 0.9rem;">
                <a href="#" data-nav="beranda" style="color: #fff; text-decoration: none; opacity: 0.8; cursor: pointer;">Beranda</a>
                <span style="opacity: 0.6;">/</span>
                <span class="current" style="font-weight: 600;">Tentang</span>
            </div>
        </div>
    </div>

    <section class="info-section" style="background: #fff;">
        <div class="tentang-container">
            {{-- Left Content --}}
            <div class="tentang-text">
                <h2 id="tentangHeading">
                    Memberdayakan Keluarga, Mensejahterakan Masyarakat
                </h2>
                <p id="tentangDeskripsi">
                    PKK Kabupaten Toba berkomitmen untuk terus berinovasi dalam meningkatkan kesejahteraan keluarga dan masyarakat. Melalui berbagai program unggulan, kami berupaya membangun sumber daya manusia yang berkualitas dan berdaya saing.
                </p>

                <ul id="tentangPrograms" class="tentang-list">
                    <li>
                        <svg fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <span>Program ketahanan dan kesejahteraan keluarga</span>
                    </li>
                    <li>
                        <svg fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <span>Pemberdayaan ekonomi keluarga</span>
                    </li>
                    <li>
                        <svg fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <span>Peningkatan kesehatan ibu dan anak</span>
                    </li>
                    <li>
                        <svg fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <span>Pelestarian nilai budaya dan kearifan lokal</span>
                    </li>
                    <li>
                        <svg fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <span>Pengembangan pendidikan dan keterampilan</span>
                    </li>
                </ul>
            </div>

            {{-- Right Content - Maps --}}
            <div class="tentang-map-wrapper">
                {{-- Map Info Header --}}
                <div class="tentang-map-header">
                    <h3 id="tentangMapsTitle">
                        <svg class="u-shrink-0" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                        Lokasi Kantor PKK Kabupaten Toba
                    </h3>
                    <p id="tentangMapsAddress">
                        Jl. D.I Panjaitan No.1, Napitupulu, Kec. Balige, Kabupaten Toba, Sumatera Utara
                    </p>
                    <a id="tentangMapsLink" href="https://goo.gl/maps/xxxxx" target="_blank" rel="noopener noreferrer" style="display:inline-flex;align-items:center;gap:0.5rem;min-height:44px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                            <polyline points="15 3 21 3 21 9"/>
                            <line x1="10" y1="14" x2="21" y2="3"/>
                        </svg>
                        Buka di Google Maps
                    </a>
                </div>

                {{-- Map Container --}}
                <div id="tentangMaps" class="tentang-map-frame">
                    {{-- Maps will be loaded here --}}
                </div>

                {{-- Map Tips --}}
                <div class="map-tips">
                    <div>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#14b8a6" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="16" x2="12" y2="12"/>
                            <line x1="12" y1="8" x2="12.01" y2="8"/>
                        </svg>
                        <span><strong>Tips:</strong> Gunakan mouse/touch untuk zoom dan geser peta</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

    @once
    @push('scripts')
    <script src="{{ asset('assets/landing/js/modules-landing-sections-page-tentang.js') }}"></script>
    @endpush
    @endonce

{{-- Dikembangkan oleh Institut Teknologi Del --}}
