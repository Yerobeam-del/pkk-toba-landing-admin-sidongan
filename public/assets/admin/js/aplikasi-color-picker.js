/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * Color picker aplikasi — dipisah dari HTML (color-picker.blade.php)
 * Nilai default warna dibaca dari data-default-color pada
 * elemen #colorPickerSection (diisi Blade, bukan JS inline).
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */

(function () {
    const section = document.getElementById('colorPickerSection');
    if (!section) return;

    const DEFAULT_COLOR = section.dataset.defaultColor || '#0d9488';

    const hexInput  = document.getElementById('colorHex');
    const picker    = document.getElementById('colorPicker');
    const resetBtn  = document.getElementById('colorReset');
    const swatches  = document.querySelectorAll('.color-swatch');

    if (!hexInput || !picker || !resetBtn) return;

    // Rumus turunan warna — HARUS sama dengan yang dipakai landing page
    // (page-aplikasi.blade.php & apps-home.blade.php) agar pratinjau jujur.
    function mixWhite(hex, ratio) {
        const r = parseInt(hex.slice(1, 3), 16);
        const g = parseInt(hex.slice(3, 5), 16);
        const b = parseInt(hex.slice(5, 7), 16);
        const m = (v) => Math.round(v + (255 - v) * ratio);
        return `rgb(${m(r)}, ${m(g)}, ${m(b)})`;
    }

    const isValidHex = (v) => /^#[0-9a-fA-F]{6}$/.test(v);

    function currentColor() {
        const v = (hexInput.value || '').trim().toLowerCase();
        return isValidHex(v) ? v : DEFAULT_COLOR;
    }

    function render() {
        const c = currentColor();

        document.getElementById('cpHeader').style.background =
            `linear-gradient(135deg, ${mixWhite(c, 0.88)}, ${mixWhite(c, 0.96)})`;
        document.getElementById('cpCircle').style.background = mixWhite(c, 0.7);
        document.getElementById('cpIcon').style.background =
            `linear-gradient(135deg, ${c}, ${mixWhite(c, 0.28)})`;
        document.getElementById('cpName').style.color = c;
        document.getElementById('cpBtn').style.background = c;

        picker.value = c;

        // Tandai swatch yang sedang aktif
        swatches.forEach((s) => {
            const aktif = s.dataset.color.toLowerCase() === c && isValidHex((hexInput.value || '').trim());
            s.style.borderColor = aktif ? '#0f172a' : 'transparent';
            s.style.transform = aktif ? 'scale(1.08)' : 'none';
        });
    }

    swatches.forEach((s) => {
        s.addEventListener('click', () => {
            hexInput.value = s.dataset.color.toLowerCase();
            render();
        });
    });

    picker.addEventListener('input', () => {
        hexInput.value = picker.value.toLowerCase();
        render();
    });

    hexInput.addEventListener('input', render);

    resetBtn.addEventListener('click', () => {
        hexInput.value = '';
        render();
    });

    render();
})();
