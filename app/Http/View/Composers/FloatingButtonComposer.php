<?php

namespace App\Http\View\Composers;

use App\Models\Application;
use Illuminate\View\View;

class FloatingButtonComposer
{
    public function compose(View $view)
    {
        // Ambil aplikasi yang aktif dan statusnya active
        $applications = Application::where('is_active', true)
            ->where('status', Application::STATUS_ACTIVE)
            ->orderBy('sort_order')
            ->get();
        
        $view->with('floatingApps', $applications);
    }
}