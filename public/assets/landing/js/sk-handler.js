/**
 * SK & Dokumen Handler Module
 * Renders document table, handles search
 */

const skDocuments = [
    { name: 'SK Ketua PKK Kabupaten Toba 2024', date: '15 Jan 2024', size: '2.4 MB', type: 'pdf' },
    { name: 'SK Sekretaris PKK Kabupaten Toba', date: '15 Jan 2024', size: '1.8 MB', type: 'pdf' },
    { name: 'SK Bendahara PKK Kabupaten Toba', date: '20 Jan 2024', size: '1.5 MB', type: 'pdf' },
    { name: 'SK Pokja 1 PKK Kabupaten Toba', date: '25 Jan 2024', size: '2.1 MB', type: 'docx' },
    { name: 'SK Pokja 2 PKK Kabupaten Toba', date: '25 Jan 2024', size: '1.9 MB', type: 'docx' },
    { name: 'SK Pokja 3 PKK Kabupaten Toba', date: '28 Jan 2024', size: '2.0 MB', type: 'pdf' },
    { name: 'SK Pokja 4 PKK Kabupaten Toba', date: '28 Jan 2024', size: '1.7 MB', type: 'pdf' },
    { name: 'Program Kerja PKK 2024-2025', date: '5 Feb 2024', size: '3.2 MB', type: 'docx' },
];

function renderSKRow(doc) {
    return `<div class="sk-table-row">
        <div class="sk-file-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
            </svg>
        </div>
        <div class="sk-file-name">${doc.name}</div>
        <div class="sk-file-date">${doc.date}</div>
        <div class="sk-file-size">${doc.size}</div>
        <div class="sk-file-actions">
            <button class="sk-action-btn view" title="Lihat" onclick="alert('Membuka dokumen: ${doc.name}')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
            </button>
            <button class="sk-action-btn download" title="Unduh" onclick="alert('Mengunduh: ${doc.name}')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="7 10 12 15 17 10"/>
                    <line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
            </button>
        </div>
    </div>`;
}

function renderSKTable(docs) {
    const tbody = document.getElementById('skTableBody');
    if (!tbody) return;
    
    if (docs.length === 0) {
        tbody.innerHTML = `<div style="padding: 3rem; text-align: center; color: var(--text-muted);">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto 1rem; display: block; color: #cbd5e0">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
            </svg>
            <p>Tidak ada dokumen ditemukan</p>
        </div>`;
        return;
    }
    
    tbody.innerHTML = docs.map(doc => renderSKRow(doc)).join('');
}

function populateSKTable() {
    renderSKTable(skDocuments);
}

function searchSK(query) {
    const filtered = skDocuments.filter(doc => doc.name.toLowerCase().includes(query.toLowerCase()));
    renderSKTable(filtered);
}

// Initialize on load
document.addEventListener('DOMContentLoaded', populateSKTable);

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { skDocuments, renderSKRow, renderSKTable, populateSKTable, searchSK };
}