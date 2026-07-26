@props(['href', 'label' => 'Kembali'])

{{--
    Tombol "Kembali" seragam untuk header halaman di Admin Panel.

    Pemakaian:
        <x-admin.back-button :href="route('admin.berita.index')" />
        <x-admin.back-button :href="route('admin.profile.edit')" label="Kembali ke Profil" />

    Dibuat sebagai component (bukan @include) agar scope-nya terisolasi.
    Dengan @include, variabel $label bisa bocor dari @foreach di halaman
    pemanggil dan mengganti teks tombol.

    Selalu mengarah ke tujuan eksplisit (route), bukan history.back(),
    supaya tetap benar walau halaman dibuka langsung lewat URL atau
    setelah form gagal tervalidasi.
--}}
<a href="{{ $href }}" {{ $attributes->merge(['class' => 'btn btn-back']) }}>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <line x1="19" y1="12" x2="5" y2="12"/>
        <polyline points="12 19 5 12 12 5"/>
    </svg>
    <span>{{ $label }}</span>
</a>
