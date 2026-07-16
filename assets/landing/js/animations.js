/**
 * Animations Module
 * Handles scroll animations, counter animations, tab switching
 */

document.addEventListener('DOMContentLoaded', function() {
    initScrollAnimations();
    initTabSwitching();
});

function initScrollAnimations() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                if (entry.target.classList.contains('stat-item')) {
                    animateCounter(entry.target);
                }
            }
        });
    }, { threshold: 0.15, rootMargin: '0px 0px -50px 0px' });

    document.querySelectorAll('.stat-item').forEach((item, i) => {
        item.style.transitionDelay = (i * 0.1) + 's';
        observer.observe(item);
    });
}

function animateCounter(el) {
    const numEl = el.querySelector('.stat-number');
    if (!numEl) return;
    
    const target = parseInt(numEl.getAttribute('data-target'));
    const start = performance.now();
    
    function update(now) {
        const progress = Math.min((now - start) / 2000, 1);
        const eased = 1 - Math.pow(1 - progress, 3);
        numEl.textContent = Math.round(eased * target);
        
        if (progress < 1) {
            requestAnimationFrame(update);
        } else {
            numEl.textContent = target;
        }
    }
    requestAnimationFrame(update);
}

function initTabSwitching() {
    // Expose switchTab function globally for inline onclick handlers
    window.switchTab = function(tabName, context = 'home') {
        const container = context === 'home' ? '#page-beranda' : '';
        
        // Remove active states
        document.querySelectorAll(`${container} .tab-btn`)?.forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll(`${container} .tab-content`)?.forEach(content => content.classList.remove('active'));
        
        // Add active states
        document.querySelector(`.tab-btn[data-tab="${tabName}"]`)?.classList.add('active');
        document.getElementById(`${context}-tab-${tabName}`)?.classList.add('active');
    };
}

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { initScrollAnimations, animateCounter, initTabSwitching };
}