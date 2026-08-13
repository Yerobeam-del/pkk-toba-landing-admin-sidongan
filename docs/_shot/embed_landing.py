# -*- coding: utf-8 -*-
"""Update the landing manual figure images with the latest (production) screenshots.

Idempotent: matches each <figure class="shot"> by its <img alt> and replaces the src.
"""
import base64, glob, os, re

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))  # docs/
MANUAL = os.path.join(ROOT, 'user-manual-landing-page.html')
SHOT = os.path.join(ROOT, '_shot', 'screenshots-landing')

FIGMAP = [
    ('Beranda Website PKK', '01-landing-home.png'),
    ('Daftar Berita', '02-landing-berita.png'),
    ('Detail Berita', '03-landing-berita-detail.png'),
    ('Struktur Organisasi di Website', '04-landing-struktur.png'),
    ('Aplikasi dan Layanan', '05-landing-aplikasi.png'),
    ('Tentang Kami', '06-landing-tentang.png'),
    ('SK dan Dokumen', '07-landing-sk.png'),
    ('Footer Website', '08-landing-footer.png'),
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
    pat = re.compile(r'(<img src=\")data:image/png;base64,[A-Za-z0-9+/=]+(\" alt=\"' + re.escape(alt) + r'\")')
    new_html, count = pat.subn(lambda m: m.group(1) + uri + m.group(2), html)
    assert count == 1, 'figure not found for alt: ' + alt
    html = new_html
    n += 1

with open(MANUAL, 'w', encoding='utf-8', newline='') as f:
    f.write(html)

print('landing figures updated:', n)
