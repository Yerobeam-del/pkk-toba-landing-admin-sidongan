@props([
    'message' => 'Mohon tunggu sebentar, sistem sedang menyiapkan data untuk Anda...'
])

<div id="global-loading-screen" class="loading-screen">
    {{-- Animated Clouds Background --}}
    <div class="cloud c1"></div>
    <div class="cloud c2"></div>
    <div class="cloud c3"></div>
    <div class="cloud c4"></div>

    {{-- Main Container --}}
    <div class="container">
        <div class="loading-logo">
            <img src="{{ asset('assets/admin/images/Logo-PKK-Transparent.png') }}" alt="Logo PKK">
        </div>

        <div class="loading-title">Memuat Halaman</div>
        <div class="loading-message">{{ $message }}</div>

        <div class="loading-dots">
            <span></span>
            <span></span>
            <span></span>
        </div>
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
        font-family: 'Plus Jakarta Sans', 'Segoe UI', system-ui, -apple-system, sans-serif;
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

    /* ===== CONTAINER (Glassmorphism) ===== */
    .container {
        position: relative;
        z-index: 10;
        text-align: center;
        background: rgba(255, 255, 255, 0.95);
        padding: 2.5rem 3.5rem;
        border-radius: 24px;
        box-shadow: 0 25px 70px rgba(13, 148, 136, 0.15);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.8);
        max-width: 500px;
        width: 90%;
        animation: floatCard 6s ease-in-out infinite;
    }
    @keyframes floatCard {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }

    /* ===== LOGO PKK & ANIMATION ===== */
    .loading-logo {
        width: 120px;
        height: 120px;
        margin: 0 auto 1.5rem;
        background: linear-gradient(135deg, #14b8a6, #0d9488);
        border-radius: 50%;
        padding: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: pulseGlow 2.5s infinite, logoFloat 3s ease-in-out infinite;
        box-shadow: 0 8px 30px rgba(13, 148, 136, 0.3);
    }
    @keyframes pulseGlow {
        0% { box-shadow: 0 0 0 0 rgba(13, 148, 136, 0.4); }
        70% { box-shadow: 0 0 0 20px rgba(13, 148, 136, 0); }
        100% { box-shadow: 0 0 0 0 rgba(13, 148, 136, 0); }
    }
    @keyframes logoFloat {
        0%, 100% { transform: translateY(0) rotate(0deg); }
        50% { transform: translateY(-8px) rotate(2deg); }
    }
    .loading-logo img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        filter: drop-shadow(0 4px 8px rgba(0,0,0,0.1));
    }

    /* ===== LOADING DOTS ===== */
    .loading-dots {
        display: flex;
        justify-content: center;
        gap: 8px;
        margin-top: 1.5rem;
    }
    .loading-dots span {
        width: 10px;
        height: 10px;
        background: linear-gradient(135deg, #14b8a6, #0d9488);
        border-radius: 50%;
        animation: bounce 1.4s infinite ease-in-out;
    }
    .loading-dots span:nth-child(1) { animation-delay: -0.32s; }
    .loading-dots span:nth-child(2) { animation-delay: -0.16s; }

    @keyframes bounce {
        0%, 80%, 100% { transform: scale(0); }
        40% { transform: scale(1); }
    }

    /* ===== TYPOGRAPHY ===== */
    .loading-title {
        font-size: 1.5rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
        letter-spacing: -0.5px;
        background: linear-gradient(135deg, #14b8a6, #0d9488);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .loading-message {
        color: #475569;
        font-size: 1rem;
        line-height: 1.6;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 768px) {
        .container {
            max-width: 95%;
            padding: 2rem 1.5rem;
        }
        .loading-logo {
            width: 100px;
            height: 100px;
            padding: 16px;
        }
        .loading-title { font-size: 1.25rem; }
        .loading-message { font-size: 0.95rem; }
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

    // Opsional: Sembunyikan loading secara otomatis saat seluruh halaman selesai dimuat
    // window.addEventListener('load', function() {
    //     setTimeout(window.hideLoading, 500); // Delay 500ms agar transisi halus
    // });
</script>
