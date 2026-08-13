const puppeteer = require('puppeteer-core');
const path = require('path');
const fs = require('fs');

const CHROME = 'C:/Program Files/Google/Chrome/Application/chrome.exe';
const BASE = 'http://127.0.0.1:8000';
const OUT = path.join(__dirname, 'screenshots-landing');
fs.mkdirSync(OUT, { recursive: true });

const sleep = (ms) => new Promise((r) => setTimeout(r, ms));

async function shot(page, name) {
  await sleep(700);
  await page.screenshot({ path: path.join(OUT, name) }); // viewport
  console.log('shot:', name);
}

async function goto(page, url) {
  await page.goto(BASE + url, { waitUntil: 'networkidle2', timeout: 90000 });
  await sleep(1400);
}

(async () => {
  const browser = await puppeteer.launch({
    executablePath: CHROME,
    headless: 'new',
    defaultViewport: { width: 1440, height: 1000, deviceScaleFactor: 1 },
    args: ['--no-sandbox', '--disable-dev-shm-usage', '--hide-scrollbars'],
  });
  const ctx = await browser.createBrowserContext();
  const page = await ctx.newPage();

  // ---- 01 BERANDA (header + hero) ----
  await goto(page, '/');
  await shot(page, '01-landing-home.png');

  // ---- 02 BERITA ----
  await goto(page, '/#page-berita');
  await shot(page, '02-landing-berita.png');

  // ---- 03 DETAIL BERITA ----
  await goto(page, '/berita/kampanye-iva-test-dan-pembagian-pita-cantik-peduli-iva-test-dan-cegah-kanker-serviks-1780392502-3539');
  await shot(page, '03-landing-berita-detail.png');

  // ---- 04 STRUKTUR ----
  await goto(page, '/#page-struktur');
  await shot(page, '04-landing-struktur.png');

  // ---- 05 APLIKASI ----
  await goto(page, '/#page-aplikasi');
  await shot(page, '05-landing-aplikasi.png');

  // ---- 06 TENTANG ----
  await goto(page, '/#page-tentang');
  await shot(page, '06-landing-tentang.png');

  // ---- 07 SK & TEMPLATE ----
  await goto(page, '/#page-sk');
  await shot(page, '07-landing-sk.png');

  // ---- 08 FOOTER ----
  await goto(page, '/');
  await page.evaluate(() => window.scrollTo(0, document.body.scrollHeight));
  await sleep(1200);
  await shot(page, '08-landing-footer.png');

  await ctx.close();
  await browser.close();
  console.log('DONE');
  process.exit(0);
})().catch((err) => {
  console.error('ERROR:', err);
  process.exit(1);
});
