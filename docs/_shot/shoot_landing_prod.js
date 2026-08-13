const puppeteer = require('puppeteer-core');
const path = require('path');
const fs = require('fs');

const CHROME = 'C:/Program Files/Google/Chrome/Application/chrome.exe';
const BASE = 'https://pkktoba.id';
const OUT = path.join(__dirname, 'screenshots-landing');
fs.mkdirSync(OUT, { recursive: true });

const sleep = (ms) => new Promise((r) => setTimeout(r, ms));

async function shot(page, name) {
  await sleep(900);
  await page.screenshot({ path: path.join(OUT, name) });
  console.log('shot:', name);
}

async function goto(page, url) {
  await page.goto(url, { waitUntil: 'networkidle2', timeout: 120000 });
  await sleep(2200);
}

async function clickNav(page, pageId) {
  const clicked = await page.evaluate((id) => {
    const link = document.querySelector(`a[data-page="${id}"]`);
    if (!link) return false;
    link.click();
    return true;
  }, pageId);
  console.log('nav click', pageId, '->', clicked);
  await sleep(3500);
  // if berita: wait for grid to fill
  if (pageId === 'berita') {
    try {
      await page.waitForFunction(() => {
        const g = document.getElementById('newsFullGrid');
        return g && g.querySelectorAll('a').length > 0;
      }, { timeout: 20000 });
      await sleep(2000);
    } catch (e) {
      console.log('WARN: berita grid not filled:', e.message);
    }
  }
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
  await goto(page, BASE + '/');
  await shot(page, '01-landing-home.png');

  // ---- 02 BERITA (via real menu) ----
  await clickNav(page, 'berita');
  const cards = await page.evaluate(() => [...document.querySelectorAll('#newsFullGrid a[href*="/berita/"]')].map((a) => a.getAttribute('href')));
  console.log('berita cards:', cards.length, cards[0]);
  await shot(page, '02-landing-berita.png');

  // ---- 03 DETAIL BERITA ----
  if (cards.length) {
    await Promise.all([
      page.waitForNavigation({ waitUntil: 'networkidle2', timeout: 60000 }).catch(() => {}),
      page.evaluate((href) => document.querySelector(`#newsFullGrid a[href="${href}"]`)?.click(), cards[0]),
    ]);
    await sleep(2500);
    await shot(page, '03-landing-berita-detail.png');
  } else {
    console.log('WARN: no berita cards, skipping detail');
  }

  // ---- 04 STRUKTUR ----
  await goto(page, BASE + '/');
  await clickNav(page, 'struktur');
  await shot(page, '04-landing-struktur.png');

  // ---- 05 APLIKASI ----
  await clickNav(page, 'aplikasi');
  await shot(page, '05-landing-aplikasi.png');

  // ---- 06 TENTANG ----
  await clickNav(page, 'tentang');
  await shot(page, '06-landing-tentang.png');

  // ---- 07 SK & DOKUMEN ----
  await clickNav(page, 'sk');
  await shot(page, '07-landing-sk.png');

  // ---- 08 FOOTER ----
  await goto(page, BASE + '/');
  await page.evaluate(() => window.scrollTo(0, document.body.scrollHeight));
  await sleep(1600);
  await shot(page, '08-landing-footer.png');

  await ctx.close();
  await browser.close();
  console.log('DONE');
  process.exit(0);
})().catch((err) => {
  console.error('ERROR:', err);
  process.exit(1);
});
