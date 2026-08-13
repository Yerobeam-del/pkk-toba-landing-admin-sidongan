const puppeteer = require('puppeteer-core');
const path = require('path');
const fs = require('fs');

const CHROME = 'C:/Program Files/Google/Chrome/Application/chrome.exe';
const BASE = 'http://127.0.0.1:8000';
const OUT = path.join(__dirname, 'screenshots');
const PDF = path.join(__dirname, 'surat-contoh.pdf');
fs.mkdirSync(OUT, { recursive: true });

const sleep = (ms) => new Promise((r) => setTimeout(r, ms));

async function shot(page, name) {
  await sleep(700);
  await page.screenshot({ path: path.join(OUT, name), fullPage: true });
  console.log('shot:', name);
}

// Sembunyikan sidebar, footer, dan header sticky agar screenshot halaman
// panjang bersih (footer fixed tidak lagi "lengket" di tengah hasil capture).
async function cleanLong(page) {
  await page.addStyleTag({
    content: `
      .sidebar, .admin-footer, .top-header, .sidebar-overlay { display: none !important; }
      .main-wrapper { margin-left: 0 !important; }
      .content-area { padding: 2rem !important; }
    `,
  });
  await sleep(400);
}

async function goto(page, url) {
  await page.goto(BASE + url, { waitUntil: 'networkidle2', timeout: 90000 });
  await sleep(900);
}

async function setDate(page, selector, value) {
  await page.$eval(selector, (el, v) => {
    el.value = v;
    el.dispatchEvent(new Event('change', { bubbles: true }));
    el.dispatchEvent(new Event('input', { bubbles: true }));
  }, value);
}

async function setVal(page, selector, value) {
  await page.$eval(selector, (el, v) => {
    el.value = v;
    el.dispatchEvent(new Event('change', { bubbles: true }));
    el.dispatchEvent(new Event('input', { bubbles: true }));
  }, value);
}

async function clickButtonByText(page, text) {
  const found = await page.evaluate((t) => {
    const btns = [...document.querySelectorAll('button[type="submit"]')];
    const btn = btns.find((b) => b.textContent.includes(t));
    if (btn) { btn.click(); return true; }
    return false;
  }, text);
  if (!found) throw new Error('button not found: ' + text);
}

async function waitSelect(page, selector, value) {
  await page.waitForFunction((sel, val) => {
    const s = document.querySelector(sel);
    return s && [...s.options].some((o) => o.value === val);
  }, { timeout: 20000 }, selector, value);
  await sleep(400);
}

