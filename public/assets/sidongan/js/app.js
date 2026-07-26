document.addEventListener('DOMContentLoaded', () => {
    const layout = document.getElementById('adminLayout');
    const toggleBtn = document.getElementById('toggleBtn');
    const overlay = document.getElementById('sidebarOverlay');
    const navItems = document.querySelectorAll('.nav-item');

    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    const isMobile = window.innerWidth <= 1024;

    if (!isMobile && isCollapsed) {
        layout.classList.add('collapsed');
    }

    toggleBtn.addEventListener('click', () => {
        if (window.innerWidth <= 1024) {
            layout.classList.toggle('mobile-open');
        } else {
            layout.classList.toggle('collapsed');
            localStorage.setItem('sidebarCollapsed', layout.classList.contains('collapsed'));
        }
    });

    navItems.forEach(item => {
        item.addEventListener('click', () => {
            if (item.classList.contains('has-submenu')) return;
            if (item.closest('.surat-submenu')) return;
            if (window.innerWidth <= 1024) {
                layout.classList.remove('mobile-open');
            }
        });
    });

    overlay.addEventListener('click', () => {
        layout.classList.remove('mobile-open');
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth > 1024) {
            layout.classList.remove('mobile-open');
            if (localStorage.getItem('sidebarCollapsed') === 'true') {
                layout.classList.add('collapsed');
            } else {
                layout.classList.remove('collapsed');
            }
        } else {
            layout.classList.remove('collapsed');
        }
    });
});

function toggleUserMenu() {
    const menu = document.getElementById('userMenu');
    const arrow = document.getElementById('userMenuArrow');
    const notificationPopup = document.getElementById('notificationPopup');
    
    if (notificationPopup && notificationPopup.classList.contains('show')) {
        notificationPopup.classList.remove('show');
        notificationPopup.style.display = 'none';
    }
    
    if (menu.classList.contains('show')) {
        menu.classList.remove('show');
        menu.style.display = 'none';
        if (arrow) arrow.style.transform = 'rotate(0deg)';
    } else {
        menu.style.display = 'block';
        void menu.offsetWidth;
        menu.classList.add('show');
        if (arrow) arrow.style.transform = 'rotate(180deg)';
    }
}

document.addEventListener('click', function(e) {
    const menu = document.getElementById('userMenu');
    const btn = e.target.closest('.user-profile-btn');
    
    if (!btn && menu && menu.style.display === 'block') {
        menu.style.display = 'none';
        document.getElementById('userMenuArrow').style.transform = 'rotate(0deg)';
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const menu = document.getElementById('userMenu');
        if (menu) {
            menu.style.display = 'none';
            document.getElementById('userMenuArrow').style.transform = 'rotate(0deg)';
        }
    }
});

function toggleNotificationPopup() {
    const popup = document.getElementById('notificationPopup');
    const userMenu = document.getElementById('userMenu');
    const arrow = document.getElementById('userMenuArrow');
    
    if (userMenu && userMenu.classList.contains('show')) {
        userMenu.classList.remove('show');
        userMenu.style.display = 'none';
        if (arrow) arrow.style.transform = 'rotate(0deg)';
    }
    
    if (popup.classList.contains('show')) {
        popup.classList.remove('show');
        popup.style.display = 'none';
    } else {
        popup.style.display = 'block';
        void popup.offsetWidth;
        popup.classList.add('show');
    }
}

function markAllAsRead() {
    fetch('/sidongan/notifications/mark-all-read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Gagal menandai notifikasi sebagai dibaca');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        alert('Terjadi kesalahan');
    });
}

document.addEventListener('click', function(e) {
    const notificationBtn = e.target.closest('button[onclick="toggleNotificationPopup()"]');
    const notificationPopup = document.getElementById('notificationPopup');
    const userMenu = document.getElementById('userMenu');
    const userBtn = e.target.closest('.user-profile-btn');
    
    if (notificationPopup && !notificationBtn && !notificationPopup.contains(e.target)) {
        if (notificationPopup.classList.contains('show')) {
            notificationPopup.classList.remove('show');
            notificationPopup.style.display = 'none';
        }
    }
    
    if (userMenu && !userBtn && !userMenu.contains(e.target)) {
        if (userMenu.classList.contains('show')) {
            userMenu.classList.remove('show');
            userMenu.style.display = 'none';
            const arrow = document.getElementById('userMenuArrow');
            if (arrow) arrow.style.transform = 'rotate(0deg)';
        }
    }
});

