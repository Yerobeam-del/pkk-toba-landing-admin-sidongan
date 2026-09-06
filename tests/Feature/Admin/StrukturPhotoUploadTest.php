<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\StrukturMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Upload & hapus foto anggota struktur melalui admin panel.
 *
 * Form struktur dikirim multipart (lihat struktur/create & edit blade),
 * jadi foto diuji sebagai UploadedFile sungguhan — bukan sekadar base64 —
 * termasuk kontrak baru "Hapus" (remove_photo=1) dan prioritas
 * cropped_photo di atas remove_photo.
 */
class StrukturPhotoUploadTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $this->admin = User::factory()->create([
            'email' => 'struktur-admin@pkk-toba.id',
            'role_id' => Role::where('name', 'administrator')->first()->id,
        ]);
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'group' => 'pengurus',
            'position' => 'Bendahara',
            'name' => 'Anggota Uji Foto',
        ], $overrides);
    }

    // ============================================================
    // Upload multipart biasa
    // ============================================================

    public function test_store_saves_uploaded_photo_file(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin)
            ->post(route('admin.struktur.store'), $this->validPayload([
                'photo' => UploadedFile::fake()->image('foto.png', 300, 300),
            ]))
            ->assertRedirect(route('admin.struktur.index'))
            ->assertSessionHas('success');

        $member = StrukturMember::where('name', 'Anggota Uji Foto')->first();
        $this->assertNotNull($member);
        $this->assertNotNull($member->photo_path);
        Storage::disk('public')->assertExists($member->photo_path);
        // Nama file dari sanitizer, bukan nama kiriman klien.
        $this->assertStringStartsWith('struktur/struktur_', $member->photo_path);
    }

    public function test_store_rejects_fake_image_content(): void
    {
        Storage::fake('public');

        // Isi teks palsu dengan ekstensi .png → harus ditolak validasi 'image'.
        $this->actingAs($this->admin)
            ->from(route('admin.struktur.create'))
            ->post(route('admin.struktur.store'), $this->validPayload([
                'photo' => UploadedFile::fake()->createWithContent('fake.png', 'bukan gambar'),
            ]))
            ->assertSessionHasErrors('photo');

        $this->assertDatabaseMissing('struktur_members', ['name' => 'Anggota Uji Foto']);
    }

    public function test_store_rejects_oversized_photo(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin)
            ->post(route('admin.struktur.store'), $this->validPayload([
                // > aturan max:2048 (KB)
                'photo' => UploadedFile::fake()->image('besar.png')->size(3000),
            ]))
            ->assertSessionHasErrors('photo');
    }

    // ============================================================
    // Base64 (hasil crop di browser)
    // ============================================================

    public function test_store_accepts_cropped_base64_photo(): void
    {
        Storage::fake('public');

        $dataUrl = 'data:image/png;base64,' . base64_encode($this->pngBytes());

        $this->actingAs($this->admin)
            ->post(route('admin.struktur.store'), $this->validPayload([
                'cropped_photo' => $dataUrl,
            ]))
            ->assertRedirect(route('admin.struktur.index'))
            ->assertSessionHas('success');

        $member = StrukturMember::where('name', 'Anggota Uji Foto')->first();
        $this->assertNotNull($member->photo_path);
        Storage::disk('public')->assertExists($member->photo_path);
        $this->assertStringEndsWith('.png', $member->photo_path);
    }

    public function test_store_rejects_non_image_base64(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin)
            ->from(route('admin.struktur.create'))
            ->post(route('admin.struktur.store'), $this->validPayload([
                // Bukan data:image/* → ditolak sanitizer sebelum menyentuh disk.
                'cropped_photo' => 'data:text/html;base64,' . base64_encode('<svg onload=alert(1)>'),
            ]))
            ->assertSessionHasErrors('photo');

        $this->assertDatabaseMissing('struktur_members', ['name' => 'Anggota Uji Foto']);
    }

    // ============================================================
    // Update: ganti foto, hapus foto, prioritas
    // ============================================================

    public function test_update_replaces_photo_and_deletes_old_file(): void
    {
        Storage::fake('public');

        $member = StrukturMember::create([
            'name' => 'Anggota Uji Foto',
            'position' => 'Bendahara',
            'pokja_id' => null,
            'sort_order' => 6,
            'photo_path' => 'struktur/lama.png',
        ]);
        Storage::disk('public')->put('struktur/lama.png', $this->pngBytes());

        $this->actingAs($this->admin)
            ->put(route('admin.struktur.update', $member), $this->validPayload([
                'photo' => UploadedFile::fake()->image('baru.png', 300, 300),
            ]))
            ->assertRedirect(route('admin.struktur.index'));

        $member->refresh();
        $this->assertNotSame('struktur/lama.png', $member->photo_path);
        Storage::disk('public')->assertExists($member->photo_path);
        Storage::disk('public')->assertMissing('struktur/lama.png');
    }

    public function test_update_with_remove_photo_clears_path_and_deletes_file(): void
    {
        Storage::fake('public');

        $member = StrukturMember::create([
            'name' => 'Anggota Uji Foto',
            'position' => 'Bendahara',
            'pokja_id' => null,
            'sort_order' => 6,
            'photo_path' => 'struktur/lama.png',
        ]);
        Storage::disk('public')->put('struktur/lama.png', $this->pngBytes());

        $this->actingAs($this->admin)
            ->put(route('admin.struktur.update', $member), $this->validPayload([
                'remove_photo' => '1',
            ]))
            ->assertRedirect(route('admin.struktur.index'));

        $this->assertNull($member->fresh()->photo_path);
        Storage::disk('public')->assertMissing('struktur/lama.png');
    }

    public function test_update_without_changes_keeps_existing_photo(): void
    {
        Storage::fake('public');

        $member = StrukturMember::create([
            'name' => 'Anggota Uji Foto',
            'position' => 'Bendahara',
            'pokja_id' => null,
            'sort_order' => 6,
            'photo_path' => 'struktur/lama.png',
        ]);
        Storage::disk('public')->put('struktur/lama.png', $this->pngBytes());

        $this->actingAs($this->admin)
            ->put(route('admin.struktur.update', $member), $this->validPayload())
            ->assertRedirect(route('admin.struktur.index'));

        $this->assertSame('struktur/lama.png', $member->fresh()->photo_path);
        Storage::disk('public')->assertExists('struktur/lama.png');
    }

    public function test_update_new_photo_takes_precedence_over_remove_flag(): void
    {
        Storage::fake('public');

        $member = StrukturMember::create([
            'name' => 'Anggota Uji Foto',
            'position' => 'Bendahara',
            'pokja_id' => null,
            'sort_order' => 6,
            'photo_path' => 'struktur/lama.png',
        ]);
        Storage::disk('public')->put('struktur/lama.png', $this->pngBytes());

        $this->actingAs($this->admin)
            ->put(route('admin.struktur.update', $member), $this->validPayload([
                'photo' => UploadedFile::fake()->image('baru.png', 300, 300),
                'remove_photo' => '1', // harus diabaikan karena ada foto baru
            ]))
            ->assertRedirect(route('admin.struktur.index'));

        $member->refresh();
        $this->assertNotNull($member->photo_path);
        $this->assertNotSame('struktur/lama.png', $member->photo_path);
        Storage::disk('public')->assertExists($member->photo_path);
        Storage::disk('public')->assertMissing('struktur/lama.png');
    }

    // ============================================================
    // Helper: PNG valid 1x1 px (GD dari CI/dev bisa beda header, jadi
    // pakai byte PNG minimal yang stabil).
    // ============================================================

    private function pngBytes(): string
    {
        return base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='
        );
    }
}
