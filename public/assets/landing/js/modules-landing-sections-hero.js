/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * JS halaman: resources/views/modules/landing/sections/hero.blade.php
 * (diekstrak dari blok <script> di dalam view)
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */


// Fungsi scroll ke Quick Access - HARUS di global scope
window.scrollToQuickAccess = function() {
    const section = document.querySelector('.quick-access-section');
    if (section) {
        const navbarHeight = 80; // Tinggi navbar fixed
        const sectionTop = section.getBoundingClientRect().top + window.pageYOffset;
        window.scrollTo({
            top: sectionTop - navbarHeight,
            behavior: 'smooth'
        });
    }
};

document.addEventListener('DOMContentLoaded', async function() {
    const heroCta = document.querySelector('.hero-cta');
    if (heroCta) {
        const activateHeroCta = function(event) {
            event.preventDefault();
            window.scrollToQuickAccess();
        };

        heroCta.addEventListener('click', activateHeroCta);
        heroCta.addEventListener('touchend', activateHeroCta, { passive: false });
    }

    try {
        const response = await fetch('/api/v1/hero-slider');
        const result = await response.json();

        const sliderContainer = document.getElementById('heroBgSlider');
        const indicatorsContainer = document.getElementById('sliderIndicators');

        if (result.success && result.data && result.data.length > 0) {
            const slidesData = result.data;
            const settings = result.settings || {};

            sliderContainer.innerHTML = '';
            indicatorsContainer.innerHTML = '';

            slidesData.forEach((slide, index) => {
                const slideDiv = document.createElement('div');
                slideDiv.className = `hero-bg-slide ${index === 0 ? 'active' : ''}`;
                slideDiv.style.backgroundImage = `url('${slide.image_url}')`;
                slideDiv.dataset.duration = slide.display_duration * 1000;
                sliderContainer.appendChild(slideDiv);

                const dotDiv = document.createElement('div');
                dotDiv.className = `slider-dot ${index === 0 ? 'active' : ''}`;
                dotDiv.dataset.index = index;
                dotDiv.addEventListener('click', () => window.goToSlide(index));
                indicatorsContainer.appendChild(dotDiv);
            });

            initHeroSliderLogic(slidesData, settings);
        } else {
            console.log('Slider kosong, menggunakan fallback statis.');
        }
    } catch (error) {
        console.error('Error loading hero slider:', error);
    }

    function initHeroSliderLogic(slides, settings) {
        let currentSlide = 0;
        const slideElements = document.querySelectorAll('.hero-bg-slide');
        const dotElements = document.querySelectorAll('.slider-dot');
        let slideTimer = null;

        window.goToSlide = function(index) {
            if (index < 0) index = slides.length - 1;
            if (index >= slides.length) index = 0;

            slideElements[currentSlide].classList.remove('active');
            slideElements[index].classList.add('active');

            dotElements[currentSlide].classList.remove('active');
            dotElements[index].classList.add('active');

            currentSlide = index;
            restartTimer();
        };

        function startTimer() {
            if (slideTimer) clearTimeout(slideTimer);
            const duration = parseInt(slideElements[currentSlide].dataset.duration) || 5000;
            slideTimer = setTimeout(() => window.goToSlide(currentSlide + 1), duration);
        }

        function restartTimer() {
            startTimer();
        }

        function pauseTimer() {
            if (slideTimer) clearTimeout(slideTimer);
        }

        if (settings.auto_play !== false && slides.length > 1) {
            startTimer();

            const heroSection = document.querySelector('.hero');
            if (heroSection) {
                heroSection.addEventListener('mouseenter', pauseTimer);
                heroSection.addEventListener('mouseleave', startTimer);
            }
        }
    }
});


