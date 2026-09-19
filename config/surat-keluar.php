<?php

/*
 ============================================================
 Dikembangkan oleh Institut Teknologi Del
 ============================================================
 Template Surat Keluar SIDONGAN.

 Setiap template berisi struktur surat standar berdasarkan
 contoh surat resmi TP PKK Kabupaten Toba. Bagian dalam
 [kurung siku] adalah placeholder yang harus diganti
 Sekretaris sebelum surat diajukan ke Ketua PKK.
 ============================================================
*/

return [

    'supervisi' => [
        'label' => 'Surat Supervisi',
        'description' => 'Pemberitahuan kegiatan supervisi Pokja/Desa beserta jadwal dan timnya.',
        'icon' => 'fa-clipboard-check',
        'color' => 'blue',
        'fields' => [
            'subject' => 'Pemberitahuan Kegiatan Supervisi [nama kegiatan/pokja yang disupervisi]',
            'nature' => 'Penting',
            'attachment_description' => '1 (satu) bendel jadwal dan tim supervisi',
        ],
        'body' => <<<TXT
Dengan hormat,

Sehubungan dengan program kerja TP PKK Kabupaten Toba tahun [tahun], dengan ini kami memberitahukan bahwa TP PKK Kabupaten Toba akan melaksanakan kegiatan supervisi di [lokasi/kecamatan/desa] yang akan dilaksanakan pada:

    Hari/Tanggal : [hari, tanggal kegiatan]
    Waktu        : [pukul]
    Tempat       : [alamat lengkap/tempat kegiatan]

Adapun kegiatan yang akan disupervisi meliputi [uraian kegiatan yang disupervisi].

Sehubungan dengan hal tersebut, kami mengharapkan kehadiran [pihak yang diharapkan hadir] pada kegiatan tersebut.

Demikian disampaikan, atas perhatian dan kerja samanya kami ucapkan terima kasih.
TXT,
        'cc' => '1. Ketua Tim Penggerak PKK Kabupaten Toba;
2. Sekretaris TP PKK Kabupaten Toba;',
    ],

    'kunjungan' => [
        'label' => 'Surat Kunjungan',
        'description' => 'Permohonan/pemberitahuan kunjungan kerja ke instansi atau daerah lain.',
        'icon' => 'fa-people-arrows',
        'color' => 'green',
        'fields' => [
            'subject' => 'Rencana Kunjungan [maksud kunjungan] di [nama instansi/daerah tujuan]',
            'nature' => 'Biasa',
            'attachment_description' => '1 (satu) bendel susunan peserta dan materi kunjungan',
        ],
        'body' => <<<TXT
Dengan hormat,

Dalam rangka [maksud dan tujuan kunjungan], bersama ini kami sampaikan bahwa TP PKK Kabupaten Toba berencana melaksanakan kunjungan ke [nama instansi/daerah tujuan] pada:

    Hari/Tanggal : [hari, tanggal kunjungan]
    Waktu        : [pukul]
    Tempat       : [alamat lengkap/tempat kunjungan]

Kunjungan ini dilaksanakan dalam rangka [uraian tujuan: benchmarking, silaturahmi, penguatan kelembagaan, dll.] dengan diikuti oleh [jumlah] peserta dari TP PKK Kabupaten Toba.

Demikian kami sampaikan, atas izin, perkenan, dan kerja samanya kami ucapkan terima kasih.
TXT,
        'cc' => null,
    ],

    'undangan' => [
        'label' => 'Surat Undangan',
        'description' => 'Undangan rapat, koordinasi, atau kegiatan TP PKK.',
        'icon' => 'fa-envelope-open-text',
        'color' => 'orange',
        'fields' => [
            'subject' => 'Undangan [nama kegiatan/rapat]',
            'nature' => 'Segera',
            'attachment_description' => null,
        ],
        'body' => <<<TXT
Dengan hormat,

Sehubungan dengan [dasar/latar belakang kegiatan], kami mengundang Saudara/i untuk hadir pada kegiatan [nama kegiatan/rapat] yang akan dilaksanakan pada:

    Hari/Tanggal : [hari, tanggal kegiatan]
    Waktu        : [pukul]
    Tempat       : [alamat lengkap/tempat kegiatan]
    Acara        : [susunan acara]

Mengingat pentingnya kegiatan tersebut, kami mengharapkan kehadiran Saudara/i tepat waktu. Apabila Saudara/i berhalangan hadir, mohon agar dapat mewakilkan kepada [nama jabatan perwakilan].

Demikian kami sampaikan, atas perhatian dan kehadirannya kami ucapkan terima kasih.
TXT,
        'cc' => null,
    ],

];
