/**
 * Hero Slider & Particles
 * Handles background slider, indicators, and floating particles
 */

document.addEventListener('DOMContentLoaded', function() {
    initHeroSlider();
    initParticles();
    initScrollEffect();
});

function initHeroSlider() {
    const slides = document.querySelectorAll('.hero-bg-slide');
    const dots = document.querySelectorAll('.slider-dot');
    if (!slides.length || !dots.length) return;
    
    let currentSlide = 0;
    let slideInterval;
    
    function goToSlide(index) {
        slides[currentSlide]?.classList.remove('active');
        dots[currentSlide]?.classList.remove('active');
        currentSlide = index;
        slides[currentSlide]?.classList.add('active');
        dots[currentSlide]?.classList.add('active');
    }
    
    function startSlider() {
        slideInterval = setInterval(() => {
            goToSlide((currentSlide + 1) % slides.length);
        }, 5000);
    }
    
    // Dot click handlers
    dots.forEach(dot => {
        dot.addEventListener('click', () => {
            goToSlide(parseInt(dot.getAttribute('data-index')));
            clearInterval(slideInterval);
            startSlider();
        });
    });
    
    startSlider();
    
    // Expose for external control
    window.heroSlider = { goToSlide, startSlider };
}

function initParticles() {
    const container = document.getElementById('particles');
    if (!container) return;
    
    for (let i = 0; i < 30; i++) {
        const p = document.createElement('div');
        p.classList.add('particle');
        p.style.left = Math.random() * 100 + '%';
        p.style.width = p.style.height = (Math.random() * 4 + 2) + 'px';
        p.style.animationDuration = (Math.random() * 15 + 10) + 's';
        p.style.animationDelay = (Math.random() * 10) + 's';
        p.style.opacity = Math.random() * 0.5 + 0.1;
        container.appendChild(p);
    }
}

function initScrollEffect() {
    window.addEventListener('scroll', () => {
        const navbar = document.getElementById('navbar');
        if (navbar) {
            navbar.classList.toggle('scrolled', window.scrollY > 50);
        }
    });
}