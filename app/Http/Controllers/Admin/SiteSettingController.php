<?php

/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Admin Panel > Pengaturan Situs.
 * Konten footer (judul, alamat, Instagram, copyright) yang juga
 * dipakai bersama oleh halaman Tentang Kami (alamat kantor).
 */
class SiteSettingController extends Controller
{
    public function index()
    {
        $settings = [];
        foreach (array_keys(SiteSetting::DEFAULTS) as $key) {
            $settings[$key] = SiteSetting::get($key);
        }

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'footer_title'     => 'required|string|max:255',
            'footer_address'   => 'required|string|max:1000',
            'instagram_url'    => 'required|url|max:500',
            'instagram_handle' => 'required|string|max:100',
            'footer_copyright' => 'required|string|max:255',
            // Logo opsional: dikirim hanya bila admin mengganti file.
            'footer_logo_toba' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'footer_logo_pkk'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            // Checkbox "hapus logo" (HTML checkbox tak ter-submit = tidak ada).
            'remove_logo_toba' => 'nullable|boolean',
            'remove_logo_pkk'  => 'nullable|boolean',
        ], [
            'instagram_url.url' => 'URL Instagram harus berupa link yang valid (contoh: https://www.instagram.com/tppkktoba_/)',
            'footer_logo_toba.image' => 'File logo Kabupaten Toba harus berupa gambar (jpg, png, webp)',
            'footer_logo_pkk.image' => 'File logo PKK harus berupa gambar (jpg, png, webp)',
            'footer_logo_toba.max' => 'Ukuran logo Kabupaten Toba maksimal 2MB',
            'footer_logo_pkk.max' => 'Ukuran logo PKK maksimal 2MB',
        ]);

        // Upload logo baru: simpan ke disk public/site/, hapus file lama
        // agar tidak menumpuk file yatim (masalah yang pernah terjadi di
        // Manajemen Aplikasi).
        foreach (['toba', 'pkk'] as $slot) {
            $field = "footer_logo_{$slot}";
            $removeField = "remove_logo_{$slot}";

            if ($request->boolean($removeField)) {
                $this->deleteLogo($field);
                continue;
            }

            if ($request->hasFile($field)) {
                $this->deleteLogo($field);
                SiteSetting::set([
                    $field => $request->file($field)->store('site', 'public'),
                ]);
            }
        }

        SiteSetting::set(collect($validated)->except([
            'footer_logo_toba', 'footer_logo_pkk', 'remove_logo_toba', 'remove_logo_pkk',
        ])->all());

        return redirect()->route('admin.settings.index')
            ->with('success', 'Pengaturan situs berhasil diperbarui');
    }

    /** Hapus file logo dari disk public + reset nilainya ke default (kosong). */
    private function deleteLogo(string $field): void
    {
        $current = SiteSetting::get($field);
        if ($current !== '' && Storage::disk('public')->exists($current)) {
            Storage::disk('public')->delete($current);
        }

        SiteSetting::set([$field => null]);
    }
}
/* Dikembangkan oleh Institut Teknologi Del */
