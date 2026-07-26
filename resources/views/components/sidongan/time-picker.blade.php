@props([
    'name',
    'label',
    'value' => null,
    'required' => true,
    'id' => null,      // dipakai skrip halaman (mis. penghitung durasi)
    'step' => 15,      // kelipatan menit pada daftar pilihan cepat
    'from' => 6,       // jam awal daftar
    'to' => 21,        // jam akhir daftar (inklusif)
])

@php
    $nilai = old($name, $value);
    // id WAJIB dideklarasikan sebagai prop. Kalau dibiarkan masuk ke $attributes,
    // atribut id akan tertulis dua kali dan pemanggil kehilangan id-nya.
    $idInput = $id ?: 'tp-' . $name;
@endphp

{{--
    Pemilih waktu SIDONGAN.

    Nilai sebenarnya tetap disimpan di <input type="time"> asli supaya:
      - form tetap terkirim seperti biasa (name & value tidak berubah),
      - validasi bawaan peramban dan validasi server tetap berlaku,
      - pengguna yang memakai pembaca layar atau keyboard tetap terlayani.

    Yang ditambahkan hanyalah daftar pilihan cepat: satu ketukan untuk jam-jam
    umum, tanpa perlu mengetik atau memutar roda waktu. Di ponsel, mengetuk
    kolomnya tetap memunculkan pemilih waktu bawaan sistem.
--}}
<div class="sd-timepicker" data-timepicker>
    <label for="{{ $idInput }}" class="sd-timepicker-label">
        {{ $label }}@if($required) <span style="color:#ef4444">*</span>@endif
    </label>

    <div class="sd-timepicker-field">
        <input type="time"
               id="{{ $idInput }}"
               name="{{ $name }}"
               value="{{ $nilai }}"
               @if($required) required @endif
               class="sd-timepicker-input"
               data-timepicker-input
               {{ $attributes }}>

        <button type="button"
                class="sd-timepicker-toggle"
                data-timepicker-toggle
                aria-label="Buka pilihan cepat {{ $label }}"
                aria-expanded="false"
                aria-haspopup="listbox"
                title="Pilihan cepat">
            <i class="fas fa-clock" aria-hidden="true"></i>
        </button>
    </div>

    <div class="sd-timepicker-panel" data-timepicker-panel role="listbox" aria-label="Pilihan cepat {{ $label }}" hidden>
        <div class="sd-timepicker-panel-head">Pilihan cepat</div>
        <div class="sd-timepicker-options">
            @for ($j = $from; $j <= $to; $j++)
                @for ($m = 0; $m < 60; $m += $step)
                    @php $jam = sprintf('%02d:%02d', $j, $m); @endphp
                    <button type="button"
                            class="sd-timepicker-option"
                            role="option"
                            aria-selected="{{ $nilai === $jam ? 'true' : 'false' }}"
                            data-timepicker-option="{{ $jam }}">{{ $jam }}</button>
                @endfor
            @endfor
        </div>
    </div>
</div>
