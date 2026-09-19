/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * Buat Surat Keluar — pemilihan template.
 * Data template dibaca dari atribut data-templates pada
 * #templatePicker yang diisi oleh Blade dari config/surat-keluar.php.
 * Klik kartu template akan mengisi subject, body, sifat, lampiran,
 * dan tembusan form secara otomatis.
 * ============================================================ */
(function () {
    'use strict';

    var picker = document.getElementById('templatePicker');
    if (!picker) return;

    var templates = {};
    try {
        templates = JSON.parse(picker.getAttribute('data-templates') || '{}');
    } catch (e) {
        templates = {};
    }

    var subjectInput = document.getElementById('subject');
    var bodyTextarea = document.getElementById('body');
    var natureSelect = document.getElementById('nature');
    var attachmentInput = document.getElementById('attachment_description');
    var ccTextarea = document.getElementById('cc');

    var PLACEHOLDER_RE = /\[[^\]\r\n]*\]/;

    function fillForm(template) {
        if (!template) return;

        var fields = template.fields || {};
        if (subjectInput && fields.subject) subjectInput.value = fields.subject;
        if (natureSelect && fields.nature) {
            var hasOption = Array.prototype.some.call(natureSelect.options, function (opt) {
                return opt.value === fields.nature;
            });
            if (hasOption) natureSelect.value = fields.nature;
        }
        if (attachmentInput && fields.attachment_description) {
            attachmentInput.value = fields.attachment_description;
        }
        if (bodyTextarea && template.body) bodyTextarea.value = template.body;
        if (ccTextarea) ccTextarea.value = template.cc || '';

        if (bodyTextarea) bodyTextarea.scrollTop = 0;
    }

    function markSelected(card) {
        var cards = picker.querySelectorAll('.so-template-card');
        Array.prototype.forEach.call(cards, function (el) {
            el.classList.remove('so-template-active');
        });
        if (card) card.classList.add('so-template-active');
    }

    function clearSelection() {
        markSelected(null);
    }

    function hasPlaceholders(value) {
        return PLACEHOLDER_RE.test(value || '');
    }

    function restoreSubmitButtons() {
        var buttons = form.querySelectorAll('button[type=submit]');
        Array.prototype.forEach.call(buttons, function (btn) {
            btn.disabled = false;
            if (btn.getAttribute('data-original-html')) {
                btn.innerHTML = btn.getAttribute('data-original-html');
            }
        });
    }

    function validateOnSubmit(event) {
        var fieldsToCheck = [
            { el: subjectInput, label: 'Perihal' },
            { el: bodyTextarea, label: 'Isi Surat' }
        ];

        for (var i = 0; i < fieldsToCheck.length; i++) {
            var field = fieldsToCheck[i];
            if (field.el && hasPlaceholders(field.el.value)) {
                event.preventDefault();
                event.stopPropagation();
                restoreSubmitButtons();

                if (window.Toast && typeof window.Toast.show === 'function') {
                    window.Toast.show(
                        'Masih ada bagian [kurung siku] pada ' + field.label.toLowerCase() +
                        ' yang belum diganti. Lengkapi dulu sebelum menyimpan.',
                        'warning',
                        6000
                    );
                }
                field.el.focus();
                return;
            }
        }
    }

    picker.addEventListener('click', function (event) {
        var card = event.target.closest('.so-template-card');
        if (!card) return;

        var key = card.getAttribute('data-template-key');
        if (!templates[key]) return;

        var template = templates[key];

        if (card.classList.contains('so-template-active')) {
            clearSelection();
            if (window.Toast && typeof window.Toast.show === 'function') {
                window.Toast.show('Template dinonaktifkan. Isi surat tidak diubah.', 'info');
            }
            return;
        }

        var bodyHasContent = bodyTextarea && bodyTextarea.value.trim().length > 0;
        var subjectIsDefault = subjectInput && /^Tanggapan:/i.test(subjectInput.value);

        if (bodyHasContent || !subjectIsDefault) {
            var doApply = window.confirm
                ? window.confirm(
                    'Isi form saat ini akan diganti dengan template "' + template.label + '". Lanjutkan?'
                )
                : true;

            if (!doApply) return;
        }

        fillForm(template);
        markSelected(card);

        if (window.Toast && typeof window.Toast.show === 'function') {
            window.Toast.show(
                'Template "' + template.label + '" diterapkan. Jangan lupa ganti bagian [kurung siku].',
                'success',
                6000
            );
        }
    });

    var form = (bodyTextarea && bodyTextarea.closest('form')) ||
        (subjectInput && subjectInput.closest('form')) ||
        (picker.closest('form')) ||
        document.querySelector('form');
    if (form) {
        var submitButtons = form.querySelectorAll('button[type=submit]');
        Array.prototype.forEach.call(submitButtons, function (btn) {
            btn.setAttribute('data-original-html', btn.innerHTML);
        });
        form.addEventListener('submit', validateOnSubmit, true);
    }

    // Kehilangan status aktif bila pengguna mengedit isi secara manual.
    if (bodyTextarea) {
        bodyTextarea.addEventListener('input', clearSelection);
    }
    if (subjectInput) {
        subjectInput.addEventListener('input', clearSelection);
    }
})();
/* Dikembangkan oleh Institut Teknologi Del */