async function login(ctx, email, password) {
  const page = await ctx.newPage();
  await goto(page, '/sidongan-login');
  await page.type('#email', email);
  await page.type('#password', password);
  await Promise.all([
    page.waitForNavigation({ waitUntil: 'networkidle2', timeout: 90000 }),
    page.click('button.btn-login'),
  ]);
  await sleep(1800);
  return page;
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
    await goto(page, '/sidongan-login');
    await shot(page, '01-login.png');
    await ctx.close();
  }

  let newDocId = null;

  // ---- SEKRETARIS: dashboard, daftar surat, buat surat, detail ----
  {
    const ctx = await browser.createBrowserContext();
    const page = await login(ctx, 'sekretaris@pkk-toba.id', 'password');

    await goto(page, '/sidongan');
    await cleanLong(page);
    await shot(page, '02-dashboard-sekretaris.png');

    await goto(page, '/sidongan/documents');
    await cleanLong(page);
    await shot(page, '03-daftar-surat.png');

    await goto(page, '/sidongan/documents/create');
    await shot(page, '04-buat-surat-baru.png');

    // ---- isi form surat ----
    await setVal(page, '#sender', 'Dinas Pemberdayaan Masyarakat dan Desa Kabupaten Toba');
      await setDate(page, 'input[name="document_date"]', '2026-08-05');
      await setVal(page, 'input[name="document_number"]', '475.2/1023/PMD/2026');
      await setVal(page, 'input[name="subject"]', 'Undangan Rapat Koordinasi Persiapan Peringatan HUT PKK ke-54');
      await setDate(page, 'input[name="agenda_date"]', '2026-08-07');
      await setVal(page, '#suggestion',
        'Mohon surat ini ditindaklanjuti. Agar didisposisikan kepada Ketua Pengurus I dan II serta Bendahara PKK untuk persiapan kegiatan, mengingat batas waktu pelaksanaan yang singkat.');
    const fileInput = await page.$('#fileInput');
    await fileInput.uploadFile(PDF);
    await sleep(1000);
    await shot(page, '05-buat-surat-terisi.png');

    // ---- submit ----
    await Promise.all([
      page.waitForNavigation({ waitUntil: 'networkidle2', timeout: 90000 }),
      page.click('button[type="submit"][form="mainForm"]'),
    ]);
    await sleep(1500);

    // ---- ambil id dokumen baru ----
    await goto(page, '/sidongan/documents');
    newDocId = await page.evaluate(() => {
      const links = [...document.querySelectorAll('tbody a')].map((a) => a.href);
      const m = links.find((h) => /\/sidongan\/documents\/\d+$/.test(h));
      return m ? (m.match(/\/sidongan\/documents\/(\d+)$/) || [])[1] : null;
    });
    console.log('newDocId:', newDocId);

    if (newDocId) {
      await goto(page, '/sidongan/documents/' + newDocId);
      await cleanLong(page);
      await shot(page, '06-detail-surat.png');
    }
    await ctx.close();
  }

  // ---- KETUA: disposisi + lembar disposisi ----
  {
    const ctx = await browser.createBrowserContext();
    const page = await login(ctx, 'ketua@pkk-toba.id', 'password');

    await goto(page, '/sidongan/disposisi');
    await cleanLong(page);
    await shot(page, '07-disposisi-index.png');

    if (newDocId) {
      await goto(page, '/sidongan/disposisi/' + newDocId);
      await page.evaluate(() => {
        const labels = [...document.querySelectorAll('label.role-option')];
        const pick = (t) => labels.find((l) => l.textContent.includes(t));
        const b = pick('Bendahara PKK');
        const p1 = pick('Ketua Pengurus I');
        if (b) b.querySelector('input').click();
        if (p1) p1.querySelector('input').click();
      });
      await sleep(400);
      const checkedRoles = await page.evaluate(() =>
        [...document.querySelectorAll('input[name="target_roles[]"]:checked')].map((i) => i.value));
      console.log('checked roles:', JSON.stringify(checkedRoles));
      await page.select('select[name="action"]', 'Untuk dilaksanakan');
      await setVal(page, '#comment',
        'Mohon berkoordinasi dengan Pokja terkait dan siapkan anggaran kegiatan. Laporan kegiatan disampaikan paling lambat 7 (tujuh) hari setelah kegiatan dilaksanakan.');
      await sleep(500);
      await shot(page, '08-form-disposisi.png');

      await Promise.all([
        page.waitForNavigation({ waitUntil: 'networkidle2', timeout: 90000 }),
        clickButtonByText(page, 'Kirim Disposisi'),
      ]);
      await sleep(1500);

      await goto(page, '/sidongan/documents/' + newDocId + '/disposisi-print');
      await cleanLong(page);
      await shot(page, '09-lembar-disposisi.png');
    }
    await ctx.close();
  }

  // ---- BENDAHARA: lapor kegiatan + form laporan ----
  {
    const ctx = await browser.createBrowserContext();
    const page = await login(ctx, 'bendahara@pkk-toba.id', 'password');

    await goto(page, '/sidongan/lapor-kegiatan');
    await cleanLong(page);
    await shot(page, '10-lapor-kegiatan-bendahara.png');

    if (newDocId) {
      await goto(page, '/sidongan/lapor-kegiatan/create/' + newDocId);
      await setDate(page, 'input[name="kegiatan_tanggal"]', '2026-08-12');
      await setVal(page, '#startTime', '09:00');
      await setVal(page, '#endTime', '12:00');

      await page.select('#provinsiSelect', 'SUMATERA UTARA');
      await waitSelect(page, '#kabupatenSelect', 'KABUPATEN TOBA');
      await page.select('#kabupatenSelect', 'KABUPATEN TOBA');
      await waitSelect(page, '#kecamatanSelect', 'BALIGE');
      await page.select('#kecamatanSelect', 'BALIGE');
      await waitSelect(page, '#kelurahanSelect', 'BALIGE');
      await page.select('#kelurahanSelect', 'BALIGE');

      await setVal(page, 'textarea[name="alamat_lengkap"]',
        'Aula Kantor Bupati Toba, Jl. DR. Sutomo No. 1, Balige, Kabupaten Toba, Sumatera Utara');
      await setVal(page, 'textarea[name="deskripsi"]',
        'Rapat koordinasi dihadiri oleh Ketua, Sekretaris, Bendahara, dan Ketua Pengurus I–IV. Agenda rapat membahas pembagian tugas panitia, penyusunan anggaran, dan jadwal kegiatan HUT PKK ke-54. Hasil rapat dituangkan dalam berita acara dan menjadi dasar pelaksanaan kegiatan berikutnya.');
      await sleep(800);
      await cleanLong(page);
      await shot(page, '11-form-laporan-terisi.png');

      await Promise.all([
        page.waitForNavigation({ waitUntil: 'networkidle2', timeout: 90000 }),
        clickButtonByText(page, 'Kirim Laporan'),
      ]);
      await sleep(1500);
    }
    await ctx.close();
  }

  // ---- KETUA: verifikasi ----
  {
    const ctx = await browser.createBrowserContext();
    const page = await login(ctx, 'ketua@pkk-toba.id', 'password');

    await goto(page, '/sidongan/verifikasi');
    await cleanLong(page);
    await shot(page, '12-verifikasi-index.png');

    const reportFormUrl = await page.evaluate(() => {
      const links = [...document.querySelectorAll('a[href*="/sidongan/verifikasi/"]')].map((a) => a.href);
      return links.find((h) => /\/verifikasi\/\d+\/form$/.test(h)) || null;
    });
    console.log('reportFormUrl:', reportFormUrl);

    if (reportFormUrl) {
      await page.goto(reportFormUrl, { waitUntil: 'networkidle2', timeout: 90000 });
      await sleep(1200);
      await setVal(page, 'textarea[name="catatan_verifikasi"]',
        'Laporan telah sesuai dengan disposisi dan dokumentasi lengkap. Disetujui.');
      await sleep(500);
      await shot(page, '13-verifikasi-form.png');

      await Promise.all([
        page.waitForNavigation({ waitUntil: 'networkidle2', timeout: 90000 }),
        clickButtonByText(page, 'Simpan Keputusan'),
      ]);
      await sleep(1200);
    }
    await ctx.close();
  }

  // ---- SEKRETARIS: arsip ----
  {
    const ctx = await browser.createBrowserContext();
    const page = await login(ctx, 'sekretaris@pkk-toba.id', 'password');
    await goto(page, '/sidongan/arsip');
    await cleanLong(page);
    await shot(page, '14-arsip-surat.png');
    await ctx.close();
  }

  await browser.close();
  console.log('DONE');
  process.exit(0);
})().catch((err) => {
  console.error('ERROR:', err);
  process.exit(1);
});
