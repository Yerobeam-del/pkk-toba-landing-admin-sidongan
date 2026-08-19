{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
{{-- Toast Notification System --}}
{{-- Berkasnya di assets/shared karena dipakai bersama Admin Panel & SIDONGAN --}}
<script src="{{ asset('assets/shared/js/toast.js') }}"></script>

{{-- Auto Show Toast from Session --}}
{{-- Pesan flash dibaca dari atribut data-* oleh assets/shared/js/toast-flash.js --}}
<div id="toast-flash"
     data-success="{{ session('success') }}"
     data-error="{{ session('error') }}"
     data-warning="{{ session('warning') }}"
     data-info="{{ session('info') }}"></div>
<script src="{{ asset('assets/shared/js/toast-flash.js') }}"></script>
{{-- Dikembangkan oleh Institut Teknologi Del --}}
