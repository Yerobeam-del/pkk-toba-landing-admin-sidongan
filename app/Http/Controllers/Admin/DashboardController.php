<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\User;
use App\Models\Application;
use App\Models\Document;
use App\Models\Template;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'totalBerita' => News::count(),
            'totalPengurus' => User::whereNotNull('email_verified_at')->count(),
            'totalTemplate' => Template::count(),
            'totalAplikasi' => Application::where('is_active', true)->count(),
            'totalUsers' => User::count(),
            'totalSKDokumen' => Document::count() ?? 0,
            'beritaTerbaru' => News::with('author')->latest()->take(5)->get(),
            'aplikasiTerbaru' => Application::latest()->take(5)->get(),
            'usersTerbaru' => User::latest()->take(5)->get(),
            'statistikBulanIni' => [
                'berita' => News::whereMonth('created_at', now()->month)->count(),
                'users' => User::whereMonth('created_at', now()->month)->count(),
            ]
        ];

        return view('admin.dashboard', $data);
    }
}
