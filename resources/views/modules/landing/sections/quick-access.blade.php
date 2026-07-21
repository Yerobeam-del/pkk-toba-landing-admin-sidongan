<section class="quick-access-section" id="quickAccess">
    <div class="section-header">
        <div class="section-label">Akses Cepat</div>
        <h2 class="section-title">Layanan Kami</h2>
        <p class="section-desc">Akses berbagai layanan digital PKK Kabupaten Toba dengan mudah dan cepat.</p>
    </div>

    {{-- 🎨 CSS KHUSUS: Paksa 1 Baris di Desktop --}}
    <style>
        .quick-access-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr) !important; /* 6 kartu = 1 baris */
            gap: 1.25rem;
            width: 100%;
        }

        /* Tablet: 3 kolom per baris */
        @media (max-width: 1024px) {
            .quick-access-grid { grid-template-columns: repeat(3, 1fr) !important; }
        }

        /* HP Besar: 2 kolom per baris */
        @media (max-width: 640px) {
            .quick-access-grid { grid-template-columns: repeat(2, 1fr) !important; }
        }

        /* HP Kecil: 1 kolom */
        @media (max-width: 480px) {
            .quick-access-grid { grid-template-columns: 1fr !important; }
        }
    </style>

    <div class="quick-access-grid">
        <a onclick="navigateTo('struktur')" class="quick-access-card">
            <div class="quick-access-icon" style="background: linear-gradient(135deg, #d69e2e, #ecc94b);">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <h3>Struktur</h3>
            <p>Pengurus TP PKK</p>
        </a>
        <a onclick="navigateTo('aplikasi')" class="quick-access-card">
            <div class="quick-access-icon" style="background: linear-gradient(135deg, #2b6cb0, #3182ce);">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
            </div>
            <h3>Aplikasi</h3>
            <p>SIEDA, SIDONGAN & lainnya</p>
        </a>
        <a onclick="navigateTo('berita')" class="quick-access-card">
            <div class="quick-access-icon" style="background: linear-gradient(135deg, #276749, #38a169);">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8"/><path d="M15 18h-5"/><path d="M10 6h8v4h-8V6Z"/></svg>
            </div>
            <h3>Berita</h3>
            <p>Kabar terkini PKK</p>
        </a>
        {{-- <a onclick="navigateTo('desa')" class="quick-access-card">
            <div class="quick-access-icon" style="background: linear-gradient(135deg, #9b2c2c, #e53e3e);">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            </div>
            <h3>Data Desa</h3>
            <p>Daftar desa & kelurahan</p>
        </a> --}}
        <a onclick="navigateTo('sk')" class="quick-access-card">
            <div class="quick-access-icon" style="background: linear-gradient(135deg, #553c9a, #805ad5);">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            </div>
            <h3>SK & Dokumen</h3>
            <p>Surat Keputusan resmi</p>
        </a>
        <a onclick="navigateTo('template')" class="quick-access-card">
            <div class="quick-access-icon" style="background: linear-gradient(135deg, #2b6cb0, #3182ce);">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
            </div>
            <h3>Template</h3>
            <p>Template siap cetak</p>
        </a>
    </div>
</section>
