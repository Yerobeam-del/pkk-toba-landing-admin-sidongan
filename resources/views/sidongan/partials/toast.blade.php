{{-- Toast Notification System --}}
{{-- Berkasnya di assets/shared karena dipakai bersama Admin Panel & SIDONGAN --}}
<script src="{{ asset('assets/shared/js/toast.js') }}"></script>

{{-- Auto Show Toast from Session --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            Toast.success("{{ session('success') }}");
        @endif
        
        @if(session('error'))
            Toast.error("{{ session('error') }}");
        @endif
        
        @if(session('warning'))
            Toast.warning("{{ session('warning') }}");
        @endif
        
        @if(session('info'))
            Toast.info("{{ session('info') }}");
        @endif
    });
</script>