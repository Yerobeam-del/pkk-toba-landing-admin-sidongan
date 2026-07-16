<div class="page" id="page-desa">
    <div class="page-header" style="background: linear-gradient(135deg, var(--primary), var(--primary-light));">
        <div class="page-header-content">
            <h1>Data Desa</h1>
            <p>Daftar desa dan kelurahan di Kabupaten Toba, Sumatera Utara</p>
            <div class="breadcrumb">
                <a onclick="navigateTo('beranda')">Beranda</a><span>/</span><span class="current">Desa</span>
            </div>
        </div>
    </div>
    
    <section class="desa-section">
        {{-- Loading State --}}
        <div id="desa-loading" style="text-align: center; padding: 5rem 2rem;">
            <div style="font-size: 1.2rem; color: #64748b; font-weight: 500;">Memuat data desa...</div>
        </div>

        {{-- Filter Container (Akan diisi otomatis oleh JS berdasarkan data database) --}}
        <div class="desa-filter" id="desaFilter" style="display: none;">
        </div>
        
        {{-- Grid Container (Kartu desa akan muncul di sini) --}}
        <div class="desa-grid" id="desaGrid" style="display: none;">
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
            <a onclick="navigateTo('beranda')" 
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.875rem 2rem; background: linear-gradient(135deg, #0f6b63, #14b8a6); color: #fff; border-radius: 12px; font-weight: 600; text-decoration: none; cursor: pointer; transition: all 0.3s; box-shadow: 0 4px 15px rgba(15,107,99,0.3);"
               onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 25px rgba(15,107,99,0.4)'"
               onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(15,107,99,0.3)'">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
                Kembali ke Beranda
            </a>
        </div>
    </section>
</div>

<script>
// Variabel untuk menyimpan data desa
let allDesaData = [];
let desaDataLoaded = false;

// Fungsi utama untuk mengambil data dari API
async function loadDesaData() {
    if (desaDataLoaded) return;
    
    const loadingEl = document.getElementById('desa-loading');
    const filterEl = document.getElementById('desaFilter');
    const gridEl = document.getElementById('desaGrid');
    const emptyEl = document.getElementById('desa-empty-state');
    
    try {
        const response = await fetch('/api/v1/desas');
        const result = await response.json();
        
        if (!result.success) throw new Error('Gagal memuat data');
        
        // Hide loading
        loadingEl.style.display = 'none';
        
        // Proses data: Gabungkan semua desa dari semua kecamatan
        allDesaData = [];
        const kecamatanList = new Set();
        
        result.data.forEach(kec => {
            if (kec.desas && kec.desas.length > 0) {
                kec.desas.forEach(desa => {
                    // Tambahkan info kecamatan ke setiap desa
                    allDesaData.push({
                        ...desa,
                        kecamatan_name: kec.name,
                        kecamatan_slug: kec.name.toLowerCase().replace(/\s+/g, '-') // Buat slug unik untuk filter
                    });
                    kecamatanList.add(kec.name);
                });
            }
        });
        
        // Cek jika tidak ada data
        if (allDesaData.length === 0) {
            emptyEl.style.display = 'block';
            desaDataLoaded = true;
            return;
        }
        
        // Tampilkan filter dan grid
        filterEl.style.display = 'block';
        gridEl.style.display = 'grid';
        
        // 1. Render Filter Buttons (Dinamis sesuai data)
        renderFilters(kecamatanList);
        
        // 2. Render Semua Kartu Desa (Awalnya tampilkan semua)
        renderCards(allDesaData);
        
        desaDataLoaded = true;
        
    } catch (error) {
        console.error('Error loading desa data:', error);
        loadingEl.innerHTML = '<p style="color:#ef4444; font-weight:500;">Gagal memuat data desa. Silakan refresh halaman.</p>';
    }
}

// Fungsi untuk membuat tombol filter
function renderFilters(kecamatanSet) {
    const container = document.getElementById('desaFilter');
    const kecamatanArray = Array.from(kecamatanSet).sort();
    
    let html = '<button class="filter-btn active" onclick="filterDesa(\'all\', this)">Semua Desa</button>';
    
    kecamatanArray.forEach(namaKec => {
        // Buat ID unik untuk setiap kecamatan (misal: "Siantar Narumonda" jadi "siantar-narumonda")
        const slug = namaKec.toLowerCase().replace(/\s+/g, '-');
        html += '<button class="filter-btn" onclick="filterDesa(\'' + slug + '\', this)">' + namaKec + '</button>';
    });
    
    container.innerHTML = html;
}

// Fungsi untuk menampilkan kartu desa
function renderCards(data) {
    const container = document.getElementById('desaGrid');
    
    if (data.length === 0) {
        container.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:3rem;color:var(--text-muted)">Tidak ada data desa untuk filter ini.</div>';
        return;
    }
    
    // Template kartu sesuai desain Anda (Gambar, Judul, Info, Statistik)
    container.innerHTML = data.map(desa => {
        const imageUrl = desa.image || '';
        const population = desa.population ? Number(desa.population).toLocaleString('id-ID') : '0';
        const households = desa.households ? Number(desa.households).toLocaleString('id-ID') : '0';
        
        return '<div class="desa-card" data-kecamatan="' + desa.kecamatan_slug + '">' +
            '<div class="desa-image" style="width:100%;height:200px;overflow:hidden;border-radius:8px 8px 0 0;background:#f0f0f0">' +
                (imageUrl ? 
                    '<img src="' + imageUrl + '" alt="' + desa.name + '" style="width:100%;height:100%;object-fit:cover" onerror="this.style.display=\'none\';this.nextElementSibling.style.display=\'flex\'">' +
                    '<div style="display:none;align-items:center;justify-content:center;height:100%;font-size:3rem;color:#ccc">' +
                        '<svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5">' +
                            '<path d="M3 21h18M5 21V7l8-4 8 4v14M8 21v-4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v4"/>' +
                            '<rect x="9" y="9" width="6" height="6"/>' +
                        '</svg>' +
                    '</div>'
                    : 
                    '<div style="display:flex;align-items:center;justify-content:center;height:100%;font-size:3rem;color:#ccc">' +
                        '<svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5">' +
                            '<path d="M3 21h18M5 21V7l8-4 8 4v14M8 21v-4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v4"/>' +
                            '<rect x="9" y="9" width="6" height="6"/>' +
                        '</svg>' +
                    '</div>'
                ) +
            '</div>' +
            '<div class="desa-info" style="padding:1rem">' +
                '<h3 class="desa-name" style="font-size:1.1rem;font-weight:bold;margin-bottom:0.25rem">' + desa.name + '</h3>' +
                '<p class="desa-kecamatan" style="font-size:0.85rem;color:var(--text-muted);margin-bottom:0.75rem">' +
                    '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:4px">' +
                        '<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>' +
                        '<circle cx="12" cy="10" r="3"/>' +
                    '</svg>' +
                    desa.kecamatan_name +
                '</p>' +
                '<div class="desa-stats" style="display:flex;justify-content:space-between;border-top:1px solid #eee;padding-top:0.75rem">' +
                    '<div class="stat-item">' +
                        '<div class="stat-value" style="font-weight:bold;color:#9b2c2c">' + population + '</div>' +
                        '<div class="stat-label" style="font-size:0.75rem;color:var(--text-muted)">Penduduk</div>' +
                    '</div>' +
                    '<div class="stat-item" style="text-align:right">' +
                        '<div class="stat-value" style="font-weight:bold;color:#9b2c2c">' + households + '</div>' +
                        '<div class="stat-label" style="font-size:0.75rem;color:var(--text-muted)">KK</div>' +
                    '</div>' +
                '</div>' +
            '</div>' +
        '</div>';
    }).join('');
}

// Fungsi Filter
function filterDesa(slug, btnElement) {
    // Update tampilan tombol aktif
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    btnElement.classList.add('active');
    
    const gridEl = document.getElementById('desaGrid');
    
    // Filter data
    if (slug === 'all') {
        renderCards(allDesaData);
    } else {
        const filtered = allDesaData.filter(d => d.kecamatan_slug === slug);
        if (filtered.length === 0) {
            gridEl.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:3rem;color:var(--text-muted)">Tidak ada data desa untuk filter ini.</div>';
        } else {
            renderCards(filtered);
        }
    }
}

// Auto-load when page becomes active
document.addEventListener('DOMContentLoaded', () => {
    const observer = new MutationObserver(() => {
        const page = document.getElementById('page-desa');
        if (page && page.classList.contains('active') && !desaDataLoaded) {
            setTimeout(() => loadDesaData(), 100);
            observer.disconnect();
        }
    });
    observer.observe(document.body, { childList: true, subtree: true });
    
    // Load immediately if already active
    const page = document.getElementById('page-desa');
    if (page && page.classList.contains('active')) {
        setTimeout(() => loadDesaData(), 100);
    }
});
</script>