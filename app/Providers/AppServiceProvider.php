<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Nama hari & bulan dari Carbon tampil dalam bahasa Indonesia.
        // Catatan: ->format() TIDAK terpengaruh locale (selalu Inggris);
        // gunakan ->translatedFormat() untuk tanggal yang ditampilkan ke pengguna.
        Carbon::setLocale('id');

        // Memaksa semua URL yang di-generate Laravel (asset, url, route) menggunakan HTTPS di production
        if (env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }

        // View Composer untuk floating button
        View::composer(
            'modules.landing.partials.floating-btn',
            \App\Http\View\Composers\FloatingButtonComposer::class
        );
    }
}
