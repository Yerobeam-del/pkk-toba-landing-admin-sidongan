<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Role;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'avatar',
        'email_verified_at',
        'password',
        'remember_token',
        'sidongan_role',
        'sieda_role',
        'sieda_kecamatan',
        'sieda_kelurahan',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi many-to-many dengan Application
     * User bisa mengakses banyak aplikasi
     */
    public function applications()
    {
        return $this->belongsToMany(Application::class, 'application_user')
                    ->withTimestamps();
    }

    /**
     * Check apakah user bisa akses aplikasi tertentu
     */
    public function canAccessApplication($applicationId)
    {
        return $this->applications()->where('application_id', $applicationId)->exists();
    }

    /**
     * Check apakah user adalah admin (bisa akses semua)
     */
    public function isAdmin()
    {
        // Izinkan jika email admin ATAU role super_admin
        return $this->email === 'admin@pkk-toba.id' || $this->sidongan_role === 'super_admin';
    }

    // ==================== SIDONGAN ROLE HELPERS ====================

    /**
     * Daftar role yang tersedia di SIDONGAN
     */
    public static function getSidonganRoles()
    {
        return [
            'ketua' => 'Ketua PKK',
            'sekretaris' => 'Sekretaris PKK',
            'bendahara' => 'Bendahara PKK',
            'staf_ahli_1' => 'Staf Ahli I',
            'staf_ahli_2' => 'Staf Ahli II',
            'pengurus_1' => 'Ketua Pengurus I',
            'pengurus_2' => 'Ketua Pengurus II',
            'pengurus_3' => 'Ketua Pengurus III',
            'pengurus_4' => 'Ketua Pengurus IV',
        ];
    }

    /**
     * Get nama role SIDONGAN yang human-readable
     */
    public function getSidonganRoleNameAttribute()
    {
        $roles = self::getSidonganRoles();
        return $roles[$this->sidongan_role] ?? '-';
    }

    /**
     * Check apakah user memiliki role SIDONGAN tertentu
     */
    public function hasSidonganRole($role)
    {
        return $this->sidongan_role === $role;
    }

    /**
     * Check apakah user memiliki akses ke SIDONGAN
     */
    public function hasSidonganAccess()
    {
        return !empty($this->sidongan_role) && 
               array_key_exists($this->sidongan_role, self::getSidonganRoles());
    }

    /**
     * Check apakah user adalah Ketua PKK (bisa disposisi & verifikasi)
     */
    public function isSidonganKetua()
    {
        return $this->hasSidonganRole('ketua');
    }

    /**
     * Check apakah user adalah Sekretaris PKK (bikin agenda surat)
     */
    public function isSidonganSekretaris()
    {
        return $this->hasSidonganRole('sekretaris');
    }

    /**
     * Check apakah user adalah Bendahara PKK
     */
    public function isSidonganBendahara()
    {
        return $this->hasSidonganRole('bendahara');
    }

    /**
     * Check apakah user adalah Ketua POKJA / Pengurus (bisa terima disposisi & buat laporan)
     */
    public function isSidonganPokja()
    {
        return in_array($this->sidongan_role, ['pengurus_1', 'pengurus_2', 'pengurus_3', 'pengurus_4']);
    }

    /**
     * Check apakah user adalah Staf Ahli
     */
    public function isSidonganStafAhli()
    {
        return in_array($this->sidongan_role, ['staf_ahli_1', 'staf_ahli_2']);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function hasRole($roleName): bool
    {
        return $this->role && $this->role->name === $roleName;
    }

    public function hasPermission($permissionName): bool
    {
        if (!$this->role) {
            return false;
        }
        
        // Administrator selalu punya semua permission
        if ($this->role->name === 'administrator') {
            return true;
        }
        
        return $this->role->hasPermission($permissionName);
    }

    public function hasAnyPermission($permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

}