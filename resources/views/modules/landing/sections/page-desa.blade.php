{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
<div class="page" id="page-desa">
    <div class="page-header u-a75">
        <div class="page-header-content">
            <h1>Data Desa</h1>
            <p>Daftar desa dan kelurahan di Kabupaten Toba, Sumatera Utara</p>
            <div class="breadcrumb">
                <a data-nav="beranda">Beranda</a><span>/</span><span class="current">Desa</span>
            </div>
        </div>
    </div>
    
    <section class="desa-section">
        {{-- Loading State --}}
        <div id="desa-loading" style="text-align: center; padding: 5rem 2rem;">
            <div style="font-size: 1.2rem; color: #64748b; font-weight: 500;">Memuat data desa...</div>
        </div>

        {{-- Filter Container (Akan diisi otomatis oleh JS berdasarkan data database) --}}
        <div class="desa-filter u-hidden" id="desaFilter">
        </div>
        
        {{-- Grid Container (Kartu desa akan muncul di sini) --}}
        <div class="desa-grid u-hidden" id="desaGrid">
        </div>

        {{-- Empty State --}}
        <div id="desa-empty-state" style="display: none; text-align: center; padding: 5rem 2rem; max-width: 650px; margin: 0 auto;">
            
            <!-- Icon Circle (Hijau Teal) -->
            <div style="width: 120px; height: 120px; margin: 0 auto 2rem; background: linear-gradient(135deg, rgba(15,107,99,0.1), rgba(20,184,166,0.1)); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="#0f6b63" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="opacity: 0.8;">
                    <path d="M3 21h18M5 21V7l8-4 8 4v14M8 21v-4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v4"/>
                    <rect x="9" y="9" width="6" height="6"/>
                </svg>
            </div>
            
            <h3 style="font-size: 1.75rem; font-weight: 800; color: #1e293b; margin: 0 0 0.75rem 0;">
                Belum Ada Data Desa
            </h3>
            
            <p style="color: #64748b; font-size: 1.05rem; line-height: 1.7; margin: 0 auto 2rem; max-width: 500px;">
                Data desa dan kelurahan di Kabupaten Toba sedang dalam proses pengumpulan. 
                Silakan kunjungi kembali nanti untuk informasi terbaru.
            </p>
            
            <!-- Tombol (Hijau Teal) -->
            <a data-nav="beranda" 
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.875rem 2rem; background: linear-gradient(135deg, #0f6b63, #14b8a6); color: #fff; border-radius: 12px; font-weight: 600; text-decoration: none; cursor: pointer; transition: all 0.3s; box-shadow: 0 4px 15px rgba(15,107,99,0.3);"
               
               >
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
                Kembali ke Beranda
            </a>
        </div>
    </section>
</div>

    @once
    @push('scripts')
    <script src="{{ asset('assets/landing/js/modules-landing-sections-page-desa.js') }}"></script>
    @endpush
    @endonce

{{-- Dikembangkan oleh Institut Teknologi Del --}}
