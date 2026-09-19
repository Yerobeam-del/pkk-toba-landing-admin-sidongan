<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
 * Dikembangkan oleh Institut Teknologi Del
 *
 * Tiga skenario redirect pasca-login Admin Panel:
 *
 *  1. Profil lengkap + workspace tersimpan → langsung ke ruang kerja itu
 *     (login berikutnya TIDAK lewat launcher).
 *  2. Profil lengkap + workspace kosong (belum pernah memilih) → launcher
 *     "Pilih Ruang Kerja".
 *  3. Deep-link (guest ditendang ke login dari halaman tertentu) → setelah
 *     login kembali ke halaman yang dituju semula via intended(), bukan ke
 *     launcher/ruang kerja.
 *
 *  Plus satu penjaga kompatibilitas: workspace='sidongan' dari perilaku
 *  lama tetap dilayani ke fallback /masuk-sidongan (kartu sudah dihapus
 *  dari launcher, tapi pilihan lama di DB tidak boleh buntu).
 */
class LoginRedirectScenarioTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    /**
     * User dengan profil lengkap — semua field pemblokir terisi dan email
     * pribadi terverifikasi, sehingga onboarding tidak menghalangi.
     */
    private function userProfilLengkap(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role_id' => Role::where('name', 'administrator')->first()->id,
            'phone_number' => '081234567890',
            'personal_email' => 'budi@example.com',
            'personal_email_verified_at' => now(),
        ], $attributes));
    }

    private function login(User $user)
    {
        // Login nyata (bukan actingAs) supaya redirect pasca-login di
        // AuthenticatedSessionController@store benar-benar ikut teruji.
        return $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);
    }

    /** SKENARIO 1: profil lengkap + workspace ada → langsung ke ruang kerja. */
    public function test_profil_lengkap_dan_workspace_ada_langsung_ke_ruang_kerja(): void
    {
        $user = $this->userProfilLengkap(['workspace' => 'pkk']);

        $this->login($user)
            ->assertRedirect(route('admin.dashboard'));

        // Pilihan area lain juga dihormati — bukan selalu dashboard PKK.
        $user->update(['workspace' => 'akun']);

        $this->post(route('logout'));
        $this->login($user)
            ->assertRedirect(route('admin.user-management.index'));
    }

    /** SKENARIO 2: profil lengkap + workspace kosong → launcher. */
    public function test_profil_lengkap_tanpa_workspace_ke_launcher(): void
    {
        $user = $this->userProfilLengkap(['workspace' => null]);

        $this->login($user)
            ->assertRedirect(route('workspace.pilih'));
    }

    /** SKENARIO 3: deep-link → setelah login kembali ke halaman asal. */
    public function test_deep_link_kembali_ke_halaman_asal_setelah_login(): void
    {
        $user = $this->userProfilLengkap(['workspace' => 'pkk']);

        // Halaman yang dituju saat belum login → disimpan di session
        // sebagai intended URL ( perilaku RedirectIfAuthenticated Laravel).
        $this->withSession(['url' => ['intended' => route('admin.user-management.index')]]);

        $this->login($user)
            ->assertRedirect(route('admin.user-management.index'));
    }

    /**
     * Penjaga kompatibilitas: pilihan lama workspace='sidongan' (sebelum
     * kartu SIDONGAN dihapus dari launcher) tetap dilayani, tidak buntu.
     */
    public function test_workspace_lama_sidongan_masih_dilayani_fallback(): void
    {
        $user = $this->userProfilLengkap([
            'workspace' => 'sidongan',
            'sidongan_role' => 'super_admin',
        ]);

        $this->login($user)
            ->assertRedirect(route('workspace.sidongan'));
    }
}
