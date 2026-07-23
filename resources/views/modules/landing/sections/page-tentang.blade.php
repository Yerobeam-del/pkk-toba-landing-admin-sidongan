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
        /* FIX: Tambahkan padding bottom untuk ruang kontrol map */
        padding-bottom: 80px;
    }

    .tentang-map-frame {
        width: 100%;
        height: 450px; /* FIX: Tinggi yang cukup */
        border-radius: 24px;
        /* FIX: Hapus overflow hidden agar kontrol map tidak terpotong */
        overflow: visible;
    }

    /* FIX: Paksa iframe mengisi container dan abaikan style inline dari API */
    .tentang-map-frame iframe {
        width: 100% !important;
        height: 100% !important;
        border: none !important;
        /* FIX: Pastikan iframe tidak terpotong */
        overflow: visible !important;
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
        /* FIX: Pastikan overlay di atas map */
        z-index: 10;
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
            order: 2; /* FIX: Map muncul di bawah pada mobile */
            margin-top: 1rem;
            /* FIX: Kurangi padding di mobile */
            padding-bottom: 70px;
        }

        .tentang-text h2 {
            font-size: 1.5rem;
        }

        .tentang-text p {
            font-size: 0.95rem;
        }

        .tentang-list li {
            font-size: 0.9rem;
        }

        .tentang-map-frame {
            height: 350px; /* FIX: Tinggi disesuaikan agar proporsional di mobile */
        }

        .tentang-map-overlay {
            padding: 1.5rem;
        }

        .tentang-map-overlay h3 {
            font-size: 1rem;
        }

        .tentang-map-overlay p {
            font-size: 0.8rem;
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
                <div id="tentangMaps" class="tentang-map-frame">
                    {{-- Maps will be loaded here --}}
                </div>
                <div class="tentang-map-overlay">
                    <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 0.3rem; color: #fff;">Lokasi Kantor PKK Kabupaten Toba</h3>
                    <p style="font-size: 0.85rem; opacity: 0.9; margin-bottom: 0.5rem; color: #fff;">Balige, Kabupaten Toba, Sumatera Utara</p>
                    <a id="tentangMapsLink" href="#" target="_blank" style="display: inline-flex; align-items: center; gap: 6px; color: #fbbf24; font-size: 0.85rem; font-weight: 600; text-decoration: none; transition: gap 0.3s;" onmouseover="this.style.gap='10px'" onmouseout="this.style.gap='6px'">
                        Buka di Google Maps
                        <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </a>
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
