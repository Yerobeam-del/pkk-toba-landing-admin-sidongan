<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    protected $fillable = ['name', 'display_name', 'group', 'description'];

    public function roles(): BelongsToMany
    {
        // Spesifikkan nama tabel pivot: 'role_permission'
        return $this->belongsToMany(Role::class, 'role_permission');
    }
}