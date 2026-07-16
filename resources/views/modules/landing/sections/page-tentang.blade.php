<div class="page" id="page-tentang" style="display: none;">
    <div class="page-header" style="background: linear-gradient(135deg, var(--primary), var(--primary-light));">
        <div class="page-header-content">
            <h1 id="tentangJudul">Tentang Kami _TEST</h1>
            <p id="tentangSubjudul">Informasi tentang PKK Kabupaten Toba</p>
            <div class="breadcrumb">
                <a onclick="navigateTo('beranda')">Beranda</a><span>/</span><span class="current">Tentang</span>
            </div>
        </div>
    </div>

    <section class="info-section" style="padding: 4rem 2rem; background: #fff;">
        <div class="info-container" style="max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: center;">
            
            {{-- Left Content --}}
            <div class="info-text">
                <h2 id="tentangHeading" style="font-size: 2rem; font-weight: 800; color: var(--primary); margin-bottom: 1rem; line-height: 1.3;">
                    Memberdayakan Keluarga, Mensejahterakan Masyarakat
                </h2>
                <p id="tentangDeskripsi" style="color: var(--text-muted); line-height: 1.8; margin-bottom: 1.5rem;">
                    PKK Kabupaten Toba berkomitmen untuk terus berinovasi...
                </p>
                
                <ul id="tentangPrograms" class="info-list" style="list-style: none; padding: 0;">
                    <li style="display: flex; align-items: flex-start; gap: 12px; padding: 10px 0; color: #4a5568;">
                        <svg style="flex-shrink: 0; margin-top: 2px; color: #48bb78; width: 20px; height: 20px;" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <span>Program ketahanan dan kesejahteraan keluarga</span>
                    </li>
                    {{-- More items will be loaded dynamically --}}
                </ul>
            </div>
            
            {{-- Right Content - Maps --}}
            <div class="info-map" style="position: relative; border-radius: 24px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.1);">
                <div id="tentangMaps" style="width: 100%; height: 400px;">
                    {{-- Maps will be loaded here --}}
                </div>
                <div class="info-map-overlay" style="position: absolute; bottom: 0; left: 0; right: 0; padding: 2rem; background: linear-gradient(to top, rgba(30, 58, 95, 0.9), transparent); color: #fff;">
                    <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 0.3rem;">Lokasi Kantor PKK Kabupaten Toba</h3>
                    <p style="font-size: 0.8rem; opacity: 0.8;">Balige, Kabupaten Toba, Sumatera Utara</p>
                    <a id="tentangMapsLink" href="#" target="_blank" class="info-map-link" style="display: inline-flex; align-items: center; gap: 6px; margin-top: 0.5rem; color: var(--gold); font-size: 0.82rem; font-weight: 600; text-decoration: none;">
                        Buka di Google Maps
                        <svg style="width: 14px; height: 14px; transition: transform 0.3s;" fill="none" stroke="currentColor" stroke-width="2">
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
                <li style="display: flex; align-items: flex-start; gap: 12px; padding: 10px 0; color: #4a5568;">
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