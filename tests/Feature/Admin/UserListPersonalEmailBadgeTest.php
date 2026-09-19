<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Daftar Manajemen Akun: badge status verifikasi email pribadi.
 *
 * Kontrak yang diuji:
 *  - kolom "Email Pribadi" menampilkan alamat + status verifikasi,
 *  - tiga keadaan: belum diatur (-), terverifikasi (ikon SVG centang),
 *    menunggu (ikon SVG jam)
 *  - halaman tetap render normal (sorting & pagination tidak rusak).
 */
class UserListPersonalEmailBadgeTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RolePermissionSeeder::class);

        $role = Role::where('name', 'administrator')->first();
        $this->superAdmin = User::factory()->create([
            'email' => 'superadmin@pkk-toba.id',
            'sidongan_role' => 'super_admin',
            'role_id' => $role->id,
        ]);
    }

    /** User dengan email pribadi terverifikasi tampil dengan ikon centang SVG. */
    public function test_verified_personal_email_shows_checkmark_badge(): void
    {
        $user = User::factory()->create([
            'personal_email' => 'budi@example.com',
            'personal_email_verified_at' => now(),
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.user-management.index'));

        $response->assertOk()
            ->assertSee('Email Pribadi', false)
            ->assertSee('budi@example.com', false)
            ->assertSee('um-pemail-badge--ok', false);
    }

    /** Email pribadi belum diverifikasi tampil dengan ikon jam SVG. */
    public function test_unverified_personal_email_shows_pending_badge(): void
    {
        $user = User::factory()->create([
            'personal_email' => 'siti@example.com',
            'personal_email_verified_at' => null,
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.user-management.index'));

        $response->assertOk()
            ->assertSee('siti@example.com', false)
            ->assertSee('belum terverifikasi', false);
    }

    /** User tanpa email pribadi menampilkan tanda hubung netral. */
    public function test_user_without_personal_email_shows_dash(): void
    {
        $user = User::factory()->create([
            'personal_email' => null,
            'personal_email_verified_at' => null,
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.user-management.index'));

        $response->assertOk()
            ->assertSee('um-pemail-badge--none', false);
    }

    /** Kolom baru tidak merusak sorting/pagination — semua baris ter-render. */
    public function test_listing_renders_all_rows_with_new_column(): void
    {
        User::factory()->count(15)->create([
            'personal_email_verified_at' => now(),
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.user-management.index', ['per_page' => 10]));

        $response->assertOk();

        // Halaman 1 memuat 10 baris + super admin sendiri (total 16 user).
        $this->assertEquals(10, $response->viewData('users')->count());
    }

    /**
     * Tab "Email Pribadi Belum Verifikasi" menampilkan HANYA akun yang
     * punya email pribadi tapi belum terverifikasi — populasi yang bisa
     * ditindaklanjuti (kirim ulang verifikasi dari halaman edit).
     */
    public function test_unverified_personal_email_tab_filters_correctly(): void
    {
        $target = User::factory()->create([
            'name' => 'Perlu Tindak Lanjut',
            'personal_email' => 'perlu-tindak@example.com',
            'personal_email_verified_at' => null,
        ]);
        // Sudah verifikasi → tidak boleh muncul.
        User::factory()->create([
            'name' => 'Sudah Verifikasi',
            'personal_email' => 'sudah-verif@example.com',
            'personal_email_verified_at' => now(),
        ]);
        // Tanpa email pribadi → tidak boleh muncul (tak bisa ditindaklanjuti).
        User::factory()->create([
            'name' => 'Tanpa Email Pribadi',
            'personal_email' => null,
            'personal_email_verified_at' => null,
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.user-management.index', ['tab' => 'pemail-unverified']));

        $response->assertOk()
            ->assertSee('Perlu Tindak Lanjut', false)
            ->assertDontSee('Sudah Verifikasi', false)
            ->assertDontSee('Tanpa Email Pribadi', false);

        $this->assertEquals(1, $response->viewData('users')->total());
    }

    /** Tab baru muncul di deretan tab dengan badge jumlah yang benar. */
    public function test_unverified_personal_email_tab_visible_with_count(): void
    {
        // Kolom personal_email UNIQUE — satu email per user.
        foreach ([1, 2, 3] as $i) {
            User::factory()->create([
                'personal_email' => "menunggu{$i}@example.com",
                'personal_email_verified_at' => null,
            ]);
        }

        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.user-management.index'));

        $response->assertOk()
            ->assertSee('Email Pribadi Belum Verifikasi', false)
            ->assertSee('pemail-unverified', false);
    }

    /** Tombol amplop kirim-ulang hanya ada di baris kandidat sah. */
    public function test_resend_button_only_for_unverified_personal_email_rows(): void
    {
        $kandidat = User::factory()->create([
            'name' => 'Kandidat Resend',
            'personal_email' => 'kandidat@example.com',
            'personal_email_verified_at' => null,
        ]);
        User::factory()->create([
            'name' => 'Sudah Amankan',
            'personal_email' => 'aman@example.com',
            'personal_email_verified_at' => now(),
        ]);
        User::factory()->create([
            'name' => 'Belum Punya Email',
            'personal_email' => null,
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.user-management.index'));

        $response->assertOk()
            ->assertSee('data-resend-pemail-id="' . $kandidat->id . '"', false)
            // Form HTML dihasilkan PHP string biasa — pastikan tidak ada
            // tombol resend lain di halaman (hanya 1 kemunculan atribut).
            ->assertSee('data-resend-pemail-name', false);

        $this->assertEquals(
            1,
            substr_count($response->getContent(), 'data-resend-pemail-id="'),
            'Tombol resend hanya boleh muncul di baris kandidat sah.'
        );
    }
}
