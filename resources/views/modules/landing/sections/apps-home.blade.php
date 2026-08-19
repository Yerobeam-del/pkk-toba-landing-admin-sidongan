{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
<section class="apps-home-section" id="aplikasiSection" style="padding: 4rem 2rem; background: #f8fafc;">
        @once
    @push('styles')
    <link rel="stylesheet" href="{{ asset('assets/landing/css/modules-landing-sections-apps-home.css') }}">
    @endpush
    @endonce


    {{-- HEADER SECTION --}}
    <div class="apps-section-header">
        <div class="apps-section-label">APLIKASI AKTIF</div>
        <h2 class="apps-section-title">Sistem yang Tersedia</h2>
        <p class="apps-section-desc">Akses layanan digital PKK Kabupaten Toba melalui aplikasi-aplikasi yang telah kami sediakan.</p>
    </div>

    {{-- Loading State --}}
    <div id="apps-loading" style="text-align: center; padding: 5rem 2rem;">
        <div style="font-size: 1.2rem; color: #64748b; font-weight: 500;">Memuat data aplikasi...</div>
    </div>

    {{-- Content Grid --}}
    <div class="apps-home-grid u-hidden" id="apps-grid"></div>

    {{-- Empty State --}}
    <div class="u-hidden" id="apps-empty">
        <div style="text-align: center; padding: 5rem 2rem; background: linear-gradient(135deg, rgba(15,118,110,0.05), rgba(56,161,105,0.05)); border-radius: 20px; max-width: 1200px; margin: 0 auto;">
            <div style="width: 100px; height: 100px; margin: 0 auto 2rem; background: linear-gradient(135deg, rgba(15,118,110,0.1), rgba(56,161,105,0.1)); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <svg width="50" height="50" viewBox="0 0 24 24" fill="none" stroke="#0f766e" stroke-width="1.5">
                    <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
                    <line x1="8" y1="21" x2="16" y2="21"/>
                    <line x1="12" y1="17" x2="12" y2="21"/>
                </svg>
            </div>
            <h3 style="font-size: 1.5rem; font-weight: 800; color: #1e293b; margin: 0 0 0.5rem 0;">Belum Ada Aplikasi</h3>
            <p style="color: #64748b; font-size: 0.95rem; margin: 0;">Sistem aplikasi sedang dalam tahap pengembangan.</p>
        </div>
    </div>
</section>

    @once
    @push('scripts')
    <script src="{{ asset('assets/landing/js/modules-landing-sections-apps-home.js') }}"></script>
    @endpush
    @endonce

{{-- Dikembangkan oleh Institut Teknologi Del --}}
