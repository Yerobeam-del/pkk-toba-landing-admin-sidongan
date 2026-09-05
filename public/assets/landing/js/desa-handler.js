/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
/**
 * Desa Handler Module - DYNAMIC VERSION
 * Fetches village data from Laravel API instead of hardcoded array
 */

let desaData = [];
let isDesaLoaded = false;
let desaSearchTerm = '';

/**
 * Fetch data from Laravel API
 */
async function loadDesaDataFromAPI() {
    if (isDesaLoaded) return;
    
    try {
        console.log('🔄 Fetching desa data from API...');
        
        const response = await fetch('/api/v1/desas');
        const result = await response.json();
        
        if (!result.success) {
            throw new Error(result.message || 'API error');
        }
        
        desaData = [];
        
        result.data.forEach(kec => {
            if (kec.desas && Array.isArray(kec.desas) && kec.desas.length > 0) {
                const filterSlug = slugify(kec.name);
                
                kec.desas.forEach(desa => {
                    desaData.push({
                        name: desa.name,
                        kecamatan: kec.name,
                        image: desa.image || null,
                        population: desa.population ? Number(desa.population).toLocaleString('id-ID') : '—',
                        households: desa.households ? Number(desa.households).toLocaleString('id-ID') : '—',
                        filter: filterSlug,
                        id: desa.id,
                        kode_wilayah: desa.kode_wilayah
                    });
                });
            }
        });
        
        console.log(`✅ Loaded ${desaData.length} desa(s) from API`);
        isDesaLoaded = true;
        
        if (document.getElementById('desaGrid')) {
            const activeBtn = document.querySelector('.filter-btn.active');
            const currentFilter = activeBtn?.dataset?.filter || 'all';
            populateDesa(currentFilter);
        }
        
    } catch (error) {
        console.error('❌ Failed to load desa data:', error);
        desaData = [];
    }
}

/**
 * Helper: Create slug from text
 */
function slugify(text) {
    return text.toString().toLowerCase()
        .replace(/\s+/g, '-')
        .replace(/[^\w\-]+/g, '')
        .replace(/\-\-+/g, '-')
        .trim();
}

/**
 * Render single desa card - CLEAN & CONSISTENT (NO DESCRIPTION)
 */
function renderDesaCard(desa) {
    const hasImage = desa.image && desa.image.trim() !== '';
    
    // Image section: SAME HEIGHT for all cards (200px)
    const imageSection = hasImage 
        ? `<div class="desa-card-image-wrapper" style="width:100%;height:200px;overflow:hidden;background:linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%)">
            <img src="${desa.image}" 
                 alt="${desa.name}" 
                 class="desa-card-image" 
                 style="width:100%;height:100%;object-fit:cover;display:block"
                 onerror="this.parentElement.innerHTML='<div style=\\'width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%)\\'><span style=\\'font-size:4rem;opacity:0.3;color:#166534\\'>🏘️</span></div>'">
           </div>`
        : `<div class="desa-card-image-placeholder" style="width:100%;height:200px;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%)">
            <span style="font-size:4rem;opacity:0.3;color:#166534">🏘️</span>
           </div>`;
    
    return `<div class="desa-card" data-filter="${desa.filter}" style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.08);display:flex;flex-direction:column;height:100%">
        ${imageSection}
        
        <div class="desa-card-body" style="padding:1.25rem;flex:1;display:flex;flex-direction:column;justify-content:space-between">
            <div>
                <h3 style="margin:0 0 0.25rem 0;font-size:1.15rem;font-weight:700;color:#166534;line-height:1.3">${desa.name}</h3>
                <p style="margin:0;font-size:0.9rem;color:var(--text-muted);display:flex;align-items:center;gap:0.35rem">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;flex-shrink:0">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                        <circle cx="12" cy="10" r="3"/>
                    </svg>
                    <span style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis">Kec. ${desa.kecamatan}</span>
                </p>
            </div>
        </div>
        
        <div class="desa-card-stats" style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;padding:1rem 1.25rem;background:#f8fafc;border-top:2px solid #f0fdf4;flex-shrink:0">
            <div class="desa-stat" style="text-align:center">
                <div class="desa-stat-number" style="font-size:1.25rem;font-weight:700;color:#166534;display:flex;align-items:center;justify-content:center;gap:0.4rem">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    ${desa.population}
                </div>
                <div class="desa-stat-label" style="font-size:0.8rem;color:var(--text-muted)">Warga</div>
            </div>
            <div class="desa-stat" style="text-align:center">
                <div class="desa-stat-number" style="font-size:1.25rem;font-weight:700;color:#166534;display:flex;align-items:center;justify-content:center;gap:0.4rem">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 10.5 12 3l9 7.5"/><path d="M5 9.5V21h14V9.5"/><path d="M10 21v-6h4v6"/></svg>
                    ${desa.households}
                </div>
                <div class="desa-stat-label" style="font-size:0.8rem;color:var(--text-muted)">KRT</div>
            </div>
        </div>
    </div>`;
}

