<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Endpoint pengaturan playback slider (admin.hero-sliders.settings).
 *
 * Pengaturan bersifat global untuk beranda publik dan disimpan sebagai
 * JSON di storage lokal (hero_slider_settings.json). Kontrak yang diuji:
 *  - hanya Super Admin yang boleh mengubah (gerbang di dalam controller),
 *  - semua field tersimpan, termasuk toggle yang dimatikan lewat hidden
 *    input "0" (checkbox HTML yang tidak dicentang tidak ikut ter-submit),
 *  - nilai tidak valid ditolak validasi.
 */
class HeroSliderSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $regularAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RolePermissionSeeder::class);

        $this->superAdmin = User::factory()->create([
            'email' => 'slider-super@pkk-toba.id',
            'sidongan_role' => 'super_admin',
            'role_id' => Role::where('name', 'administrator')->first()->id,
        ]);

        $this->regularAdmin = User::factory()->create([
            'email' => 'slider-regular@pkk-toba.id',
            'sidongan_role' => null,
            'role_id' => Role::where('name', 'administrator')->first()->id,
        ]);

        // Fake disk disk pendamatan settings — sekali di setUp agar file
        // yang ditulis endpoint benar-benar bisa dibaca kembali di tes.
        Storage::fake('local');
    }

    private function settings(): array
    {
        return json_decode(
            Storage::disk('local')->get('hero_slider_settings.json'),
            true
        ) ?? [];
    }

    public function test_super_admin_can_save_settings(): void
    {
        Storage::fake('local');

        $this->actingAs($this->superAdmin)
            ->post(route('admin.hero-sliders.settings'), [
                'auto_play' => '1',
                'show_arrows' => '1',
                'show_dots' => '0',
                'transition_duration' => '800',
            ])
            ->assertRedirect(route('admin.hero-sliders.index'))
            ->assertSessionHas('success');

        $settings = $this->settings();
        $this->assertTrue($settings['auto_play']);
        $this->assertTrue($settings['show_arrows']);
        $this->assertFalse($settings['show_dots']);
        $this->assertSame(800, $settings['transition_duration']);
    }

    public function test_unchecked_toggles_persist_as_false(): void
    {
        Storage::fake('local');

        // Mensimulasikan submit form dengan semua checkbox tidak dicentang:
        // JS panel mengirim hidden input "0" untuk tiap toggle.
        $this->actingAs($this->superAdmin)
            ->post(route('admin.hero-sliders.settings'), [
                'auto_play' => '0',
                'show_arrows' => '0',
                'show_dots' => '0',
                'transition_duration' => '500',
            ])
            ->assertRedirect(route('admin.hero-sliders.index'));

        $settings = $this->settings();
        $this->assertFalse($settings['auto_play']);
        $this->assertFalse($settings['show_arrows']);
        $this->assertFalse($settings['show_dots']);
    }

    public function test_omitted_fields_fall_back_to_defaults(): void
    {
        Storage::fake('local');

        // Payload parsial (mis. klien lama): field yang tidak dikirim
        // mengikuti nilai default, bukan error.
        $this->actingAs($this->superAdmin)
            ->post(route('admin.hero-sliders.settings'), [
                'auto_play' => '1',
            ])
            ->assertRedirect(route('admin.hero-sliders.index'));

        $settings = $this->settings();
        $this->assertTrue($settings['auto_play']);
        $this->assertFalse($settings['show_arrows']);   // default
        $this->assertTrue($settings['show_dots']);      // default
        $this->assertSame(500, $settings['transition_duration']); // default
    }

    public function test_empty_transition_duration_keeps_previous_value(): void
    {
        Storage::fake('local');

        // Simpan nilai awal dulu.
        $this->actingAs($this->superAdmin)
            ->post(route('admin.hero-sliders.settings'), ['transition_duration' => '1200']);

        // Submit dengan field kosong → nilai lama dipertahankan, bukan 0.
        $this->actingAs($this->superAdmin)
            ->post(route('admin.hero-sliders.settings'), ['transition_duration' => '']);

        $this->assertSame(1200, $this->settings()['transition_duration']);
    }

    public function test_invalid_transition_duration_is_rejected(): void
    {
        Storage::fake('local');

        $this->actingAs($this->superAdmin)
            ->from(route('admin.hero-sliders.index'))
            ->post(route('admin.hero-sliders.settings'), [
                'transition_duration' => '50', // < min:100
            ])
            ->assertSessionHasErrors('transition_duration');

        Storage::disk('local')->assertMissing('hero_slider_settings.json');
    }

    public function test_regular_admin_is_forbidden(): void
    {
        Storage::fake('local');

        // Regular admin lolos middleware permission (Gate::before memberi
        // akses lewat role administrator), jadi 403 murni dari gerbang
        // isSuperAdmin() di dalam controller.
        $this->actingAs($this->regularAdmin)
            ->post(route('admin.hero-sliders.settings'), [
                'auto_play' => '1',
                'transition_duration' => '800',
            ])
            ->assertForbidden();

        Storage::disk('local')->assertMissing('hero_slider_settings.json');
    }

    public function test_guest_is_redirected_to_login(): void
    {
        // Middleware 'auth' (web guard) mengalihkan tamu ke halaman login.
        $this->post(route('admin.hero-sliders.settings'), ['auto_play' => '1'])
            ->assertRedirect();

        $this->assertGuest();
        Storage::disk('local')->assertMissing('hero_slider_settings.json');
    }

    public function test_index_passes_settings_to_view(): void
    {
        Storage::fake('local');

        $this->actingAs($this->superAdmin)
            ->post(route('admin.hero-sliders.settings'), [
                'auto_play' => '0',
                'show_arrows' => '1',
                'show_dots' => '1',
                'transition_duration' => '650',
            ]);

        $this->actingAs($this->superAdmin)
            ->get(route('admin.hero-sliders.index'))
            ->assertOk()
            ->assertViewHas('sliderSettings', function ($settings) {
                return $settings['auto_play'] === false
                    && $settings['show_arrows'] === true
                    && $settings['show_dots'] === true
                    && $settings['transition_duration'] === 650;
            });
    }
}
