# -*- coding: utf-8 -*-
"""Build a standalone A4 cover page (front cover only) from the user manual."""
import os, re

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))  # docs/
MANUAL = os.path.join(ROOT, 'user-manual-sidongan.html')
OUT = os.path.join(ROOT, 'sampul-user-manual-sidongan.html')

with open(MANUAL, encoding='utf-8') as f:
    html = f.read()

# 1) Extract the <style> block
m = re.search(r'<style>(.*?)</style>', html, re.DOTALL)
assert m, 'style block not found'
style = m.group(1)

# 2) Extract the cover <section> (starts at <section class="cover" and ends at its closing </section>)
start = html.find('<section class="cover"')
assert start != -1, 'cover section not found'
end = html.find('</section>', start)
assert end != -1
cover = html[start:end + len('</section>')]

# 3) Overrides: exactly one A4 page, no margins, vertically centered, edge-to-edge gradient
overrides = '''
    /* ====== overrides for standalone A4 cover ====== */
    @page { size: A4 portrait; margin: 0; }
    html, body { margin: 0; padding: 0; background: #ffffff; }
    .cover {
        width: 210mm;
        height: 297mm;
        min-height: 297mm;
        box-sizing: border-box;
        padding: 22mm 20mm;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .cover .inner { width: 100%; }
    .cover .logo-circle { animation: none; }
    @media print {
        body { background: #fff; }
        .cover { width: 210mm; height: 297mm; page-break-after: avoid; }
    }
'''

doc = (
    '<!DOCTYPE html>\n'
    '<html lang="id">\n'
    '<head>\n'
    '<meta charset="UTF-8">\n'
    '<meta name="viewport" content="width=device-width, initial-scale=1.0">\n'
    '<title>User Manual SIDONGAN — Sampul</title>\n'
    '<style>\n' + style + '\n' + overrides + '\n'
    '</style>\n'
    '</head>\n'
    '<body>\n'
    + cover + '\n'
    '</body>\n'
    '</html>\n'
)

with open(OUT, 'w', encoding='utf-8') as f:
    f.write(doc)

print('cover HTML written:', OUT)
print('cover section chars:', len(cover))
