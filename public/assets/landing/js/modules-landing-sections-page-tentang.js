/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * JS halaman: resources/views/modules/landing/sections/page-tentang.blade.php
 * (diekstrak dari blok <script> di dalam view)
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */


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
            const mapsLink = document.getElementById('tentangMapsLink');
            if (mapsLink) {
                mapsLink.href = data.maps_link;
            }
        }

    } catch (error) {
        console.error('❌ Error loading tentang kami:', error);
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    const page = document.getElementById('page-tentang');
    if (page) {
        page.style.display = 'block';
        loadTentangKami();
    }
});

// Expose for SPA navigation
window.loadTentangKami = loadTentangKami;


