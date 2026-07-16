<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\SyncKecamatansToba::class,
        \App\Console\Commands\ClearOldNotifications::class, // ✅ Tambahkan ini
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Jalankan setiap hari jam 00:05
        // Membersihkan notifikasi yang SUDAH DIBACA dari hari sebelumnya
        $schedule->command('notifications:cleanup')
            ->dailyAt('00:05')
            ->appendOutputTo(storage_path('logs/notification-cleanup.log'));
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}