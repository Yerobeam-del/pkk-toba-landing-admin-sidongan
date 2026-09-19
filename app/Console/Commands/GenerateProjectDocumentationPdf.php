<?php

namespace App\Console\Commands;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Console\Command;

class GenerateProjectDocumentationPdf extends Command
{
    protected $signature = 'docs:generate-pdf
                            {--output=docs/dokumentasi-proses-bisnis-arsitektur.pdf : Lokasi file PDF keluaran}';

    protected $description = 'Generate PDF dokumentasi proses bisnis dan arsitektur aplikasi';

    public function handle(): int
    {
        $output = base_path($this->option('output'));
        $directory = dirname($output);

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $pdf = Pdf::loadView('docs.project-documentation-pdf', [
            'generatedAt' => now()->translatedFormat('d F Y H:i'),
        ])
            ->setPaper('a4', 'portrait')
            ->setOption('isRemoteEnabled', false)
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('defaultFont', 'DejaVu Sans');

        $pdf->save($output);

        $this->info('PDF berhasil dibuat: ' . $output);

        return self::SUCCESS;
    }
}
