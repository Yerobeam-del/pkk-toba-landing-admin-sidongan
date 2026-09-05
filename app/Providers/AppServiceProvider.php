<?php



/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiting\Limit;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // ============================================================
        // SATU PINTU OTORISASI (Gate::before)
        // ============================================================
        // Semua pengecekan ability — middleware 'permission', blade @can,
        // dan $user->can() — melewati hook ini lebih dulu:
        //
        //   1. Super Admin → selalu true (status dicek lewat SATU method:
        //      User::isSuperAdmin(), lihat app/Models/User.php).
        //   2. Selain itu → true bila user punya permission dengan nama
        //      yang sama dengan ability (mis. 'manage-berita'), yang dihitung
        //      dari izin bawaan role DITAMBAH izin pribadi akun.
        //   3. Return null → evaluasi berlanjut; karena tidak ada ability
        //      lain yang didefinisikan, hasilnya default DENY.
        //
        // Dengan pola ini tidak perlu if super admin tersebar di controller.
        Gate::before(function ($user, string $ability) {
            if ($user?->isSuperAdmin()) {
                return true;
            }

            return $user?->hasPermission($ability) ? true : null;
        });

        // Nama hari & bulan dari Carbon tampil dalam bahasa Indonesia.
        // Catatan: ->format() TIDAK terpengaruh locale (selalu Inggris);
        // gunakan ->translatedFormat() untuk tanggal yang ditampilkan ke pengguna.
        Carbon::setLocale('id');

        // Rate limiter untuk endpoint publik tanpa auth (API v1, health).
        // Tanpa ini endpoint publik bisa dilempar beban tanpa batas.
        RateLimiter::for('public-api', function (Request $request) {
            return Limit::perMinute(120)->by($request->ip());
        });

        // Memaksa semua URL yang di-generate Laravel (asset, url, route)
        // menggunakan HTTPS di production. PENTING: env() hanya terbaca dari
        // file .env — ketika `config:cache` dipakai di production (yang
        // direkomendasikan), env() di luar config mengembalikan null dan
        // forceScheme diam-diam tidak jalan. config('app.env') selalu benar.
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // View Composer untuk floating button
        View::composer(
            'modules.landing.partials.floating-btn',
            \App\Http\View\Composers\FloatingButtonComposer::class
        );
    }
}
/* Dikembangkan oleh Institut Teknologi Del */
