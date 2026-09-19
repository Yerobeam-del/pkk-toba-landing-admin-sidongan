<?php

/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * Pembaca dokumen Word (.docx) untuk impor ulang isi Surat Keluar
 * yang sudah diedit Sekretaris di Microsoft Word.
 *
 * DOCX adalah paket ZIP; ekstensi `zip` tidak selalu aktif di
 * environment ini, jadi entry `word/document.xml` dibaca manual:
 * - Central Directory dipindai dari akhir berkas untuk menemukan
 *   offset Local File Header tiap entry.
 * - Data entry dibaca sesuai compression method: 0 (STORE) atau
 *   8 (DEFLATE — hasil simpanan Word; di-inflate via gzinflate).
 *
 * Teks paragraf diekstrak dari <w:t> dan dipisahkan per <w:p>,
 * lalu bagian di antara penanda [MULAI ISI SURAT] dan
 * [AKHIR ISI SURAT] dikembalikan sebagai isi surat baru.
 * ============================================================
 */

namespace App\Support;

class DocxParser
{
    /**
     * Ambil isi surat (teks biasa) dari berkas .docx hasil edit.
     *
     * @return string|null  Null bila berkas bukan DOCX valid atau penanda tidak ditemukan.
     */
    public static function extractBody(string $docxBinary): ?string
    {
        $xml = self::readDocumentXml($docxBinary);
        if ($xml === null) {
            return null;
        }

        $text = self::paragraphsToText($xml);
        if ($text === null) {
            return null;
        }

        return $text;
    }

    /**
     * Baca dan de-kompres entry word/document.xml dari arsip ZIP.
     */
    public static function readDocumentXml(string $binary): ?string
    {
        // End of Central Directory (22 byte tanpa comment):
        // disk(2) cdStart(2) countDisk(2) countTotal(2) size(4) offset(4) commentLen(2)
        $eocdPos = strrpos($binary, "PK\x05\x06");
        if ($eocdPos === false || strlen($binary) - $eocdPos < 22) {
            return null;
        }

        $entries = unpack('vdisk/vcdStart/vcountDisk/vcountTotal/Vsize/Voffset', substr($binary, $eocdPos + 4, 18));

        $cdOffset = (int) $entries['offset'];
        $cdSize = (int) $entries['size'];
        if ($cdOffset + $cdSize > strlen($binary)) {
            return null;
        }

        $cd = substr($binary, $cdOffset, $cdSize);
        $pos = 0;
        $cdLength = strlen($cd);

        while ($pos + 46 <= $cdLength) {
            $sig = substr($cd, $pos, 4);
            if ($sig !== "PK\x01\x02") {
                break;
            }

            // Central directory header: sig(4) verMade(2) verNeed(2) flags(2)
            // method(2) time(2) date(2) crc(4) comp(4) uncomp(4) nameLen(2)
            // extraLen(2) commentLen(2) diskStart(2) internal(2) extAttr(4)
            // localOffset(4) name...
            $sizes = unpack('vmethod/Vcompressed/Vuncompressed', substr($cd, $pos + 10, 14));
            $lens = unpack('vnameLen/vextraLen/vcommentLen', substr($cd, $pos + 28, 6));
            $localOffset = unpack('V', substr($cd, $pos + 42, 4))[1];
            $name = substr($cd, $pos + 46, $lens['nameLen']);

            if ($name === 'word/document.xml') {
                return self::readEntry($binary, (int) $localOffset, (int) $sizes['method'], (int) $sizes['compressed'], (int) $sizes['uncompressed']);
            }

            $pos += 46 + $lens['nameLen'] + $lens['extraLen'] + $lens['commentLen'];
        }

        return null;
    }

    private static function readEntry(string $binary, int $localOffset, int $method, int $compressedSize, int $uncompressedSize): ?string
    {
        // Local File Header: cek signature dan panjang nama/extra (bisa
        // berbeda dari central directory bila flag data-descriptor aktif).
        $header = substr($binary, $localOffset, 30);
        if (strlen($header) < 30 || substr($header, 0, 4) !== "PK\x03\x04") {
            return null;
        }

        // Local file header: sig(4) version(2) flags(2) method(2) time(2)
        // date(2) crc(4) comp(4) uncomp(4) nameLen(2) extraLen(2) name...
        $nameLen = unpack('v', substr($header, 26, 2))[1];
        $extraLen = unpack('v', substr($header, 28, 2))[1];
        $dataStart = $localOffset + 30 + $nameLen + $extraLen;

        // Ukuran dari central directory bisa 0 bila data descriptor dipakai;
        // fallback: pakai sisa berkas untuk DEFLATE (gzinflate berhenti di akhir stream).
        $compressed = substr($binary, $dataStart, $compressedSize > 0 ? $compressedSize : null);
        if ($compressed === '' || $compressed === false) {
            return null;
        }

        if ($method === 0) {
            return $uncompressedSize > 0 ? substr($compressed, 0, $uncompressedSize) : $compressed;
        }

        if ($method === 8) {
            // Stream DEFLATE ZIP terbaca dengan zlib_decode/gzinflate
            // tergantung build PHP (Windows memakai header zlib). Coba
            // keduanya — yang valid menghasilkan teks XML.
            $maxLen = $uncompressedSize > 0 ? $uncompressedSize : 0;
            $inflated = @gzinflate($compressed, $maxLen);
            if ($inflated === false || $inflated === '') {
                $inflated = function_exists('zlib_decode') ? @zlib_decode($compressed, $maxLen) : false;
            }
            return ($inflated === false || $inflated === '') ? null : $inflated;
        }

        return null;
    }

    /**
     * Konversi paragraf <w:p> menjadi teks biasa; kembalikan potongan
     * di antara penanda body bila penanda ada, atau seluruh teks bila tidak.
     */
    private static function paragraphsToText(string $xml): ?string
    {
        if (!preg_match_all('/<w:p[ >].*?<\/w:p>|<w:p\/>/s', $xml, $paraMatches)) {
            return null;
        }

        $lines = [];
        foreach ($paraMatches[0] as $para) {
            preg_match_all('/<w:t(?:[^>]*)>(.*?)<\/w:t>/s', $para, $tMatches);
            $line = implode('', $tMatches[1] ?? []);
            $lines[] = html_entity_decode($line, ENT_QUOTES | ENT_XML1, 'UTF-8');
        }

        $start = null;
        $end = null;
        foreach ($lines as $i => $line) {
            $trimmed = trim($line);
            if ($start === null && $trimmed === DocxBuilder::BODY_START) {
                $start = $i + 1;
            } elseif ($trimmed === DocxBuilder::BODY_END) {
                $end = $i;
                break;
            }
        }

        if ($start === null || $end === null || $end < $start) {
            return null;
        }

        $bodyLines = array_slice($lines, $start, $end - $start);

        // Trim baris kosong beruntun di awal/akhir
        while ($bodyLines && trim($bodyLines[0]) === '') {
            array_shift($bodyLines);
        }
        while ($bodyLines && trim(end($bodyLines)) === '') {
            array_pop($bodyLines);
        }

        return implode("\n", $bodyLines);
    }
}
