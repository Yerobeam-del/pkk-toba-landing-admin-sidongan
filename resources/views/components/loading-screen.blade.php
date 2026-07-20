{{-- Loading Screen Overlay --}}
<div id="global-loading-screen" class="global-loading-screen">
    <div class="loading-content">
        {{-- Logo --}}
        <div class="loading-logo">
            <img src="{{ asset('assets/landing/images/Logo-PKK-Transparent.png') }}" alt="PKK Logo" class="loading-logo-img">
        </div>

        {{-- Spinner --}}
        <div class="loading-spinner">
            <div class="spinner-ring"></div>
            <div class="spinner-ring"></div>
            <div class="spinner-ring"></div>
        </div>

        <p class="loading-text">Memuat halaman...</p>
    </div>
</div>

<style>
.global-loading-screen {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #0f6b63 0%, #14a098 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 99999;
    transition: opacity 0.5s ease, visibility 0.5s ease;
}

.global-loading-screen.fade-out {
    opacity: 0;
    visibility: hidden;
}

.loading-content {
    text-align: center;
    color: white;
}

.loading-logo {
    margin-bottom: 2rem;
    animation: pulse 2s ease-in-out infinite;
}

.loading-logo-img {
    width: 120px;
    height: 120px;
    object-fit: contain;
    filter: drop-shadow(0 4px 12px rgba(0,0,0,0.2));
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.loading-spinner {
    position: relative;
    width: 80px;
    height: 80px;
    margin: 0 auto 1.5rem;
}

.spinner-ring {
    position: absolute;
    border: 4px solid transparent;
    border-top-color: white;
    border-radius: 50%;
    animation: spin 1.5s cubic-bezier(0.5, 0, 0.5, 1) infinite;
}

.spinner-ring:nth-child(1) {
    width: 80px;
    height: 80px;
    animation-delay: 0s;
}

.spinner-ring:nth-child(2) {
    width: 60px;
    height: 60px;
    top: 10px;
    left: 10px;
    animation-delay: 0.2s;
    border-top-color: rgba(255,255,255,0.7);
}

.spinner-ring:nth-child(3) {
    width: 40px;
    height: 40px;
    top: 20px;
    left: 20px;
    animation-delay: 0.4s;
    border-top-color: rgba(255,255,255,0.4);
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.loading-text {
    font-size: 1.1rem;
    font-weight: 500;
    letter-spacing: 0.5px;
    opacity: 0.9;
}
</style>

<script>
window.addEventListener('load', function() {
    const loadingScreen = document.getElementById('global-loading-screen');
    if (loadingScreen) {
        setTimeout(function() {
            loadingScreen.classList.add('fade-out');
            setTimeout(function() {
                loadingScreen.remove();
            }, 500);
        }, 300);
    }
});
</script>
