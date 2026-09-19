{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
<footer class="footer">
    <div class="footer-container">

        {{-- Title --}}
        <div class="footer-title-section">
            <h2 class="footer-title">{{ $footerTitle }}</h2>
        </div>

        {{-- Main Content: 2 Columns --}}
        <div class="footer-main">

            {{-- Left Column: Logo Kabupaten Toba + Logo PKK + Info --}}
            <div class="footer-left">
                <div class="footer-brand-row">
                    @php
                        // Logo dinamis dari Admin Panel > Pengaturan Situs;
                        // fallback ke file bawaan bila tidak diunggah.
                        $logoToba = $footerLogoToba
                            ? asset('storage/' . $footerLogoToba)
                            : asset('assets/landing/images/Logo-Kabupaten-Toba-Transparent.png');
                        $logoPkk = $footerLogoPkk
                            ? asset('storage/' . $footerLogoPkk)
                            : asset('assets/landing/images/Logo-PKK-Transparent.png');
                    @endphp

                    <img src="{{ $logoToba }}" alt="Kabupaten Toba Logo" class="footer-secondary-logo">

                    <img src="{{ $logoPkk }}" alt="PKK Logo" class="footer-brand-logo">

                    <div class="footer-info">
                        <p class="footer-address">{!! nl2br(e($footerAddress)) !!}</p>

                        <div class="footer-contact">
                            <h3 class="footer-contact-title">Ikuti Kami:</h3>
                            <div class="footer-contact-links">
                                <a href="{{ $instagramUrl }}" target="_blank" rel="noopener noreferrer" class="footer-contact-link">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="contact-icon">
                                        <rect width="20" height="20" x="2" y="2" rx="5" ry="5"></rect>
                                        <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                                        <line x1="17.5" x2="17.51" y1="6.5" y2="6.5"></line>
                                    </svg>
                                    {{ $instagramHandle }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Quick Access (data dari FooterComposer) --}}
            <div class="footer-right">
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
            </div>
        </div>

        {{-- Bottom Copyright --}}
        <div class="footer-bottom">
            <p class="footer-copyright">&copy; {{ date('Y') }} {{ $footerCopyright }}. All rights reserved.</p>
        </div>

    </div>
</footer>
{{-- Dikembangkan oleh Institut Teknologi Del --}}
