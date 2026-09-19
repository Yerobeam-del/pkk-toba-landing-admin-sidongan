<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/**
 * Manajemen Akun: mengubah email pribadi user.
 *
 * Kontrak yang diuji:
 *  - email pribadi diganti → verifikasi lama DIRESET + link verifikasi baru
 *    dikirim ke alamat baru (fitur Lupa Password terkunci sampai diverifikasi),
 *  - email pribadi tidak berubah → status verifikasi tidak disentuh,
 *  - email pribadi dikosongkan di form → nilai lama dipertahankan (perilaku
 *    lama "jangan timpa dengan kosong") dan tidak ada verifikasi terkirim.
 */
class UserPersonalEmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        Notification::fake();

        $role = Role::where('name', 'administrator')->first();
        $this->superAdmin = User::factory()->create([
            'email' => 'superadmin@pkk-toba.id',
            'sidongan_role' => 'super_admin',
            'role_id' => $role->id,
        ]);
    }

    /** Payload edit akun minimum yang valid. */
    private function editPayload(User $user, array $overrides = []): array
    {
        return array_merge([
            'name' => $user->name,
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'personal_email' => $user->personal_email,
            'role_id' => $user->role_id,
            'sidongan_role' => $user->sidongan_role,
            'sieda_role' => $user->sieda_role,
        ], $overrides);
    }

    public function test_changing_personal_email_resets_verification_and_sends_link(): void
    {
        $target = User::factory()->create([
            'email' => 'budi@pkk-toba.id',
            'personal_email' => 'lama@example.com',
            'personal_email_verified_at' => now(),
            'role_id' => Role::where('name', 'anggota')->first()->id,
        ]);

        $this->actingAs($this->superAdmin)
            ->put(route('admin.user-management.update', $target), $this->editPayload($target, [
                'personal_email' => 'baru@example.com',
            ]))
            ->assertRedirect()
            ->assertSessionHas('success');

        $target->refresh();
        $this->assertSame('baru@example.com', $target->personal_email);
        // Verifikasi lama dibatalkan — email baru belum terbukti milik user
        $this->assertNull($target->personal_email_verified_at);

        // Satu email verifikasi ke alamat BARU
        Notification::assertSentTo($target, \App\Notifications\PersonalEmailVerificationNotification::class);
        $sent = Notification::sent($target, \App\Notifications\PersonalEmailVerificationNotification::class);
        $this->assertCount(1, $sent);
        $this->assertSame('baru@example.com', $sent->last()->email);
    }

    public function test_unchanged_personal_email_keeps_verification(): void
    {
        $verifiedAt = now()->subDays(3);
        $target = User::factory()->create([
            'email' => 'citra@pkk-toba.id',
            'personal_email' => 'tetap@example.com',
            'personal_email_verified_at' => $verifiedAt,
            'role_id' => Role::where('name', 'anggota')->first()->id,
        ]);

        $this->actingAs($this->superAdmin)
            ->put(route('admin.user-management.update', $target), $this->editPayload($target, [
                'name' => 'Nama Baru Saja',
            ]))
            ->assertRedirect();

        $target->refresh();
        $this->assertSame('tetap@example.com', $target->personal_email);
        // Status verifikasi tetap (detik mungkin terpotong oleh format DB, cukup samakan tanggal-jam)
        $this->assertSame(
            $verifiedAt->format('Y-m-d H:i'),
            $target->personal_email_verified_at->format('Y-m-d H:i')
        );

        Notification::assertNothingSentTo($target, \App\Notifications\PersonalEmailVerificationNotification::class);
    }

    public function test_empty_personal_email_input_keeps_old_value_silently(): void
    {
        $target = User::factory()->create([
            'email' => 'dewi@pkk-toba.id',
            'personal_email' => 'dipertahankan@example.com',
            'personal_email_verified_at' => now(),
            'role_id' => Role::where('name', 'anggota')->first()->id,
        ]);

        $this->actingAs($this->superAdmin)
            ->put(route('admin.user-management.update', $target), $this->editPayload($target, [
                'personal_email' => null,
            ]))
            ->assertRedirect();

        $target->refresh();
        // Perilaku lama: input kosong TIDAK menimpa nilai terisi
        $this->assertSame('dipertahankan@example.com', $target->personal_email);
        $this->assertNotNull($target->personal_email_verified_at);

        Notification::assertNothingSentTo($target, \App\Notifications\PersonalEmailVerificationNotification::class);
    }
}
/* Dikembangkan oleh Institut Teknologi Del */
