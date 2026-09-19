<?php

/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * Pembuat dokumen Word (.docx) untuk Surat Keluar SIDONGAN.
 *
 * DOCX adalah paket ZIP berisi XML (OOXML). Ekstensi `zip` tidak
 * selalu aktif di environment ini, jadi arsip ZIP ditulis manual
 * (metode STORE tanpa kompresi — valid secara spesifikasi ZIP dan
 * selalu bisa dibaca Word). CRC32 dan Local File Header dihitung
 * sesuai spesifikasi PKZIP.
 *
 * Isi surat dibungkus penanda teks [MULAI ISI SURAT] dan
 * [AKHIR ISI SURAT] agar DocxParser dapat mengekstrak kembali
 * isi yang sudah diedit Sekretaris di Microsoft Word.
 * ============================================================
 */

namespace App\Support;

use App\Models\OutgoingLetter;

class DocxBuilder
{
    public const BODY_START = '[MULAI ISI SURAT]';
    public const BODY_END = '[AKHIR ISI SURAT]';

    /**
     * Bangun berkas .docx (biner) untuk sebuah Surat Keluar.
     */
    public static function build(OutgoingLetter $letter): string
    {
        $documentXml = self::documentXml($letter);

        $files = [
            '[Content_Types].xml' => self::contentTypesXml(),
            '_rels/.rels' => self::rootRelsXml(),
            'word/document.xml' => $documentXml,
            'word/_rels/document.xml.rels' => self::documentRelsXml(),
            'word/styles.xml' => self::stylesXml(),
        ];

        return self::zip($files);
    }

    // =========================================================
    // ZIP WRITER (metode STORE, tanpa ekstensi zip)
    // =========================================================

    private static function zip(array $files): string
    {
        $local = '';
        $central = '';
        $offset = 0;

        foreach ($files as $name => $content) {
            $nameBytes = $name;
            $crc = crc32($content);
            $size = strlen($content);

            $localHeader = "PK\x03\x04"
                . "\x14\x00"              // version needed (2)
                . "\x00\x00"              // flags (2)
                . "\x00\x00"              // method = STORE (2)
                . "\x00\x00"              // mod time (2)
                . "\x33\x21"              // mod date (2) — nilai tetap, tak dipakai Word
                . pack('V', $crc)         // crc32 (4)
                . pack('V', $size)        // compressed size (4)
                . pack('V', $size)        // uncompressed size (4)
                . pack('v', strlen($nameBytes)) // name length (2)
                . "\x00\x00"              // extra length (2)
                . $nameBytes;

            $local .= $localHeader . $content;

            $centralHeader = "PK\x01\x02"
                . "\x14\x00"              // version made by (2)
                . "\x14\x00"              // version needed (2)
                . "\x00\x00"              // flags (2)
                . "\x00\x00"              // method = STORE (2)
                . "\x00\x00"              // mod time (2)
                . "\x33\x21"              // mod date (2)
                . pack('V', $crc)         // crc32 (4)
                . pack('V', $size)        // compressed size (4)
                . pack('V', $size)        // uncompressed size (4)
                . pack('v', strlen($nameBytes)) // name length (2)
                . "\x00\x00"              // extra length (2)
                . "\x00\x00"              // comment length (2)
                . "\x00\x00"              // disk number start (2)
                . "\x00\x00"              // internal attrs (2)
                . pack('V', 0)            // external attrs (4)
                . pack('V', $offset)      // local header offset (4)
                . $nameBytes;

            $central .= $centralHeader;

            $offset += strlen($localHeader) + $size;
        }

        $end = "PK\x05\x06"
            . "\x00\x00"
            . "\x00\x00"
            . pack('v', count($files))
            . pack('v', count($files))
            . pack('V', strlen($central))
            . pack('V', $offset)
            . "\x00\x00";

        return $local . $central . $end;
    }

    // =========================================================
    // OOXML PIECES
    // =========================================================

