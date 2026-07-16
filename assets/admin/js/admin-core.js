/**
 * Admin Core JS - Handles modal, tabs, and basic interactions
 */

// Modal Functions
function openModal(title, contentHtml, onSave) {
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalBody').innerHTML = contentHtml;
    document.getElementById('modalOverlay').classList.add('open');
    
    if (onSave) {
        document.getElementById('saveBtn').onclick = () => {
            onSave();
            closeModal();
        };
    }
}

function closeModal() {
    document.getElementById('modalOverlay').classList.remove('open');
}

// Tab Navigation for Struktur
function switchStrukturTab(tabId) {
    document.querySelectorAll('.tab-nav-struktur .tab-btn').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.tab === tabId);
    });
    document.querySelectorAll('.tab-content-struktur').forEach(content => {
        content.classList.toggle('active', content.id === `tab-${tabId}`);
    });
}

// Close modal on outside click
document.getElementById('modalOverlay')?.addEventListener('click', (e) => {
    if (e.target.id === 'modalOverlay') closeModal();
});

// CSRF Token for AJAX
document.addEventListener('DOMContentLoaded', () => {
    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    if (token) {
        window.fetch = new Proxy(window.fetch, {
            apply(target, thisArg, args) {
                const [url, options = {}] = args;
                options.headers = {
                    ...options.headers,
                    'X-CSRF-TOKEN': token
                };
                return Reflect.apply(target, thisArg, [url, options]);
            }
        });
    }
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { openModal, closeModal, switchStrukturTab };
}