<?php



/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Role;
use App\Models\Permission;
use App\Notifications\PersonalEmailVerificationNotification;
use App\Notifications\ResetPasswordNotification;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'personal_email',
        'phone_number',
        'avatar',
        'email_verified_at',
        'personal_email_verified_at',
        'onboarding_skipped_at',
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
            'personal_email_verified_at' => 'datetime',
            'onboarding_skipped_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Tentukan alamat email tujuan untuk notifikasi mail.
     *
     * - PersonalEmailVerificationNotification → kirim ke $notification->email
     *   (email pribadi yang belum disimpan di DB, dibawa oleh notification instance)
     * - Notifikasi lain → personal_email jika sudah diverifikasi, fallback ke login email
     */
    public function routeNotificationForMail($notification = null): array|string
    {
        // PersonalEmailVerificationNotification membawa email tujuannya sendiri
        if ($notification instanceof PersonalEmailVerificationNotification) {
            return $notification->email;
        }

        // Notifikasi lain (reset password, dll) → personal_email jika sudah diverifikasi
        if ($this->personal_email_verified_at && $this->personal_email) {
            return $this->personal_email;
        }

        return $this->email;
    }

    /**
     * Send the password reset notification using custom branded notification.
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token, 'web'));
    }

    /**
     * Send password reset notification for SIDONGAN guard.
     */
    public function sendSidonganPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token, 'sidongan'));
    }

    /**
     * Cek apakah personal email sudah diverifikasi.
     */
    public function hasVerifiedPersonalEmail(): bool
    {
        return !is_null($this->personal_email_verified_at);
    }

    /**
     * Tandai personal email sebagai terverifikasi.
     */
    public function markPersonalEmailAsVerified(): bool
    {
        return $this->forceFill([
            'personal_email_verified_at' => $this->freshTimestamp(),
        ])->save();
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
     * Check apakah user adalah Super Admin (akses penuh).
     *
     * Sumber status Super Admin ada DUA dan keduanya valid:
     *  1. role di tabel roles bernama 'super_admin'
     *     (dipakai sebagian controller/view lama),
     *  2. kolom users.sidongan_role === 'super_admin'
     *     (penanda lama yang juga dipakai dropdown "Role SIDONGAN";
     *      akun Super Admin resmi dari SuperAdminSeeder memakai cara ini).
     *
     * Sebelumnya kode server & blade campur aduk memakai salah satunya saja,
     * sehingga tombol bisa tampil di UI tapi ditolak server — atau sebaliknya.
     * Mulai sekarang SEMUA pengecekan "apakah Super Admin" WAJIB lewat method
     * ini (lihat UserManagementController, SiedaDataController, dan view
     * admin/user-management/*).
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin') || $this->sidongan_role === 'super_admin';
    }

    /**
     * Check apakah user adalah admin (bisa akses semua)
     */
    public function isAdmin()
    {
        // Izinkan jika email admin ATAU Super Admin
        return $this->email === 'admin@pkk-toba.id' || $this->isSuperAdmin();
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
     * Check apakah user adalah Ketua PKK (bisa disposisi & verifikasi).
     * Super Admin SELALU termasuk — menyebabkan sebagian fitur SIDONGAN
     * tidak bisa diakses Super Admin jika di-hardcode 'ketua' saja.
     */
    public function isSidonganKetua()
    {
        return $this->isSuperAdmin() || $this->hasSidonganRole('ketua');
    }

    /**
     * Check apakah user adalah Sekretaris PKK (bikin agenda surat).
     * Super Admin SELALU termasuk (lihat isSidonganKetua).
     */
    public function isSidonganSekretaris()
    {
        return $this->isSuperAdmin() || $this->hasSidonganRole('sekretaris');
    }

    /**
     * Check apakah user adalah Bendahara PKK.
     * Super Admin SELALU termasuk (lihat isSidonganKetua).
     */
    public function isSidonganBendahara()
    {
        return $this->isSuperAdmin() || $this->hasSidonganRole('bendahara');
    }

    /**
     * Check apakah user adalah Ketua POKJA / Pengurus (bisa terima disposisi & buat laporan).
     * Super Admin SELALU termasuk (lihat isSidonganKetua).
     */
    public function isSidonganPokja()
    {
        return $this->isSuperAdmin()
            || in_array($this->sidongan_role, ['pengurus_1', 'pengurus_2', 'pengurus_3', 'pengurus_4']);
    }

    /**
     * Check apakah user adalah Staf Ahli.
     * Super Admin SELALU termasuk (lihat isSidonganKetua).
     */
    public function isSidonganStafAhli()
    {
        return $this->isSuperAdmin() || in_array($this->sidongan_role, ['staf_ahli_1', 'staf_ahli_2']);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function hasRole($roleName): bool
    {
        return $this->role && $this->role->name === $roleName;
    }

    /**
     * Izin yang diberikan langsung ke akun ini, di luar izin bawaan role.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_user');
    }

    /**
     * Izin efektif = izin dari role (baseline) DITAMBAH izin pribadi akun.
     */
    public function hasPermission($permissionName): bool
    {
        // Administrator DAN Super Admin selalu punya semua permission (full access)
        if ($this->role && in_array($this->role->name, ['administrator', 'super_admin'])) {
            return true;
        }

        if ($this->role && $this->role->hasPermission($permissionName)) {
            return true;
        }

        return $this->permissions()->where('name', $permissionName)->exists();
    }

    /**
     * Daftar nama izin efektif, berguna untuk menampilkan ringkasan akses.
     */
    public function effectivePermissionNames(): array
    {
        if ($this->role && in_array($this->role->name, ['administrator', 'super_admin'])) {
            return Permission::pluck('name')->all();
        }

        $dariRole = $this->role ? $this->role->permissions->pluck('name') : collect();

        return $dariRole->merge($this->permissions->pluck('name'))->unique()->values()->all();
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
/* Dikembangkan oleh Institut Teknologi Del */