    private static function contentTypesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>'
            . '<Override PartName="/word/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.styles+xml"/>'
            . '</Types>';
    }

    private static function rootRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>'
            . '</Relationships>';
    }

    private static function documentRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            . '</Relationships>';
    }

    private static function stylesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<w:styles xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">'
            . '<w:style w:type="paragraph" w:styleId="Normal">'
            . '<w:name w:val="Normal"/>'
            . '<w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="24"/></w:rPr>'
            . '</w:style>'
            . '</w:styles>';
    }

    private static function esc(?string $text): string
    {
        return htmlspecialchars((string) $text, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    private static function p(string $text, string $align = 'both', bool $bold = false, int $sizeHalfPt = 24, string $spacingAfter = '120'): string
    {
        $rPr = '<w:rPr>'
            . '<w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/>'
            . ($bold ? '<w:b/>' : '')
            . '<w:sz w:val="' . $sizeHalfPt . '"/>'
            . '</w:rPr>';

        return '<w:p>'
            . '<w:pPr><w:jc w:val="' . $align . '"/><w:spacing w:after="' . $spacingAfter . '"/></w:pPr>'
            . ($text !== '' ? '<w:r>' . $rPr . '<w:t xml:space="preserve">' . self::esc($text) . '</w:t></w:r>' : '')
            . '</w:p>';
    }

    private static function documentXml(OutgoingLetter $letter): string
    {
        $bodyStartP = str_replace(['<w:jc w:val="both"/>', '<w:t xml:space="preserve">' . self::esc('x') . '</w:t>'], ['<w:jc w:val="left"/>', '<w:t xml:space="preserve">' . self::esc(self::BODY_START) . '</w:t>'], self::p('x', 'both', true));
        $bodyEndP = str_replace(['<w:jc w:val="both"/>', '<w:t xml:space="preserve">' . self::esc('x') . '</w:t>'], ['<w:jc w:val="left"/>', '<w:t xml:space="preserve">' . self::esc(self::BODY_END) . '</w:t>'], self::p('x', 'both', true));

        $paras = [];

        // ---- Kop surat ----
        $paras[] = self::p('PEMBERDAYAAN DAN KESEJAHTERAAN KELUARGA', 'center', true, 26, '0');
        $paras[] = self::p('TIM PENGGERAK PKK KABUPATEN TOBA', 'center', true, 24, '0');
        $paras[] = self::p('Alamat Sekretariat TP PKK Kabupaten Toba', 'center', false, 18, '240');

        // ---- Meta surat ----
        $paras[] = self::p('Nomor      : ' . ($letter->outgoing_number ?: '(nomor diterbitkan setelah disetujui)'), 'left', false, 24, '0');
        $paras[] = self::p('Sifat      : ' . $letter->nature, 'left', false, 24, '0');
        $paras[] = self::p('Lampiran   : ' . ($letter->attachment_description ?: ($letter->hasAttachment() ? $letter->attachment_name : '-')), 'left', false, 24, '0');
        $paras[] = self::p('Perihal    : ' . $letter->subject, 'left', false, 24, '360');

        // ---- Tujuan ----
        $paras[] = self::p('Kepada Yth.', 'left', false, 24, '0');
        $paras[] = self::p($letter->recipient, 'left', true, 24, '0');
        if ($letter->recipient_address) {
            $paras[] = self::p($letter->recipient_address, 'left', false, 24, '360');
        } else {
            $paras[] = self::p('', 'left', false, 24, '360');
        }

        // ---- Isi surat (diantara penanda) ----
        $paras[] = $bodyStartP;
        foreach (preg_split('/\r\n|\r|\n/', (string) $letter->body) as $line) {
            $paras[] = self::p($line);
        }
        $paras[] = $bodyEndP;

        // ---- Tembusan ----
        if ($letter->cc) {
            $paras[] = self::p('Tembusan:', 'left', true, 20, '0');
            foreach (preg_split('/\r\n|\r|\n/', (string) $letter->cc) as $line) {
                if (trim($line) !== '') {
                    $paras[] = self::p(trim($line), 'left', false, 20, '0');
                }
            }
            $paras[] = self::p('', 'left', false, 20, '0');
        }

        // ---- Tanda tangan ----
        $paras[] = self::p('Toba, ' . ($letter->letter_date ? $letter->letter_date->locale('id')->translatedFormat('d F Y') : '-'), 'left', false, 24, '0');
        $paras[] = self::p($letter->signatory_title ?: 'Ketua TP PKK Kabupaten Toba', 'left', false, 24, '0');
        $paras[] = self::p('', 'left', false, 24, '0');
        $paras[] = self::p('', 'left', false, 24, '0');
        $paras[] = self::p('', 'left', false, 24, '0');
        $paras[] = self::p($letter->signatory_name ?: '', 'left', true, 24, '0');

        $sectPr = '<w:sectPr>'
            . '<w:pgSz w:w="11906" w:h="16838"/>'
            . '<w:pgMar w:top="1134" w:right="1134" w:bottom="1134" w:left="1418"/>'
            . '</w:sectPr>';

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">'
            . '<w:body>' . implode('', $paras) . $sectPr . '</w:body>'
            . '</w:document>';
    }
}
