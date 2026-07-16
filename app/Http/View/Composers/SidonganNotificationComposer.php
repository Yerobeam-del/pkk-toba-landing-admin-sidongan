<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Models\Notification;

class SidonganNotificationComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $user = auth()->guard('sidongan')->user();
        
        if ($user) {
            // Ambil 5 notifikasi terakhir
            $notifications = Notification::where('user_id', $user->id)
                ->latest()
                ->take(5)
                ->get();
            
            // Hitung yang belum dibaca
            $unreadCount = Notification::where('user_id', $user->id)
                ->whereNull('read_at')
                ->count();
        } else {
            $notifications = collect();
            $unreadCount = 0;
        }
        
        // Kirim data ke view layout
        $view->with([
            'sidonganNotifications' => $notifications,
            'sidonganUnreadCount' => $unreadCount,
        ]);
    }
}