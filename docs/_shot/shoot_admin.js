const puppeteer = require('puppeteer-core');
const path = require('path');
const fs = require('fs');

const CHROME = 'C:/Program Files/Google/Chrome/Application/chrome.exe';
const BASE = 'http://127.0.0.1:8000';
const OUT = path.join(__dirname, 'screenshots-admin');
fs.mkdirSync(OUT, { recursive: true });

const sleep = (ms) => new Promise((r) => setTimeout(r, ms));

async function shot(page, name) {
  await sleep(700);
  await page.screenshot({ path: path.join(OUT, name), fullPage: true });
  console.log('shot:', name);
}

async function goto(page, url) {
  await page.goto(BASE + url, { waitUntil: 'networkidle2', timeout: 90000 });
  await sleep(900);
}

// Sembunyikan sidebar, footer, dan header sticky agar screenshot halaman panjang bersih
async function cleanLong(page) {
  await page.addStyleTag({
    content: `
      .sidebar, .admin-footer, .top-header { display: none !important; }
      .main-wrapper { margin-left: 0 !important; }
      .content-area { padding: 2rem !important; }
    `,
  });
  await sleep(400);
}

(async () => {
  const browser = await puppeteer.launch({
    executablePath: CHROME,
    headless: 'new',
    defaultViewport: { width: 1440, height: 1000, deviceScaleFactor: 1 },
    args: ['--no-sandbox', '--disable-dev-shm-usage', '--hide-scrollbars'],
  });

  // ---- 01 LOGIN PAGE ----
  {
    const ctx = await browser.createBrowserContext();
    const page = await ctx.newPage();
    await goto(page, '/login');
    await shot(page, '01-admin-login.png');
    await ctx.close();
  }

  // ---- LOGIN as Super Admin ----
  const ctx = await browser.createBrowserContext();
  const page = await ctx.newPage();
  await goto(page, '/login');
  await page.type('#email', 'super.admin@pkk-toba.id');
  await page.type('#password', 'PassKeyPKKTobaDel2026!');
  await Promise.all([
    page.waitForNavigation({ waitUntil: 'networkidle2', timeout: 90000 }),
    page.click('button.btn-login'),
  ]);
  await sleep(1800);

  // ---- 02 DASHBOARD ----
  await goto(page, '/admin');
  await cleanLong(page);
  await shot(page, '02-admin-dashboard.png');

  // ---- 03 HERO SLIDERS (Kelola Beranda) ----
  await goto(page, '/admin/hero-sliders');
  await cleanLong(page);
  await shot(page, '03-admin-hero-sliders.png');

  // ---- 04 STRUKTUR ----
  await goto(page, '/admin/struktur');
  await cleanLong(page);
  await shot(page, '04-admin-struktur.png');

  // ---- 05 APLIKASI ----
  await goto(page, '/admin/aplikasi');
  await cleanLong(page);
  await shot(page, '05-admin-aplikasi.png');

  // ---- 06 BERITA ----
  await goto(page, '/admin/berita');
  await cleanLong(page);
  await shot(page, '06-admin-berita.png');

  // ---- 07 SK & DOKUMEN ----
  await goto(page, '/admin/sk');
  await cleanLong(page);
  await shot(page, '07-admin-sk-dokumen.png');

  // ---- 08 TEMPLATE ----
  await goto(page, '/admin/template');
  await cleanLong(page);
  await shot(page, '08-admin-template.png');

  // ---- 09 TENTANG KAMI ----
  await goto(page, '/admin/tentang');
  await cleanLong(page);
  await shot(page, '09-admin-tentang.png');

  // ---- 10 USER MANAGEMENT ----
  await goto(page, '/admin/user-management');
  await cleanLong(page);
  await shot(page, '10-admin-user-management.png');

  // ---- 11 FORM BUAT USER ----
  await goto(page, '/admin/user-management/create');
  await cleanLong(page);
  await shot(page, '11-admin-user-create.png');

  // ---- 12 DATA SIDONGAN ----
  await goto(page, '/admin/sidongan-data');
  await cleanLong(page);
  await shot(page, '12-admin-sidongan-data.png');

  // ---- 13 DATA SIEDA ----
  await goto(page, '/admin/sieda-data');
  await cleanLong(page);
  await shot(page, '13-admin-sieda-data.png');

  // ---- 14 PROFILE ----
  await goto(page, '/admin/profile');
  await cleanLong(page);
  await shot(page, '14-admin-profile.png');

  await ctx.close();
  await browser.close();
  console.log('DONE');
  process.exit(0);
})().catch((err) => {
  console.error('ERROR:', err);
  process.exit(1);
});
