<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\User;
use App\Services\SiedaSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Audit alur sinkronisasi user Admin Panel ↔ SIEDA (service + endpoint).
 *
 * Kontrak yang diuji:
 *  1. OUTBOUND — SiedaSyncService menandatangani request dengan
 *     X-Sieda-Key + timestamp + HMAC dan mengirim payload yang benar;
 *     gagal jaringan/5xx mengembalikan false (bukan melempar).
 *  2. REVOKE   — menghapus sieda_role lewat form edit kini ikut
 *     revokeAccess() (sebelumnya akun SIEDA tetap aktif).
 *  3. INBOUND  — /api/sieda/sync-avatar memverifikasi HMAC + timestamp;
 *     signature salah / timestamp basi ditolak 401 tanpa menyentuh DB.
 */
class SiedaSyncTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        config()->set('services.sieda.sync_secret', 'testing-shared-secret');
        config()->set('services.sieda.base_url', 'http://sieda.test');

        $this->superAdmin = User::factory()->create([
            'email' => 'admin@pkk-toba.id',
            'role_id' => Role::where('name', 'administrator')->first()->id,
            'sidongan_role' => 'super_admin',
        ]);
    }

    // ============================================================
    // Helpers
    // ============================================================

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Akun SIEDA',
            'email' => 'sieda-user@pkk-toba.id',
            'password' => 'PasswordRahasia123!',
            'password_confirmation' => 'PasswordRahasia123!',
            'role_id' => Role::where('name', 'anggota')->first()->id,
            'sieda_role' => 'operator',
        ], $overrides);
    }

    /** Header HMAC sah untuk body (pola yang wajib diikuti SIEDA). */
    private function signedHeaders(string $body): array
    {
        $timestamp = (string) time();
        $secret = config('services.sieda.sync_secret');

        return [
            'X-Sieda-Key' => $secret,
            'X-Sieda-Timestamp' => $timestamp,
            'X-Sieda-Signature' => hash_hmac('sha256', $timestamp . '.' . $body, $secret),
            'Content-Type' => 'application/json',
        ];
    }

    private function pngBytes(): string
    {
        return base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='
        );
    }

    // ============================================================
    // 1. OUTBOUND: service menandatangani & melaporkan kegagalan
    // ============================================================

    public function test_sync_user_sends_signed_payload_to_sieda(): void
    {
        Http::fake(['sieda.test/*' => Http::response(['success' => true], 200)]);

        $ok = app(SiedaSyncService::class)->syncUser([
            'name' => 'Budi',
            'email' => 'budi@pkk-toba.id',
            'password' => 'secret123',
            'sieda_role' => 'kader',
            'kecamatan_code' => '1201',
            'kelurahan_code' => '120101',
        ]);

        $this->assertTrue($ok);

        Http::assertSent(function ($request) {
            $signature = hash_hmac(
                'sha256',
                $request->header('X-Sieda-Timestamp')[0] . '.' . $request->body(),
                'testing-shared-secret'
            );

            return $request->url() === 'http://sieda.test/api/sieda/sync-user'
                && $request->method() === 'POST'
                && hash_equals($signature, (string) $request->header('X-Sieda-Signature')[0])
                && str_contains($request->body(), 'budi@pkk-toba.id')
                && str_contains($request->body(), 'kader');
        });
    }

    public function test_sync_user_returns_false_when_sieda_unreachable(): void
    {
        Http::fake(fn () => Http::response([], 500));

        $ok = app(SiedaSyncService::class)->syncUser([
            'name' => 'Budi',
            'email' => 'budi@pkk-toba.id',
            'password' => 'secret123',
            'sieda_role' => 'kader',
        ]);

        $this->assertFalse($ok);
    }

    public function test_sync_user_returns_false_when_secret_not_configured(): void
    {
        config()->set('services.sieda.sync_secret', '');

        $ok = app(SiedaSyncService::class)->syncUser([
            'name' => 'Budi',
            'email' => 'budi@pkk-toba.id',
        ]);

        $this->assertFalse($ok);
    }

    // ============================================================
    // 2. REVOKE: menghapus sieda_role lewat edit → cabut akses SIEDA
    // ============================================================

    public function test_clearing_sieda_role_in_edit_revokes_sieda_access(): void
    {
        Http::fake(['sieda.test/*' => Http::response(['success' => true], 200)]);

        $target = User::factory()->create([
            'email' => 'revoke-me@pkk-toba.id',
            'role_id' => Role::where('name', 'anggota')->first()->id,
            'sieda_role' => 'operator',
        ]);

        $this->actingAs($this->superAdmin)
            ->put(route('admin.user-management.update', $target), $this->validPayload([
                'sieda_role' => null, // role SIEDA dihapus
            ]))
            ->assertRedirect(route('admin.user-management.edit', $target));

        $this->assertNull($target->fresh()->sieda_role);

        // revokeAccess harus terpanggil ke endpoint revoke SIEDA
        Http::assertSent(fn ($request) => str_contains($request->url(), '/api/sieda/revoke-access'));
    }

    public function test_updating_sieda_role_uses_old_email_as_path(): void
    {
        Http::fake(['sieda.test/*' => Http::response(['success' => true], 200)]);

        $target = User::factory()->create([
            'email' => 'old-email@pkk-toba.id',
            'role_id' => Role::where('name', 'anggota')->first()->id,
            'sieda_role' => 'operator',
        ]);

        $this->actingAs($this->superAdmin)
            ->put(route('admin.user-management.update', $target), $this->validPayload([
                'email' => 'renamed@pkk-toba.id', // rename email, role tetap
            ]))
            ->assertRedirect(route('admin.user-management.edit', $target));

        // PUT ke /sync-user/{email-lama} — bukan POST buat akun baru
        Http::assertSent(fn ($request) => str_starts_with($request->url(), 'http://sieda.test/api/sieda/sync-user/old-email'));
    }

    public function test_failed_sync_shows_warning_flash_instead_of_success_claim(): void
    {
        Http::fake(fn () => Http::response([], 500));

        $target = User::factory()->create([
            'email' => 'flash@pkk-toba.id',
            'role_id' => Role::where('name', 'anggota')->first()->id,
            'sieda_role' => 'viewer',
        ]);

        $this->actingAs($this->superAdmin)
            ->put(route('admin.user-management.update', $target), $this->validPayload([
                'email' => 'flash@pkk-toba.id',
            ]))
            ->assertRedirect()
            // Tidak mengklaim "dan disinkronisasi" saat SIEDA menolak
            ->assertSessionHas('success', fn ($msg) => !str_contains($msg, 'dan disinkronisasi ke SIEDA'));
    }

    // ============================================================
    // 3. INBOUND: /api/sieda/sync-avatar (HMAC + anti-replay)
    // ============================================================

    public function test_avatar_sync_accepts_valid_hmac_and_stores_avatar(): void
    {
        Storage::fake('public');

        $target = User::factory()->create(['email' => 'avatar@pkk-toba.id']);

        $body = json_encode([
            'email' => 'avatar@pkk-toba.id',
            'avatar_base64' => base64_encode($this->pngBytes()),
        ]);

        $this->postJson('/api/sieda/sync-avatar', json_decode($body, true), $this->signedHeaders($body))
            ->assertOk()
            ->assertJson(['success' => true]);

        $target->refresh();
        $this->assertNotNull($target->avatar);
        Storage::disk('public')->assertExists($target->avatar);
    }

    public function test_avatar_sync_rejects_invalid_signature(): void
    {
        Storage::fake('public');

        $target = User::factory()->create([
            'email' => 'bad-sig@pkk-toba.id',
            'avatar' => 'avatars/keep-me.jpg',
        ]);

        $body = json_encode([
            'email' => 'bad-sig@pkk-toba.id',
            'avatar_base64' => base64_encode($this->pngBytes()),
        ]);

        $headers = $this->signedHeaders($body);
        $headers['X-Sieda-Signature'] = str_repeat('0', 64); // signature palsu

        $this->postJson('/api/sieda/sync-avatar', json_decode($body, true), $headers)
            ->assertStatus(401);

        // Tidak ada perubahan DB
        $this->assertSame('avatars/keep-me.jpg', $target->fresh()->avatar);
    }

    public function test_avatar_sync_rejects_stale_timestamp_replay(): void
    {
        Storage::fake('public');

        User::factory()->create(['email' => 'stale@pkk-toba.id']);

        $body = json_encode([
            'email' => 'stale@pkk-toba.id',
            'avatar_base64' => base64_encode($this->pngBytes()),
        ]);

        // Timestamp 10 menit lalu — di luar toleransi ±5 menit
        $timestamp = (string) (time() - 600);
        $secret = config('services.sieda.sync_secret');

        $this->postJson('/api/sieda/sync-avatar', json_decode($body, true), [
            'X-Sieda-Key' => $secret,
            'X-Sieda-Timestamp' => $timestamp,
            'X-Sieda-Signature' => hash_hmac('sha256', $timestamp . '.' . $body, $secret),
            'Content-Type' => 'application/json',
        ])->assertStatus(401);
    }

    public function test_avatar_sync_rejects_non_image_payload(): void
    {
        Storage::fake('public');

        $target = User::factory()->create(['email' => 'evil@pkk-toba.id']);

        $body = json_encode([
            'email' => 'evil@pkk-toba.id',
            'avatar_base64' => base64_encode('<svg onload="alert(1)"></svg>'), // SVG/script payload
        ]);

        $this->postJson('/api/sieda/sync-avatar', json_decode($body, true), $this->signedHeaders($body))
            ->assertStatus(422);

        $this->assertNull($target->fresh()->avatar);
    }
}
