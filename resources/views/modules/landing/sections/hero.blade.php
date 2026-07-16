<section class="hero">
    {{-- 1. Container Background Slider --}}
    <div class="hero-bg-slider" id="heroBgSlider">
        {{-- Fallback jika JS gagal load --}}
        <div class="hero-bg-slide active" style="background-image: url('{{ asset('assets/landing/images/Background_1.jpg') }}')"></div>
    </div>
    
    {{-- 2. Overlay --}}
    <div class="hero-bg-overlay"></div>
    
    {{-- 3. Particles --}}
    <div class="hero-particles" id="particles"></div>

    {{-- 4. Konten Hero --}}
    <div class="hero-content">
        {{-- Badge --}}
        <div class="hero-badge">
            <div class="hero-badge-dot"></div>
            <span>Portal Resmi Digital</span>
        </div>
        
        {{-- Logo --}}
        <div class="hero-logo-container">
            <img src="{{ asset('assets/landing/images/PKK-Logo.png') }}" alt="PKK Logo" class="hero-logo">
            <div class="hero-logo-glow"></div>
        </div>
        
        {{-- Heading --}}
        <h1>
            Selamat Datang di Portal<br>
            <span class="highlight">PKK Kabupaten Toba</span>
        </h1>
        
        {{-- Subtitle --}}
        <p class="hero-subtitle">
            Melayani masyarakat Kabupaten Toba melalui transformasi digital untuk pemberdayaan keluarga dan kesejahteraan masyarakat yang lebih baik.
        </p>
        
        {{-- CTA Button --}}
        <a onclick="scrollToQuickAccess()" class="hero-cta">
            <span>Jelajahi Layanan</span>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M7 17l9.2-9.2M17 17V7.8H7.8"/>
            </svg>
        </a>
    </div>

    {{-- 5. Indicators/Dots --}}
    <div class="hero-slider-indicators" id="sliderIndicators"></div>
</section>

<script>
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
</script>