<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/**
 * Alur pasca-login Admin Panel (paritas dengan SIEDA):
 *  - field pemblokir kosong (phone_number / personal_email) → /onboarding
 *  - email pending belum diverifikasi → /onboarding JUGA: panel verifikasi
 *    "Cek Email" kini menyatu di onboarding (tidak ada halaman notice
 *    terpisah yang menduplikasi alurnya)
 *  - "Lewati" di Admin Panel bersifat SEMENTARA: berlaku selama session,
 *    hilang saat logout, user ditanya lagi di login berikutnya
 *  - email pribadi yang diganti via onboarding membatalkan verifikasi lama
 */
class AdminOnboardingLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    private function makeUser(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'sidongan_role' => null,
            'role_id' => Role::where('name', 'administrator')->first()->id,
            'phone_number' => null,
            'personal_email' => null,
            'personal_email_verified_at' => null,
        ], $attributes));
    }

    private function loginAs(User $user)
    {
        // Login nyata (bukan actingAs) supaya melewati redirect pasca-login
        // di AuthenticatedSessionController@store — itulah yang diuji.
        return $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);
    }

    public function test_login_redirects_to_onboarding_when_blocking_fields_missing(): void
    {
        $user = $this->makeUser();

        $this->loginAs($user)
            ->assertRedirect(route('onboarding'));
    }

    public function test_complete_user_goes_straight_to_dashboard(): void
    {
        $user = $this->makeUser([
            'phone_number' => '081234567890',
            'personal_email' => 'budi@example.com',
            'personal_email_verified_at' => now(),
        ]);

        // User belum pernah memilih ruang kerja → launcher "Pilih Ruang Kerja".
        $this->loginAs($user)
            ->assertRedirect(route('workspace.pilih'));

        // User yang sudah punya pilihan default → langsung ke ruang kerja itu.
        $user->update(['workspace' => 'pkk']);

        $this->post(route('logout'));
        $this->loginAs($user)
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_pending_unverified_email_redirects_to_onboarding_check_email_panel(): void
    {
        $user = $this->makeUser([
            'phone_number' => '081234567890',
            'personal_email' => 'budi@example.com',
            'personal_email_verified_at' => null,
        ]);

        $this->withSession(['pending_personal_email' => 'budi@example.com']);

        // Panel "Cek Email" menyatu di onboarding — login dengan email pending
        // mendarat di sana, bukan di halaman notice terpisah.
        $this->loginAs($user)
            ->assertRedirect(route('onboarding'));
    }

    public function test_old_notice_route_forwards_to_onboarding(): void
    {
        $user = $this->makeUser([
            'phone_number' => '081234567890',
            'personal_email' => 'budi@example.com',
            'personal_email_verified_at' => null,
        ]);
        $this->actingAs($user);

        // Deep link / bookmark ke halaman notice lama tetap berfungsi:
        // dialihkan ke onboarding, bukan 404.
        $this->get(route('personal-email.notice'))
            ->assertRedirect(route('onboarding'));
    }

    public function test_admin_skip_is_temporary_session_only(): void
    {
        $user = $this->makeUser();
        $this->loginAs($user);

        // User tanpa pilihan ruang kerja → launcher (bukan langsung dashboard).
        $this->get(route('onboarding.skip'))
            ->assertRedirect(route('workspace.pilih'));

        // Skip tidak persisten di DB untuk Admin Panel
        $this->assertNull($user->fresh()->onboarding_skipped_at);
        $this->assertTrue(session('onboarding_skipped'));
    }

    public function test_admin_skip_does_not_persist_across_logout(): void
    {
        $user = $this->makeUser();
        $this->loginAs($user);

        $this->get(route('onboarding.skip'));
        $this->assertTrue(session('onboarding_skipped'));

        // Logout meng-invalidate session → flag hilang
        $this->post(route('logout'));
        $this->assertNull(session('onboarding_skipped'));

        // Login berikutnya: onboarding muncul lagi (skip sementara)
        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('onboarding'));
    }

    public function test_changing_personal_email_via_onboarding_resets_verification(): void
    {
        Notification::fake();

        // Data lama: email berupa placeholder '-' (dianggap kosong oleh
        // ProfileFields) tapi pernah tercatat "terverifikasi".
        $user = $this->makeUser([
            'phone_number' => '081234567890',
            'personal_email' => '-',
            'personal_email_verified_at' => now(),
        ]);
        $this->actingAs($user);

        $this->post(route('onboarding.store'), [
            'personal_email' => 'baru@example.com',
        ]);

        $user->refresh();
        $this->assertSame('baru@example.com', $user->personal_email);
        // Verifikasi lama dibatalkan — link baru dikirim ke email yang baru.
        $this->assertNull($user->personal_email_verified_at);
    }

    public function test_sidongan_skip_remains_persistent_in_db(): void
    {
        // Perilaku lama SIDONGAN tidak berubah: skip tersimpan di DB.
        $user = $this->makeUser();

        // detectSystem() mengecek guard sidongan lebih dulu → sistem 'sidongan'
        $this->actingAs($user, 'sidongan');
        $this->get(route('onboarding.skip'));

        $this->assertNotNull($user->fresh()->onboarding_skipped_at);
    }
}
/* Dikembangkan oleh Institut Teknologi Del */
