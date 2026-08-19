{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
@php
    // Dipakai bersama oleh form tambah & edit aplikasi.
    // $current = warna tersimpan (boleh null berarti memakai warna default).
    $palette      = \App\Models\Application::getColorPalette();
    $defaultColor = \App\Models\Application::DEFAULT_COLOR;
    $current      = old('color', $current ?? null);
    $pickerValue  = $current ?: $defaultColor;
@endphp

<div id="colorPickerSection" data-default-color="{{ $defaultColor }}" class="u-mb-6">
    <label class="u-label">
        Warna Kartu di Landing Page
    </label>

    <div style="display:grid;grid-template-columns:1fr 280px;gap:1.5rem;align-items:start">
        <div>
            {{-- Palet siap pakai --}}
            <div style="display:flex;flex-wrap:wrap;gap:0.6rem;margin-bottom:1rem">
                @foreach ($palette as $hex => $nama)
                    <button type="button"
                            class="color-swatch"
                            data-color="{{ $hex }}"
                            title="{{ $nama }}"
                            aria-label="Pilih warna {{ $nama }}"
                            style="width:38px;height:38px;border-radius:10px;background:{{ $hex }};border:3px solid transparent;cursor:pointer;padding:0;transition:transform .15s ease,border-color .15s ease">
                    </button>
                @endforeach
            </div>

            {{-- Warna bebas --}}
            <div style="display:flex;align-items:center;gap:0.75rem;flex-wrap:wrap">
                <input type="color" id="colorPicker" value="{{ $pickerValue }}"
                       style="width:52px;height:42px;padding:2px;border:1px solid #e2e8f0;border-radius:10px;cursor:pointer;background:#fff">

                <input type="text" name="color" id="colorHex"
                       value="{{ $current }}"
                       placeholder="{{ $defaultColor }} (default)"
                       maxlength="7" spellcheck="false"
                       class="form-control"
                       style="width:150px;font-family:ui-monospace,monospace;text-transform:lowercase">

                <button type="button" id="colorReset" class="btn-reset-color"
                        style="padding:9px 16px;border:1px solid #e2e8f0;background:#fff;border-radius:10px;cursor:pointer;font-size:0.85rem;font-weight:600;color:var(--text-muted)">
                    Pakai default
                </button>
            </div>

            @error('color')
                <small style="color:#dc2626;display:block;margin-top:0.5rem;font-size:0.8rem">{{ $message }}</small>
            @enderror

            <small style="color:var(--text-muted);display:block;margin-top:0.6rem;font-size:0.8rem">
                Pilih dari palet atau tentukan warna sendiri. Kosongkan untuk memakai
                warna default PKK (<code>{{ $defaultColor }}</code>). Warna ini dipakai di
                halaman Aplikasi dan di beranda.
            </small>
        </div>

        {{-- Pratinjau langsung, memakai rumus turunan warna yang sama dengan landing page --}}
        <div>
            <span style="display:block;font-size:0.8rem;color:var(--text-muted);margin-bottom:0.5rem;font-weight:600">Pratinjau</span>
            <div id="colorPreviewCard"
                 style="border-radius:16px;overflow:hidden;border:1px solid rgba(0,0,0,.06);box-shadow:0 4px 15px rgba(0,0,0,.06);background:#fff">
                <div id="cpHeader" style="padding:1.5rem;text-align:center;position:relative;overflow:hidden">
                    <div id="cpCircle" style="position:absolute;top:-50%;right:-30%;width:140px;height:140px;border-radius:50%;opacity:.45"></div>
                    <div id="cpIcon" style="width:52px;height:52px;border-radius:14px;margin:0 auto;position:relative;z-index:2;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800">
                        A
                    </div>
                </div>
                <div style="padding:1rem 1.25rem 1.25rem">
                    <div id="cpName" style="font-weight:800;font-size:1rem;margin-bottom:.15rem">NAMA APP</div>
                    <div style="font-size:.75rem;color:#94a3b8;margin-bottom:.75rem">Nama panjang aplikasi</div>
                    <div id="cpBtn" style="text-align:center;padding:9px 0;border-radius:12px;color:#fff;font-weight:600;font-size:.8rem">
                        Akses Aplikasi
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- Dikembangkan oleh Institut Teknologi Del --}}

@push('scripts')
<script src="{{ asset('assets/admin/js/aplikasi-color-picker.js') }}"></script>
@endpush
