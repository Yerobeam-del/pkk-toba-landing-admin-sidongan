# -*- coding: utf-8 -*-
"""Build a merged print-ready HTML: page 1 = full-bleed cover, rest = manual content."""
import os

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))  # docs/
MANUAL = os.path.join(ROOT, 'user-manual-sidongan.html')
OUT = os.path.join(ROOT, '_shot', 'manual-print-merged.html')

with open(MANUAL, encoding='utf-8') as f:
    html = f.read()

# Insert named-page rules inside the existing @media print block.
# Anchor: the existing @page line inside print media.
anchor = '        @page{size:A4;margin:16mm 14mm;}'
assert anchor in html, 'print @page anchor not found'
addition = (
    '        @page{size:A4;margin:16mm 14mm;}\n'
    '        @page cover-page{size:A4 portrait;margin:0;}\n'
    '        .cover{page:cover-page;min-height:297mm;box-sizing:border-box;'
    'display:flex;align-items:center;justify-content:center;}\n'
    '        .cover .logo-circle{animation:none;}\n'
)
html = html.replace(anchor, addition, 1)

# Also ensure screen .cover has box-sizing to avoid overflow when flexing
html = html.replace(
    '.cover{\n        background:linear-gradient(135deg,#4c1d95 0%,#6d28d9 45%,#8b5cf6 100%);\n        color:#fff; position:relative; overflow:hidden; padding:3.5rem 2rem 3rem; text-align:center;\n    }',
    '.cover{\n        background:linear-gradient(135deg,#4c1d95 0%,#6d28d9 45%,#8b5cf6 100%);\n        color:#fff; position:relative; overflow:hidden; padding:3.5rem 2rem 3rem; text-align:center; box-sizing:border-box;\n    }',
    1,
)

with open(OUT, 'w', encoding='utf-8') as f:
    f.write(html)

print('merged print HTML written:', OUT)
