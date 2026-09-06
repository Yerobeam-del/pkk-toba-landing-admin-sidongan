<?php

namespace Tests\Feature\Sidongan;

use App\Models\Document;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Gerbang otorisasi SIDONGAN per role (hasil audit menyeluruh).
 *
 * Kontrak yang diuji:
 *  - Super Admin melihat menu & tombol yang selama ini tersembunyi
 *    (view kini memakai helper isSidongan*() yang sama dengan server),
 *  - hanya Sekretaris boleh mencatat surat masuk (create/store),
 *  - Sekretaris hanya boleh menghapus surat buatannya sendiri,
 *    sementara Super Admin boleh menghapus surat siapa pun.
 *
 * Semua aktor dibuat "profil lengkap" (phone_number + personal_email)
 * agar tidak terlempar ke onboarding oleh middleware sidongan.profile.
 */
class SidonganRoleAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $sekretaris;
    protected User $bendahara;
    protected int $adminRoleId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $this->adminRoleId = Role::where('name', 'administrator')->first()->id;

        $this->superAdmin = $this->makeSidonganUser('super-admin@pkk-toba.id', 'super_admin');
        $this->sekretaris = $this->makeSidonganUser('sekretaris@pkk-toba.id', 'sekretaris');
        $this->bendahara  = $this->makeSidonganUser('bendahara@pkk-toba.id', 'bendahara');

        Storage::fake('public');
    }

    // ============================================================
    // Helpers
    // ============================================================

    private function makeSidonganUser(string $email, string $sidonganRole): User
    {
        return User::factory()->create([
            'email' => $email,
            'sidongan_role' => $sidonganRole,
            'role_id' => $this->adminRoleId,
            'phone_number' => '081234567890',
            'personal_email' => str_replace('@', '.', $email), // unik & terisi
        ]);
    }

    private function suratPayload(array $overrides = []): array
    {
        return array_merge([
            'sender' => 'Dinas Sosial',
            'document_number' => '001/Dinsos/2026',
            'document_date' => '2026-09-01',
            'subject' => 'Undangan Rapat Koordinasi',
            'suggestion' => 'Mohon instruksi pengiriman delegasi.',
            'file' => UploadedFile::fake()->createWithContent('surat.pdf', "%PDF-1.4\n%dummy"),
        ], $overrides);
    }

    private function createSurat(User $creator, array $overrides = []): Document
    {
        Storage::disk('public')->put($overrides['file_path'] ?? 'sidongan/documents/existing.pdf', 'isi surat');

        return Document::create(array_merge([
            'title' => 'Surat Uji',
            'slug' => 'surat-uji-' . uniqid(),
            'sender' => 'Pengirim',
            'document_number' => 'DOC-' . uniqid(),
            'document_date' => '2026-09-01',
            'subject' => 'Surat Uji',
            'suggestion' => 'Saran uji',
            'file_path' => 'sidongan/documents/existing.pdf',
            'file_name' => 'existing.pdf',
            'file_type' => 'application/pdf',
            'file_size' => 123,
            'status' => 'menunggu_disposisi',
            'created_by' => $creator->id,
        ], $overrides));
    }

    // ============================================================
    // Super Admin melihat menu yang selama ini tersembunyi
    // ============================================================

    public function test_super_admin_sees_sidongan_menus(): void
    {
        $this->actingAs($this->superAdmin, 'sidongan')
            ->get(route('sidongan.dashboard'))
            ->assertOk()
            ->assertSee('Surat')             // menu surat (dulu: hanya sekretaris)
            ->assertSee('Disposisi Surat')   // menu ketua
            ->assertSee('Verifikasi Laporan'); // menu ketua
    }

    public function test_super_admin_sees_create_surat_button(): void
    {
        $this->actingAs($this->superAdmin, 'sidongan')
            ->get(route('sidongan.documents.index'))
            ->assertOk()
            ->assertSee(route('sidongan.documents.create')); // tombol "Buat Surat Baru"
    }

    public function test_super_admin_can_open_create_surat_form(): void
    {
        // Gerbang server (create) kini mengizinkan Super Admin via helper.
        $this->actingAs($this->superAdmin, 'sidongan')
            ->get(route('sidongan.documents.create'))
            ->assertOk();
    }

    public function test_bendahara_does_not_see_create_surat_button(): void
    {
        $this->actingAs($this->bendahara, 'sidongan')
            ->get(route('sidongan.documents.index'))
            ->assertOk()
            ->assertDontSee(route('sidongan.documents.create'));
    }

    // ============================================================
    // Hanya Sekretaris boleh mencatat surat (create/store)
    // ============================================================

    public function test_bendahara_gets_403_on_surat_create(): void
    {
        $this->actingAs($this->bendahara, 'sidongan')
            ->get(route('sidongan.documents.create'))
            ->assertForbidden();
    }

    public function test_bendahara_gets_403_on_surat_store(): void
    {
        $this->actingAs($this->bendahara, 'sidongan')
            ->post(route('sidongan.documents.store'), $this->suratPayload())
            ->assertForbidden();

        $this->assertDatabaseMissing('sidongan_documents', [
            'document_number' => '001/Dinsos/2026',
        ]);
    }

    public function test_sekretaris_can_store_surat(): void
    {
        $this->actingAs($this->sekretaris, 'sidongan')
            ->post(route('sidongan.documents.store'), $this->suratPayload())
            ->assertRedirect(route('sidongan.documents.index'))
            ->assertSessionHas('success');

        $doc = Document::where('document_number', '001/Dinsos/2026')->first();
        $this->assertNotNull($doc);
        $this->assertSame($this->sekretaris->id, $doc->created_by);
        $this->assertSame('menunggu_disposisi', $doc->status);
        Storage::disk('public')->assertExists($doc->file_path);
    }

    // ============================================================
    // Sekretaris hanya boleh menghapus surat buatannya sendiri
    // ============================================================

    public function test_sekretaris_cannot_delete_other_sekretaris_surat(): void
    {
        $otherSekretaris = $this->makeSidonganUser('sekretaris-2@pkk-toba.id', 'sekretaris');
        $surat = $this->createSurat($otherSekretaris);

        $this->actingAs($this->sekretaris, 'sidongan')
            ->delete(route('sidongan.documents.destroy', $surat))
            ->assertForbidden();

        $this->assertDatabaseHas('sidongan_documents', ['id' => $surat->id]);
        Storage::disk('public')->assertExists($surat->file_path);
    }

    public function test_sekretaris_can_delete_own_surat(): void
    {
        $surat = $this->createSurat($this->sekretaris);

        $this->actingAs($this->sekretaris, 'sidongan')
            ->delete(route('sidongan.documents.destroy', $surat))
            ->assertRedirect(route('sidongan.documents.index'))
            ->assertSessionHas('success');

        // Tabel memakai soft deletes → baris tetap ada tapi bertanda terhapus.
        $this->assertSoftDeleted('sidongan_documents', ['id' => $surat->id]);
        Storage::disk('public')->assertMissing($surat->file_path);
    }

    public function test_super_admin_can_delete_any_surat(): void
    {
        $surat = $this->createSurat($this->sekretaris);

        $this->actingAs($this->superAdmin, 'sidongan')
            ->delete(route('sidongan.documents.destroy', $surat))
            ->assertRedirect(route('sidongan.documents.index'))
            ->assertSessionHas('success');

        $this->assertSoftDeleted('sidongan_documents', ['id' => $surat->id]);
    }

    public function test_bendahara_cannot_delete_any_surat(): void
    {
        $surat = $this->createSurat($this->sekretaris);

        $this->actingAs($this->bendahara, 'sidongan')
            ->delete(route('sidongan.documents.destroy', $surat))
            ->assertForbidden();

        $this->assertDatabaseHas('sidongan_documents', ['id' => $surat->id]);
    }
}
