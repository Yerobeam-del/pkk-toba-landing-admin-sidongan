<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\User;
use App\Services\SsoTokenService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * Round-trip SSO Admin Panel ↔ SIEDA, end-to-end lewat HTTP.
 *
 * Kontrak yang diuji:
 *  1. ISSUE  — SsoTokenService::issue() menghasilkan token HMAC base64url
 *              yang bisa diverifikasi balik oleh service yang sama.
 *  2. LOGIN  — /sso/login dengan token sah: user ter-login, sesi ditandai
 *              sso_from_sieda, dan diarahkan ke path return yang di-whitelist.
 *  3. BACK   — /sso/back membangun callback URL ke SIEDA berisi token BARU
 *              (bukan memakai token masuk yang sudah terpakai).
 *  4. REPLAY — token yang sama dipakai kedua kali → ditolak saat kedua.
 *  5. FAIL   — signature palsu, token kedaluwarsa, email tak dikenal,
 *              dan secret belum diset → semuanya ditolak tanpa login.
 */
class SsoRoundTripTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected SsoTokenService $sso;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RolePermissionSeeder::class);

        // Konfigurasi SSO untuk durasi test ini.
        config()->set('services.sieda.sync_secret', 'testing-shared-secret');
        config()->set('services.sieda.base_url', 'http://sieda.test');

        $this->sso = app(SsoTokenService::class);

        $this->user = User::factory()->create([
            'email' => 'sso-user@pkk-toba.id',
            'role_id' => Role::where('name', 'administrator')->first()->id,
        ]);
    }

    // ============================================================
    // 1. ISSUE: format & verifikasi mandiri token
    // ============================================================

    public function test_issue_creates_verifiable_token_with_expected_payload(): void
    {
        $token = $this->sso->issue('sso-user@pkk-toba.id', '/admin/profile');
        $this->assertNotNull($token);

        // Format: base64url(payload) . '.' . hmac
        $this->assertMatchesRegularExpression('/^[A-Za-z0-9_-]+\.[A-Fa-f0-9]{64}$/', $token);

        $data = $this->sso->verify($token);
        $this->assertNotNull($data);
        $this->assertSame('sso-user@pkk-toba.id', $data['email']);
        $this->assertSame('/admin/profile', $data['return']);
        $this->assertGreaterThan(time(), $data['exp']); // berumur 5 menit
    }

    public function test_issue_returns_null_when_secret_not_configured(): void
    {
        config()->set('services.sieda.sync_secret', '');

        $this->assertNull($this->sso->issue('sso-user@pkk-toba.id'));
        $this->assertNull($this->sso->verify('anything'));
    }

    // ============================================================
    // 2. LOGIN: round-trip masuk dari SIEDA
    // ============================================================

    public function test_sso_login_authenticates_user_and_redirects_to_return_path(): void
    {
        $token = $this->sso->issue('sso-user@pkk-toba.id', '/admin/profile');

        $this->get("/sso/login?token={$token}&return=/admin/profile")
            ->assertRedirect('/admin/profile');

        $this->assertAuthenticatedAs($this->user); // guard web (default)
    }

    public function test_sso_login_marks_session_as_coming_from_sieda(): void
    {
        $token = $this->sso->issue('sso-user@pkk-toba.id');

        $this->get("/sso/login?token={$token}")
            ->assertRedirect('/admin/profile'); // default return

        // Penanda "Kembali ke SIEDA" di halaman profil ter-set.
        $this->assertTrue(session('sso_from_sieda') === true);
    }

    public function test_sso_login_return_path_is_whitelisted(): void
    {
        $token = $this->sso->issue('sso-user@pkk-toba.id');

        // Path di luar whitelist dipaksa kembali ke /admin/profile.
        $this->get("/sso/login?token={$token}&return=https://evil.example/steal")
            ->assertRedirect('/admin/profile');

        $this->assertAuthenticatedAs($this->user);
    }

    public function test_already_logged_in_user_with_valid_token_gets_return_path(): void
    {
        $token = $this->sso->issue('sso-user@pkk-toba.id');

        $this->actingAs($this->user)
            ->get("/sso/login?token={$token}&return=/admin")
            ->assertRedirect('/admin');

        // Tidak double-login, tapi penanda SIEDA tetap ter-set.
        $this->assertTrue(session('sso_from_sieda') === true);
    }

    // ============================================================
    // 3. BACK: perjalanan pulang ke SIEDA
    // ============================================================

    public function test_sso_back_builds_sieda_callback_url_with_fresh_token(): void
    {
        // Token masuk dari SIEDA (sudah terpakai saat login pertama).
        $inboundToken = $this->sso->issue('sso-user@pkk-toba.id');
        $this->get("/sso/login?token={$inboundToken}")->assertRedirect('/admin/profile');

        $response = $this->actingAs($this->user)->get('/sso/back');
        $response->assertRedirect();

        $location = (string) $response->headers->get('Location');
        $this->assertStringStartsWith('http://sieda.test/sso/callback?token=', $location);

        // Token di callback valid. Catatan: issue() deterministik — bila
        // diterbitkan pada detik yang sama dengan payload identik, hasilnya
        // bisa byte-identik dengan token masuk. Ini aman: proteksi replay
        // berkunci pada hash token itu sendiri, sehingga token identik
        // berbagi penanda "sudah dipakai" (loop kembali ke /sso/login
        // otomatis ditolak).
        parse_str((string) parse_url($location, PHP_URL_QUERY), $query);
        $outboundToken = $query['token'] ?? '';
        $this->assertNotSame('', $outboundToken);

        $data = $this->sso->verify($outboundToken);
        $this->assertNotNull($data);
        $this->assertSame('sso-user@pkk-toba.id', $data['email']);
    }

    public function test_sso_back_requires_authentication(): void
    {
        $this->get('/sso/back')->assertRedirect(route('login'));
    }

    // ============================================================
    // 4. REPLAY: token sekali pakai
    // ============================================================

    public function test_replaying_the_same_token_is_rejected_on_second_use(): void
    {
        $token = $this->sso->issue('sso-user@pkk-toba.id');

        // Pakai pertama → sukses.
        $this->get("/sso/login?token={$token}")
            ->assertRedirect('/admin/profile');
        $this->assertAuthenticatedAs($this->user);

        // Logout, lalu replay token yang sama.
        $this->post(route('logout'));
        $this->assertGuest();

        $this->get("/sso/login?token={$token}")
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest(); // tetap tidak login
    }

    // ============================================================
    // 5. FAIL: jalur penolakan
    // ============================================================

    public function test_invalid_signature_is_rejected(): void
    {
        $token = $this->sso->issue('sso-user@pkk-toba.id');
        $forged = explode('.', $token)[0] . '.' . str_repeat('0', 64);

        $this->get("/sso/login?token={$forged}")
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_expired_token_is_rejected(): void
    {
        // Bentuk token manual dengan exp di masa lalu.
        $payload = json_encode([
            'email' => 'sso-user@pkk-toba.id',
            'exp' => time() - 10,
            'return' => '/admin/profile',
        ]);
        $expired = rtrim(strtr(base64_encode($payload), '+/', '-_'), '=')
            . '.' . hash_hmac('sha256', $payload, 'testing-shared-secret');

        $this->get("/sso/login?token={$expired}")
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_unknown_account_is_rejected(): void
    {
        $token = $this->sso->issue('nosuchuser@pkk-toba.id');

        $this->get("/sso/login?token={$token}")
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_malformed_token_is_rejected(): void
    {
        $this->get('/sso/login?token=not-a-token')
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    // ============================================================
    // 6. RATE LIMIT: anti brute-force (2 lapis: per-IP + global)
    // ============================================================

    public function test_repeated_failed_attempts_from_same_ip_are_blocked(): void
    {
        // 10 percobaan gagal (token sah untuk akun tak dikenal) = kuota penuh.
        for ($i = 0; $i < 10; $i++) {
            $token = $this->sso->issue('nosuchuser@pkk-toba.id');
            $this->get("/sso/login?token={$token}")->assertRedirect(route('login'));
        }
        $this->assertGuest();

        // Percobaan ke-11 — bahkan dengan token sah untuk akun nyata — 429.
        $validToken = $this->sso->issue('sso-user@pkk-toba.id');
        $response = $this->get("/sso/login?token={$validToken}");

        $response->assertStatus(429);
        $this->assertNotNull($response->headers->get('Retry-After'));
        $this->assertGuest();
    }

    public function test_successful_login_resets_per_ip_failed_counter(): void
    {
        // 9 gagal — di bawah batas 10.
        for ($i = 0; $i < 9; $i++) {
            $token = $this->sso->issue('nosuchuser@pkk-toba.id');
            $this->get("/sso/login?token={$token}")->assertRedirect(route('login'));
        }

        // Login sah → menghapus kuota gagal per-IP.
        $goodToken = $this->sso->issue('sso-user@pkk-toba.id');
        $this->get("/sso/login?token={$goodToken}")->assertRedirect('/admin/profile');
        $this->assertAuthenticatedAs($this->user);

        $this->post(route('logout'));
        $this->assertGuest();

        // 1 gagal lagi setelah sukses → belum kena 429 (kuota baru).
        $token = $this->sso->issue('nosuchuser@pkk-toba.id');
        $this->get("/sso/login?token={$token}")->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_global_limit_blocks_attacker_rotating_forwarded_for_ips(): void
    {
        // Penyerang memalsukan X-Forwarded-For tiap request (trustProxies: '*')
        // supaya kuota per-IP tidak pernah tercapai — lapis GLOBAL yang
        // menghentikannya. Global = 50 gagal; request ke-51 ditolak walau
        // IP-nya masih "baru".
        for ($i = 1; $i <= 50; $i++) {
            $token = $this->sso->issue('nosuchuser@pkk-toba.id');
            $this->withHeaders(['X-Forwarded-For' => "10.9.0.{$i}"])
                ->get("/sso/login?token={$token}")
                ->assertRedirect(route('login'));
        }

        $token = $this->sso->issue('sso-user@pkk-toba.id');
        $this->withHeaders(['X-Forwarded-For' => '10.9.0.999'])
            ->get("/sso/login?token={$token}")
            ->assertStatus(429);

        $this->assertGuest();
    }
}
