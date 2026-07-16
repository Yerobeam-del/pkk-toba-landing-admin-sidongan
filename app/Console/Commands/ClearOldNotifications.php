<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Notification;
use Carbon\Carbon;

class ClearOldNotifications extends Command
{
    protected $signature = 'notifications:cleanup';
    protected $description = 'Hapus notifikasi yang sudah dibaca dari hari-hari sebelumnya';

    public function handle()
    {
        $this->info('Memulai pembersihan notifikasi...');

        // 1. Hapus Notifikasi SUDAH DIBACA dari kemarin atau sebelumnya
        // Notifikasi yang belum dibaca akan dibiarkan tetap ada agar bisa dilihat esok hari
        $deletedReadCount = Notification::whereNotNull('read_at')
            ->whereDate('created_at', '<', Carbon::today())
            ->delete();

        $this->info("{$deletedReadCount} notifikasi 'Sudah Dibaca' berhasil dihapus.");

        // 2. (Opsional) Hapus notifikasi BELUM DIBACA yang sudah terlalu lama (misal > 30 hari)
        // agar database tidak menumpuk data yang sudah tidak relevan
        $deletedOldUnreadCount = Notification::whereNull('read_at')
            ->whereDate('created_at', '<', Carbon::today()->subDays(30))
            ->delete();

        if ($deletedOldUnreadCount > 0) {
            $this->info("{$deletedOldUnreadCount} notifikasi belum dibaca yang lebih dari 30 hari dihapus.");
        }

        $this->info('Selesai. Notifikasi hari ini dan yang belum dibaca tetap aman.');
        return Command::SUCCESS;
    }
}