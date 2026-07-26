@props(['href', 'label' => 'Batal'])

{{--
    Tombol "Batal" seragam untuk kaki form create/edit di Admin Panel.

    Pemakaian:
        <x-admin.cancel-button :href="route('admin.berita.index')" />

    Untuk tombol Batal yang menutup modal (bukan berpindah halaman), jangan
    pakai component ini — cukup gunakan kelas `btn btn-cancel` pada <button>
    agar tampilannya sama tapi perilakunya tetap menutup modal.

    Dibuat sebagai component (bukan @include) agar scope-nya terisolasi
    dari variabel halaman pemanggil.
--}}
<a href="{{ $href }}" {{ $attributes->merge(['class' => 'btn btn-cancel']) }}>{{ $label }}</a>
