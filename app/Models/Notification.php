<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id', 'type', 'title', 'message', 
        'related_id', 'related_type', 'read_at'
    ];
    
    protected $casts = [
        'read_at' => 'datetime'
    ];

    // Relasi ke User
    public function user() 
    { 
        return $this->belongsTo(User::class); 
    }

    // ✅ Scope: Hanya notifikasi belum dibaca
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    // ✅ Scope: Hanya notifikasi sudah dibaca
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    // ✅ Helper: Mark as read & auto-delete
    public function markAsReadAndDelete()
    {
        $this->update(['read_at' => now()]);
        
        // Auto-delete setelah ditandai dibaca
        $this->delete();
        
        return true;
    }
}