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
        <div class="loading-icon">
            <svg class="spinner" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
            </svg>
        </div>

        <div class="loading-title">Memuat Halaman</div>
        <div class="loading-message">{{ $message }}</div>
    </div>
</div>

<style>
    .loading-screen {
        position: fixed;
        top: 0; left: 0;
        width: 100vw; height: 100vh;
        background: linear-gradient(180deg, #fffbeb 0%, #fef3c7 50%, #ffffff 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        overflow: hidden;
        font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        transition: opacity 0.4s ease, visibility 0.4s ease;
    }

    /* State ketika loading disembunyikan */
    .loading-screen.hidden {
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
    }

    /* ===== ANIMATED CLOUDS (Sama persis dengan 404) ===== */
    .cloud {
        position: absolute;
        background: #fff;
        border-radius: 100px;
        opacity: 0.9;
        box-shadow: 0 10px 30px rgba(217, 119, 6, 0.08);
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
        background: rgba(255, 255, 255, 0.92);
        padding: 2.5rem 3.5rem;
        border-radius: 24px;
        box-shadow: 0 25px 70px rgba(217, 119, 6, 0.15);
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

    /* ===== LOADING ICON & SPINNER ===== */
    .loading-icon {
        width: 72px;
        height: 72px;
        margin: 0 auto 1.25rem;
        background: linear-gradient(135deg, #f59e0b, #d97706);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: pulseGlow 2.5s infinite;
    }
    @keyframes pulseGlow {
        0% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.4); }
        70% { box-shadow: 0 0 0 18px rgba(245, 158, 11, 0); }
        100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); }
    }
    .spinner {
        width: 36px;
        height: 36px;
        color: #ffffff;
        animation: spin 1.2s linear infinite;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* ===== TYPOGRAPHY ===== */
    .loading-title {
        font-size: 1.5rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
        letter-spacing: -0.5px;
        background: linear-gradient(135deg, #f59e0b, #d97706);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
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
