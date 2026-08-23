<?php



/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\News;
use App\Models\Template;
use App\Models\User;
use App\Models\Document;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // Hitung secara terpisah agar jika satu tabel gagal, yang lain tetap jalan
            $counts = new \stdClass();
            try { $counts->total_berita = News::count(); } catch (\Exception $e) { $counts->total_berita = 0; }
            try { $counts->total_pengurus = User::whereNotNull('email_verified_at')->count(); } catch (\Exception $e) { $counts->total_pengurus = 0; }
            try { $counts->total_template = Template::count(); } catch (\Exception $e) { $counts->total_template = 0; }
            try { $counts->total_aplikasi = Application::where('is_active', true)->count(); } catch (\Exception $e) { $counts->total_aplikasi = 0; }
            try { $counts->total_users = User::count(); } catch (\Exception $e) { $counts->total_users = 0; }
            try { $counts->total_sk_dokumen = Document::count(); } catch (\Exception $e) { $counts->total_sk_dokumen = 0; }
            try { $counts->berita_bulan_ini = News::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(); } catch (\Exception $e) { $counts->berita_bulan_ini = 0; }
            try { $counts->users_bulan_ini = User::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(); } catch (\Exception $e) { $counts->users_bulan_ini = 0; }

            // Eager load relasi untuk menghindari N+1
            try { $beritaTerbaru = News::latest()->take(5)->get(['id', 'title', 'created_at']); } catch (\Exception $e) { $beritaTerbaru = collect(); }
            try { $usersTerbaru = User::with('role:id,name,display_name')->latest()->take(5)->get(['id', 'name', 'role_id', 'created_at']); } catch (\Exception $e) { $usersTerbaru = collect(); }

            // Data untuk chart (6 bulan terakhir) — resilient per tabel
            $chartData = [];
            try {
                $chartData = DB::select('
                    SELECT
                        DATE_FORMAT(created_at, "%Y-%m") as month,
                        COUNT(*) as count
                    FROM (
                        SELECT created_at FROM news UNION ALL
                        SELECT created_at FROM users WHERE email_verified_at IS NOT NULL
                    ) combined
                    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                    GROUP BY month
                    ORDER BY month ASC
                ');
            } catch (\Exception $e) {
                $chartData = [];
            }

            // Recent activity (gabungan dari berita terbaru + user terbaru)
            $recentActivities = collect();
            try {
                foreach ($beritaTerbaru as $b) {
                    $recentActivities->push([
                        'type' => 'berita',
                        'icon' => 'newspaper',
                        'text' => 'Berita "' . Str::limit($b->title, 40) . '" diterbitkan',
                        'time' => $b->created_at,
                    ]);
                }
                foreach ($usersTerbaru as $u) {
                    $recentActivities->push([
                        'type' => 'user',
                        'icon' => 'user',
                        'text' => 'Akun "' . $u->name . '" dibuat',
                        'time' => $u->created_at,
                    ]);
                }
                $recentActivities = $recentActivities->sortByDesc('time')->take(8)->values();
            } catch (\Exception $e) {
                $recentActivities = collect();
            }

            $data = [
                'totalBerita' => $counts->total_berita ?? 0,
                'totalPengurus' => $counts->total_pengurus ?? 0,
                'totalTemplate' => $counts->total_template ?? 0,
                'totalAplikasi' => $counts->total_aplikasi ?? 0,
                'totalUsers' => $counts->total_users ?? 0,
                'totalSKDokumen' => $counts->total_sk_dokumen ?? 0,
                'beritaTerbaru' => $beritaTerbaru,
                'usersTerbaru' => $usersTerbaru,
                'chartData' => $chartData,
                'recentActivities' => $recentActivities,
                'statistikBulanIni' => [
                    'berita' => $counts->berita_bulan_ini ?? 0,
                    'users' => $counts->users_bulan_ini ?? 0,
                ],
            ];

            return view('admin.dashboard', $data);
        } catch (\Exception $e) {
            Log::error('Dashboard error: ' . $e->getMessage());

            $data = [
                'totalBerita' => 0,
                'totalPengurus' => 0,
                'totalTemplate' => 0,
                'totalAplikasi' => 0,
                'totalUsers' => 0,
                'totalSKDokumen' => 0,
                'beritaTerbaru' => collect(),
                'usersTerbaru' => collect(),
                'chartData' => [],
                'recentActivities' => collect(),
                'statistikBulanIni' => [
                    'berita' => 0,
                    'users' => 0
                ],
            ];

            return view('admin.dashboard', $data);
        }
    }
}
/* Dikembangkan oleh Institut Teknologi Del */