/**
 * Populate grid with filtered data (supports kecamatan filter + search)
 */
function populateDesa(filter = 'all') {
    const grid = document.getElementById('desaGrid');
    const toolbarEl = document.getElementById('desaToolbar');
    const loadingEl = document.getElementById('desa-loading');
    const emptyEl = document.getElementById('desa-empty-state');
    if (!grid) return;
    
    if (!isDesaLoaded && desaData.length === 0) {
        grid.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:3rem;color:var(--text-muted)">⏳ Memuat data...</div>';
        loadDesaDataFromAPI();
        return;
    }
    
    // Hide loading, show toolbar + grid
    if (loadingEl) loadingEl.style.display = 'none';
    if (toolbarEl) toolbarEl.style.display = 'block';
    if (emptyEl) emptyEl.style.display = 'none';
    grid.style.display = 'grid';
    
    let filtered = filter === 'all'
        ? desaData
        : desaData.filter(d => d.filter === filter);
    
    // Apply search filter
    if (desaSearchTerm) {
        filtered = filtered.filter(d =>
            d.name.toLowerCase().includes(desaSearchTerm) ||
            d.kecamatan.toLowerCase().includes(desaSearchTerm)
        );
    }
    
    if (filtered.length === 0) {
        grid.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:3rem;color:var(--text-muted)">Tidak ada desa yang cocok dengan filter/pencarian.</div>';
        return;
    }
    
    grid.innerHTML = filtered.map(d => renderDesaCard(d)).join('');
}

/**
 * Filter handler (called by onclick from filter buttons)
 */
let currentDesaFilter = 'all';

function filterDesa(filter, btn) {
    currentDesaFilter = filter;
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');
    populateDesa(filter);
}

/**
 * Initialize filters dynamically
 */
function initDesaFilters() {
    const filterContainer = document.getElementById('desaFilter');
    if (!filterContainer) return;
    
    const uniqueFilters = [...new Set(desaData.map(d => d.filter))].sort();
    
    let html = `<button class="filter-btn active" onclick="filterDesa('all', this)" data-filter="all">Semua Desa</button>`;
    
    uniqueFilters.forEach(slug => {
        const count = desaData.filter(d => d.filter === slug).length;
        const namaKec = desaData.find(d => d.filter === slug)?.kecamatan || slug;
        html += `<button class="filter-btn" onclick="filterDesa('${slug}', this)" data-filter="${slug}">${namaKec} (${count})</button>`;
    });
    
    filterContainer.innerHTML = html;
}

// Initialize
document.addEventListener('DOMContentLoaded', async () => {
    console.log('🚀 Desa Handler initialized');
    
    await loadDesaDataFromAPI();
    
    if (document.getElementById('desaFilter')) {
        initDesaFilters();
    }
    
    if (document.getElementById('desaGrid')) {
        populateDesa('all');
    }
    
    // Bind search input
    const searchInput = document.getElementById('desaSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            desaSearchTerm = this.value.trim().toLowerCase();
            populateDesa(currentDesaFilter);
        });
    }
});

// Export
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { desaData, renderDesaCard, populateDesa, filterDesa, loadDesaDataFromAPI };
}
/* Dikembangkan oleh Institut Teknologi Del */
