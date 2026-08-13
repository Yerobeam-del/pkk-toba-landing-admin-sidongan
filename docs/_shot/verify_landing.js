const puppeteer = require('puppeteer-core');
const path = require('path');
const fs = require('fs');
const CHROME = 'C:/Program Files/Google/Chrome/Application/chrome.exe';
const DIR = path.join(__dirname, 'screenshots-landing');

(async () => {
  const browser = await puppeteer.launch({ executablePath: CHROME, headless: 'new', args: ['--no-sandbox', '--disable-dev-shm-usage'] });
  const page = await browser.newPage();
  const files = fs.readdirSync(DIR).filter((f) => f.endsWith('.png')).sort();
  for (const f of files) {
    const r = await page.evaluate(async (b64) => {
      const img = new Image();
      img.src = 'data:image/png;base64,' + b64;
      await img.decode();
      const c = document.createElement('canvas');
      c.width = img.naturalWidth; c.height = img.naturalHeight;
      const ctx = c.getContext('2d');
      ctx.drawImage(img, 0, 0);
      const d = ctx.getImageData(0, 0, c.width, c.height).data;
      const colors = new Set();
      for (let i = 0; i < d.length; i += 8) {
        colors.add((d[i] << 16) | (d[i + 1] << 8) | d[i + 2]);
      }
      return { w: c.width, h: c.height, uniq: colors.size };
    }, fs.readFileSync(path.join(DIR, f)).toString('base64'));
    console.log(f, JSON.stringify(r));
  }
  await browser.close();
  process.exit(0);
})().catch((e) => { console.error(e); process.exit(1); });
