<?php

namespace Tests\Feature\Admin;

use App\Models\Permission;
use App\Models\Role;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Admin Panel > Pengaturan Situs (admin.settings.*).
 *
 * Kontrak yang diuji:
 *  - Super Admin & pemegang permission manage-settings bisa membuka
 *    halaman dan menyimpan perubahan,
 *  - pengguna tanpa permission ditolak,
 *  - nilai tidak valid ditolak validasi,
 *  - perubahan langsung terlihat di footer beranda (composer + flush cache),
 *  - nilai default tetap ter-render walau baris setting belum di-seed.
 */
class SiteSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $this->seed(\Database\Seeders\SiteSettingsSeeder::class);

        // Disk public difake agar upload logo tidak menulis folder
        // storage asli dan bisa diverifikasi isinya.
        Storage::fake('public');
    }

    private function superAdmin(): User
    {
        return User::factory()->create([
            'sidongan_role' => 'super_admin',
            'role_id' => Role::where('name', 'administrator')->first()->id,
        ]);
    }

    private function userWithPermission(): User
    {
        $user = User::factory()->create([
            'sidongan_role' => null,
            'role_id' => Role::where('name', 'anggota')->first()->id,
        ]);
        $user->role->permissions()->attach(
            Permission::where('name', 'manage-settings')->first()
        );

        return $user;
    }

    public function test_super_admin_can_view_settings_page(): void
    {
        $this->actingAs($this->superAdmin())
            ->get(route('admin.settings.index'))
            ->assertOk()
            ->assertSee('Pengaturan Situs')
            ->assertSee(SiteSetting::get('footer_address'), false);
    }

    public function test_user_with_permission_can_update_settings(): void
    {
        $response = $this->actingAs($this->userWithPermission())
            ->post(route('admin.settings.update'), [
                'footer_title' => 'PKK Kabupaten Toba Baru',
                'footer_address' => "Jl. Baru No. 2,\nKabupaten Toba",
                'instagram_url' => 'https://www.instagram.com/akunbaru_/',
                'instagram_handle' => '@akunbaru_',
                'footer_copyright' => 'TP-PKK Toba',
            ]);

        $response->assertRedirect(route('admin.settings.index'));

        $this->assertSame('PKK Kabupaten Toba Baru', SiteSetting::get('footer_title'));
        $this->assertSame("@akunbaru_", SiteSetting::get('instagram_handle'));
        $this->assertStringContainsString('Jl. Baru No. 2', SiteSetting::get('footer_address'));
    }

    public function test_user_without_permission_is_forbidden(): void
    {
        $user = User::factory()->create([
            'sidongan_role' => null,
            'role_id' => Role::where('name', 'anggota')->first()->id,
        ]);

        $this->actingAs($user)
            ->get(route('admin.settings.index'))
            ->assertForbidden();

        $this->actingAs($user)
            ->post(route('admin.settings.update'), [
                'footer_title' => 'X',
                'footer_address' => 'Y',
                'instagram_url' => 'https://example.com',
                'instagram_handle' => '@x',
                'footer_copyright' => 'Z',
            ])
            ->assertForbidden();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('admin.settings.index'))->assertRedirect(route('login'));
    }

    public function test_validation_rejects_invalid_input(): void
    {
        $this->actingAs($this->superAdmin())
            ->from(route('admin.settings.index'))
            ->post(route('admin.settings.update'), [
                'footer_title' => '',
                'footer_address' => '',
                'instagram_url' => 'bukan-url',
                'instagram_handle' => '',
                'footer_copyright' => '',
            ])
            ->assertSessionHasErrors(['footer_title', 'footer_address', 'instagram_url', 'instagram_handle', 'footer_copyright']);
    }

    public function test_updated_setting_appears_on_landing_footer(): void
    {
        SiteSetting::set([
            'footer_title' => 'UJI FOOTER BERANDA',
            'footer_address' => "Jl. Uji No. 9,\nKabupaten Uji",
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('UJI FOOTER BERANDA', false)
            ->assertSee('Jl. Uji No. 9,', false);
    }

    public function test_footer_renders_defaults_when_settings_table_is_empty(): void
    {
        SiteSetting::query()->delete();

        $this->get('/')
            ->assertOk()
            ->assertSee('PKK Kabupaten Toba', false)
            ->assertSee('Sumatera Utara 22311', false);
    }

    public function test_logo_upload_stores_file_and_renders_in_footer(): void
    {
        $this->actingAs($this->superAdmin())
            ->post(route('admin.settings.update'), [
                'footer_title' => 'PKK Kabupaten Toba',
                'footer_address' => 'Jl. Uji',
                'instagram_url' => 'https://www.instagram.com/tppkktoba_/',
                'instagram_handle' => '@tppkktoba_',
                'footer_copyright' => 'TP-PKK Kabupaten Toba',
                'footer_logo_toba' => UploadedFile::fake()->image('logo-toba.png', 100, 100),
            ]);

        $path = SiteSetting::get('footer_logo_toba');
        $this->assertStringStartsWith('site/', $path);
        Storage::disk('public')->assertExists($path);

        $this->get('/')
            ->assertOk()
            ->assertSee('storage/' . $path, false);
    }

    public function test_logo_reupload_deletes_old_file(): void
    {
        // Upload pertama
        $this->actingAs($this->superAdmin())
            ->post(route('admin.settings.update'), [
                'footer_title' => 'PKK Kabupaten Toba',
                'footer_address' => 'Jl. Uji',
                'instagram_url' => 'https://www.instagram.com/tppkktoba_/',
                'instagram_handle' => '@tppkktoba_',
                'footer_copyright' => 'TP-PKK Kabupaten Toba',
                'footer_logo_pkk' => UploadedFile::fake()->image('lama.png'),
            ]);
        $old = SiteSetting::get('footer_logo_pkk');

        // Upload pengganti
        $this->actingAs($this->superAdmin())
            ->post(route('admin.settings.update'), [
                'footer_title' => 'PKK Kabupaten Toba',
                'footer_address' => 'Jl. Uji',
                'instagram_url' => 'https://www.instagram.com/tppkktoba_/',
                'instagram_handle' => '@tppkktoba_',
                'footer_copyright' => 'TP-PKK Kabupaten Toba',
                'footer_logo_pkk' => UploadedFile::fake()->image('baru.png'),
            ]);

        Storage::disk('public')->assertMissing($old);
        Storage::disk('public')->assertExists(SiteSetting::get('footer_logo_pkk'));
    }

    public function test_remove_logo_checkbox_resets_to_fallback(): void
    {
        $this->actingAs($this->superAdmin())
            ->post(route('admin.settings.update'), [
                'footer_title' => 'PKK Kabupaten Toba',
                'footer_address' => 'Jl. Uji',
                'instagram_url' => 'https://www.instagram.com/tppkktoba_/',
                'instagram_handle' => '@tppkktoba_',
                'footer_copyright' => 'TP-PKK Kabupaten Toba',
                'footer_logo_toba' => UploadedFile::fake()->image('custom.png'),
            ]);
        $custom = SiteSetting::get('footer_logo_toba');

        $this->actingAs($this->superAdmin())
            ->post(route('admin.settings.update'), [
                'footer_title' => 'PKK Kabupaten Toba',
                'footer_address' => 'Jl. Uji',
                'instagram_url' => 'https://www.instagram.com/tppkktoba_/',
                'instagram_handle' => '@tppkktoba_',
                'footer_copyright' => 'TP-PKK Kabupaten Toba',
                'remove_logo_toba' => '1',
            ]);

        Storage::disk('public')->assertMissing($custom);
        $this->assertSame('', SiteSetting::get('footer_logo_toba'));

        // Footer kembali memakai file bawaan
        $this->get('/')
            ->assertOk()
            ->assertSee('assets/landing/images/Logo-Kabupaten-Toba-Transparent.png', false);
    }

    public function test_logo_validation_rejects_non_image(): void
    {
        $this->actingAs($this->superAdmin())
            ->from(route('admin.settings.index'))
            ->post(route('admin.settings.update'), [
                'footer_title' => 'PKK Kabupaten Toba',
                'footer_address' => 'Jl. Uji',
                'instagram_url' => 'https://www.instagram.com/tppkktoba_/',
                'instagram_handle' => '@tppkktoba_',
                'footer_copyright' => 'TP-PKK Kabupaten Toba',
                'footer_logo_toba' => UploadedFile::fake()->create('virus.pdf', 100),
            ])
            ->assertSessionHasErrors(['footer_logo_toba']);
    }
}
/* Dikembangkan oleh Institut Teknologi Del */
