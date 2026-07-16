<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL; // <-- Tambahan: Import Facade URL

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Tambahan: Memaksa HTTPS jika diakses via proxy/tunnel (localtunnel)
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            URL::forceScheme('https');
        }

        // Daftarkan View Composer untuk floating button (Kode lama Anda tetap aman)
        View::composer(
            'modules.landing.partials.floating-btn',
            \App\Http\View\Composers\FloatingButtonComposer::class
        );
    }
}
