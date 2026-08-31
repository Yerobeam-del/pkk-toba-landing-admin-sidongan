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
    if (event.target.closest('[data-action="close-reset-password"]')) {
        closeResetPasswordModal();
    }
});
