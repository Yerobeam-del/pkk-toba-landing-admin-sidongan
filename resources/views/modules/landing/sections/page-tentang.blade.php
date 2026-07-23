@extends('layouts.app')
@section('title', 'Tentang Kami')

@section('content')
<style>
    /* Base Styles */
    .tentang-section {
        padding: 4rem 2rem;
        max-width: 1200px;
        margin: 0 auto;
    }

    .tentang-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 3rem;
        align-items: start;
    }

    .tentang-content h2 {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 1.5rem;
        line-height: 1.3;
    }

    .tentang-content p {
        font-size: 1rem;
        line-height: 1.8;
        color: var(--text-muted);
        margin-bottom: 1.5rem;
    }

    .tentang-list {
        list-style: none;
        padding: 0;
        margin: 2rem 0;
    }

    .tentang-list li {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        margin-bottom: 1rem;
        font-size: 0.95rem;
        color: var(--text-dark);
        line-height: 1.6;
    }

    .tentang-list li::before {
        content: "✓";
        color: var(--primary);
        font-weight: 700;
        flex-shrink: 0;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .map-container {
        position: sticky;
        top: 100px;
        background: #fff;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        border: 1px solid rgba(0,0,0,0.06);
    }

    .map-wrapper {
        position: relative;
        width: 100%;
        /* FIX: Tinggi yang cukup untuk map agar tidak terpotong */
        min-height: 450px;
    }

    .map-wrapper iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: none;
        /* FIX: Pastikan iframe tidak terpotong */
        min-height: 450px;
    }

    .map-info {
        padding: 1.5rem;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: #fff;
    }

    .map-info h3 {
        font-size: 1.1rem;
        font-weight: 700;
        margin: 0 0 0.5rem 0;
    }

    .map-info p {
        font-size: 0.9rem;
        margin: 0 0 1rem 0;
        opacity: 0.95;
    }

    .map-info a {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: #fff;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        padding: 0.5rem 1rem;
        background: rgba(255,255,255,0.2);
        border-radius: 8px;
        transition: all 0.2s;
    }

    .map-info a:hover {
        background: rgba(255,255,255,0.3);
        transform: translateY(-2px);
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .tentang-section {
            padding: 2rem 1rem;
        }

        .tentang-grid {
            /* FIX: Ubah ke 1 kolom di mobile */
            grid-template-columns: 1fr;
            gap: 2rem;
            /* FIX: Urutkan konten dulu, baru map */
            grid-template-rows: auto auto;
        }

        .tentang-content {
            /* FIX: Pastikan konten di atas */
            order: 1;
        }

        .tentang-content h2 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .tentang-content p {
            font-size: 0.95rem;
            line-height: 1.7;
        }

        .tentang-list li {
            font-size: 0.9rem;
        }

        .map-container {
            /* FIX: Map tidak sticky di mobile */
            position: static;
            /* FIX: Pastikan map di bawah */
            order: 2;
            width: 100%;
        }

        .map-wrapper {
            /* FIX: Tinggi map yang pas di mobile */
            min-height: 350px;
        }

        .map-wrapper iframe {
            min-height: 350px;
        }

        .map-info {
            padding: 1.25rem;
        }

        .map-info h3 {
            font-size: 1rem;
        }

        .map-info p {
            font-size: 0.85rem;
        }

        .map-info a {
            font-size: 0.85rem;
            padding: 0.4rem 0.8rem;
        }
    }

    /* Small Mobile */
    @media (max-width: 480px) {
        .tentang-section {
            padding: 1.5rem 0.75rem;
        }

        .tentang-content h2 {
            font-size: 1.25rem;
        }

        .map-wrapper {
            min-height: 300px;
        }

        .map-wrapper iframe {
            min-height: 300px;
        }
    }
</style>

<section class="tentang-section">
    <div class="tentang-grid">
        {{-- Left Content --}}
        <div class="tentang-content">
            <h2>Memberdayakan Keluarga, Mensejahterakan Masyarakat</h2>

            <p>
                PKK Kabupaten Toba berkomitmen untuk terus berinovasi dalam meningkatkan kesejahteraan keluarga dan masyarakat. Melalui berbagai program unggulan, kami berupaya membangun sumber daya manusia yang berkualitas dan berdaya saing.
            </p>

            <ul class="tentang-list">
                <li>Program ketahanan dan kesejahteraan keluarga</li>
                <li>Pemberdayaan ekonomi keluarga</li>
                <li>Peningkatan kesehatan ibu dan anak</li>
                <li>Pelestarian nilai budaya dan kearifan lokal</li>
                <li>Pengembangan pendidikan dan keterampilan</li>
            </ul>

            <p>
                Dengan semangat gotong royong dan kebersamaan, PKK Kabupaten Toba terus bergerak maju untuk mewujudkan masyarakat yang sejahtera, mandiri, dan berakhlak.
            </p>
        </div>

        {{-- Right Content - Maps --}}
        <div class="map-container">
            <div class="map-wrapper">
                {{-- Google Maps Embed --}}
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3983.1234567890123!2d99.12345678901234!3d2.1234567890123456!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMnCsMDcnMjQuNCJOIDk5wrAwNycyNC40IkU!5e0!3m2!1sen!2sid!4v1234567890123!5m2!1sen!2sid"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
            <div class="map-info">
                <h3>Lokasi Kantor PKK Kabupaten Toba</h3>
                <p>Balige, Kabupaten Toba, Sumatera Utara</p>
                <a href="https://goo.gl/maps/xxxxx" target="_blank">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                        <polyline points="15 3 21 3 21 9"/>
                        <line x1="10" y1="14" x2="21" y2="3"/>
                    </svg>
                    Buka di Google Maps
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
