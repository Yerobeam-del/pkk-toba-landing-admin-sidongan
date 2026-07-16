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
                            <h3 class="footer-contact-title">Contact Us:</h3>
                            <div class="footer-contact-links">
                                <a href="mailto:info@pkktoba.id" class="footer-contact-link">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="contact-icon">
                                        <rect width="20" height="16" x="2" y="4" rx="2"></rect>
                                        <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
                                    </svg>
                                    info@pkktoba.id
                                </a>
                                <a href="tel:+6282120194130" class="footer-contact-link">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="contact-icon">
                                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                    </svg>
                                    +62 821-2019-4130
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
                <a href="{{ $app->url ?? '#' }}" target="_blank" class="quick-access-item" title="{{ $app->name }}">
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
            <p class="footer-copyright">Copyright © {{ date('Y') }} PKK Kabupaten Toba. All Rights Reserved.</p>
        </div>
        
    </div>
</footer>