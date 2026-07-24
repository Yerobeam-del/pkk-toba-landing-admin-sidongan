<style>
    /* Tentang Page Specific Styles */
    .tentang-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 4rem;
        align-items: start;
        max-width: 1200px;
        margin: 0 auto;
        padding: 4rem 2rem;
    }

    .tentang-text h2 {
        font-size: 2rem;
        font-weight: 800;
        color: #0d9488; /* Fallback color - teal */
        margin-bottom: 1rem;
        line-height: 1.3;
    }

    .tentang-text p {
        color: #475569; /* Fallback color - slate gray */
        line-height: 1.8;
        margin-bottom: 1.5rem;
        font-size: 1rem;
    }

    .tentang-list {
        list-style: none;
        padding: 0;
        margin: 2rem 0;
    }

    .tentang-list li {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 10px 0;
        color: #334155; /* Darker color for better readability */
        font-size: 0.95rem;
        line-height: 1.6;
    }

    .tentang-list li svg {
        flex-shrink: 0;
        margin-top: 2px;
        color: #10b981; /* Green checkmark */
        width: 20px;
        height: 20px;
    }

    .tentang-map-wrapper {
        position: relative;
        border-radius: 24px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.1);
        background: #fff;
        overflow: hidden;
    }

    .tentang-map-header {
        background: linear-gradient(135deg, #14b8a6, #0f766e);
        color: #fff;
        padding: 1.5rem 2rem;
    }

    .tentang-map-header h3 {
        font-size: 1.25rem;
        font-weight: 700;
        margin: 0 0 0.5rem 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        color: #fff;
    }

    .tentang-map-header p {
        font-size: 0.95rem;
        margin: 0 0 1rem 0;
        opacity: 0.95;
        color: #fff;
    }

    .tentang-map-header a {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: #fff;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        padding: 0.5rem 1rem;
        background: rgba(255,255,255,0.2);
        border-radius: 8px;
        transition: all 0.3s;
    }

    .tentang-map-header a:hover {
        background: rgba(255,255,255,0.3);
        transform: translateY(-2px);
    }

    .tentang-map-frame {
        width: 100%;
        height: 500px;
        border-radius: 0;
    }

    .tentang-map-frame iframe {
        width: 100% !important;
        height: 100% !important;
        border: none !important;
        display: block;
    }

    .map-tips {
        background: #f8fafc;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        margin-top: 1rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .map-tips > div {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #64748b;
        font-size: 0.85rem;
    }

    .map-tips .divider {
        width: 1px;
        height: 20px;
        background: #e2e8f0;
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .tentang-container {
            grid-template-columns: 1fr;
            gap: 2rem;
            padding: 2rem 1rem;
        }

        .tentang-text {
            order: 1;
        }

        .tentang-map-wrapper {
            order: 2;
            margin-top: 2rem;
        }

        .tentang-text h2 {
            font-size: 1.5rem;
        }

        .tentang-text p {
            font-size: 0.95rem;
        }

        .tentang-map-header {
            padding: 1.25rem 1rem;
        }

        .tentang-map-header h3 {
            font-size: 1.1rem;
        }

        .tentang-map-frame {
            height: 350px !important;
        }

        .map-tips {
            padding: 0.75rem 1rem;
        }

        .map-tips > div {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .map-tips .divider {
            display: none;
        }
    }
</style>

<div class="page" id="page-tentang">
    <div class="page-header" style="background: linear-gradient(135deg, #14b8a6, #0d9488); padding: 4rem 2rem 2rem; text-align: center; color: #fff;">
        <div style="max-width: 800px; margin: 0 auto;">
            <h1 id="tentangJudul" style="font-size: 2.5rem; font-weight: 800; margin-bottom: 0.5rem; color: #fff;">Tentang Kami</h1>
            <p id="tentangSubjudul" style="font-size: 1.1rem; margin-bottom: 1rem; opacity: 0.95; color: #fff;">Informasi tentang PKK Kabupaten Toba</p>
            <div style="display: flex; justify-content: center; gap: 0.5rem; font-size: 0.9rem;">
                <a onclick="navigateTo('beranda')" style="color: #fff; text-decoration: none; opacity: 0.85; cursor: pointer;">Beranda</a>
                <span style="opacity: 0.6;">/</span>
                <span style="font-weight: 600; color: #fff;">Tentang</span>
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

                <p style="margin-top: 2rem; font-style: italic; color: #64748b;">
                    Dengan semangat gotong royong dan kebersamaan, PKK Kabupaten Toba terus bergerak maju untuk mewujudkan masyarakat yang sejahtera, mandiri, dan berakhlak.
                </p>
            </div>

            {{-- Right Content - Maps --}}
            <div class="tentang-map-wrapper">
                {{-- Map Info Header --}}
                <div class="tentang-map-header">
                    <h3>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink: 0;">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                        Lokasi Kantor PKK Kabupaten Toba
                    </h3>
                    <p>Jl. D.I Panjaitan No.1, Napitupulu, Kec. Balige, Kabupaten Toba, Sumatera Utara</p>
                    <a href="https://goo.gl/maps/xxxxx" target="_blank" rel="noopener noreferrer">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                            <polyline points="15 3 21 3 21 9"/>
                            <line x1="10" y1="14" x2="21" y2="3"/>
                        </svg>
                        Buka di Google Maps
                    </a>
                </div>

                {{-- Interactive Map Container --}}
                <div id="tentangMaps" class="tentang-map-frame">
                    {{-- Maps will be loaded here --}}
                </div>

                {{-- Map Controls Info --}}
                <div class="map-tips">
                    <div>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#14b8a6" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="16" x2="12" y2="12"/>
                            <line x1="12" y1="8" x2="12.01" y2="8"/>
                        </svg>
                        <span><strong>Tips:</strong> Gunakan mouse/touch untuk zoom dan geser peta</span>
                    </div>
                    <div class="divider"></div>
                    <div>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#14b8a6" stroke-width="2">
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
        document.getElementById('tentangJudul').textContent = data.judul || 'Tentang Kami';
        document.getElementById('tentangSubjudul').textContent = data.subjudul || 'Informasi tentang PKK Kabupaten Toba';
        document.getElementById('tentangHeading').textContent = data.heading || 'Memberdayakan Keluarga, Mensejahterakan Masyarakat';
        document.getElementById('tentangDeskripsi').textContent = data.deskripsi || 'PKK Kabupaten Toba berkomitmen untuk terus berinovasi...';

        // Update programs list
        const programsList = document.getElementById('tentangPrograms');
        if (data.program_list && data.program_list.length > 0) {
            programsList.innerHTML = data.program_list.map(program => `
                <li>
                    <svg fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    <span>${program}</span>
                </li>
            `).join('');
        }

        // Update maps
        const mapsContainer = document.getElementById('tentangMaps');
        if (data.maps_embed_code) {
            mapsContainer.innerHTML = data.maps_embed_code;
        }

        // Update maps link
        if (data.maps_link) {
            const mapsLink = document.querySelector('.tentang-map-header a');
            if (mapsLink) {
                mapsLink.href = data.maps_link;
            }
        }

    } catch (error) {
        console.error('❌ Error loading tentang kami:', error);
    }
}

// Initialize - Show the page
document.addEventListener('DOMContentLoaded', function() {
    const page = document.getElementById('page-tentang');
    if (page) {
        // Show the page
        page.style.display = 'block';

        // Load data
        loadTentangKami();
    }
});

// Expose for SPA navigation
window.loadTentangKami = loadTentangKami;
</script>
