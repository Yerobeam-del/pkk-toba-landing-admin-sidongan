<div id="global-loading-screen" class="loading-screen">
    {{-- Animated Clouds Background --}}
    <div class="cloud c1"></div>
    <div class="cloud c2"></div>
    <div class="cloud c3"></div>
    <div class="cloud c4"></div>

    {{-- Logo PKK dengan Animasi --}}
    <div class="logo-container">
        <img src="{{ asset('assets/admin/images/Logo-PKK-Transparent.png') }}" alt="Loading">
    </div>
</div>

<style>
    .loading-screen {
        position: fixed;
        top: 0; left: 0;
        width: 100vw; height: 100vh;
        background: linear-gradient(135deg, #f0fdfa 0%, #ccfbf1 50%, #f0fdfa 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        overflow: hidden;
        transition: opacity 0.4s ease, visibility 0.4s ease;
    }

    /* State ketika loading disembunyikan */
    .loading-screen.hidden {
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
    }

    /* ===== ANIMATED CLOUDS ===== */
    .cloud {
        position: absolute;
        background: #fff;
        border-radius: 100px;
        opacity: 0.8;
        box-shadow: 0 10px 30px rgba(13, 148, 136, 0.08);
        z-index: 1;
    }
    .cloud::before, .cloud::after {
        content: '';
        position: absolute;
        background: #fff;
        border-radius: 50%;
    }
    .c1 { width: 120px; height: 40px; top: 15%; left: -150px; animation: drift 28s linear infinite; }
    .c1::before { width: 50px; height: 50px; top: -25px; left: 20px; }
    .c1::after { width: 70px; height: 70px; top: -35px; left: 45px; }

    .c2 { width: 160px; height: 50px; top: 45%; left: -200px; animation: drift 38s linear infinite; animation-delay: -8s; }
    .c2::before { width: 60px; height: 60px; top: -30px; left: 30px; }
    .c2::after { width: 90px; height: 90px; top: -45px; left: 60px; }

    .c3 { width: 100px; height: 35px; top: 70%; left: -130px; animation: drift 22s linear infinite; animation-delay: -15s; }
    .c3::before { width: 40px; height: 40px; top: -20px; left: 15px; }
    .c3::after { width: 55px; height: 55px; top: -28px; left: 35px; }

    .c4 { width: 140px; height: 45px; top: 25%; left: -180px; animation: drift 32s linear infinite; animation-delay: -20s; }
    .c4::before { width: 55px; height: 55px; top: -28px; left: 25px; }
    .c4::after { width: 80px; height: 80px; top: -40px; left: 50px; }

    @keyframes drift {
        from { transform: translateX(-250px); }
        to { transform: translateX(110vw); }
    }

    /* ===== LOGO CONTAINER & ANIMATION ===== */
    .logo-container {
        position: relative;
        z-index: 10;
        width: 140px;
        height: 140px;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: logoPulse 2s ease-in-out infinite;
    }

    @keyframes logoPulse {
        0%, 100% {
            transform: scale(1);
            opacity: 1;
        }
        50% {
            transform: scale(1.08);
            opacity: 0.85;
        }
    }

    .logo-container img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        filter: drop-shadow(0 8px 24px rgba(13, 148, 136, 0.25));
        animation: logoRotate 8s linear infinite;
    }

    @keyframes logoRotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 768px) {
        .logo-container {
            width: 100px;
            height: 100px;
        }
    }
</style>

<script>
    // Fungsi global untuk menampilkan loading screen
    window.showLoading = function() {
        const loader = document.getElementById('global-loading-screen');
        if (loader) {
            loader.classList.remove('hidden');
        }
    };

    // Fungsi global untuk menyembunyikan loading screen
    window.hideLoading = function() {
        const loader = document.getElementById('global-loading-screen');
        if (loader) {
            loader.classList.add('hidden');
        }
    };
</script>
