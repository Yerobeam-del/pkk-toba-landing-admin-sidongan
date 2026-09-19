<?php

namespace App\Http\Controllers\Sidongan;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\OutgoingLetter;
use App\Models\Notification;
use App\Models\OutgoingLetterActivity;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;
use App\Support\DocxBuilder;
use App\Support\DocxParser;

class OutgoingLetterController extends Controller
{
    private function user()
    {
        return Auth::guard('sidongan')->user();
    }

    private function ensureSecretary(): void
    {
        abort_unless($this->user()?->isSidonganSekretaris(), 403, 'Hanya Sekretaris yang dapat membuat Surat Keluar.');
    }

    private function ensureOwner(OutgoingLetter $letter): void
    {
        $user = $this->user();
        abort_unless($user && ($user->isSuperAdmin() || $letter->created_by === $user->id || $user->isSidonganKetua()), 403, 'Akses Surat Keluar ditolak.');
    }

    /**
     * Validasi aturan lampiran PDF (maks 5 MB). Pesan error konsisten
     * dengan modul Surat Masuk.
     */
    private function attachmentRules(): array
    {
        return [
            'attachment_file' => 'nullable|file|mimes:pdf|max:5120',
        ];
    }

    private function attachmentMessages(): array
    {
        return [
            'attachment_file.file' => 'File lampiran tidak valid',
            'attachment_file.mimes' => 'File lampiran harus berformat PDF',
            'attachment_file.max' => 'Ukuran file lampiran maksimal 5MB',
        ];
    }

    /**
     * Simpan lampiran yang sudah lolos validasi; bila gagal tersimpan
     * (isi file bukan PDF), kembalikan pesan kesalahan.
     */
    private function handleAttachmentUpload(Request $request, OutgoingLetter $letter): ?array
    {
        if (!$request->hasFile('attachment_file')) {
            return null;
        }

        if (!$letter->storeAttachment($request->file('attachment_file'))) {
            return ['attachment_file' => 'File lampiran tidak valid atau bukan PDF yang sebenarnya.'];
        }

        OutgoingLetterActivity::record($letter, 'attachment_added', 'Menambahkan lampiran ' . $letter->attachment_name);

        return null;
    }

