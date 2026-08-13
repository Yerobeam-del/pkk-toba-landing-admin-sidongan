# -*- coding: utf-8 -*-
"""Fix the doubled data-URI prefix on the two SIDONGAN logo imgs in the admin manual."""
import os, re

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))  # docs/
SIDONGAN = os.path.join(ROOT, 'user-manual-sidongan.html')
ADMIN = os.path.join(ROOT, 'user-manual-admin-panel.html')

with open(SIDONGAN, encoding='utf-8') as f:
    src = f.read()

# correct logo URI from the clean sidongan manual
m = re.search(r'<img src="(data:image/svg\+xml;base64,[^"]+)" alt="Logo SIDONGAN">', src)
assert m, 'clean logo not found in sidongan manual'
logo_uri = m.group(1)
assert not logo_uri.startswith('data:image/svg+xml;base64,data:'), 'sidongan logo itself is doubled?'

with open(ADMIN, encoding='utf-8') as f:
    html = f.read()

pat = re.compile(r'<img src="[^"]*" alt="Logo SIDONGAN">')
new_html, n = pat.subn(lambda m: '<img src="' + logo_uri + '" alt="Logo SIDONGAN">', html)
assert n == 2, 'expected 2 logo imgs in admin manual, found ' + str(n)
html = new_html

with open(ADMIN, 'w', encoding='utf-8', newline='') as f:
    f.write(html)

print('admin logo fixed:', n, 'imgs')
