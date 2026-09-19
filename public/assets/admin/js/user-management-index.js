/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * Index User Management — dipisah dari HTML.
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */


// ==========================================
// TOGGLE STATUS FUNCTION
// ==========================================
async function toggleStatus(userId, userName, currentStatus) {
    const action = currentStatus ? 'menonaktifkan' : 'mengaktifkan';
    try {
        const confirmed = await Toast.confirm(
            `Apakah Anda yakin ingin <strong>${action}</strong> akun <strong>"${userName}"</strong>?`,
            { title: 'Konfirmasi Perubahan Status', confirmText: 'Ya, Ubah', cancelText: 'Batal', type: 'warning' }
        );
        if (!confirmed) return;

        const response = await fetch(`/admin/user-management/${userId}/toggle-status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') || {}).content || '',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        const data = await response.json();
        if (data.success) {
            Toast.success(data.message);
            setTimeout(() => location.reload(), 1000);
        } else {
            Toast.error(data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        Toast.error('Terjadi kesalahan saat mengubah status akun');
    }
}

// ==========================================
// RESET PASSWORD MODAL
// ==========================================
function showResetPasswordModal(userId, userName) {
    document.getElementById('resetPasswordUserId').value = userId;
    document.getElementById('resetPasswordUserName').textContent = userName;
    document.getElementById('resetPasswordInput').value = '';
    document.getElementById('resetPasswordConfirmInput').value = '';
    document.getElementById('resetPasswordModal').style.display = 'flex';
}

function closeResetPasswordModal() {
    document.getElementById('resetPasswordModal').style.display = 'none';
}

// Close modal on overlay click
document.getElementById('resetPasswordModal').addEventListener('click', function(e) {
    if (e.target === this) closeResetPasswordModal();
});

// Handle form submit
document.getElementById('resetPasswordForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const userId = document.getElementById('resetPasswordUserId').value;
    const password = document.getElementById('resetPasswordInput').value;
    const passwordConfirm = document.getElementById('resetPasswordConfirmInput').value;
    const submitBtn = document.getElementById('resetPasswordSubmitBtn');

    if (password.length < 8) {
        Toast.warning('Password minimal 8 karakter!');
        return;
    }

    if (password !== passwordConfirm) {
        Toast.warning('Konfirmasi password tidak cocok!');
        return;
    }

    submitBtn.disabled = true;
    submitBtn.textContent = 'Menyimpan...';

    try {
        const response = await fetch(`/admin/user-management/${userId}/reset-password`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') || {}).content || '',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                password: password,
                password_confirmation: passwordConfirm
            })
        });

        const data = await response.json();
        
        if (data.success) {
            Toast.success(data.message);
            closeResetPasswordModal();
        } else {
            Toast.error(data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        Toast.error('Terjadi kesalahan saat mereset password');
    } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Reset Password';
    }
});


// ==========================================
// RESEND VERIFIKASI EMAIL PRIBADI (dari baris tabel)
// ==========================================
async function resendPersonalEmailVerification(userId, userName, btn) {
    const confirmed = await Toast.confirm(
        `Kirim ulang link verifikasi ke email pribadi akun <strong>"${userName}"</strong>?`,
        { title: 'Kirim Ulang Verifikasi', confirmText: 'Ya, Kirim', cancelText: 'Batal', type: 'info' }
    );
    if (!confirmed) return;

    const original = btn.getAttribute('title');
    btn.disabled = true;
    try {
        const response = await fetch(`/admin/user-management/${userId}/resend-personal-email-verification`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') || {}).content || '',
                'Accept': 'application/json'
            }
        });
        const data = await response.json();
        const mulaiCooldown = (detik) => {
            let sisa = detik;
            const t = setInterval(() => {
                sisa--;
                if (sisa <= 0) { clearInterval(t); btn.disabled = false; btn.setAttribute('title', original); return; }
                btn.setAttribute('title', `Tunggu ${sisa} detik sebelum kirim ulang`);
            }, 1000);
        };
        if (data.success) {
            Toast.success(data.message);
            // Cooldown klien 60 detik mengikuti aturan server
            mulaiCooldown(60);
        } else if (response.status === 429) {
            // Server menolak (baru saja dikirim) — kunci tombol sesuai sisa detik
            Toast.error(data.message);
            const sisa = parseInt((data.message.match(/Tunggu (\d+)/) || [])[1] || '60', 10);
            mulaiCooldown(sisa);
        } else {
            Toast.error(data.message || 'Gagal mengirim email verifikasi');
            btn.disabled = false;
        }
    } catch (error) {
        console.error('Error:', error);
        Toast.error('Terjadi kesalahan saat mengirim ulang verifikasi');
        btn.disabled = false;
    }
}

// ============================================================
// Dikembangkan oleh Institut Teknologi Del
// DELEGATION (menggantikan onclick inline)
// ============================================================
// ==========================================
// DROPDOWN MENU AKSI (⋮) — kolom Aksi tabel
// Saat terbuka, dropdown dipindah ke <body> dengan position:fixed agar
// tidak terpotong .table-wrapper (overflow-x: auto memotong elemen
// absolut di dalamnya). Saat ditutup, dikembalikan ke induknya.
// ==========================================
function initActionsMenu() {
    document.addEventListener('click', function (event) {
        const trigger = event.target.closest('.um-menu-trigger');
        if (trigger) {
            event.stopPropagation();
            toggleActionsMenu(trigger);
            return;
        }
        // Aksi dipilih dari menu: tutup menu lalu biarkan handler aksi
        // (delegasi di bawah / onclick inline) yang memproses.
        if (event.target.closest('.um-menu-item')) {
            closeAllActionsMenus();
            return;
        }
        // Klik di luar menu & trigger mana pun: tutup dropdown terbuka
        if (!event.target.closest('.um-actions-menu') && !event.target.closest('.um-menu-dropdown')) {
            closeAllActionsMenus();
        }
    });

    // ESC juga menutup menu yang terbuka
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') closeAllActionsMenus();
    });

    // Dropdown "melayang" (fixed) — tutup saat halaman/tabel discroll
    // atau jendela di-resize agar tidak lepas dari barisnya.
    window.addEventListener('scroll', closeAllActionsMenus, { passive: true, capture: true });
    window.addEventListener('resize', closeAllActionsMenus);
}

function toggleActionsMenu(trigger) {
    const menu = trigger.closest('.um-actions-menu');
    if (!menu) return;

    const wasOpen = menu.classList.contains('um-menu-open');
    closeAllActionsMenus();
    if (wasOpen) return;

    const dropdown = menu.querySelector('.um-menu-dropdown');
    dropdown.__ownerMenu = menu;
    document.body.appendChild(dropdown);
    dropdown.style.display = 'flex';

    const triggerRect = trigger.getBoundingClientRect();
    const ddRect = dropdown.getBoundingClientRect();

    // Default: di bawah trigger, rata kanan. Kalau tak muat di viewport,
    // taruh di atas trigger.
    let top = triggerRect.bottom + 6;
    if (top + ddRect.height > window.innerHeight - 8) {
        top = triggerRect.top - ddRect.height - 6;
    }
    let left = triggerRect.right - ddRect.width;
    if (left < 8) left = triggerRect.left;

    dropdown.style.position = 'fixed';
    dropdown.style.top = Math.max(8, top) + 'px';
    dropdown.style.left = Math.max(8, left) + 'px';
    dropdown.style.right = 'auto';

    menu.classList.add('um-menu-open');
    trigger.setAttribute('aria-expanded', 'true');
}

function closeAllActionsMenus() {
    document.querySelectorAll('.um-actions-menu.um-menu-open').forEach(function (menu) {
        menu.classList.remove('um-menu-open');
        const trigger = menu.querySelector('.um-menu-trigger');
        if (trigger) trigger.setAttribute('aria-expanded', 'false');
    });
    // Kembalikan dropdown yang sedang "melayang" di body ke induknya
    document.querySelectorAll('body > .um-menu-dropdown').forEach(function (dropdown) {
        dropdown.style.display = '';
        dropdown.style.position = '';
        dropdown.style.top = '';
        dropdown.style.left = '';
        dropdown.style.right = '';
        if (dropdown.__ownerMenu) dropdown.__ownerMenu.appendChild(dropdown);
    });
}

document.addEventListener('DOMContentLoaded', initActionsMenu);

// ============================================================
// Dikembangkan oleh Institut Teknologi Del
// DELEGATION (menggantikan onclick inline)
// ============================================================
document.addEventListener('click', function (event) {
    const statusBtn = event.target.closest('[data-toggle-status]');
    if (statusBtn) {
        toggleStatus(
            statusBtn.getAttribute('data-toggle-status-id'),
            statusBtn.getAttribute('data-toggle-status-name') || '',
            statusBtn.getAttribute('data-toggle-status') === '1'
        );
        return;
    }
    const resetBtn = event.target.closest('[data-reset-password-id]');
    if (resetBtn) {
        showResetPasswordModal(
            resetBtn.getAttribute('data-reset-password-id'),
            resetBtn.getAttribute('data-reset-password-name') || ''
        );
        return;
    }
    // Kirim ulang verifikasi email pribadi dari baris tabel
    const resendBtn = event.target.closest('[data-resend-pemail-id]');
    if (resendBtn) {
        resendPersonalEmailVerification(
            resendBtn.getAttribute('data-resend-pemail-id'),
            resendBtn.getAttribute('data-resend-pemail-name') || '',
            resendBtn
        );
        return;
    }
    if (event.target.closest('[data-action="close-reset-password"]')) {
        closeResetPasswordModal();
    }
});
