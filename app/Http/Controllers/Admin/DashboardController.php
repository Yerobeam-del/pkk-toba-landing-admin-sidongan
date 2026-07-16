<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
// Import semua Model yang diperlukan
use App\Models\News; 
use App\Models\Desa; 
use App\Models\StrukturMember; 
use App\Models\Template; 

class DashboardController extends Controller
{
    public function index()
    {
        // Hitung data dari database
        $totalBerita = News::count();
        $totalDesa = Desa::count();
        
        // Hitung Pengurus (Asumsi modelnya StrukturMember)
        // Jika modelnya berbeda (misal: User atau Pengurus), ganti di sini
        $totalPengurus = StrukturMember::count(); 
        
        $totalTemplate = Template::count();

        // Kirim data ke view
        return view('admin.dashboard', compact(
            'totalBerita',
            'totalDesa',
            'totalPengurus',
            'totalTemplate'
        ));
    }
}