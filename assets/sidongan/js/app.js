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