{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
<footer class="footer">
    <div class="footer-container">

        {{-- Title --}}
        <div class="footer-title-section">
            <h2 class="footer-title">PKK Kabupaten Toba</h2>
        </div>

        {{-- Main Content: 2 Columns --}}
        <div class="footer-main">

            {{-- Left Column: Logo + Info --}}
            <div class="footer-left">
                <div class="footer-brand-row">
                    <img src="{{ asset('assets/landing/images/Logo-PKK-Transparent.png') }}" alt="PKK Logo" class="footer-brand-logo">

                    <div class="footer-info">
                        <p class="footer-address">
                            Jl. D. I. Panjaitan, No. 1, Balige,<br>
                            Kabupaten Toba,<br>
                            Sumatera Utara 22311
                        </p>

                        <div class="footer-contact">
                            <h3 class="footer-contact-title">Ikuti Kami:</h3>
                            <div class="footer-contact-links">
                                <a href="https://www.instagram.com/tppkktoba_/" target="_blank" rel="noopener noreferrer" class="footer-contact-link">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="contact-icon">
                                        <rect width="20" height="20" x="2" y="2" rx="5" ry="5"></rect>
                                        <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                                        <line x1="17.5" x2="17.51" y1="6.5" y2="6.5"></line>
                                    </svg>
                                    @tppkktoba_
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Logo Kabupaten Toba --}}
            <div class="footer-right">
                <img src="{{ asset('assets/landing/images/Logo-Kabupaten-Toba-Transparent.png') }}" alt="Kabupaten Toba Logo" class="footer-secondary-logo">
            </div>
        </div>

        {{-- Quick Access Section - Dynamic from Database --}}
        @php
            // Ambil aplikasi yang aktif, status active, DAN show_in_footer = true
            $quickAccessApps = \App\Models\Application::where('is_active', true)
                ->where('status', 'active')
                ->where('show_in_footer', true)
                ->orderBy('sort_order')
                ->get();
        @endphp

        @if($quickAccessApps->count() > 0)
        <div class="footer-quick-access">
            <h3 class="quick-access-title">Quick Access</h3>
            <div class="quick-access-links">
                @foreach($quickAccessApps as $app)
                <a href="{{ $app->url ?? '#' }}" target="_blank" class="quick-access-item" title="{{ $app->name }}" style="min-height:44px;">
                    @if($app->icon)
                        <img src="{{ asset('storage/' . $app->icon) }}" alt="{{ $app->short_name }}" class="app-icon">
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="14" y="14" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                        </svg>
                    @endif
                    <span>{{ $app->short_name ?? $app->name }}</span>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Bottom Copyright --}}
        <div class="footer-bottom">
            <p class="footer-copyright">&copy; {{ date('Y') }} TP-PKK Kabupaten Toba. All rights reserved.</p>
        </div>

    </div>
</footer>
{{-- Dikembangkan oleh Institut Teknologi Del --}}
