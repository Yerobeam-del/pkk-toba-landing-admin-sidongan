<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/**
 * Tombol "Kirim Ulang Verifikasi" email pribadi (Admin Panel > Manajemen
 * Akun > Edit):
 *  - email belum terverifikasi → link terkirim, response JSON sukses,
 *  - email sudah terverifikasi → tidak ada email terkirim (already_verified),
 *  - user tanpa email pribadi → ditolak 422,
 *  - cooldown 60 detik antar pengiriman (429),
 *  - tanpa permission manage-users → ditolak 403.
 */
class ResendPersonalEmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        Notification::fake();
    }

    private function superAdmin(): User
    {
        return User::factory()->create([
            'email' => 'superadmin@pkk-toba.id',
            'sidongan_role' => 'super_admin',
            'role_id' => Role::where('name', 'administrator')->first()->id,
        ]);
    }

    private function target(array $attrs = []): User
    {
        return User::factory()->create(array_merge([
            'email' => 'target@pkk-toba.id',
            'personal_email' => 'pribadi@example.com',
            'personal_email_verified_at' => null,
            'role_id' => Role::where('name', 'anggota')->first()->id,
        ], $attrs));
    }

    public function test_resend_sends_verification_link(): void
    {
        $user = $this->target();

        $this->actingAs($this->superAdmin())
            ->postJson(route('admin.user-management.resend-personal-email-verification', $user))
            ->assertOk()
            ->assertJson(['success' => true]);

        Notification::assertSentTo($user, \App\Notifications\PersonalEmailVerificationNotification::class);
        $sent = Notification::sent($user, \App\Notifications\PersonalEmailVerificationNotification::class);
        $this->assertSame('pribadi@example.com', $sent->last()->email);
    }

    public function test_resend_is_rate_limited_within_cooldown(): void
    {
        $user = $this->target();
        $admin = $this->superAdmin();

        $this->actingAs($admin)
            ->postJson(route('admin.user-management.resend-personal-email-verification', $user))
            ->assertOk();

        // Kirim kedua langsung → kena cooldown
        $this->actingAs($admin)
            ->postJson(route('admin.user-management.resend-personal-email-verification', $user))
            ->assertStatus(429);

        // Hanya SATU email yang benar-benar terkirim
        Notification::assertSentTimes(\App\Notifications\PersonalEmailVerificationNotification::class, 1);
    }

    public function test_already_verified_user_gets_no_email(): void
    {
        $user = $this->target(['personal_email_verified_at' => now()]);

        $this->actingAs($this->superAdmin())
            ->postJson(route('admin.user-management.resend-personal-email-verification', $user))
            ->assertOk()
            ->assertJson(['success' => true, 'already_verified' => true]);

        Notification::assertNothingSent();
    }

    public function test_user_without_personal_email_is_rejected(): void
    {
        $user = $this->target(['personal_email' => null]);

        $this->actingAs($this->superAdmin())
            ->postJson(route('admin.user-management.resend-personal-email-verification', $user))
            ->assertStatus(422);

        Notification::assertNothingSent();
    }

    public function test_user_without_manage_users_permission_is_forbidden(): void
    {
        $user = $this->target();
        $plainUser = User::factory()->create([
            'email' => 'polos@pkk-toba.id',
            'role_id' => Role::where('name', 'anggota')->first()->id,
        ]);

        $this->actingAs($plainUser)
            ->postJson(route('admin.user-management.resend-personal-email-verification', $user))
            ->assertStatus(403);

        Notification::assertNothingSent();
    }

    public function test_cooldown_cache_expires_after_60_seconds(): void
    {
        $user = $this->target();
        $admin = $this->superAdmin();

        $this->actingAs($admin)
            ->postJson(route('admin.user-management.resend-personal-email-verification', $user))
            ->assertOk();

        // Simulasi cooldown lewat: hapus cache (keperluan admin darurat saat test)
        Cache::forget('resend-personal-email-verification:' . $user->id);

        $this->actingAs($admin)
            ->postJson(route('admin.user-management.resend-personal-email-verification', $user))
            ->assertOk();

        Notification::assertSentTimes(\App\Notifications\PersonalEmailVerificationNotification::class, 2);
    }
}
/* Dikembangkan oleh Institut Teknologi Del */
