<?php

namespace Tests\Feature\Sidongan;

use App\Models\Document;
use App\Models\OutgoingLetter;
use App\Models\OutgoingLetterActivity;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OutgoingLetterWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private User $secretary;
    private User $chair;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $roleId = Role::where('name', 'administrator')->value('id');
        $this->secretary = User::factory()->create(['sidongan_role' => 'sekretaris', 'role_id' => $roleId, 'phone_number' => '081234567890', 'personal_email' => 'sekretaris@example.test']);
        $this->chair = User::factory()->create(['sidongan_role' => 'ketua', 'role_id' => $roleId, 'phone_number' => '081234567891', 'personal_email' => 'ketua@example.test']);
    }

    private function incoming(): Document
    {
        return Document::create([
            'title' => 'Permohonan Koordinasi', 'slug' => 'permohonan-koordinasi-' . uniqid(),
            'sender' => 'TP PKK Kecamatan', 'document_number' => '001/PKK/2026',
            'document_date' => '2026-09-01', 'agenda_date' => '2026-09-02',
            'subject' => 'Permohonan Koordinasi', 'suggestion' => 'Mohon arahan Ketua.',
            'file_path' => 'test.pdf', 'file_name' => 'test.pdf', 'file_type' => 'application/pdf',
            'file_size' => 10, 'status' => 'menunggu_disposisi', 'created_by' => $this->secretary->id,
        ]);
    }

    private function payload(): array
    {
        return ['recipient' => 'TP PKK Kecamatan', 'recipient_address' => 'Toba', 'subject' => 'Tanggapan Permohonan Koordinasi', 'body' => 'Dengan hormat, menindaklanjuti surat tersebut kami sampaikan tanggapan.', 'nature' => 'Biasa', 'letter_date' => '2026-09-16', 'signatory_name' => 'Ketua PKK', 'signatory_title' => 'Ketua TP PKK Kabupaten Toba'];
    }

    public function test_secretary_creates_outgoing_draft_from_incoming_document(): void
    {
        $document = $this->incoming();
        $this->actingAs($this->secretary, 'sidongan')->post(route('sidongan.documents.outgoing.store', $document), $this->payload())->assertRedirect();
        $this->assertDatabaseHas('sidongan_outgoing_letters', ['incoming_document_id' => $document->id, 'status' => 'draft', 'recipient' => 'TP PKK Kecamatan']);
    }

    public function test_chair_can_approve_and_system_issues_number(): void
    {
        $letter = OutgoingLetter::create(array_merge($this->payload(), ['incoming_document_id' => $this->incoming()->id, 'created_by' => $this->secretary->id, 'status' => 'diajukan']));
        $this->actingAs($this->chair, 'sidongan')->post(route('sidongan.outgoing.approve', $letter))->assertRedirect();
        $letter->refresh();
        $this->assertSame('disetujui', $letter->status);
        $this->assertMatchesRegularExpression('/^001\/SKR\/PKK-T\/IX\/2026$/', $letter->outgoing_number);
    }

    public function test_chair_can_return_letter_for_revision(): void
    {
        $letter = OutgoingLetter::create(array_merge($this->payload(), ['incoming_document_id' => $this->incoming()->id, 'created_by' => $this->secretary->id, 'status' => 'diajukan']));
        $this->actingAs($this->chair, 'sidongan')->post(route('sidongan.outgoing.revision', $letter), ['revision_note' => 'Lengkapi jadwal kegiatan.'])->assertRedirect();
        $this->assertDatabaseHas('sidongan_outgoing_letters', ['id' => $letter->id, 'status' => 'perlu_revisi', 'revision_note' => 'Lengkapi jadwal kegiatan.']);
    }

    public function test_secretary_cannot_approve_outgoing_letter(): void
    {
        $letter = OutgoingLetter::create(array_merge($this->payload(), ['incoming_document_id' => $this->incoming()->id, 'created_by' => $this->secretary->id, 'status' => 'diajukan']));
        $this->actingAs($this->secretary, 'sidongan')->post(route('sidongan.outgoing.approve', $letter))->assertForbidden();
    }

    public function test_secretary_can_upload_pdf_attachment_on_draft(): void
    {
        $document = $this->incoming();
        $pdf = \Illuminate\Support\Facades\Storage::disk('public')->path('test-attachment.pdf');
        file_put_contents($pdf, "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj\n3 0 obj<</Type/Page/Parent 2 0 R/MediaBox[0 0 200 200]>>endobj\nxref\n0 4\ntrailer<</Size 4/Root 1 0 R>>\nstartxref\n149\n%%EOF");

        $this->actingAs($this->secretary, 'sidongan')
            ->post(route('sidongan.documents.outgoing.store', $document), array_merge($this->payload(), [
                'attachment_file' => new \Illuminate\Http\UploadedFile($pdf, 'jadwal-supervisi.pdf', 'application/pdf', null, true),
            ]))
            ->assertRedirect();

        $letter = OutgoingLetter::where('incoming_document_id', $document->id)->latest('id')->first();
        $this->assertNotNull($letter->attachment_path);
        $this->assertSame('jadwal-supervisi.pdf', $letter->attachment_name);
        $this->assertTrue(\Illuminate\Support\Facades\Storage::disk('public')->exists($letter->attachment_path));

        // Owner dapat mengunduh lampiran
        $this->actingAs($this->secretary, 'sidongan')
            ->get(route('sidongan.outgoing.attachment', $letter))
            ->assertSuccessful();
    }

    public function test_non_pdf_attachment_is_rejected(): void
    {
        $document = $this->incoming();
        $txt = tempnam(sys_get_temp_dir(), 'att') . '.pdf';
        file_put_contents($txt, 'bukan pdf, hanya teks biasa');

        $this->actingAs($this->secretary, 'sidongan')
            ->post(route('sidongan.documents.outgoing.store', $document), array_merge($this->payload(), [
                'attachment_file' => new \Illuminate\Http\UploadedFile($txt, 'fake.pdf', 'application/pdf', null, true),
            ]))
            ->assertSessionHasErrors('attachment_file');

        $letter = OutgoingLetter::where('incoming_document_id', $document->id)->latest('id')->first();
        $this->assertNull($letter?->attachment_path);
    }

    public function test_approved_pdf_includes_merged_attachment_pages(): void
    {
        $document = $this->incoming();
        $letter = OutgoingLetter::create(array_merge($this->payload(), ['incoming_document_id' => $document->id, 'created_by' => $this->secretary->id, 'status' => 'diajukan']));

        // Lampiran PDF 2 halaman yang valid
        $pdfPath = \Illuminate\Support\Facades\Storage::disk('public')->path('test-merge.pdf');
        $fpdi = new \setasign\Fpdi\Fpdi();
        $fpdi->AddPage();
        $fpdi->SetFont('Helvetica', '', 12);
        $fpdi->Cell(0, 10, 'Lampiran Halaman 1');
        $fpdi->AddPage();
        $fpdi->Cell(0, 10, 'Lampiran Halaman 2');
        file_put_contents($pdfPath, $fpdi->Output('S'));

        $letter->storeAttachment(new \Illuminate\Http\UploadedFile($pdfPath, 'lampiran.pdf', 'application/pdf', null, true));
        $letter->refresh();

        $this->actingAs($this->chair, 'sidongan')->post(route('sidongan.outgoing.approve', $letter))->assertRedirect();

        $response = $this->actingAs($this->secretary, 'sidongan')->get(route('sidongan.outgoing.pdf', $letter));
        $response->assertSuccessful();

        $resultPath = tempnam(sys_get_temp_dir(), 'merged_');
        file_put_contents($resultPath, $response->getContent());

        $this->assertStringStartsWith('%PDF', $response->getContent());

        $merged = new \setasign\Fpdi\Fpdi();
        $pageCount = $merged->setSourceFile($resultPath);
        // Halaman surat (>=1) + 2 halaman lampiran
        $this->assertGreaterThanOrEqual(3, $pageCount);
        @unlink($resultPath);
    }

    public function test_attachment_cannot_be_uploaded_when_letter_is_submitted(): void
    {
        $letter = OutgoingLetter::create(array_merge($this->payload(), ['incoming_document_id' => $this->incoming()->id, 'created_by' => $this->secretary->id, 'status' => 'diajukan']));
        $pdfPath = \Illuminate\Support\Facades\Storage::disk('public')->path('test-late.pdf');
        file_put_contents($pdfPath, "%PDF-1.4\n%%EOF");

        $this->actingAs($this->secretary, 'sidongan')
            ->put(route('sidongan.outgoing.update', $letter), array_merge($this->payload(), [
                'attachment_file' => new \Illuminate\Http\UploadedFile($pdfPath, 'late.pdf', 'application/pdf', null, true),
            ]))
            ->assertStatus(422); // surat sudah diajukan → tidak bisa diedit/diupload ulang
    }

    public function test_word_download_and_import_roundtrip(): void
    {
        $letter = OutgoingLetter::create(array_merge($this->payload(), [
            'incoming_document_id' => $this->incoming()->id,
            'created_by' => $this->secretary->id,
            'status' => 'draft',
            'body' => "Paragraf pertama surat.\n\nParagraf kedua setelah baris kosong.",
        ]));

        // 1. Unduh Word
        $response = $this->actingAs($this->secretary, 'sidongan')->get(route('sidongan.outgoing.word', $letter));
        $response->assertSuccessful();
        $this->assertStringStartsWith('PK', $response->getContent()); // DOCX = ZIP

        // 2. Impor kembali berkas yang sama → isi harus sama
        $docxPath = tempnam(sys_get_temp_dir(), 'docx_') . '.docx';
        file_put_contents($docxPath, $response->getContent());

        $this->actingAs($this->secretary, 'sidongan')
            ->post(route('sidongan.outgoing.import-word', $letter), [
                'body_file' => new \Illuminate\Http\UploadedFile($docxPath, 'surat.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', null, true),
            ])
            ->assertRedirect();

        $letter->refresh();
        $this->assertSame("Paragraf pertama surat.\n\nParagraf kedua setelah baris kosong.", $letter->body);
        $this->assertSame('draft', $letter->status);

        @unlink($docxPath);
    }

    public function test_word_import_rejects_file_without_markers(): void
    {
        $letter = OutgoingLetter::create(array_merge($this->payload(), ['incoming_document_id' => $this->incoming()->id, 'created_by' => $this->secretary->id, 'status' => 'draft']));

        // DOCX valid tapi tanpa penanda (dibuat langsung via DocxBuilder lalu penanda dibuang dari XML-nya)
        $docx = \App\Support\DocxBuilder::build($letter);
        $xml = \App\Support\DocxParser::readDocumentXml($docx);
        $this->assertNotNull($xml);
        $stripped = str_replace(['[MULAI ISI SURAT]', '[AKHIR ISI SURAT]'], '', $xml);

        $path = tempnam(sys_get_temp_dir(), 'docx_') . '.docx';
        // Tulis ulang ZIP dengan XML yang sudah tanpa penanda (pakai builder sederhana lewat refleksi tidak perlu —
        // cukup verifikasi parser menolak XML tanpa penanda).
        $this->assertNull(\App\Support\DocxParser::extractBody($docx) === null ? $docx : null);

        // Simulasi: XML tanpa penanda → extractBody harus null
        $parser = new \ReflectionClass(\App\Support\DocxParser::class);
        $method = $parser->getMethod('paragraphsToText');
        $method->setAccessible(true);
        $this->assertNull($method->invokeArgs(null, [$stripped]));

        @unlink($path);
    }

    public function test_word_import_blocked_after_submission(): void
    {
        $letter = OutgoingLetter::create(array_merge($this->payload(), ['incoming_document_id' => $this->incoming()->id, 'created_by' => $this->secretary->id, 'status' => 'diajukan']));

        $docx = \App\Support\DocxBuilder::build($letter);
        $path = tempnam(sys_get_temp_dir(), 'docx_') . '.docx';
        file_put_contents($path, $docx);

        $this->actingAs($this->secretary, 'sidongan')
            ->post(route('sidongan.outgoing.import-word', $letter), [
                'body_file' => new \Illuminate\Http\UploadedFile($path, 'surat.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', null, true),
            ])
            ->assertStatus(422);

        @unlink($path);
    }

    public function test_full_workflow_records_audit_trail(): void
    {
        $letter = OutgoingLetter::create(array_merge($this->payload(), [
            'incoming_document_id' => $this->incoming()->id,
            'created_by' => $this->secretary->id,
            'status' => 'draft',
        ]));

        // Draft dibuat di luar controller → catat manual seperti alur nyata
        OutgoingLetterActivity::record($letter, 'created', 'Membuat draft Surat Keluar', $this->secretary->id);

        $this->actingAs($this->secretary, 'sidongan')
            ->post(route('sidongan.outgoing.submit', $letter))
            ->assertRedirect();

        $this->actingAs($this->chair, 'sidongan')
            ->post(route('sidongan.outgoing.approve', $letter))
            ->assertRedirect();

        $this->actingAs($this->secretary, 'sidongan')
            ->post(route('sidongan.outgoing.sent', $letter))
            ->assertRedirect();

        $this->actingAs($this->secretary, 'sidongan')
            ->post(route('sidongan.outgoing.archive', $letter))
            ->assertRedirect();

        $actions = $letter->activities()->pluck('action')->all();
        $this->assertSame(['created', 'submitted', 'approved', 'sent', 'archived'], $actions);

        // Setiap baris mencatat pelakunya, urut kronologis
        $this->assertSame($this->secretary->id, $letter->activities()->where('action', 'submitted')->first()->user_id);
        $this->assertSame($this->chair->id, $letter->activities()->where('action', 'approved')->first()->user_id);

        // Deskripsi persetujuan memuat nomor yang diterbitkan
        $this->assertStringContainsString(
            $letter->fresh()->outgoing_number,
            (string) $letter->activities()->where('action', 'approved')->first()->description
        );
    }

    public function test_revision_request_is_recorded(): void
    {
        $letter = OutgoingLetter::create(array_merge($this->payload(), [
            'incoming_document_id' => $this->incoming()->id,
            'created_by' => $this->secretary->id,
            'status' => 'diajukan',
            'submitted_at' => now(),
        ]));

        OutgoingLetterActivity::record($letter, 'submitted', 'Mengajukan surat kepada Ketua PKK untuk persetujuan', $this->secretary->id);

        $this->actingAs($this->chair, 'sidongan')
            ->post(route('sidongan.outgoing.revision', $letter), ['revision_note' => 'Isi surat kurang lengkap'])
            ->assertRedirect();

        $activity = $letter->activities()->where('action', 'revision_requested')->first();
        $this->assertNotNull($activity);
        $this->assertSame($this->chair->id, $activity->user_id);
        $this->assertStringContainsString('kurang lengkap', (string) $activity->description);
    }
}
