@extends('layouts.app')
@section('title', 'Tentang Kami')

@section('content')
{{-- Hero Section --}}
<section style="background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%); color: #fff; padding: 4rem 0 3rem; position: relative; overflow: hidden">
    <div style="position: absolute; inset: 0; opacity: 0.05; background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 32px 32px"></div>

    <div style="max-width: 1200px; margin: 0 auto; padding: 0 2rem; position: relative; z-index: 1">
        <h1 style="font-size: 2.5rem; font-weight: 800; margin: 0 0 1rem 0; text-align: center">Visi, Misi, & Motto</h1>
        <p style="font-size: 1.1rem; margin: 0 0 1.5rem 0; opacity: 0.9; text-align: center">
            Motto: Tampakna do Rantosna, Rimni Tahi do Gogona - (Kebersamaan mencerminkan Kekuatan)
        </p>

        {{-- Breadcrumb --}}
        <nav style="display: flex; gap: 0.5rem; justify-content: center; align-items: center; font-size: 0.9rem">
            <a href="/" style="color: rgba(255,255,255,0.8); text-decoration: none">Beranda</a>
            <span style="color: rgba(255,255,255,0.6)">/</span>
            <span style="color: #fbbf24; font-weight: 600">Tentang</span>
        </nav>
    </div>
</section>

{{-- Content Section --}}
<section style="padding: 4rem 0; background: #fff">
    <div style="max-width: 1200px; margin: 0 auto; padding: 0 2rem">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; align-items: start">

            {{-- Left Content --}}
            <div>
                <h2 style="font-size: 2rem; font-weight: 800; color: var(--primary); margin: 0 0 1.5rem 0; line-height: 1.2">
                    VISI: TOBA MANTAP 2029
                </h2>

                <p style="font-size: 1.1rem; color: #64748b; margin: 0 0 2rem 0; font-style: italic; line-height: 1.6">
                    "Maju daerahnya, sejahtera rakyatnya dan berkelanjutan pembangunannya!"
                </p>

                <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 1.25rem">
                    <li style="display: flex; gap: 1rem; align-items: flex-start">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#14b8a6" stroke-width="2" style="flex-shrink: 0; margin-top: 0.125rem">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span style="color: #475569; line-height: 1.6; font-size: 0.95rem">
                            MEMBANGUN SUMBER DAYA MANUSIA YANG BERDAYA SAING DAN BERAKHLAK
                        </span>
                    </li>
                    <li style="display: flex; gap: 1rem; align-items: flex-start">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#14b8a6" stroke-width="2" style="flex-shrink: 0; margin-top: 0.125rem">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span style="color: #475569; line-height: 1.6; font-size: 0.95rem">
                            MEMBANGUN INFRASTRUKTUR YANG TERINTEGRASI BERKUALITAS DAN MERATA
                        </span>
                    </li>
                    <li style="display: flex; gap: 1rem; align-items: flex-start">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#14b8a6" stroke-width="2" style="flex-shrink: 0; margin-top: 0.125rem">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span style="color: #475569; line-height: 1.6; font-size: 0.95rem">
                            MENINGKATKAN PEMBANGUNAN EKONOMI YANG BERKELANJUTAN BERBASIS POTENSI DAERAH DAN MENDUKUNG KEMANDIRIAN DAERAH
                        </span>
                    </li>
                    <li style="display: flex; gap: 1rem; align-items: flex-start">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#14b8a6" stroke-width="2" style="flex-shrink: 0; margin-top: 0.125rem">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span style="color: #475569; line-height: 1.6; font-size: 0.95rem">
                            MEWUJUDKAN TATA KELOLA PEMERINTAHAN YANG BAIK DAN BERSIH SEBAGAI PELAYAN (PARHOBAS) RAKYAT
                        </span>
                    </li>
                    <li style="display: flex; gap: 1rem; align-items: flex-start">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#14b8a6" stroke-width="2" style="flex-shrink: 0; margin-top: 0.125rem">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span style="color: #475569; line-height: 1.6; font-size: 0.95rem">
                            MENINGKATKAN KEAMANAN DAN KETERTIBAN
                        </span>
                    </li>
                    <li style="display: flex; gap: 1rem; align-items: flex-start">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#14b8a6" stroke-width="2" style="flex-shrink: 0; margin-top: 0.125rem">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span style="color: #475569; line-height: 1.6; font-size: 0.95rem">
                            MELESTARIKAN NILAI BUDAYA DAN KEARIFAN LOKAL
                        </span>
                    </li>
                </ul>
            </div>

            {{-- Right Content - Maps --}}
            <div style="position: sticky; top: 2rem">
                <div style="background: #f8fafc; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.1)">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3983.4567890123456!2d99.12345678901234!3d2.1234567890123456!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMn7CsDA3JzI0LjQiTiA5OcKwMDcnMjQuNCJF!5e0!3m2!1sen!2sid!4v1234567890123!5m2!1sen!2sid"
                        width="100%"
                        height="450"
                        style="border: 0; display: block"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                    ></iframe>
                </div>

                <div style="padding: 1.5rem; background: #fff; border-radius: 0 0 16px 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); border-top: 1px solid #e2e8f0">
                    <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--text-dark); margin: 0 0 0.5rem 0">
                        Lokasi Kantor PKK Kabupaten Toba
                    </h3>
                    <p style="color: #64748b; font-size: 0.95rem; margin: 0 0 1rem 0; line-height: 1.5">
                        Balige, Kabupaten Toba, Sumatera Utara
                    </p>
                    <a href="https://maps.google.com/?q=PKK+Kabupaten+Toba+Balige"
                       target="_blank"
                       style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1.25rem; background: linear-gradient(135deg, #14b8a6, #0d9488); color: #fff; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.9rem; transition: all 0.2s"
                       onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(20,184,166,0.3)'"
                       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                            <polyline points="15 3 21 3 21 9"/>
                            <line x1="10" y1="14" x2="21" y2="3"/>
                        </svg>
                        Buka di Google Maps
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
/* Responsive untuk Mobile */
@media (max-width: 768px) {
    section[style*="padding: 4rem 0"] {
        padding: 2.5rem 0 !important;
    }

    section[style*="padding: 4rem 0"] > div {
        padding: 0 1.25rem !important;
    }

    section[style*="padding: 4rem 0"] > div > div {
        grid-template-columns: 1fr !important;
        gap: 2rem !important;
    }

    /* Pindahkan map ke bawah di mobile */
    section[style*="padding: 4rem 0"] > div > div > div:last-child {
        order: -1;
        position: static !important;
    }

    section[style*="padding: 4rem 0"] > div > div > div:last-child > div {
        border-radius: 12px !important;
    }

    section[style*="padding: 4rem 0"] > div > div > div:last-child iframe {
        height: 300px !important;
    }

    h1 {
        font-size: 1.75rem !important;
    }

    h2 {
        font-size: 1.5rem !important;
    }

    /* Perbaiki list di mobile */
    ul {
        gap: 1rem !important;
    }

    ul li span {
        font-size: 0.9rem !important;
    }
}

/* Tablet */
@media (max-width: 1024px) and (min-width: 769px) {
    section[style*="padding: 4rem 0"] > div > div {
        gap: 2rem !important;
    }

    section[style*="padding: 4rem 0"] > div > div > div:last-child iframe {
        height: 400px !important;
    }
}
</style>
@endsection
