<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WilayahIndonesiaService
{
    protected $baseUrl = 'https://wilayah.id/api';
    protected $regencyCode = '12.12'; // Kabupaten Toba
    
    /**
     * Get all kecamatan in Kabupaten Toba from wilayah.id API
     */
    public function getKecamatansToba()
    {
        try {
            $url = "{$this->baseUrl}/districts/{$this->regencyCode}.json";
            
            $response = Http::timeout(30)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'User-Agent' => 'PKK-Toba-System/1.0'
                ])
                ->get($url);
            
            if (!$response->successful()) {
                Log::error("❌ API Error: Status {$response->status()}");
                return $this->getFallbackData();
            }
            
            $result = $response->json();
            
            // ✅ Akses array 'data' dari response wrapper
            $data = $result['data'] ?? [];
            
            if (!is_array($data) || empty($data)) {
                Log::warning("⚠️ API response data kosong");
                return $this->getFallbackData();
            }
            
            // Normalisasi format agar cocok dengan model database
            return array_map(function($item) {
                return [
                    'kode_wilayah' => $item['code'] ?? null,
                    'name' => $item['name'] ?? null,
                    'kabupaten_kode' => $this->regencyCode
                ];
            }, array_filter($data, fn($i) => !empty($i['code']) && !empty($i['name'])));
            
        } catch (\Exception $e) {
            Log::error("🔥 Fetch Exception: " . $e->getMessage());
            return $this->getFallbackData();
        }
    }
    
    /**
     * Fallback data (16 Kecamatan Kab. Toba sesuai API wilayah.id)
     */
    protected function getFallbackData()
    {
        return [
            ['kode_wilayah' => '12.12.01', 'name' => 'Balige', 'kabupaten_kode' => '12.12'],
            ['kode_wilayah' => '12.12.02', 'name' => 'Laguboti', 'kabupaten_kode' => '12.12'],
            ['kode_wilayah' => '12.12.03', 'name' => 'Silaen', 'kabupaten_kode' => '12.12'],
            ['kode_wilayah' => '12.12.04', 'name' => 'Habinsaran', 'kabupaten_kode' => '12.12'],
            ['kode_wilayah' => '12.12.05', 'name' => 'Pintu Pohan Meranti', 'kabupaten_kode' => '12.12'],
            ['kode_wilayah' => '12.12.06', 'name' => 'Borbor', 'kabupaten_kode' => '12.12'],
            ['kode_wilayah' => '12.12.07', 'name' => 'Porsea', 'kabupaten_kode' => '12.12'],
            ['kode_wilayah' => '12.12.08', 'name' => 'Ajibata', 'kabupaten_kode' => '12.12'],
            ['kode_wilayah' => '12.12.09', 'name' => 'Lumban Julu', 'kabupaten_kode' => '12.12'],
            ['kode_wilayah' => '12.12.10', 'name' => 'Uluan', 'kabupaten_kode' => '12.12'],
            ['kode_wilayah' => '12.12.19', 'name' => 'Sigumpar', 'kabupaten_kode' => '12.12'],
            ['kode_wilayah' => '12.12.20', 'name' => 'Siantar Narumonda', 'kabupaten_kode' => '12.12'],
            ['kode_wilayah' => '12.12.21', 'name' => 'Nassau', 'kabupaten_kode' => '12.12'],
            ['kode_wilayah' => '12.12.22', 'name' => 'Tampahan', 'kabupaten_kode' => '12.12'],
            ['kode_wilayah' => '12.12.23', 'name' => 'Bonatua Lunasi', 'kabupaten_kode' => '12.12'],
            ['kode_wilayah' => '12.12.24', 'name' => 'Parmaksian', 'kabupaten_kode' => '12.12'],
        ];
    }
    
    /**
     * Sync kecamatan from API to database
     */
    public function syncKecamatansToba()
    {
        Log::info("🚀 Starting sync kecamatan Toba ({$this->regencyCode})...");
        
        $apiData = $this->getKecamatansToba();
        
        if (empty($apiData)) {
            Log::warning("⛔ No data to sync");
            return 0;
        }
        
        $count = 0;
        foreach ($apiData as $kec) {
            if (empty($kec['kode_wilayah']) || empty($kec['name'])) continue;
            
            \App\Models\Kecamatan::updateOrCreate(
                [
                    'kode_wilayah' => $kec['kode_wilayah'],
                    'kabupaten_kode' => $kec['kabupaten_kode']
                ],
                [
                    'name' => $kec['name']
                ]
            );
            $count++;
        }
        
        Log::info("✅ Synced {$count} kecamatan(s) to database");
        return $count;
    }
}