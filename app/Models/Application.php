<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = [
        'name',
        'short_name',
        'description',
        'url',
        'icon',
        'category',
        'sort_order',
        'is_active',
        'status',
        'features',
        'show_in_floating',
        'show_in_footer',
        'show_in_quick_access'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'features' => 'array',
        'show_in_floating' => 'boolean',
        'show_in_footer' => 'boolean',
        'show_in_quick_access' => 'boolean',
    ];

    // Status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_MAINTENANCE = 'maintenance';
    const STATUS_DEVELOPMENT = 'development';

    public static function getStatuses()
    {
        return [
            self::STATUS_ACTIVE => 'Aktif',
            self::STATUS_MAINTENANCE => 'Dalam Maintenance',
            self::STATUS_DEVELOPMENT => 'Dalam Pengembangan',
        ];
    }

    public function getStatusLabelAttribute()
    {
        return self::getStatuses()[$this->status] ?? $this->status;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('status', self::STATUS_ACTIVE);
    }

    public function scopeMaintenance($query)
    {
        return $query->where('status', self::STATUS_MAINTENANCE);
    }

    public function scopeDevelopment($query)
    {
        return $query->where('status', self::STATUS_DEVELOPMENT);
    }

    public function scopeLayanan($query)
    {
        return $query->where('category', 'layanan');
    }

    public function scopeAplikasi($query)
    {
        return $query->where('category', 'aplikasi');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'application_user')
            ->withTimestamps();
    }
}