function markNotificationReadAndRedirect(notificationId, redirectUrl) {
    fetch(`/sidongan/notifications/${notificationId}/read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const popup = document.getElementById('notificationPopup');
            if (popup) popup.style.display = 'none';
            
            const countEl = document.getElementById('notifCountBadge');
            if (countEl) countEl.style.display = 'none';

            window.location.href = redirectUrl;
        }
    });
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const notificationPopup = document.getElementById('notificationPopup');
        const userMenu = document.getElementById('userMenu');
        
        if (notificationPopup) notificationPopup.style.display = 'none';
        if (userMenu) userMenu.style.display = 'none';
        
        const arrow = document.getElementById('userMenuArrow');
        if (arrow) arrow.style.transform = 'rotate(0deg)';
    }
});

function toggleSuratMenu(event) {
    if (event) {
        event.stopPropagation();
    }
    
    const submenu = document.getElementById('suratSubmenu');
    const arrow = document.getElementById('suratArrow');
    const layout = document.getElementById('adminLayout');
    
    if (!submenu || !arrow) return;
    
    const isCollapsed = layout.classList.contains('collapsed');
    const isMobile = window.innerWidth <= 1024;
    const isSubmenuOpen = submenu.style.display === 'block';
    
    if (isMobile && !layout.classList.contains('mobile-open')) {
        layout.classList.add('mobile-open');
    }
    
    if (isCollapsed && !isMobile) {
        layout.classList.remove('collapsed');
        localStorage.setItem('sidebarCollapsed', 'false');
        
        submenu.style.display = 'block';
        arrow.style.transform = 'rotate(180deg)';
        localStorage.setItem('suratMenuOpen', 'true');
        
    } else {
        if (isSubmenuOpen) {
            submenu.style.display = 'none';
            arrow.style.transform = 'rotate(0deg)';
            localStorage.setItem('suratMenuOpen', 'false');
        } else {
            submenu.style.display = 'block';
            arrow.style.transform = 'rotate(180deg)';
            localStorage.setItem('suratMenuOpen', 'true');
        }
    }
}

// ==========================================================
// PEMILIH WAKTU (sd-timepicker)
// ==========================================================
// Menambahkan panel "pilihan cepat" pada <input type="time">.
// Input aslinya tidak diganti, jadi pengiriman form, validasi peramban,
// dan pemilih waktu bawaan ponsel tetap berjalan seperti biasa.
// Dipasang dengan delegasi event agar komponen yang muncul belakangan
// (mis. setelah render ulang) tetap ikut tertangani.
(function () {
    'use strict';

    const tutupSemua = (kecuali) => {
        document.querySelectorAll('[data-timepicker]').forEach((tp) => {
            if (tp === kecuali) return;
            const panel = tp.querySelector('[data-timepicker-panel]');
            const tombol = tp.querySelector('[data-timepicker-toggle]');
            if (panel) panel.hidden = true;
            if (tombol) tombol.setAttribute('aria-expanded', 'false');
        });
    };

    const tandaiTerpilih = (tp) => {
        const input = tp.querySelector('[data-timepicker-input]');
        if (!input) return;
        tp.querySelectorAll('[data-timepicker-option]').forEach((opt) => {
            const cocok = opt.dataset.timepickerOption === input.value;
            opt.setAttribute('aria-selected', cocok ? 'true' : 'false');
        });
    };

    // Gulirkan panel ke pilihan yang sedang aktif agar tidak perlu mencari
    const gulirKeTerpilih = (tp) => {
        const terpilih = tp.querySelector('[data-timepicker-option][aria-selected="true"]');
        const wadah = tp.querySelector('.sd-timepicker-options');
        if (terpilih && wadah) wadah.scrollTop = terpilih.offsetTop - wadah.clientHeight / 2 + terpilih.clientHeight / 2;
    };

    document.addEventListener('click', function (e) {
        const tombol = e.target.closest('[data-timepicker-toggle]');
        const opsi = e.target.closest('[data-timepicker-option]');
        const didalam = e.target.closest('[data-timepicker]');

        if (tombol) {
            const tp = tombol.closest('[data-timepicker]');
            const panel = tp.querySelector('[data-timepicker-panel]');
            const akanDibuka = panel.hidden;
            tutupSemua(tp);
            panel.hidden = !akanDibuka;
            tombol.setAttribute('aria-expanded', akanDibuka ? 'true' : 'false');
            if (akanDibuka) { tandaiTerpilih(tp); gulirKeTerpilih(tp); }
            return;
        }

        if (opsi) {
            const tp = opsi.closest('[data-timepicker]');
            const input = tp.querySelector('[data-timepicker-input]');
            input.value = opsi.dataset.timepickerOption;
            // Beri tahu skrip lain (mis. penghitung durasi) bahwa nilainya berubah
            input.dispatchEvent(new Event('input', { bubbles: true }));
            input.dispatchEvent(new Event('change', { bubbles: true }));
            tandaiTerpilih(tp);
            tp.querySelector('[data-timepicker-panel]').hidden = true;
            tp.querySelector('[data-timepicker-toggle]').setAttribute('aria-expanded', 'false');
            return;
        }

        // Klik di luar menutup semua panel
        if (!didalam) tutupSemua(null);
    });

    // Esc menutup panel yang terbuka
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') tutupSemua(null);
    });

    // Jaga penanda tetap sinkron bila nilai diubah lewat kolom input langsung
    document.addEventListener('input', function (e) {
        const input = e.target.closest('[data-timepicker-input]');
        if (input) tandaiTerpilih(input.closest('[data-timepicker]'));
    });
})();
