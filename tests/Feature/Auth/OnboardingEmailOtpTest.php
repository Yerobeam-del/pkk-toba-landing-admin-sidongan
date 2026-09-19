<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

/**
 * Verifikasi email pribadi LANGSUNG di halaman onboarding memakai kode OTP
 * 6 digit — SATU PINTU untuk SEMUA sistem (Admin Panel & SIDONGAN, paritas
 * penuh):
 *  - menyimpan profil dengan email → OTP terkirim & panel aktif,
 *  - kode benar → email terverifikasi, OTP dibersihkan,
 *  - kode salah → hitungan percobaan bertambah, email belum terverifikasi,
 *  - kode kedaluwarsa ditolak,
 *  - batas percobaan terlampaui → harus minta kode baru,
 *  - resend punya cooldown,
 *  - login SIDONGAN dengan email pending mendarat di onboarding (bukan
 *    dashboard / halaman profil) — panel OTP yang menangani verifikasi.
 */
class OnboardingEmailOtpTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        Notification::fake();
    }

    private function makeUser(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'sidongan_role' => 'super_admin',
            'role_id' => Role::where('name', 'administrator')->first()->id,
            'phone_number' => '081234567890',
            'personal_email' => null,
        ], $attributes));
    }

    private function submitProfile(User $user, string $email)
    {
        return $this->actingAs($user)
            ->post(route('onboarding.store'), [
                'personal_email' => $email,
            ]);
    }

    public function test_store_sends_otp_and_activates_panel(): void
    {
        $user = $this->makeUser();

        $this->submitProfile($user, 'baru@example.com')
            ->assertRedirect(route('onboarding'));

        // SATU email gabungan berisi kode OTP + link fallback — bukan dua email
        Notification::assertSentTo($user, \App\Notifications\PersonalEmailOtpNotification::class);
        Notification::assertNotSentTo($user, \App\Notifications\PersonalEmailVerificationNotification::class);

        // Email gabungan membawa kode dan link sekaligus
        $sent = Notification::sent($user, \App\Notifications\PersonalEmailOtpNotification::class);
        $this->assertCount(1, $sent);
        $this->assertNotNull($sent->last()->code);
        $this->assertStringContainsString('personal-email/verify', $sent->last()->verifyUrl);

        // OTP tersimpan ter-hash (bukan teks polos) + email tujuan tercatat
        $user->refresh();
        $this->assertNotNull($user->personal_email_otp_hash);
        $this->assertSame('baru@example.com', $user->personal_email_otp_email);
        $this->assertNotNull($user->personal_email_otp_expires_at);
    }

    public function test_correct_code_verifies_email_and_clears_otp(): void
    {
        $user = $this->makeUser();
        $this->submitProfile($user, 'baru@example.com');

        // Ambil kode asli dari notifikasi (fake)
        $notification = collect(Notification::sent($user, \App\Notifications\PersonalEmailOtpNotification::class))->last();
        $code = $notification->code;

        $this->post(route('onboarding.verify-email'), ['otp_code' => $code])
            ->assertRedirect(route('onboarding'))
            ->assertSessionHas('success');

        $user->refresh();
        $this->assertSame('baru@example.com', $user->personal_email);
        $this->assertNotNull($user->personal_email_verified_at);
        $this->assertNull($user->personal_email_otp_hash);
        $this->assertNull($user->personal_email_otp_email);
    }

    public function test_wrong_code_increments_attempts(): void
    {
        $user = $this->makeUser();
        $this->submitProfile($user, 'baru@example.com');

        $this->post(route('onboarding.verify-email'), ['otp_code' => '000000'])
            ->assertSessionHasErrors('otp_code');

        $user->refresh();
        $this->assertSame(1, $user->personal_email_otp_attempts);
        $this->assertNull($user->personal_email_verified_at);
    }

    public function test_expired_code_is_rejected(): void
    {
        $user = $this->makeUser();
        $this->submitProfile($user, 'baru@example.com');

        $user->forceFill(['personal_email_otp_expires_at' => now()->subMinute()])->save();

        $this->post(route('onboarding.verify-email'), ['otp_code' => '123456'])
            ->assertSessionHasErrors('otp_code');

        $this->assertNull($user->fresh()->personal_email_verified_at);
    }

    public function test_too_many_attempts_blocks_verification(): void
    {
        $user = $this->makeUser();
        $this->submitProfile($user, 'baru@example.com');

        $user->forceFill(['personal_email_otp_attempts' => 5])->save();

        $this->post(route('onboarding.verify-email'), ['otp_code' => '123456'])
            ->assertSessionHasErrors('otp_code');

        $this->assertNull($user->fresh()->personal_email_verified_at);
    }

    public function test_resend_respects_cooldown(): void
    {
        $user = $this->makeUser();
        $this->submitProfile($user, 'baru@example.com');

        // Kirim ulang langsung → masih cooldown
        $this->post(route('onboarding.resend-email-otp'))
            ->assertSessionHasErrors('otp_code');

        // Setelah cooldown lewat → kode baru terkirim
        $user->forceFill(['personal_email_otp_requested_at' => now()->subMinutes(2)])->save();

        $this->post(route('onboarding.resend-email-otp'))
            ->assertRedirect(route('onboarding'))
            ->assertSessionHas('status');

        Notification::assertSentTimes(
            \App\Notifications\PersonalEmailOtpNotification::class,
            2
        );
    }

    // =============================================================
    // Polling status verifikasi: panel "Cek Email" onboarding mengecek
    // berkala, sehingga panel otomatis hilang saat email diverifikasi
    // lewat link di email (perangkat/tab lain).
    // =============================================================

    public function test_email_status_reports_pending(): void
    {
        $user = $this->makeUser();
        $this->submitProfile($user, 'baru@example.com');

        $this->actingAs($user)
            ->get(route('onboarding.email-status'))
            ->assertOk()
            ->assertJson([
                'authenticated' => true,
                'verified' => false,
                'redirect' => null,
            ]);
    }

    public function test_email_status_reports_verified_and_clears_session(): void
    {
        $user = $this->makeUser();
        $this->submitProfile($user, 'baru@example.com');

        // Verifikasi terjadi lewat jalur lain (mis. link di email)
        $user->forceFill(['personal_email_verified_at' => now()])->save();

        $this->actingAs($user)
            ->get(route('onboarding.email-status'))
            ->assertOk()
            ->assertJson([
                'authenticated' => true,
                'verified' => true,
            ])
            ->assertJsonStructure(['redirect']);

        // Status pending bersih dari session + flash sukses siap tampil
        $this->actingAs($user)
            ->get(route('onboarding'))
            ->assertOk()
            ->assertSee('berhasil diverifikasi', false);
    }

    public function test_email_status_requires_authentication(): void
    {
        $this->get(route('onboarding.email-status'))
            ->assertStatus(401)
            ->assertJson(['verified' => false, 'authenticated' => false]);
    }

    // =============================================================
    // Paritas SIDONGAN: alur OTP satu pintu yang sama, lewat guard
    // 'sidongan' — bukan lagi verifikasi link-only dari halaman profil.
    // =============================================================

    /** User khas SIDONGAN: guard sidongan, tanpa role admin panel. */
    private function makeSidonganUser(array $attributes = []): User
    {
        return $this->makeUser(array_merge([
            'role_id' => null,
        ], $attributes));
    }

    public function test_sidongan_store_sends_otp_and_activates_panel(): void
    {
        $user = $this->makeSidonganUser();

        $this->actingAs($user, 'sidongan')
            ->post(route('onboarding.store'), ['personal_email' => 'sekretaris@example.com'])
            ->assertRedirect(route('onboarding'))
            ->assertSessionHas('status');

        // SATU email gabungan (OTP + link fallback) — bukan dua email berbeda
        Notification::assertSentTo($user, \App\Notifications\PersonalEmailOtpNotification::class);
        Notification::assertNotSentTo($user, \App\Notifications\PersonalEmailVerificationNotification::class);

        $user->refresh();
        $this->assertNotNull($user->personal_email_otp_hash);
        $this->assertSame('sekretaris@example.com', $user->personal_email_otp_email);

        // Halaman onboarding kini menampilkan panel OTP aktif (form kode)
        $this->actingAs($user, 'sidongan')
            ->get(route('onboarding'))
            ->assertOk()
            ->assertSee('Kode 6 digit telah dikirim', false);
    }

    public function test_sidongan_correct_code_verifies_email(): void
    {
        $user = $this->makeSidonganUser();
        $this->actingAs($user, 'sidongan')
            ->post(route('onboarding.store'), ['personal_email' => 'sekretaris@example.com']);

        $code = collect(Notification::sent($user, \App\Notifications\PersonalEmailOtpNotification::class))->last()->code;

        $this->actingAs($user, 'sidongan')
            ->post(route('onboarding.verify-email'), ['otp_code' => $code])
            ->assertRedirect(route('onboarding'))
            ->assertSessionHas('success');

        $user->refresh();
        $this->assertSame('sekretaris@example.com', $user->personal_email);
        $this->assertNotNull($user->personal_email_verified_at);
        $this->assertNull($user->personal_email_otp_hash);
    }

    public function test_sidongan_wrong_code_increments_attempts(): void
    {
        $user = $this->makeSidonganUser();
        $this->actingAs($user, 'sidongan')
            ->post(route('onboarding.store'), ['personal_email' => 'sekretaris@example.com']);

        $this->actingAs($user, 'sidongan')
            ->post(route('onboarding.verify-email'), ['otp_code' => '000000'])
            ->assertSessionHasErrors('otp_code');

        $this->assertSame(1, $user->fresh()->personal_email_otp_attempts);
    }

    public function test_sidongan_resend_respects_cooldown(): void
    {
        $user = $this->makeSidonganUser();
        $this->actingAs($user, 'sidongan')
            ->post(route('onboarding.store'), ['personal_email' => 'sekretaris@example.com']);

        // Kirim ulang langsung → masih cooldown
        $this->actingAs($user, 'sidongan')
            ->post(route('onboarding.resend-email-otp'))
            ->assertSessionHasErrors('otp_code');

        // Setelah cooldown lewat → kode baru terkirim (total 2 email OTP)
        $user->forceFill(['personal_email_otp_requested_at' => now()->subMinutes(2)])->save();

        $this->actingAs($user, 'sidongan')
            ->post(route('onboarding.resend-email-otp'))
            ->assertRedirect(route('onboarding'))
            ->assertSessionHas('status');

        Notification::assertSentTimes(\App\Notifications\PersonalEmailOtpNotification::class, 2);
    }

    public function test_sidongan_login_with_pending_email_lands_on_onboarding(): void
    {
        // Login SIDONGAN dengan email terisi tapi belum terverifikasi →
        // onboarding (panel "Cek Email"), BUKAN dashboard.
        $user = $this->makeSidonganUser([
            'phone_number' => '081234567890',
            'personal_email' => 'pending@example.com',
            'personal_email_verified_at' => null,
        ]);

        $this->post(route('sidongan.login.post'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('onboarding'));

        // Panel tampil dengan tombol kirim kode (OTP belum pernah dikirim)
        $this->actingAs($user, 'sidongan')
            ->get(route('onboarding'))
            ->assertOk()
            ->assertSee('Kirim kode verifikasi', false);
    }

    public function test_sidongan_verified_email_goes_straight_to_dashboard(): void
    {
        // Email sudah terverifikasi → tidak ada redirect onboarding.
        $user = $this->makeSidonganUser([
            'phone_number' => '081234567890',
            'personal_email' => 'verified@example.com',
            'personal_email_verified_at' => now(),
        ]);

        $this->post(route('sidongan.login.post'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('sidongan.dashboard'));
    }

    public function test_verified_email_no_longer_requires_onboarding(): void
    {
        $user = $this->makeUser();
        $this->submitProfile($user, 'baru@example.com');

        $notification = collect(Notification::sent($user, \App\Notifications\PersonalEmailOtpNotification::class))->last();
        $this->post(route('onboarding.verify-email'), ['otp_code' => $notification->code]);

        // Login ulang: profil lengkap + email terverifikasi → langsung ke
        // ruang kerja. User belum pernah memilih → launcher "Pilih Ruang Kerja".
        $this->post(route('logout'));
        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('workspace.pilih'));
    }
}
/* Dikembangkan oleh Institut Teknologi Del */
