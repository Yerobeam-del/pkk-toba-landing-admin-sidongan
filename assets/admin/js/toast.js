/**
 * Toast Notification System dengan Progress Bar
 * Usage: Toast.show('Pesan sukses', 'success')
 * Types: success, error, warning, info
 */

const Toast = {
    container: null,
    
    // Inisialisasi container
    init() {
        if (this.container) return;
        
        this.container = document.createElement('div');
        this.container.id = 'toast-container';
        this.container.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
            pointer-events: none;
        `;
        document.body.appendChild(this.container);
        
        // Tambahkan CSS animations
        this.addStyles();
    },
    
    // Tambahkan CSS
    addStyles() {
        if (document.getElementById('toast-styles')) return;
        
        const style = document.createElement('style');
        style.id = 'toast-styles';
        style.textContent = `
            @keyframes toastSlideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes toastSlideOut {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(100%); opacity: 0; }
            }
            @keyframes modalFadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            @keyframes modalSlideIn {
                from { transform: scale(0.9); opacity: 0; }
                to { transform: scale(1); opacity: 1; }
            }
            .toast-item {
                pointer-events: auto;
                cursor: pointer;
                transition: all 0.3s ease;
                position: relative;
                overflow: hidden;
            }
            .toast-item:hover {
                transform: translateX(-5px);
                box-shadow: 0 6px 16px rgba(0,0,0,0.2) !important;
            }
            .toast-content {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                padding: 1rem 1.25rem;
            }
            .toast-progress {
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                height: 3px;
                background: rgba(0,0,0,0.05);
                overflow: hidden;
            }
            .toast-progress-bar {
                height: 100%;
                width: 100%;
                transform-origin: left;
                animation: toastProgress linear forwards;
            }
            @keyframes toastProgress {
                from { transform: scaleX(1); }
                to { transform: scaleX(0); }
            }
            .toast-item:hover .toast-progress-bar {
                animation-play-state: paused;
            }
            .modal-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 10000;
                display: flex;
                align-items: center;
                justify-content: center;
                animation: modalFadeIn 0.2s ease;
            }
            .modal-content {
                background: white;
                border-radius: 12px;
                padding: 1.5rem;
                max-width: 400px;
                width: 90%;
                box-shadow: 0 20px 60px rgba(0,0,0,0.3);
                animation: modalSlideIn 0.3s ease;
            }
            .modal-title {
                font-size: 1.1rem;
                font-weight: 700;
                color: #1e293b;
                margin-bottom: 0.75rem;
            }
            .modal-message {
                color: #64748b;
                margin-bottom: 1.5rem;
                line-height: 1.6;
            }
            .modal-actions {
                display: flex;
                gap: 0.75rem;
                justify-content: flex-end;
            }
            .btn-modal {
                padding: 0.6rem 1.25rem;
                border-radius: 8px;
                font-weight: 600;
                font-size: 0.9rem;
                cursor: pointer;
                transition: all 0.2s;
                border: none;
            }
            .btn-modal:hover {
                transform: translateY(-2px);
            }
            .btn-cancel {
                background: #f1f5f9;
                color: #475569;
            }
            .btn-cancel:hover {
                background: #e2e8f0;
            }
            .btn-confirm {
                background: #dc2626;
                color: white;
            }
            .btn-confirm:hover {
                background: #b91c1c;
            }
        `;
        document.head.appendChild(style);
    },
    
    // Tampilkan toast
    show(message, type = 'info', duration = 4000) {
        this.init();
        
        const configs = {
            success: {
                bg: '#f0fdf4',
                border: '#22c55e',
                text: '#166534',
                icon: `<polyline points="20 6 9 17 4 12"/>`
            },
            error: {
                bg: '#fef2f2',
                border: '#ef4444',
                text: '#dc2626',
                icon: `<circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>`
            },
            warning: {
                bg: '#fffbeb',
                border: '#f59e0b',
                text: '#92400e',
                icon: `<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>`
            },
            info: {
                bg: '#eff6ff',
                border: '#3b82f6',
                text: '#1e40af',
                icon: `<circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>`
            }
        };
        
        const config = configs[type] || configs.info;
        
        const toast = document.createElement('div');
        toast.className = 'toast-item';
        toast.style.cssText = `
            background: ${config.bg};
            border-left: 4px solid ${config.border};
            color: ${config.text};
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            min-width: 300px;
            max-width: 500px;
            animation: toastSlideIn 0.3s ease;
        `;
        
        toast.innerHTML = `
            <div class="toast-content">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="${config.border}" stroke-width="2" style="flex-shrink:0">
                    ${config.icon}
                </svg>
                <span style="font-weight: 500; font-size: 0.9rem; flex: 1;">${message}</span>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="${config.text}" stroke-width="2" style="opacity: 0.5; cursor: pointer;">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </div>
            ${duration > 0 ? `
            <div class="toast-progress">
                <div class="toast-progress-bar" style="background: ${config.border}; animation-duration: ${duration}ms;"></div>
            </div>
            ` : ''}
        `;
        
        // Click to close
        toast.querySelector('.toast-content svg:last-child').addEventListener('click', () => this.remove(toast));
        
        // Hover pause/resume
        let timeoutId;
        let remainingTime = duration;
        let startTime = Date.now();
        
        const startTimer = () => {
            startTime = Date.now();
            timeoutId = setTimeout(() => this.remove(toast), remainingTime);
        };
        
        const pauseTimer = () => {
            clearTimeout(timeoutId);
            remainingTime -= (Date.now() - startTime);
        };
        
        const resumeTimer = () => {
            startTime = Date.now();
            timeoutId = setTimeout(() => this.remove(toast), remainingTime);
        };
        
        if (duration > 0) {
            startTimer();
            
            toast.addEventListener('mouseenter', () => {
                pauseTimer();
            });
            
            toast.addEventListener('mouseleave', () => {
                resumeTimer();
            });
        }
        
        this.container.appendChild(toast);
        
        return toast;
    },
    
    // Hapus toast
    remove(toast) {
        toast.style.animation = 'toastSlideOut 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    },
    
    // Custom Confirm Dialog
    confirm(message, options = {}) {
        this.init();
        
        return new Promise((resolve) => {
            const title = options.title || 'Konfirmasi';
            const confirmText = options.confirmText || 'Ya';
            const cancelText = options.cancelText || 'Batal';
            const type = options.type || 'warning';
            
            const colors = {
                warning: { button: '#dc2626', icon: '#f59e0b' },
                danger: { button: '#dc2626', icon: '#ef4444' },
                info: { button: '#3b82f6', icon: '#3b82f6' },
                success: { button: '#22c55e', icon: '#22c55e' }
            };
            
            const color = colors[type] || colors.warning;
            
            const overlay = document.createElement('div');
            overlay.className = 'modal-overlay';
            overlay.innerHTML = `
                <div class="modal-content" style="max-width: 500px; width: 90%;">
                    <div style="display:flex;align-items:flex-start;gap:1rem;margin-bottom:1rem">
                        <div style="width:48px;height:48px;background:${type === 'danger' || type === 'warning' ? '#fef2f2' : '#eff6ff'};border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="${color.icon}" stroke-width="2">
                                ${type === 'danger' || type === 'warning' 
                                    ? '<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>' 
                                    : '<circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>'}
                            </svg>
                        </div>
                        <div style="flex:1;min-width:0">
                            <div class="modal-title" style="margin:0 0 0.5rem 0;font-size:1.25rem">${title}</div>
                            <div class="modal-message" style="word-break: break-word; overflow-wrap: break-word; hyphens: auto; line-height: 1.6; color: #64748b;">
                                ${message}
                            </div>
                        </div>
                    </div>
                    <div class="modal-actions" style="display:flex;gap:0.75rem;justify-content:flex-end;margin-top:1.5rem">
                        <button class="btn-modal btn-cancel" style="padding:0.75rem 1.5rem">${cancelText}</button>
                        <button class="btn-modal btn-confirm" style="padding:0.75rem 1.5rem;background: ${color.button};color:white;border:none;border-radius:8px;font-weight:600;cursor:pointer;transition:all 0.2s" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 12px ${color.button}40'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='none'">${confirmText}</button>
                    </div>
                </div>
            `;
            
            document.body.appendChild(overlay);
            
            const handleResponse = (confirmed) => {
                overlay.style.animation = 'modalFadeIn 0.2s ease reverse';
                setTimeout(() => overlay.remove(), 200);
                resolve(confirmed);
            };
            
            overlay.querySelector('.btn-cancel').addEventListener('click', () => handleResponse(false));
            overlay.querySelector('.btn-confirm').addEventListener('click', () => handleResponse(true));
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) handleResponse(false);
            });
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && overlay.parentElement) handleResponse(false);
            }, { once: true });
        });
    },
    
    // Shortcut methods
    success(message, duration = 4000) {
        return this.show(message, 'success', duration);
    },
    
    error(message, duration = 5000) {
        return this.show(message, 'error', duration);
    },
    
    warning(message, duration = 4000) {
        return this.show(message, 'warning', duration);
    },
    
    info(message, duration = 4000) {
        return this.show(message, 'info', duration);
    }
};

// Export ke global
window.Toast = Toast;