<?php

/*
 * ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * Middleware ini menyuntikkan tanda tangan pengembang secara
 * tersembunyi ke dalam setiap halaman HTML yang dirender.
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InjectDeveloperCredit
{
    /**
     * Sisipkan komentar HTML tersembunyi berisi kredit pengembang
     * sebelum tag </body> pada setiap respons HTML.
     *
     * Komentar ini tidak terlihat di layar, tetapi tetap ada di
     * source halaman sehingga identitas pengembang selalu
     * terdokumentasi, apa pun yang terjadi pada berkas view.
     *
     * Dikembangkan oleh Institut Teknologi Del
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Hanya sentuh respons HTML (bukan JSON, file, redirect, dll.)
        if (! $response instanceof Response) {
            return $response;
        }

        // Ambil objek View SEBELUM getContent() — getContent() merender
        // view lalu MENIMPA $response->original dengan string hasil render,
        // yang membuat assertViewHas()/assertSee dsb. gagal di seluruh test
        // suite (dan API apa pun yang membaca ->original). Objek View
        // dipulihkan setelah setContent agar perilaku runtime tidak berubah.
        $originalView = $response->original ?? null;

        $original = $response->getContent();
        if (! is_string($original) || $original === '') {
            return $response;
        }

        // Pada saat middleware berjalan, header Content-Type belum
        // tentu diisi (Laravel mengisinya saat prepare()). Deteksi
        // HTML dari header bila ada, atau dari isi respons bila
        // header belum diset.
        $contentType = $response->headers->get('Content-Type', '');
        $looksLikeHtml = str_contains($contentType, 'text/html')
            || str_contains($contentType, 'application/xhtml')
            || ($contentType === '' && (
                str_contains($original, '<html')
                || str_contains($original, '<!DOCTYPE')
            ));
        if (! $looksLikeHtml) {
            return $response;
        }

        $credit = "\n"
            . "<!-- ============================================================\n"
            . "     Dikembangkan oleh Institut Teknologi Del\n"
            . "     ============================================================ -->\n";

        if (str_contains($original, '</body>')) {
            $original = str_replace('</body>', $credit . '</body>', $original);
        } else {
            $original .= $credit;
        }

        $response->setContent($original);

        // Pulihkan objek View agar ->original tetap berisi View (bukan string)
        $response->original = $originalView;

        return $response;
    }
}
