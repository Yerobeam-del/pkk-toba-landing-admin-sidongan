/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * JS halaman: resources/views/sidongan/notifications/index.blade.php
 * (diekstrak dari blok <script> di dalam view)
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */


function markAsRead(notificationId, element) {
    element.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
    element.style.opacity = '0';
    element.style.transform = 'translateX(20px)';
    
    fetch(`/sidongan/notifications/${notificationId}/read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        }
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            setTimeout(() => {
                element.remove();
                let el = document.getElementById('unreadCount');
                if(el) {
                    let count = parseInt(el.textContent) - 1;
                    el.textContent = Math.max(0, count);
                    if(count === 0) {
                        setTimeout(() => location.reload(), 500);
                    }
                }
            }, 300);
        } else {
            element.style.opacity = '1';
            element.style.transform = 'translateX(0)';
        }
    })
    .catch(() => {
        element.style.opacity = '1';
        element.style.transform = 'translateX(0)';
    });
}

// Nama sengaja dibedakan dari markAllAsRead() di app.js yang dipakai
// popup lonceng. Dulu keduanya bernama sama sehingga app.js menimpanya
// dan konfirmasi di halaman ini tidak pernah muncul.
function hapusSemuaNotifikasi() {
    Toast.confirm(
        'Semua notifikasi akan ditandai terbaca dan dihapus dari daftar.',
        { title: 'Hapus Semua Notifikasi?', confirmText: 'Ya, Hapus', cancelText: 'Batal', type: 'danger' }
    ).then(function (setuju) {
        if (!setuju) return;
        {
        fetch('/sidongan/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            }
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                // Animate out all notifications
                document.querySelectorAll('.notif-item').forEach((item, index) => {
                    setTimeout(() => {
                        item.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
                        item.style.opacity = '0';
                        item.style.transform = 'translateX(20px)';
                    }, index * 50);
                });
                
                setTimeout(() => {
                    location.reload();
                }, 500);
            }
        });
        }
    });
}



// ============================================================
// Dikembangkan oleh Institut Teknologi Del
// DELEGATION (menggantikan onclick inline)
// ============================================================
document.addEventListener('click', function (event) {
    const clearBtn = event.target.closest('[data-action="hapus-semua-notifikasi"]');
    if (clearBtn) {
        hapusSemuaNotifikasi();
        return;
    }
    const row = event.target.closest('[data-notif-id]');
    if (row) {
        markAsRead(row.getAttribute('data-notif-id'), row);
    }
});
