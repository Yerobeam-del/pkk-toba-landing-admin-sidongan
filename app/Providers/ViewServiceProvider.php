<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Http\View\Composers\SidonganNotificationComposer;

class ViewServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // ✅ Daftarkan composer untuk layout SIDONGAN
        View::composer('sidongan.layouts.app', SidonganNotificationComposer::class);
    }
}