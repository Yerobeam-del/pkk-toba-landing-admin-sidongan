<style>
    /* Tentang Page Specific Styles */
    .tentang-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 4rem;
        align-items: center;
        max-width: 1200px;
        margin: 0 auto;
        padding: 4rem 2rem;
    }

    .tentang-text h2 {
        font-size: 2rem;
        font-weight: 800;
        color: var(--primary);
        margin-bottom: 1rem;
        line-height: 1.3;
    }

    .tentang-text p {
        color: var(--text-muted);
        line-height: 1.8;
        margin-bottom: 1.5rem;
        font-size: 1rem;
    }

    .tentang-list {
        list-style: none;
        padding: 0;
    }

    .tentang-list li {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 10px 0;
        color: #4a5568;
        font-size: 0.95rem;
    }

    .tentang-map-wrapper {
        position: relative;
        border-radius: 24px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.1);
        background: #fff;
    }

    .tentang-map-frame {
        width: 100%;
        height: 450px; /* FIX: Ditingkatkan agar kontrol/panah map tidak terpotong */
        border-radius: 24px;
        overflow: hidden;
    }

    /* FIX: Paksa iframe mengisi container dan abaikan style inline dari API */
    .tentang-map-frame iframe {
        width: 100% !important;
        height: 100% !important;
        border: none !important;
    }

    .tentang-map-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 2rem;
        background: linear-gradient(to top, rgba(30, 58, 95, 0.95), transparent);
        color: #fff;
        border-bottom-left-radius: 24px;
        border-bottom-right-radius: 24px;
    }

    /* Mobile Responsive Fixes */
    @media (max-width: 768px) {
        .tentang-container {
            grid-template-columns: 1fr;
            gap: 2rem;
            padding: 2rem 1rem;
        }

        .tentang-text {
            order: 1; /* FIX: Teks muncul di atas pada mobile */
        }

        .tentang-map-wrapper {
            order: 2; /* Map di bawah pada mobile */
            margin-top: 2rem;
        }

        .tentang-map-header {
            padding: 1.25rem 1rem;
            border-radius: 12px 12px 0 0;
        }

        .tentang-map-header h3 {
            font-size: 1.1rem;
        }

        .tentang-map-header p {
            font-size: 0.85rem;
        }

        .tentang-map-frame {
            height: 350px !important; /* Lebih pendek di mobile */
            border-radius: 0 0 12px 12px;
        }

        .tentang-map-wrapper > div:last-child {
            padding: 0.75rem 1rem;
            font-size: 0.8rem;
        }

        .tentang-map-wrapper > div:last-child > div {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .tentang-map-wrapper > div:last-child > div > div[style*="width: 1px"] {
            display: none; /* Hilangkan divider di mobile */
        }
    }
</style>

<div class="page" id="page-tentang" style="display: none;">
    <div class="page-header" style="background: linear-gradient(135deg, var(--primary), var(--primary-light));">
        <div class="page-header-content" style="padding: 4rem 2rem 2rem; text-align: center; color: #fff;">
            <h1 id="tentangJudul" style="font-size: 2.5rem; font-weight: 800; margin-bottom: 0.5rem;">Tentang Kami</h1>
            <p id="tentangSubjudul" style="font-size: 1.1rem; opacity: 0.9; margin-bottom: 1rem;">Informasi tentang PKK Kabupaten Toba</p>
            <div class="breadcrumb" style="display: flex; justify-content: center; gap: 0.5rem; font-size: 0.9rem;">
                <a onclick="navigateTo('beranda')" style="color: #fff; text-decoration: none; opacity: 0.8; cursor: pointer;">Beranda</a>
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
                    PKK Kabupaten Toba berkomitmen untuk terus berinovasi...
                </p>

                <ul id="tentangPrograms" class="tentang-list">
                    <li>
                        <svg style="flex-shrink: 0; margin-top: 2px; color: #48bb78; width: 20px; height: 20px;" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <span>Program ketahanan dan kesejahteraan keluarga</span>
                    </li>
                </ul>
            </div>

            {{-- Right Content - Maps --}}
            <div class="tentang-map-wrapper">
                {{-- Map Info Header --}}
                <div class="tentang-map-header" style="background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: #fff; padding: 1.5rem 2rem; border-radius: 16px 16px 0 0;">
                    <h3 style="font-size: 1.25rem; font-weight: 700; margin: 0 0 0.5rem 0; display: flex; align-items: center; gap: 0.75rem;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink: 0;">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                        Lokasi Kantor PKK Kabupaten Toba
                    </h3>
                    <p style="font-size: 0.95rem; opacity: 0.95; margin: 0 0 1rem 0;">
                        Jl. D.I Panjaitan No.1, Napitupulu, Kec. Balige, Kabupaten Toba, Sumatera Utara
                    </p>
                    <a href="https://goo.gl/maps/xxxxx" target="_blank" style="display: inline-flex; align-items: center; gap: 0.5rem; color: #fff; text-decoration: none; font-weight: 600; font-size: 0.9rem; padding: 0.5rem 1rem; background: rgba(255,255,255,0.2); border-radius: 8px; transition: all 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.3)'; this.style.transform='translateY(-2px)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'; this.style.transform='translateY(0)'">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                            <polyline points="15 3 21 3 21 9"/>
                            <line x1="10" y1="14" x2="21" y2="3"/>
                        </svg>
                        Buka di Google Maps
                    </a>
                </div>

                {{-- Interactive Map Container --}}
                <div id="tentangMaps" class="tentang-map-frame" style="width: 100%; height: 500px; border-radius: 0 0 16px 16px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.1);">
                    {{-- Maps will be loaded here --}}
                </div>

                {{-- Map Controls Info --}}
                <div style="background: #f8fafc; padding: 1rem 1.5rem; border-radius: 8px; margin-top: 1rem; display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; color: var(--text-muted); font-size: 0.85rem;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="16" x2="12" y2="12"/>
                            <line x1="12" y1="8" x2="12.01" y2="8"/>
                        </svg>
                        <span style="font-weight: 600;">Tips:</span>
                        <span>Gunakan mouse/touch untuk zoom dan geser peta</span>
                    </div>
                    <div style="width: 1px; height: 20px; background: var(--border);"></div>
                    <div style="display: flex; align-items: center; gap: 0.5rem; color: var(--text-muted); font-size: 0.85rem;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2"/>
                            <path d="M3 9h18"/>
                            <path d="M9 21V9"/>
                        </svg>
                        <span>Klik tombol Layers di pojok kanan atas untuk ganti tampilan</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
// Load Tentang Kami data
async function loadTentangKami() {
    try {
        const response = await fetch('/api/v1/tentang');
        const result = await response.json();

        if (!result.success) throw new Error(result.message);

        const data = result.data;

        // Update text content
        document.getElementById('tentangJudul').textContent = data.judul;
        document.getElementById('tentangSubjudul').textContent = data.subjudul;
        document.getElementById('tentangHeading').textContent = data.heading;
        document.getElementById('tentangDeskripsi').textContent = data.deskripsi;

        // Update programs list
        const programsList = document.getElementById('tentangPrograms');
        if (data.program_list && data.program_list.length > 0) {
            programsList.innerHTML = data.program_list.map(program => `
                <li>
                    <svg style="flex-shrink: 0; margin-top: 2px; color: #48bb78; width: 20px; height: 20px;" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    <span>${program}</span>
                </li>
            `).join('');
        }

        // Update maps
        document.getElementById('tentangMaps').innerHTML = data.maps_embed_code;
        if (data.maps_link) {
            document.getElementById('tentangMapsLink').href = data.maps_link;
            document.getElementById('tentangMapsLink').style.display = 'inline-flex';
        } else {
            document.getElementById('tentangMapsLink').style.display = 'none';
        }

    } catch (error) {
        console.error('❌ Error loading tentang kami:', error);
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    const page = document.getElementById('page-tentang');
    if (page && page.style.display !== 'none') {
        loadTentangKami();
    }
});

// Expose for SPA navigation
window.loadTentangKami = loadTentangKami;
</script>
