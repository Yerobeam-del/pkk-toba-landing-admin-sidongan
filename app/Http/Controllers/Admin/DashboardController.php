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
            // Optimasi: Kurangi jumlah query dengan menggabungkan hitungan
            $counts = DB::selectOne('
                SELECT
                    (SELECT COUNT(*) FROM news) as total_berita,
                    (SELECT COUNT(*) FROM users WHERE email_verified_at IS NOT NULL) as total_pengurus,
                    (SELECT COUNT(*) FROM templates) as total_template,
                    (SELECT COUNT(*) FROM applications WHERE is_active = 1) as total_aplikasi,
                    (SELECT COUNT(*) FROM users) as total_users,
                    (SELECT COUNT(*) FROM documents) as total_sk_dokumen,
                    (SELECT COUNT(*) FROM news WHERE MONTH(created_at) = ? AND YEAR(created_at) = ?) as berita_bulan_ini,
                    (SELECT COUNT(*) FROM users WHERE MONTH(created_at) = ? AND YEAR(created_at) = ?) as users_bulan_ini
            ', [
                now()->month, now()->year,
                now()->month, now()->year,
            ]);

            // Eager load relasi untuk menghindari N+1
            $beritaTerbaru = News::latest()->take(5)->get(['id', 'title', 'created_at']);
            $usersTerbaru = User::with('role:id,name,display_name')
                ->latest()
                ->take(5)
                ->get(['id', 'name', 'role_id', 'created_at']);

            // Data untuk chart (6 bulan terakhir)
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

            // Recent activity (gabungan dari berita terbaru + user terbaru)
            $recentActivities = collect();
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
