<?php

namespace App\Http\View\Composers;

use App\Models\Application;
use Illuminate\View\View;

class FloatingButtonComposer
{
    public function compose(View $view)
    {
        // Aplikasi aktif yang dipilih tampil di floating button lewat
        // Admin Panel > Manajemen Aplikasi.
        //
        // Filter show_in_floating WAJIB ada di sini: sebelumnya query ini
        // melewatkannya, lalu blade menimpanya dengan query sendiri sehingga
        // composer ini tidak berpengaruh sama sekali.
        $applications = Application::where('is_active', true)
            ->where('status', Application::STATUS_ACTIVE)
            ->where('show_in_floating', true)
            ->orderBy('sort_order')
            ->get();

        $view->with('floatingApps', $applications);
    }
}