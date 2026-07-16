<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wilayah\Wilayah;
use Illuminate\Http\Request;

class WilayahController extends Controller
{
    public function provinces()
    {
        $provinces = Wilayah::provinsi()
            ->orderBy('nama')
            ->get(['kode', 'nama']);
        
        return response()->json($provinces);
    }
    
    public function regencies($provinceCode)
    {
        $regencies = Wilayah::kabupaten($provinceCode)
            ->orderBy('nama')
            ->get(['kode', 'nama']);
        
        return response()->json($regencies);
    }
    
    public function districts($regencyCode)
    {
        $districts = Wilayah::kecamatan($regencyCode)
            ->orderBy('nama')
            ->get(['kode', 'nama']);
        
        return response()->json($districts);
    }
    
    public function villages($districtCode)
    {
        $villages = Wilayah::kelurahan($districtCode)
            ->orderBy('nama')
            ->get(['kode', 'nama']);
        
        return response()->json($villages);
    }
}