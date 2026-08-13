# -*- coding: utf-8 -*-
"""Update the existing figure images in the user manual with the latest screenshots.

Idempotent: matches each <figure class="shot"> by its <img alt> and replaces the src.
"""
import base64, glob, os, re

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))  # docs/
MANUAL = os.path.join(ROOT, 'user-manual-sidongan.html')
SHOT = os.path.join(ROOT, '_shot', 'screenshots')

# alt text -> screenshot file
FIGMAP = [
    ('Halaman Login SIDONGAN', '01-login.png'),
    ('Dashboard SIDONGAN', '02-dashboard-sekretaris.png'),
    ('Daftar Surat SIDONGAN', '03-daftar-surat.png'),
    ('Formulir Buat Surat Masuk terisi', '05-buat-surat-terisi.png'),
    ('Detail Surat', '06-detail-surat.png'),
    ('Arsip Surat', '14-arsip-surat.png'),
    ('Daftar Disposisi Surat', '07-disposisi-index.png'),
    ('Formulir Disposisi terisi', '08-form-disposisi.png'),
    ('Lembar Disposisi cetak', '09-lembar-disposisi.png'),
    ('Daftar Verifikasi Laporan', '12-verifikasi-index.png'),
    ('Formulir Verifikasi Laporan', '13-verifikasi-form.png'),
    ('Daftar Lapor Kegiatan Bendahara', '10-lapor-kegiatan-bendahara.png'),
    ('Formulir Laporan Kegiatan terisi', '11-form-laporan-terisi.png'),
]

def data_uri(name):
    files = glob.glob(os.path.join(SHOT, name))
    assert files, name
    with open(files[0], 'rb') as f:
        return 'data:image/png;base64,' + base64.b64encode(f.read()).decode('ascii')

with open(MANUAL, encoding='utf-8') as f:
    html = f.read()

n = 0
for alt, shot in FIGMAP:
    uri = data_uri(shot)
    # replace src inside the <img ... alt="ALT" ...>
    pat = re.compile(r'(<img src=")data:image/png;base64,[A-Za-z0-9+/=]+(" alt="' + re.escape(alt) + r'")')
    new_html, count = pat.subn(lambda m: m.group(1) + uri + m.group(2), html)
    assert count == 1, 'figure not found for alt: ' + alt
    html = new_html
    n += 1

with open(MANUAL, 'w', encoding='utf-8', newline='') as f:
    f.write(html)

print('figures updated:', n)
