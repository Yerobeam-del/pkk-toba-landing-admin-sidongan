<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WilayahIndonesiaService;

class SyncKecamatansToba extends Command
{
    protected $signature = 'wilayah:sync-toba';
    protected $description = 'Sync kecamatan Kabupaten Toba from API wilayah.id';

    public function handle(WilayahIndonesiaService $service)
    {
        $this->info('🔄 Syncing kecamatan Kabupaten Toba...');
        
        $count = $service->syncKecamatansToba();
        
        $this->info("✅ Successfully synced {$count} kecamatan(s)!");
        
        return Command::SUCCESS;
    }
}