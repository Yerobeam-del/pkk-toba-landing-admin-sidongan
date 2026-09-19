<?php

/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
namespace App\Http\View\Composers;

use App\Models\Application;
use App\Models\SiteSetting;
use Illuminate\View\View;

/**
 * Menyuntikkan data footer ke partial
 * modules.landing.partials.footer:
 *  - Konten yang diatur dari Admin Panel > Pengaturan Situs
 *    (judul, alamat, Instagram, copyright) via SiteSetting.
 *  - Quick Access: aplikasi aktif yang dipilih tampil di footer
 *    lewat Admin Panel > Manajemen Aplikasi.
 */
class FooterComposer
{
    public function compose(View $view)
    {
        $view->with([
            'footerTitle'     => SiteSetting::get('footer_title'),
            'footerAddress'   => SiteSetting::get('footer_address'),
            'instagramUrl'    => SiteSetting::get('instagram_url'),
            'instagramHandle' => SiteSetting::get('instagram_handle'),
            'footerCopyright' => SiteSetting::get('footer_copyright'),
            // Path relatif di disk public (mis. site/logo.png); kosong =
            // pakai file logo bawaan di assets/landing/images.
            'footerLogoToba'  => SiteSetting::get('footer_logo_toba'),
            'footerLogoPkk'   => SiteSetting::get('footer_logo_pkk'),
        ]);

        // Filter show_in_footer WAJIB ada di sini (pola sama dengan
        // FloatingButtonComposer): sebelumnya query ini numpang @php
        // di blade footer.
        $quickAccessApps = Application::where('is_active', true)
            ->where('status', Application::STATUS_ACTIVE)
            ->where('show_in_footer', true)
            ->orderBy('sort_order')
            ->get();

        $view->with('quickAccessApps', $quickAccessApps);
    }
}
/* Dikembangkan oleh Institut Teknologi Del */
