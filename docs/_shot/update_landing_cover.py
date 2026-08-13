# -*- coding: utf-8 -*-
"""Update the landing manual cover: PKK logo (transparent PNG) + teal-green gradient."""
import base64, os, re

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))  # docs/
MANUAL = os.path.join(ROOT, 'user-manual-landing-page.html')
PKK = os.path.join(ROOT, '..', 'public', 'assets', 'landing', 'images', 'Logo-PKK-Transparent.png')

with open(PKK, 'rb') as f:
    pkk_uri = 'data:image/png;base64,' + base64.b64encode(f.read()).decode('ascii')

with open(MANUAL, encoding='utf-8') as f:
    html = f.read()

# 1) Replace SIDONGAN logo with PKK logo (cover + sidebar)
pat = re.compile(r'<img src="[^"]*" alt="Logo SIDONGAN">')
new_html, n = pat.subn(lambda m: '<img src="' + pkk_uri + '" alt="Logo PKK">', html)
assert n == 2, 'expected 2 logo imgs, found ' + str(n)
html = new_html

# 2) Cover gradient purple -> teal (site primary #0f6b63)
old_grad = 'background:linear-gradient(135deg,#4c1d95 0%,#6d28d9 45%,#8b5cf6 100%);'
new_grad = 'background:linear-gradient(135deg,#0a4d47 0%,#0f6b63 45%,#1ba394 100%);'
assert html.count(old_grad) == 1, 'cover gradient count: ' + str(html.count(old_grad))
html = html.replace(old_grad, new_grad, 1)

# 3) Cover URL color: light purple -> light mint
old_url = '.cover .url{margin-top:1.6rem;font-family:\'JetBrains Mono\',monospace;font-size:.8rem;color:#e9d5ff;letter-spacing:.06em;}'
new_url = '.cover .url{margin-top:1.6rem;font-family:\'JetBrains Mono\',monospace;font-size:.8rem;color:#a7f3d0;letter-spacing:.06em;}'
assert old_url in html, 'cover url style not found'
html = html.replace(old_url, new_url, 1)

# 4) Logo circle shadow: purple tint -> teal tint
old_sh = 'box-shadow:0 16px 40px rgba(30,4,80,.45);'
new_sh = 'box-shadow:0 16px 40px rgba(6,47,43,.45);'
assert old_sh in html, 'logo-circle shadow not found'
html = html.replace(old_sh, new_sh, 1)

with open(MANUAL, 'w', encoding='utf-8', newline='') as f:
    f.write(html)

print('cover updated:', MANUAL)
print('logos replaced:', n, '| gradient:', new_grad[:60])
