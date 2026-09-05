<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Coverage gerbang Super Admin terpadu (User::isSuperAdmin) di
 * UserManagementController.
 *
 * Dua aktor dibandingkan:
 *  - SUPER ADMIN   : sidongan_role='super_admin' (+ role administrator)
 *  - REGULAR ADMIN : role administrator TANPA sidongan_role super_admin
 *                    (sebelum unifikasi, akun ini lolos beberapa gate
 *                    hasRole('super_admin') tapi ditolak yang lain).
 *
 * Catatan: super admin selalu lolos middleware 'permission:manage-users'
 * lewat Gate::before, jadi perbedaan 200 vs 403 di bawah murni berasal
 * dari gerbang DI DALAM controller — yang memang sedang diuji.
 */
class UserManagementAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $regularAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed permissions + roles (manage-users, administrator, anggota, dll)
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);

        $this->superAdmin = $this->makeSuperAdmin();
        $this->regularAdmin = $this->makeRegularAdmin();
    }

    // ============================================================
    // Helpers
    // ============================================================

    private function makeUser(array $attrs = []): User
    {
        return User::factory()->create(array_merge([
            'email' => fake()->unique()->userName() . '@pkk-toba.id',
        ], $attrs));
    }

    private function makeSuperAdmin(array $attrs = []): User
    {
        $role = Role::where('name', 'administrator')->first();

        return $this->makeUser(array_merge([
            'sidongan_role' => 'super_admin',
            'role_id'       => $role->id,
        ], $attrs));
    }

    private function makeRegularAdmin(array $attrs = []): User
    {
        $role = Role::where('name', 'administrator')->first();

        return $this->makeUser(array_merge([
            'sidongan_role' => null,
            'role_id'       => $role->id,
        ], $attrs));
    }

    /** Target nonaktif (email belum verifikasi) untuk aksi toggle/activate. */
    private function makeInactiveTarget(): User
    {
        return $this->makeUser(['email_verified_at' => null]);
    }

    // ============================================================
    // Toggle status (aktifkan/nonaktifkan)
    // ============================================================

    public function test_super_admin_can_toggle_user_status(): void
    {
        $target = $this->makeInactiveTarget();

        $this->actingAs($this->superAdmin)
            ->postJson(route('admin.user-management.toggle-status', $target))
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertNotNull($target->fresh()->email_verified_at);
    }

    public function test_regular_admin_cannot_toggle_user_status(): void
    {
        $target = $this->makeInactiveTarget();

        $this->actingAs($this->regularAdmin)
            ->postJson(route('admin.user-management.toggle-status', $target))
            ->assertStatus(403)
            ->assertJson(['success' => false]);

        // Status target tidak berubah
        $this->assertNull($target->fresh()->email_verified_at);
    }

    public function test_super_admin_cannot_deactivate_own_account(): void
    {
        $this->actingAs($this->superAdmin)
            ->postJson(route('admin.user-management.toggle-status', $this->superAdmin))
            ->assertStatus(403)
            ->assertJson(['success' => false]);

        $this->assertNotNull($this->superAdmin->fresh()->email_verified_at);
    }

    public function test_super_admin_cannot_toggle_another_super_admin(): void
    {
        $otherSuper = $this->makeSuperAdmin(['email_verified_at' => null]);

        $this->actingAs($this->superAdmin)
            ->postJson(route('admin.user-management.toggle-status', $otherSuper))
            ->assertStatus(403);

        $this->assertNull($otherSuper->fresh()->email_verified_at);
    }

    // ============================================================
    // Delete akun
    // ============================================================

    public function test_super_admin_can_delete_user(): void
    {
        Http::fake(); // revokeAccess ke SIEDA → no-op aman

        $target = $this->makeUser();

        $this->actingAs($this->superAdmin)
            ->delete(route('admin.user-management.destroy', $target))
            ->assertRedirect(route('admin.user-management.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('users', ['id' => $target->id]);
    }

    public function test_regular_admin_cannot_delete_user(): void
    {
        Http::fake();

        $target = $this->makeUser();

        $this->actingAs($this->regularAdmin)
            ->delete(route('admin.user-management.destroy', $target))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['id' => $target->id]);
    }

    public function test_super_admin_cannot_delete_own_account(): void
    {
        Http::fake();

        $this->actingAs($this->superAdmin)
            ->delete(route('admin.user-management.destroy', $this->superAdmin))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['id' => $this->superAdmin->id]);
    }

    public function test_super_admin_cannot_delete_another_super_admin(): void
    {
        Http::fake();

        $otherSuper = $this->makeSuperAdmin();

        $this->actingAs($this->superAdmin)
            ->delete(route('admin.user-management.destroy', $otherSuper))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['id' => $otherSuper->id]);
    }

    // ============================================================
    // Reset password
    // ============================================================

    public function test_super_admin_can_reset_user_password(): void
    {
        $target = $this->makeUser(['password' => Hash::make('PasswordLama123!')]);

        $this->actingAs($this->superAdmin)
            ->postJson(route('admin.user-management.reset-password', $target), [
                'password'              => 'PasswordBaru123!',
                'password_confirmation' => 'PasswordBaru123!',
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertTrue(Hash::check('PasswordBaru123!', $target->fresh()->password));
    }

    public function test_regular_admin_cannot_reset_user_password(): void
    {
        $target = $this->makeUser(['password' => Hash::make('PasswordLama123!')]);

        $this->actingAs($this->regularAdmin)
            ->postJson(route('admin.user-management.reset-password', $target), [
                'password'              => 'PasswordBaru123!',
                'password_confirmation' => 'PasswordBaru123!',
            ])
            ->assertStatus(403)
            ->assertJson(['success' => false]);

        // Password lama tetap
        $this->assertTrue(Hash::check('PasswordLama123!', $target->fresh()->password));
    }

    /**
     * Gerbang "tidak bisa reset password Super Admin lain" secara efektif
     * tidak terjangkau: penjaga pertama (hanya super admin boleh reset)
     * sudah menolak semua aktor non-super, dan aktor super admin selalu
     * lolos penjaga kedua. Dites untuk mendokumentasikan kontrak itu.
     */
    public function test_regular_admin_cannot_reset_super_admin_password(): void
    {
        $otherSuper = $this->makeSuperAdmin(['password' => Hash::make('PasswordLama123!')]);

        $this->actingAs($this->regularAdmin)
            ->postJson(route('admin.user-management.reset-password', $otherSuper), [
                'password'              => 'PasswordBaru123!',
                'password_confirmation' => 'PasswordBaru123!',
            ])
            ->assertStatus(403);

        $this->assertTrue(Hash::check('PasswordLama123!', $otherSuper->fresh()->password));
    }

    // ============================================================
    // Bulk action
    // ============================================================

    public function test_super_admin_can_bulk_activate_users(): void
    {
        $targets = User::factory()->count(3)->create(['email_verified_at' => null]);

        $this->actingAs($this->superAdmin)
            ->postJson(route('admin.user-management.bulk-action'), [
                'action'   => 'activate',
                'user_ids' => $targets->pluck('id')->all(),
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        foreach ($targets as $t) {
            $this->assertNotNull($t->fresh()->email_verified_at);
        }
    }

    public function test_super_admin_can_bulk_delete_users(): void
    {
        Http::fake();

        $targets = $this->makeUser();
        $ids = [$targets->id];

        $this->actingAs($this->superAdmin)
            ->postJson(route('admin.user-management.bulk-action'), [
                'action'   => 'delete',
                'user_ids' => $ids,
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('users', ['id' => $targets->id]);
    }

    public function test_regular_admin_cannot_run_bulk_actions(): void
    {
        $targets = $this->makeInactiveTarget();

        $this->actingAs($this->regularAdmin)
            ->postJson(route('admin.user-management.bulk-action'), [
                'action'   => 'activate',
                'user_ids' => [$targets->id],
            ])
            ->assertStatus(403)
            ->assertJson(['success' => false]);

        $this->assertNull($targets->fresh()->email_verified_at);
    }

    public function test_bulk_action_silently_skips_super_admin_targets(): void
    {
        $otherSuper = $this->makeSuperAdmin(['email_verified_at' => null]);
        $regular    = $this->makeInactiveTarget();

        $this->actingAs($this->superAdmin)
            ->postJson(route('admin.user-management.bulk-action'), [
                'action'   => 'activate',
                'user_ids' => [$otherSuper->id, $regular->id],
            ])
            ->assertOk();

        // Super admin target di-skip, akun biasa ter-aktifkan
        $this->assertNull($otherSuper->fresh()->email_verified_at);
        $this->assertNotNull($regular->fresh()->email_verified_at);
    }

    public function test_bulk_action_silently_skips_own_account(): void
    {
        // Target AKTIF supaya 'deactivate' benar-benar memengaruhinya;
        // super admin ikut dikirim agar terbukti ter-skip (bukan ter-nonaktifkan).
        $target = $this->makeUser();

        $this->actingAs($this->superAdmin)
            ->postJson(route('admin.user-management.bulk-action'), [
                'action'   => 'deactivate',
                'user_ids' => [$this->superAdmin->id, $target->id],
            ])
            ->assertOk();

        $this->assertNotNull($this->superAdmin->fresh()->email_verified_at); // diri sendiri aman
        $this->assertNull($target->fresh()->email_verified_at);              // target ter-nonaktifkan
    }

    // ============================================================
    // Export CSV
    // ============================================================

    public function test_super_admin_can_export_users_csv(): void
    {
        $this->makeUser(); // ada minimal 1 baris data

        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.user-management.export'))
            ->assertOk()
            ->assertHeader('content-type'); // CSV download (Symfony bisa menambah suffix charset)

        $this->assertStringContainsString(
            'text/csv',
            strtolower((string) $response->baseResponse->headers->get('Content-Type'))
        );
    }

    public function test_regular_admin_cannot_export_users(): void
    {
        $this->actingAs($this->regularAdmin)
            ->get(route('admin.user-management.export'))
            ->assertRedirect()
            ->assertSessionHas('error');
    }

    // ============================================================
    // Index scoping
    // ============================================================

    public function test_regular_admin_does_not_see_super_admins_in_listing(): void
    {
        $visible = $this->makeUser();

        $this->actingAs($this->regularAdmin)
            ->get(route('admin.user-management.index'))
            ->assertOk()
            ->assertViewHas('users', function ($viewUsers) use ($visible) {
                $ids = $viewUsers->getCollection()->pluck('id')->all();

                return in_array($visible->id, $ids)
                    && !in_array($this->superAdmin->id, $ids);
            });
    }
}