    public function index(Request $request)
    {
        $user = $this->user();
        $query = OutgoingLetter::with(['incomingDocument', 'creator', 'approver'])->latest();
        if (!$user->isSidonganKetua()) {
            $query->where('created_by', $user->id);
        }

        // Statistik dihitung sebelum filter agar selalu merepresentasikan keseluruhan
        $statTotal = (clone $query)->count();
        $statMenunggu = (clone $query)->whereIn('status', ['draft', 'diajukan', 'perlu_revisi'])->count();
        $statDisetujui = (clone $query)->whereIn('status', ['disetujui', 'dikirim'])->count();
        $statDiarsipkan = (clone $query)->where('status', 'diarsipkan')->count();

        // Filter pencarian: perihal, penerima, nomor surat keluar, nomor agenda surat masuk
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('subject', 'like', "%{$s}%")
                    ->orWhere('recipient', 'like', "%{$s}%")
                    ->orWhere('outgoing_number', 'like', "%{$s}%")
                    ->orWhereHas('incomingDocument', function ($d) use ($s) {
                        $d->where('agenda_number', 'like', "%{$s}%");
                    });
            });
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter rentang tanggal surat
        if ($request->filled('date_from')) {
            $query->whereDate('letter_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('letter_date', '<=', $request->date_to);
        }

        $allowedPerPages = [5, 10, 15, 25, 50];
        $perPage = (int) $request->input('per_page', 15);
        if (!in_array($perPage, $allowedPerPages, true)) {
            $perPage = 15;
        }

        return view('sidongan.outgoing.index', [
            'letters' => $query->paginate($perPage)->withQueryString(),
            'statTotal' => $statTotal,
            'statMenunggu' => $statMenunggu,
            'statDisetujui' => $statDisetujui,
            'statDiarsipkan' => $statDiarsipkan,
            'allowedPerPages' => $allowedPerPages,
            'currentPerPage' => $perPage,
        ]);
    }

    public function createFromIncoming(Document $document)
    {
        $this->ensureSecretary();
        if (!$this->user()->isSuperAdmin() && $document->created_by !== $this->user()->id) {
            abort(403, 'Surat masuk hanya dapat ditindaklanjuti oleh Sekretaris pembuatnya.');
        }
        return view('sidongan.outgoing.create', [
            'document' => $document,
            'letter' => new OutgoingLetter([
                'recipient' => $document->sender,
                'subject' => 'Tanggapan: ' . ($document->subject ?: $document->title),
                'letter_date' => now()->toDateString(),
                'signatory_name' => config('app.pkk_chairperson_name', 'Ny. Astita Effendi Sintong P. Napitupulu'),
            ]),
            'templates' => config('surat-keluar', []),
        ]);
    }

    public function store(Request $request, Document $document)
    {
        $this->ensureSecretary();
        if (!$this->user()->isSuperAdmin() && $document->created_by !== $this->user()->id) abort(403);
        $data = $request->validate(array_merge([
            'recipient' => 'required|string|max:255',
            'recipient_address' => 'nullable|string|max:2000',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'nature' => 'required|string|max:50',
            'attachment_description' => 'nullable|string|max:255',
            'cc' => 'nullable|string|max:2000',
            'letter_date' => 'required|date',
            'signatory_name' => 'required|string|max:255',
            'signatory_title' => 'required|string|max:255',
        ], $this->attachmentRules()), $this->attachmentMessages());
        $data['incoming_document_id'] = $document->id;
        $data['created_by'] = $this->user()->id;
        $data['status'] = 'draft';
        $letter = OutgoingLetter::create($data);

        OutgoingLetterActivity::record($letter, 'created', 'Membuat draft Surat Keluar dari Surat Masuk ' . ($document->agenda_number ?? '-'));

        if ($errors = $this->handleAttachmentUpload($request, $letter)) {
            return back()->withErrors($errors)->withInput();
        }

        return redirect()->route('sidongan.outgoing.show', $letter)->with('success', 'Draft Surat Keluar berhasil dibuat dari Surat Masuk.');
    }

    public function show(OutgoingLetter $letter)
    {
        $this->ensureOwner($letter);
        $letter->load(['incomingDocument', 'creator', 'approver', 'activities.user']);
        return view('sidongan.outgoing.show', compact('letter'));
    }

    public function edit(OutgoingLetter $letter)
    {
        $this->ensureSecretary();
        $this->ensureOwner($letter);
        abort_unless($letter->isEditable(), 422, 'Surat tidak dapat diedit pada status saat ini.');
        $letter->load('incomingDocument');
        return view('sidongan.outgoing.edit', compact('letter'));
    }

    public function update(Request $request, OutgoingLetter $letter)
    {
        $this->ensureSecretary();
        $this->ensureOwner($letter);
        abort_unless($letter->isEditable(), 422);
        $data = $request->validate(array_merge([
            'recipient' => 'required|string|max:255', 'recipient_address' => 'nullable|string|max:2000',
            'subject' => 'required|string|max:255', 'body' => 'required|string',
            'nature' => 'required|string|max:50', 'attachment_description' => 'nullable|string|max:255',
            'cc' => 'nullable|string|max:2000', 'letter_date' => 'required|date',
            'signatory_name' => 'required|string|max:255', 'signatory_title' => 'required|string|max:255',
        ], $this->attachmentRules()), $this->attachmentMessages());
        $data['status'] = 'draft';
        $wasDraft = $letter->status === 'draft';
        $letter->update($data);

        OutgoingLetterActivity::record($letter, 'updated', $wasDraft ? 'Mengubah draft Surat Keluar' : 'Mengubah surat hasil revisi');

        if ($request->boolean('remove_attachment')) {
            $letter->deleteAttachmentFile();
            $letter->forceFill(['attachment_path' => null, 'attachment_name' => null, 'attachment_type' => null, 'attachment_size' => null])->save();
            OutgoingLetterActivity::record($letter, 'attachment_removed', 'Menghapus lampiran ' . ($letter->attachment_name ?? ''));
        } elseif ($errors = $this->handleAttachmentUpload($request, $letter)) {
            return back()->withErrors($errors)->withInput();
        }

        return redirect()->route('sidongan.outgoing.show', $letter)->with('success', 'Draft Surat Keluar berhasil diperbarui.');
    }

    /**
     * Unduh surat dalam format Word (.docx) agar mudah diedit
     * Sekretaris sebelum diajukan/disetujui.
     */
    public function word(OutgoingLetter $letter)
    {
        $this->ensureOwner($letter);

        $filename = 'Surat-Keluar-' . ($letter->outgoing_number ?: 'draft-' . $letter->id) . '.docx';
        $filename = str_replace(['\\', '/', ':', '*', '?', '"', '<', '>', '|'], '-', $filename);

        return response(DocxBuilder::build($letter), 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Impor ulang isi surat dari berkas .docx hasil edit. Hanya isi
     * di antara penanda yang diambil — kop, meta, dan tanda tangan
     * tetap dari database agar tidak bisa diubah diam-diam.
     */
    public function importWord(Request $request, OutgoingLetter $letter)
    {
        $this->ensureSecretary();
        $this->ensureOwner($letter);
        abort_unless($letter->isEditable(), 422, 'Impor hanya dapat dilakukan pada surat berstatus draft/perlu revisi.');

        $data = $request->validate([
            'body_file' => 'required|file|max:10240',
        ], [
            'body_file.required' => 'Berkas Word (.docx) wajib dipilih',
            'body_file.file' => 'Berkas tidak valid',
            'body_file.max' => 'Ukuran berkas maksimal 10MB',
        ]);

        $body = DocxParser::extractBody(file_get_contents($request->file('body_file')->getRealPath()));

        if ($body === null || trim($body) === '') {
            return back()->withErrors([
                'body_file' => 'Isi surat tidak dapat dibaca dari berkas. Pastikan penanda [MULAI ISI SURAT] dan [AKHIR ISI SURAT] tidak dihapus dan berkas adalah .docx yang valid.',
            ]);
        }

        $letter->update(['body' => $body, 'status' => 'draft']);

        OutgoingLetterActivity::record($letter, 'word_imported', 'Mengimpor isi surat dari berkas Word');

        return back()->with('success', 'Isi surat berhasil diimpor dari berkas Word.');
    }

    public function downloadAttachment(OutgoingLetter $letter)
    {
        $this->ensureOwner($letter);
        abort_unless($letter->hasAttachment(), 404, 'Lampiran tidak ditemukan.');
        abort_unless(Storage::disk('public')->exists($letter->attachment_path), 404, 'File lampiran tidak ditemukan.');

        return Storage::disk('public')->download($letter->attachment_path, $letter->attachment_name ?: 'lampiran-surat-keluar.pdf');
    }

    public function submit(OutgoingLetter $letter)
    {
        $this->ensureSecretary();
        $this->ensureOwner($letter);
        abort_unless($letter->isEditable(), 422, 'Surat sudah diajukan atau diproses.');
        $letter->update(['status' => 'diajukan', 'submitted_at' => now(), 'revision_note' => null]);
        OutgoingLetterActivity::record($letter, 'submitted', 'Mengajukan surat kepada Ketua PKK untuk persetujuan');
        foreach (\App\Models\User::where('sidongan_role', 'ketua')->get() as $ketua) {
            Notification::create(['user_id' => $ketua->id, 'type' => 'outgoing.submitted', 'title' => 'Surat Keluar Menunggu Persetujuan', 'message' => "Surat {$letter->subject} menunggu persetujuan Ketua PKK.", 'related_id' => $letter->id, 'related_type' => self::class]);
            // Email ke Ketua — gagal kirim tidak boleh menggagalkan pengajuan
            try {
                $ketua->notify(new \App\Notifications\OutgoingLetterSubmittedNotification($letter));
            } catch (\Throwable $e) {
                report($e);
            }
        }
        return back()->with('success', 'Surat Keluar berhasil diajukan kepada Ketua PKK.');
    }

    public function approve(OutgoingLetter $letter)
    {
        abort_unless($this->user()?->isSidonganKetua(), 403, 'Hanya Ketua PKK yang dapat menyetujui Surat Keluar.');
        abort_unless($letter->status === 'diajukan', 422, 'Surat belum berada pada tahap persetujuan.');
        $date = $letter->letter_date ?: now();
        $letter->update(['status' => 'disetujui', 'outgoing_number' => $letter->outgoing_number ?: OutgoingLetter::generateNumber($date), 'approved_by' => $this->user()->id, 'approved_at' => now()]);
        OutgoingLetterActivity::record($letter, 'approved', 'Menyetujui surat dan menerbitkan nomor ' . $letter->outgoing_number, $this->user()->id);
        Notification::create(['user_id' => $letter->created_by, 'type' => 'outgoing.approved', 'title' => 'Surat Keluar Disetujui', 'message' => "Surat Keluar {$letter->outgoing_number} telah disetujui Ketua PKK.", 'related_id' => $letter->id, 'related_type' => self::class]);
        return back()->with('success', 'Surat Keluar disetujui dan nomor surat diterbitkan.');
    }

    public function requestRevision(Request $request, OutgoingLetter $letter)
    {
        abort_unless($this->user()?->isSidonganKetua(), 403, 'Hanya Ketua PKK yang dapat meminta revisi.');
        $data = $request->validate(['revision_note' => 'required|string|max:2000']);
        abort_unless($letter->status === 'diajukan', 422);
        $letter->update(['status' => 'perlu_revisi', 'revision_note' => $data['revision_note']]);
        OutgoingLetterActivity::record($letter, 'revision_requested', 'Meminta revisi: "' . $data['revision_note'] . '"', $this->user()->id);
        Notification::create(['user_id' => $letter->created_by, 'type' => 'outgoing.revision', 'title' => 'Revisi Surat Keluar', 'message' => 'Surat Keluar memerlukan revisi: ' . $data['revision_note'], 'related_id' => $letter->id, 'related_type' => self::class]);
        return back()->with('success', 'Surat dikembalikan kepada Sekretaris untuk direvisi.');
    }

    public function pdf(OutgoingLetter $letter)
    {
        $this->ensureOwner($letter);
        abort_unless(in_array($letter->status, ['disetujui', 'dikirim', 'diarsipkan'], true), 403, 'PDF hanya tersedia setelah surat disetujui.');
        $letter->load('incomingDocument');
        $pdf = Pdf::loadView('sidongan.outgoing.pdf', compact('letter'))->setPaper('a4');
        $content = $pdf->output();

        // Gabungkan lampiran PDF pendukung ke PDF final (surat + lampiran
        // dalam satu berkas). Bila penggabungan gagal (lampiran korup dsb.),
        // kembalikan surat saja agar cetak tetap berjalan.
        if ($letter->hasAttachment()) {
            $attachmentDiskPath = Storage::disk('public')->path($letter->attachment_path);
            if (is_file($attachmentDiskPath)) {
                $tmp = tempnam(sys_get_temp_dir(), 'sk_');
                file_put_contents($tmp, $content);
                try {
                    $merged = new Fpdi();
                    $pageCount = $merged->setSourceFile($tmp);
                    for ($i = 1; $i <= $pageCount; $i++) {
                        $template = $merged->importPage($i);
                        $size = $merged->getTemplateSize($template);
                        $merged->AddPage($size['orientation'], [$size['width'], $size['height']]);
                        $merged->useTemplate($template);
                    }
                    $attachmentPageCount = $merged->setSourceFile($attachmentDiskPath);
                    for ($i = 1; $i <= $attachmentPageCount; $i++) {
                        $template = $merged->importPage($i);
                        $size = $merged->getTemplateSize($template);
                        $merged->AddPage($size['orientation'], [$size['width'], $size['height']]);
                        $merged->useTemplate($template);
                    }
                    $content = $merged->Output('S');
                } catch (\Throwable $e) {
                    // Biarkan $content berisi surat tanpa lampiran.
                } finally {
                    @unlink($tmp);
                }
            }
        }

        $filename = ($letter->outgoing_number ?: 'surat-keluar') . '.pdf';

        return response($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    public function markSent(OutgoingLetter $letter)
    {
        $this->ensureSecretary();
        $this->ensureOwner($letter);
        abort_unless($letter->status === 'disetujui', 422);
        $letter->update(['status' => 'dikirim', 'sent_at' => now()]);
        OutgoingLetterActivity::record($letter, 'sent', 'Menandai surat sebagai sudah dikirim');
        return back()->with('success', 'Surat Keluar ditandai sudah dikirim.');
    }

    public function archive(OutgoingLetter $letter)
    {
        $this->ensureSecretary();
        $this->ensureOwner($letter);
        abort_unless(in_array($letter->status, ['dikirim', 'disetujui'], true), 422);
        $letter->update(['status' => 'diarsipkan', 'archived_at' => now()]);
        OutgoingLetterActivity::record($letter, 'archived', 'Mengarsipkan Surat Keluar');
        return back()->with('success', 'Surat Keluar berhasil diarsipkan.');
    }
}
