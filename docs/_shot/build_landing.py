# -*- coding: utf-8 -*-
"""Inject the SIDONGAN manual CSS, logo, and landing screenshots into the landing manual."""
import base64, glob, os, re

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))  # docs/
SIDONGAN = os.path.join(ROOT, 'user-manual-sidongan.html')
LANDING = os.path.join(ROOT, 'user-manual-landing-page.html')
SHOT = os.path.join(ROOT, '_shot', 'screenshots-landing')

with open(SIDONGAN, encoding='utf-8') as f:
    src = f.read()

with open(LANDING, encoding='utf-8') as f:
    html = f.read()

# 1) CSS block
m = re.search(r'<style>(.*?)</style>', src, re.DOTALL)
assert m, 'css not found'
css = m.group(1)
assert '__CSS__' in html
html = html.replace('__CSS__', css, 1)

# 2) Logo (data URI from the sidongan manual cover img)
m = re.search(r'<img src="(data:image/svg\+xml;base64,[^"]+)" alt="Logo SIDONGAN">', src)
assert m, 'logo not found'
logo_uri = m.group(1)
assert html.count('__LOGO__') == 2, html.count('__LOGO__')
html = html.replace('__LOGO__', logo_uri, 2)  # 2 occurrences (sidebar + cover)

# 3) Screenshots
def img_uri(name):
    files = glob.glob(os.path.join(SHOT, name))
    assert files, name
    with open(files[0], 'rb') as f:
        return 'data:image/png;base64,' + base64.b64encode(f.read()).decode('ascii')

SHOTS = ['01-landing-home.png', '02-landing-berita.png', '03-landing-berita-detail.png',
         '04-landing-struktur.png', '05-landing-aplikasi.png', '06-landing-tentang.png',
         '07-landing-sk.png', '08-landing-footer.png']
for i, shot in enumerate(SHOTS, 1):
    token = '__IMG%02d__' % i
    assert token in html, 'token missing: ' + token
    html = html.replace(token, img_uri(shot), 1)

assert '__IMG' not in html and '__CSS__' not in html and '__LOGO__' not in html
with open(LANDING, 'w', encoding='utf-8', newline='') as f:
    f.write(html)

print('landing manual built:', LANDING)
print('figures:', html.count('figure class="shot"'))
