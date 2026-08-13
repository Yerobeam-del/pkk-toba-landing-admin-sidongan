# -*- coding: utf-8 -*-
"""Build standalone A4 cover + merged print-ready HTML for the landing manual."""
import os, re

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))  # docs/
MANUAL = os.path.join(ROOT, 'user-manual-landing-page.html')
COVER_OUT = os.path.join(ROOT, 'sampul-landing-page.html')
MERGED_OUT = os.path.join(ROOT, '_shot', 'manual-print-merged-landing.html')

with open(MANUAL, encoding='utf-8') as f:
    html = f.read()

# ---------------- COVER ----------------
m = re.search(r'<style>(.*?)</style>', html, re.DOTALL)
style = m.group(1)
start = html.find('<section class="cover"')
end = html.find('</section>', start)
cover = html[start:end + len('</section>')]

overrides = '''
    /* ====== overrides for standalone A4 cover ====== */
    @page { size: A4 portrait; margin: 0; }
    html, body { margin: 0; padding: 0; background: #ffffff; }
    .cover {
        width: 210mm; height: 297mm; min-height: 297mm; box-sizing: border-box;
        padding: 22mm 20mm; display: flex; align-items: center; justify-content: center;
    }
    .cover .inner { width: 100%; }
    .cover .logo-circle { animation: none; }
    @media print {
        body { background: #fff; }
        .cover { width: 210mm; height: 297mm; page-break-after: avoid; }
    }
'''
doc = ('<!DOCTYPE html>\n<html lang="id">\n<head>\n<meta charset="UTF-8">\n'
       '<meta name="viewport" content="width=device-width, initial-scale=1.0">\n'
       '<title>User Manual Website PKK — Sampul</title>\n<style>\n' + style + '\n' + overrides + '\n</style>\n'
       '</head>\n<body>\n' + cover + '\n</body>\n</html>\n')
with open(COVER_OUT, 'w', encoding='utf-8') as f:
    f.write(doc)
print('cover written:', COVER_OUT)

# ---------------- MERGED PRINT ----------------
anchor = '        @page{size:A4;margin:16mm 14mm;}'
assert anchor in html, 'print @page anchor not found'
addition = (
    '        @page{size:A4;margin:16mm 14mm;}\n'
    '        @page cover-page{size:A4 portrait;margin:0;}\n'
    '        .cover{page:cover-page;min-height:297mm;box-sizing:border-box;'
    'display:flex;align-items:center;justify-content:center;}\n'
    '        .cover .logo-circle{animation:none;}\n'
)
merged = html.replace(anchor, addition, 1)
merged = merged.replace(
    '.cover{\n        background:linear-gradient(135deg,#0a4d47 0%,#0f6b63 45%,#1ba394 100%);\n        color:#fff; position:relative; overflow:hidden; padding:3.5rem 2rem 3rem; text-align:center;\n    }',
    '.cover{\n        background:linear-gradient(135deg,#0a4d47 0%,#0f6b63 45%,#1ba394 100%);\n        color:#fff; position:relative; overflow:hidden; padding:3.5rem 2rem 3rem; text-align:center; box-sizing:border-box;\n    }',
    1,
)
with open(MERGED_OUT, 'w', encoding='utf-8') as f:
    f.write(merged)
print('merged print written:', MERGED_OUT)
