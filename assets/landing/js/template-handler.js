/**
 * Template Handler Module - Simple Version (Like struktur-handler.js)
 */

// Global data
window.templateData = [];

/**
 * Fetch data from API
 */
async function loadTemplatesFromAPI() {
    try {
        const response = await fetch('/api/v1/templates');
        const result = await response.json();
        
        if (!result.success) throw new Error(result.message || 'API error');
        
        // Filter only published templates
        window.templateData = (result.data || []).filter(tpl => tpl.status === 'published');
        
        _renderTemplates(window.templateData);
    } catch (error) {
        console.error('❌ Failed to load templates:', error);
        window.templateData = [];
        _renderTemplates([]);
    }
}

/**
 * Render templates to table
 */
function _renderTemplates(templates) {
    const grid = document.getElementById('templateGrid');
    const loading = document.getElementById('templateLoading');
    const empty = document.getElementById('templateEmpty');
    const tbody = document.getElementById('templateBody');
    
    if (!grid || !loading || !empty || !tbody) {
        console.error('❌ Template elements not found');
        return;
    }
    
    // Hide loading
    loading.style.display = 'none';
    
    // Empty state
    if (!templates || templates.length === 0) {
        grid.style.display = 'none';
        empty.style.display = 'block';
        return;
    }
    
    // Show table
    empty.style.display = 'none';
    grid.style.display = 'block';
    
    // Render rows
    tbody.innerHTML = templates.map((tpl, i) => {
        const ext = tpl.file_name ? tpl.file_name.split('.').pop().toUpperCase() : 'FILE';
        const date = tpl.formatted_date || (tpl.upload_date ? new Date(tpl.upload_date).toLocaleDateString('id-ID', { day:'numeric', month:'short', year:'numeric' }) : '-');
        const size = tpl.file_size || '-';
        
        return `
            <tr style="border-bottom:1px solid #e2e8f0" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                <td style="padding:1rem 1.5rem;color:#64748b">${i + 1}</td>
                <td style="padding:1rem 1.5rem;font-weight:600;color:#1e293b">${tpl.name}</td>
                <td style="padding:1rem 1.5rem">
                    <span style="background:#f1f5f9;padding:0.25rem 0.75rem;border-radius:6px;font-size:0.75rem;font-weight:600;color:#475569">${ext}</span>
                </td>
                <td style="padding:1rem 1.5rem;color:#64748b">${date}</td>
                <td style="padding:1rem 1.5rem;color:#64748b">${size}</td>
                <td style="padding:1rem 1.5rem;text-align:center">
                    <a href="${tpl.file_url}" download="${tpl.file_name}" style="display:inline-flex;align-items:center;gap:0.35rem;padding:0.5rem 1rem;background:linear-gradient(135deg,#0f6b63,#14b8a6);color:#fff;border-radius:8px;font-size:0.8rem;font-weight:600;text-decoration:none" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 12px rgba(20,184,166,0.3)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="7 10 12 15 17 10"/>
                            <line x1="12" y1="15" x2="12" y2="3"/>
                        </svg>
                        Unduh
                    </a>
                </td>
            </tr>
        `;
    }).join('');
}

/**
 * Search handler
 */
function handleTemplateSearch(searchTerm) {
    const term = searchTerm.toLowerCase().trim();
    
    if (term === '') {
        _renderTemplates(window.templateData);
        return;
    }
    
    const filtered = window.templateData.filter(t => 
        (t.name && t.name.toLowerCase().includes(term)) ||
        (t.file_name && t.file_name.toLowerCase().includes(term)) ||
        (t.description && t.description.toLowerCase().includes(term))
    );
    
    _renderTemplates(filtered);
}

/**
 * Initialize page
 */
function initTemplatePage() {
    // Setup search
    const searchInput = document.getElementById('templateSearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => handleTemplateSearch(e.target.value));
    }
    
    // Load data
    loadTemplatesFromAPI();
}

// Initialize when DOM ready (same pattern as struktur-handler.js)
window.addEventListener('load', () => setTimeout(initTemplatePage, 300));

// Expose functions
window.handleTemplateSearch = handleTemplateSearch;
window.initTemplatePage = initTemplatePage;