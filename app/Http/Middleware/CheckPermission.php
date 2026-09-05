<?php



/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CheckPermission
{
    /**
     * Otorisasi lewat Gate: memakai ability yang didefinisikan di
     * AuthServiceProvider-pattern (silang lihat AppServiceProvider::boot()).
     * Super Admin otomatis lolos lewat Gate::before — tidak perlu if lagi
     * di sini. Check via Gate (bukan memanggil hasAnyPermission langsung)
     * supaya semua jalur otorisasi punya satu titik kebijakan yang sama.
     */
    public function handle(Request $request, Closure $next, $permission)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $permissions = is_array($permission) ? $permission : explode('|', $permission);

        $allowed = false;
        foreach ($permissions as $p) {
            if (Gate::allows($p)) {
                $allowed = true;
                break;
            }
        }

        if (!$allowed) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }
        return $next($request);
    }
}
/* Dikembangkan oleh Institut Teknologi Del */
