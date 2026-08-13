const puppeteer = require('puppeteer-core');
const path = require('path');
const fs = require('fs');

const CHROME = 'C:/Program Files/Google/Chrome/Application/chrome.exe';
const DIR = path.join(__dirname, 'screenshots-landing');

(async () => {
  const browser = await puppeteer.launch({
    executablePath: CHROME,
    headless: 'new',
    defaultViewport: { width: 1200, height: 900 },
    args: ['--no-sandbox', '--disable-dev-shm-usage'],
  });
  const page = await browser.newPage();
  const files = fs.readdirSync(DIR).filter((f) => f.endsWith('.png')).sort();
  for (const f of files) {
    await page.goto('file:///' + path.join(DIR, f).replace(/\\/g, '/'), { waitUntil: 'load' });
    await new Promise((r) => setTimeout(r, 400));
    const info = await page.evaluate(() => {
      const img = document.querySelector('img');
      const c = document.createElement('canvas');
      c.width = img.naturalWidth; c.height = img.naturalHeight;
      const ctx = c.getContext('2d');
      ctx.drawImage(img, 0, 0);
      const d = ctx.getImageData(0, 0, c.width, c.height).data;
      const colors = new Set();
      let nonWhite = 0;
      for (let i = 0; i < d.length; i += 40) {
        const key = d[i] + ',' + d[i + 1] + ',' + d[i + 2];
        colors.add(key);
        if (!(d[i] > 245 && d[i + 1] > 245 && d[i + 2] > 245)) nonWhite++;
      }
      return { w: c.width, h: c.height, colors: colors.size, nonWhitePct: Math.round((nonWhite / (d.length / 40)) * 100) };
    });
    console.log(f, '->', info.w + 'x' + info.h, '| warna:', info.colors, '| non-putih:', info.nonWhitePct + '%');
  }
  await browser.close();
  process.exit(0);
})().catch((e) => { console.error('ERROR:', e.message); process.exit(1); });
