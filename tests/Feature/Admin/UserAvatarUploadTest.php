<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Upload avatar akun di manajemen pengguna (create & edit).
 *
 * Kedua form dikirim multipart biasa (input name="photo", lihat blade
 * user-management), jadi jalur yang diuji adalah file upload sungguhan
 * via ImageUploadSanitizer::store — bukan hanya jalur base64 cropper.
 * Fokusnya: multipart membentuk Request dengan benar (enctype),
 * file tersimpan di disk public, dan file lama ikut terhapus.
 */
class UserAvatarUploadTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected int $adminRoleId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $this->adminRoleId = Role::where('name', 'administrator')->first()->id;

        $this->superAdmin = User::factory()->create([
            'email' => 'avatar-super@pkk-toba.id',
            'sidongan_role' => 'super_admin',
            'role_id' => $this->adminRoleId,
        ]);

        Storage::fake('public');
    }

    private function storePayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Pengguna Uji Avatar',
            'email' => 'avatar-user@pkk-toba.id',
            'password' => 'PasswordRahasia123!',
            'password_confirmation' => 'PasswordRahasia123!',
            'role_id' => $this->adminRoleId,
        ], $overrides);
    }

    private function updatePayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Pengguna Uji Avatar',
            'email' => 'avatar-user@pkk-toba.id',
            'role_id' => $this->adminRoleId,
        ], $overrides);
    }

    private function makeTargetUser(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'email' => 'avatar-user@pkk-toba.id',
            'avatar' => 'avatars/old.png',
            'role_id' => $this->adminRoleId,
        ], $overrides));
    }

    // ============================================================
    // Create (store)
    // ============================================================

    public function test_store_saves_uploaded_avatar_file(): void
    {
        $this->actingAs($this->superAdmin)
            ->post(route('admin.user-management.store'), $this->storePayload([
                'photo' => UploadedFile::fake()->image('avatar.png', 200, 200),
            ]))
            ->assertRedirect(route('admin.user-management.index'))
            ->assertSessionHas('success');

        $user = User::where('email', 'avatar-user@pkk-toba.id')->first();
        $this->assertNotNull($user);
        $this->assertNotNull($user->avatar);
        $this->assertStringStartsWith('avatars/avatar_', $user->avatar);
        Storage::disk('public')->assertExists($user->avatar);
    }

    public function test_store_accepts_cropped_base64_avatar(): void
    {
        $dataUrl = 'data:image/png;base64,' . base64_encode($this->pngBytes());

        $this->actingAs($this->superAdmin)
            ->post(route('admin.user-management.store'), $this->storePayload([
                'cropped_photo' => $dataUrl,
            ]))
            ->assertRedirect(route('admin.user-management.index'));

        $user = User::where('email', 'avatar-user@pkk-toba.id')->first();
        $this->assertNotNull($user->avatar);
        Storage::disk('public')->assertExists($user->avatar);
    }

    public function test_store_rejects_fake_image_content(): void
    {
        $this->actingAs($this->superAdmin)
            ->post(route('admin.user-management.store'), $this->storePayload([
                'photo' => UploadedFile::fake()->createWithContent('fake.jpg', 'bukan gambar'),
            ]))
            ->assertSessionHasErrors('photo');

        $this->assertDatabaseMissing('users', ['email' => 'avatar-user@pkk-toba.id']);
    }

    // ============================================================
    // Edit (update)
    // ============================================================

    public function test_update_replaces_avatar_and_deletes_old_file(): void
    {
        $target = $this->makeTargetUser();
        Storage::disk('public')->put('avatars/old.png', $this->pngBytes());

        $this->actingAs($this->superAdmin)
            ->put(route('admin.user-management.update', $target), $this->updatePayload([
                'photo' => UploadedFile::fake()->image('baru.png', 200, 200),
            ]))
            ->assertRedirect(route('admin.user-management.edit', $target))
            ->assertSessionHas('success');

        $target->refresh();
        $this->assertNotSame('avatars/old.png', $target->avatar);
        Storage::disk('public')->assertExists($target->avatar);
        Storage::disk('public')->assertMissing('avatars/old.png');
    }

    public function test_update_with_remove_photo_clears_avatar_and_deletes_file(): void
    {
        $target = $this->makeTargetUser();
        Storage::disk('public')->put('avatars/old.png', $this->pngBytes());

        $this->actingAs($this->superAdmin)
            ->put(route('admin.user-management.update', $target), $this->updatePayload([
                'remove_photo' => '1',
            ]))
            ->assertRedirect(route('admin.user-management.edit', $target));

        $this->assertNull($target->fresh()->avatar);
        Storage::disk('public')->assertMissing('avatars/old.png');
    }

    public function test_update_cropped_base64_takes_precedence_over_remove_flag(): void
    {
        $target = $this->makeTargetUser();
        Storage::disk('public')->put('avatars/old.png', $this->pngBytes());

        $dataUrl = 'data:image/png;base64,' . base64_encode($this->pngBytes());

        $this->actingAs($this->superAdmin)
            ->put(route('admin.user-management.update', $target), $this->updatePayload([
                'cropped_photo' => $dataUrl,
                'remove_photo' => '1', // harus diabaikan karena ada foto baru
            ]))
            ->assertRedirect(route('admin.user-management.edit', $target));

        $target->refresh();
        $this->assertNotNull($target->avatar);
        $this->assertNotSame('avatars/old.png', $target->avatar);
        Storage::disk('public')->assertExists($target->avatar);
        Storage::disk('public')->assertMissing('avatars/old.png');
    }

    public function test_update_without_changes_keeps_existing_avatar(): void
    {
        $target = $this->makeTargetUser();
        Storage::disk('public')->put('avatars/old.png', $this->pngBytes());

        $this->actingAs($this->superAdmin)
            ->put(route('admin.user-management.update', $target), $this->updatePayload())
            ->assertRedirect(route('admin.user-management.edit', $target));

        $this->assertSame('avatars/old.png', $target->fresh()->avatar);
        Storage::disk('public')->assertExists('avatars/old.png');
    }

    // ============================================================
    // Helper: PNG valid 1x1 px (byte PNG minimal yang stabil).
    // ============================================================

    private function pngBytes(): string
    {
        return base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='
        );
    }
}
