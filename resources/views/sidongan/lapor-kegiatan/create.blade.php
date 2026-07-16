@extends('sidongan.layouts.app')
@section('title', 'Buat Laporan Kegiatan - SIDONGAN')

@section('content')
@php
    // ✅ Ambil URL kembali dari session
    $backUrl = session('lapor_kegiatan_back_url', route('sidongan.lapor_kegiatan.index'));
    
    // Validasi URL - pastikan bukan halaman create/edit/preview
    if (str_contains($backUrl, '/create') || 
        str_contains($backUrl, '/edit') || 
        str_contains($backUrl, '/disposisi-print')) {
        $backUrl = route('sidongan.lapor_kegiatan.index');
    }
@endphp

<style>
    /* Container tanpa max-width */
    .lapor-container {
        padding: 0 1.5rem;
    }
    
    /* Responsive Grid */
    .responsive-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        align-items: start;
    }
    
    /* Mobile Responsive */
    @media (max-width: 968px) {
        .lapor-container {
            padding: 0 1rem;
        }
        
        .responsive-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        
        .detail-surat-card {
            position: static !important;
            margin-bottom: 1rem;
        }
        
        .form-header h1 {
            font-size: 1.125rem !important;
        }
        
        .form-header p {
            font-size: 0.75rem !important;
        }
        
        .section-title {
            font-size: 0.8rem !important;
        }
        
        .info-row {
            flex-direction: column !important;
            gap: 0.25rem !important;
        }
        
        .info-label, .info-value {
            font-size: 0.75rem !important;
        }
        
        .btn-submit {
            width: 100% !important;
            justify-content: center !important;
        }
        
        .btn-group {
            flex-direction: column-reverse !important;
            gap: 0.5rem !important;
        }
        
        .btn-group > * {
            width: 100% !important;
        }
        
        .location-grid {
            grid-template-columns: 1fr !important;
        }
    }
    
    @media (max-width: 480px) {
        .lapor-container {
            padding: 0 0.75rem;
        }
        
        .form-header h1 {
            font-size: 1rem !important;
        }
        
        .time-grid {
            grid-template-columns: 1fr !important;
        }
    }
</style>

<div class="lapor-container">
    {{-- HEADER --}}
    <div style="background: linear-gradient(135deg, #0891b2, #14b8a6); padding: 1.5rem 2rem; border-radius: 1rem; margin-bottom: 1.5rem; color: white; box-shadow: 0 4px 20px rgba(8, 145, 178, 0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 3rem; height: 3rem; background: rgba(255, 255, 255, 0.25); border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px); box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                    <i class="fas fa-clipboard-list" style="font-size: 1.5rem; color: white;"></i>
                </div>
                <div>
                    <h1 class="form-header" style="font-size: 1.25rem; font-weight: 700; margin: 0 0 0.25rem 0;">Buat Laporan Kegiatan</h1>
                    <p style="font-size: 0.875rem; opacity: 0.95; margin: 0;">Isi data kegiatan yang telah dilaksanakan</p>
                </div>
            </div>
            
            <a href="{{ $backUrl }}" 
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.25rem; background: rgba(255,255,255,0.25); color: white; text-decoration: none; border-radius: 0.5rem; font-weight: 600; transition: all 0.25s ease; white-space: nowrap; backdrop-filter: blur(4px); border: 1px solid rgba(255, 255, 255, 0.3);" 
               onmouseover="this.style.background='rgba(255,255,255,0.35)'; this.style.transform='translateY(-2px)'" 
               onmouseout="this.style.background='rgba(255,255,255,0.25)'; this.style.transform='translateY(0)'">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    {{-- LAYOUT 2 KOLOM --}}
    <div class="responsive-grid">
        
        {{-- KOLOM KIRI: Detail Surat --}}
        @if($document)
        <div class="detail-surat-card" style="background: white; border-radius: 0.75rem; box-shadow: 0 2px 12px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; overflow: hidden; position: sticky; top: 1rem;">
            <div style="background: linear-gradient(135deg, #f0f9ff, #e0f2fe); padding: 1.25rem 1.5rem; border-bottom: 2px solid #bae6fd;">
                <div style="display: flex; gap: 0.75rem; align-items: center; margin-bottom: 0.75rem; flex-wrap: wrap;">
                    <span style="font-size: 0.8rem; font-family: monospace; background: #0891b2; color: white; padding: 0.375rem 0.75rem; border-radius: 0.5rem; font-weight: 700;">
                        {{ $document->agenda_number }}
                    </span>
                    <span style="font-size: 0.8rem; padding: 0.375rem 0.875rem; border-radius: 9999px; font-weight: 600; background: #dbeafe; color: #1e40af;">
                        {{ $document->status === 'berjalan' ? 'Sedang Berjalan' : ucfirst(str_replace('_', ' ', $document->status)) }}
                    </span>
                </div>
                <h2 style="font-size: 1.125rem; font-weight: 700; color: #0c4a6e; margin: 0; line-height: 1.4;">
                    {{ $document->subject ?? $document->title }}
                </h2>
            </div>

            <div style="padding: 1.5rem;">
                <div style="margin-bottom: 1.5rem;">
                    <h3 class="section-title" style="font-size: 0.9rem; font-weight: 700; color: #0891b2; margin: 0 0 1rem 0; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-envelope" style="color: #14b8a6;"></i>
                        Data Surat
                    </h3>
                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        <div class="info-row" style="display: flex; justify-content: space-between; padding-bottom: 0.75rem; border-bottom: 1px solid #f1f5f9;">
                            <span class="info-label" style="font-size: 0.85rem; color: #64748b;">Pengirim</span>
                            <span class="info-value" style="font-size: 0.85rem; font-weight: 500; color: #0f172a; text-align: right; max-width: 60%;">{{ $document->sender }}</span>
                        </div>
                        <div class="info-row" style="display: flex; justify-content: space-between; padding-bottom: 0.75rem; border-bottom: 1px solid #f1f5f9;">
                            <span class="info-label" style="font-size: 0.85rem; color: #64748b;">Nomor Surat</span>
                            <span class="info-value" style="font-size: 0.85rem; font-weight: 500; color: #0f172a;">{{ $document->document_number }}</span>
                        </div>
                        <div class="info-row" style="display: flex; justify-content: space-between; padding-bottom: 0.75rem; border-bottom: 1px solid #f1f5f9;">
                            <span class="info-label" style="font-size: 0.85rem; color: #64748b;">Tanggal Surat</span>
                            <span class="info-value" style="font-size: 0.85rem; font-weight: 500; color: #0f172a;">{{ $document->document_date ? \Carbon\Carbon::parse($document->document_date)->locale('id')->translatedFormat('d M Y') : '-' }}</span>
                        </div>
                        <div class="info-row" style="display: flex; justify-content: space-between;">
                            <span class="info-label" style="font-size: 0.85rem; color: #64748b;">Perihal</span>
                            <span class="info-value" style="font-size: 0.85rem; font-weight: 500; color: #0f172a; text-align: right; max-width: 60%;">{{ $document->subject }}</span>
                        </div>
                    </div>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <h3 class="section-title" style="font-size: 0.9rem; font-weight: 700; color: #0891b2; margin: 0 0 1rem 0; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-clipboard-list" style="color: #14b8a6;"></i>
                        Data Agenda
                    </h3>
                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        <div class="info-row" style="display: flex; justify-content: space-between; padding-bottom: 0.75rem; border-bottom: 1px solid #f1f5f9;">
                            <span class="info-label" style="font-size: 0.85rem; color: #64748b;">Nomor Agenda</span>
                            <span class="info-value" style="font-size: 0.85rem; font-weight: 600; color: #0891b2; font-family: monospace;">{{ $document->agenda_number }}</span>
                        </div>
                        <div class="info-row" style="display: flex; justify-content: space-between; padding-bottom: 0.75rem; border-bottom: 1px solid #f1f5f9;">
                            <span class="info-label" style="font-size: 0.85rem; color: #64748b;">Tanggal Agenda</span>
                            <span class="info-value" style="font-size: 0.85rem; font-weight: 500; color: #0f172a;">{{ $document->created_at->locale('id')->translatedFormat('d M Y') }}</span>
                        </div>
                        <div class="info-row" style="display: flex; justify-content: space-between;">
                            <span class="info-label" style="font-size: 0.85rem; color: #64748b;">Dibuat oleh</span>
                            <span class="info-value" style="font-size: 0.85rem; font-weight: 500; color: #0f172a;">{{ $document->creator->name ?? 'Sekretaris PKK' }}</span>
                        </div>
                    </div>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <span style="display: block; font-size: 0.8rem; color: #64748b; margin-bottom: 0.5rem; font-weight: 600;">Saran Sekretaris:</span>
                    <div style="background: #eff6ff; border-radius: 0.5rem; padding: 1rem; font-size: 0.85rem; color: #1e40af; border: 1px solid #bfdbfe; line-height: 1.6;">
                        {{ $document->suggestion ?? '-' }}
                    </div>
                </div>

                @if($document->file_path)
                <div style="margin-bottom: 1.5rem;">
                    <h3 class="section-title" style="font-size: 0.9rem; font-weight: 700; color: #0891b2; margin: 0 0 0.75rem 0; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-paperclip" style="color: #14b8a6;"></i>
                        Lampiran Surat
                    </h3>
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.5rem; padding: 1rem;">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div style="width: 3rem; height: 3rem; background: #fee2e2; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fas fa-file-pdf" style="color: #ef4444; font-size: 1.25rem;"></i>
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <p style="font-size: 0.875rem; font-weight: 600; color: #0f172a; margin: 0 0 0.125rem 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $document->file_name }}
                                </p>
                                <p style="font-size: 0.75rem; color: #64748b; margin: 0;">
                                    {{ $document->file_size ? round($document->file_size / 1024, 2) . ' KB' : 'File surat' }}
                                </p>
                            </div>
                            <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank" 
                               style="display: inline-flex; align-items: center; justify-content: center; width: 2.5rem; height: 2.5rem; background: #dbeafe; color: #2563eb; border-radius: 0.375rem; text-decoration: none; transition: all 0.2s; flex-shrink: 0;"
                               onmouseover="this.style.background='#bfdbfe'; this.style.transform='translateY(-2px)'" 
                               onmouseout="this.style.background='#dbeafe'; this.style.transform='translateY(0)'"
                               title="Lihat Dokumen">
                                <i class="fas fa-eye" style="font-size: 0.875rem;"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @endif

                @if($document->disposisi_data)
                    @php
                        $dispo = is_string($document->disposisi_data) ? json_decode($document->disposisi_data, true) : $document->disposisi_data;
                    @endphp
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.5rem; padding: 1rem;">
                        <h3 class="section-title" style="font-size: 0.9rem; font-weight: 700; color: #0891b2; margin: 0 0 0.75rem 0; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-share-alt" style="color: #14b8a6;"></i>
                            Disposisi Ketua
                        </h3>
                        
                        <div style="margin-bottom: 0.75rem;">
                            <span style="display: block; font-size: 0.75rem; color: #64748b; margin-bottom: 0.35rem; font-weight: 600;">Didisposisikan ke:</span>
                            <div style="display: flex; flex-wrap: wrap; gap: 0.35rem;">
                                @if(isset($dispo['target_roles']))
                                    @foreach($dispo['target_roles'] as $role)
                                    <span style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.6rem; background: #dbeafe; color: #1e40af; border-radius: 0.375rem; font-size: 0.7rem; font-weight: 600;">
                                        <i class="fas fa-users" style="font-size: 0.6rem;"></i>
                                        {{ ucfirst(str_replace('_', ' ', $role)) }}
                                    </span>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        
                        @if(isset($dispo['action']))
                        <div style="margin-bottom: 0.75rem;">
                            <span style="display: block; font-size: 0.75rem; color: #64748b; margin-bottom: 0.35rem; font-weight: 600;">Tindakan:</span>
                            <span style="display: inline-block; padding: 0.375rem 0.75rem; background: #f3e8ff; color: #7c3aed; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 600;">
                                {{ $dispo['action'] }}
                            </span>
                        </div>
                        @endif
                        
                        @if(isset($dispo['comment']) && $dispo['comment'])
                        <div>
                            <span style="display: block; font-size: 0.75rem; color: #64748b; margin-bottom: 0.35rem; font-weight: 600;">Komentar:</span>
                            <div style="background: white; border: 1px solid #e2e8f0; border-radius: 0.375rem; padding: 0.75rem; font-size: 0.8rem; color: #475569; font-style: italic; line-height: 1.5;">
                                "{{ $dispo['comment'] }}"
                            </div>
                        </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
        @endif

        {{-- KOLOM KANAN: Form Laporan Kegiatan --}}
        <div style="background: white; border-radius: 0.75rem; box-shadow: 0 2px 12px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; overflow: hidden;">
            <div style="padding: 1.25rem 1.5rem; background: linear-gradient(135deg, #dcfce7, #bbf7d0); border-bottom: 2px solid #86efac;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 2.5rem; height: 2.5rem; background: #16a34a; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(22,163,74,0.3);">
                        <i class="fas fa-plus" style="color: white; font-size: 1rem;"></i>
                    </div>
                    <div>
                        <h2 class="form-header" style="font-size: 1.05rem; font-weight: 700; color: #1e293b; margin: 0;">Formulir Laporan Kegiatan</h2>
                        <p style="font-size: 0.8rem; color: #166534; margin: 0;">Lengkapi semua informasi kegiatan</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('sidongan.lapor_kegiatan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                @if($document)
                <input type="hidden" name="document_id" value="{{ $document->id }}">
                @endif

                <div style="padding: 1.5rem;">
                    {{-- Nama Kegiatan --}}
                    <div style="margin-bottom: 1.25rem;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 0.5rem;">
                            Nama Kegiatan <span style="color: #ef4444;">*</span>
                        </label>
                        <input type="text" name="kegiatan_nama" placeholder="Contoh: Rapat Koordinasi Bulanan" required 
                            value="{{ old('kegiatan_nama', $document->subject ?? '') }}"
                            style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.875rem; transition: all 0.2s; box-sizing: border-box;" 
                            onfocus="this.style.borderColor='#0891b2'; this.style.boxShadow='0 0 0 3px rgba(8,145,178,0.1)'" 
                            onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                        @error('kegiatan_nama') <p style="font-size: 0.75rem; color: #ef4444; margin-top: 0.25rem;">{{ $message }}</p> @enderror
                    </div>

                    {{-- Tanggal dan Jam Kegiatan --}}
                    <div class="time-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
                        <div>
                            <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 0.5rem;">
                                Tanggal Kegiatan <span style="color: #ef4444;">*</span>
                            </label>
                            <input type="date" name="kegiatan_tanggal" required value="{{ old('kegiatan_tanggal') }}"
                                style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.875rem; transition: all 0.2s; box-sizing: border-box;" 
                                onfocus="this.style.borderColor='#0891b2'; this.style.boxShadow='0 0 0 3px rgba(8,145,178,0.1)'" 
                                onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                            @error('kegiatan_tanggal') <p style="font-size: 0.75rem; color: #ef4444; margin-top: 0.25rem;">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 0.5rem;">
                                Jam Mulai <span style="color: #ef4444;">*</span>
                            </label>
                            <input type="time" name="start_time" required value="{{ old('start_time') }}"
                                style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.875rem; transition: all 0.2s; box-sizing: border-box;" 
                                onfocus="this.style.borderColor='#0891b2'; this.style.boxShadow='0 0 0 3px rgba(8,145,178,0.1)'" 
                                onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                            @error('start_time') <p style="font-size: 0.75rem; color: #ef4444; margin-top: 0.25rem;">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div style="margin-bottom: 1.25rem;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 0.5rem;">
                            Jam Selesai <span style="color: #ef4444;">*</span>
                        </label>
                        <input type="time" name="end_time" required value="{{ old('end_time') }}"
                            style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.875rem; transition: all 0.2s; box-sizing: border-box;" 
                            onfocus="this.style.borderColor='#0891b2'; this.style.boxShadow='0 0 0 3px rgba(8,145,178,0.1)'" 
                            onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                        @error('end_time') <p style="font-size: 0.75rem; color: #ef4444; margin-top: 0.25rem;">{{ $message }}</p> @enderror
                    </div>

                    {{-- Lokasi Kegiatan --}}
                    <div style="margin-bottom: 1.5rem; padding: 1.25rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.5rem;">
                        <h3 style="font-size: 0.9rem; font-weight: 700; color: #0891b2; margin: 0 0 1rem 0; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-map-marker-alt" style="color: #14b8a6;"></i>
                            Lokasi Kegiatan
                        </h3>

                        <div class="location-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                            <div>
                                <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 0.5rem;">
                                    Provinsi <span style="color: #ef4444;">*</span>
                                </label>
                                <select name="provinsi" id="provinsiSelect" required
                                        style="width: 100%; padding: 0.75rem 2.5rem 0.75rem 1rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.875rem; background: white; box-sizing: border-box; cursor: pointer; appearance: none; -webkit-appearance: none; background-image: url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E&quot;); background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 16px;"
                                        onfocus="this.style.borderColor='#0891b2'; this.style.boxShadow='0 0 0 3px rgba(8,145,178,0.1)'" 
                                        onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                                    <option value="">Memuat data provinsi...</option>
                                </select>
                                @error('provinsi') <p style="font-size: 0.75rem; color: #ef4444; margin-top: 0.25rem;">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 0.5rem;">
                                    Kab/Kota <span style="color: #ef4444;">*</span>
                                </label>
                                <select name="kabupaten" id="kabupatenSelect" required
                                        style="width: 100%; padding: 0.75rem 2.5rem 0.75rem 1rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.875rem; background: white; box-sizing: border-box; cursor: pointer; appearance: none; -webkit-appearance: none; background-image: url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E&quot;); background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 16px;"
                                        onfocus="this.style.borderColor='#0891b2'; this.style.boxShadow='0 0 0 3px rgba(8,145,178,0.1)'" 
                                        onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                                    <option value="">Pilih provinsi terlebih dahulu</option>
                                </select>
                                @error('kabupaten') <p style="font-size: 0.75rem; color: #ef4444; margin-top: 0.25rem;">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="location-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                            <div>
                                <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 0.5rem;">
                                    Kecamatan <span style="color: #ef4444;">*</span>
                                </label>
                                <select name="kecamatan" id="kecamatanSelect" required
                                        style="width: 100%; padding: 0.75rem 2.5rem 0.75rem 1rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.875rem; background: white; box-sizing: border-box; cursor: pointer; appearance: none; -webkit-appearance: none; background-image: url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E&quot;); background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 16px;"
                                        onfocus="this.style.borderColor='#0891b2'; this.style.boxShadow='0 0 0 3px rgba(8,145,178,0.1)'" 
                                        onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                                    <option value="">Pilih kabupaten/kota terlebih dahulu</option>
                                </select>
                                @error('kecamatan') <p style="font-size: 0.75rem; color: #ef4444; margin-top: 0.25rem;">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 0.5rem;">
                                    Kelurahan/Desa <span style="color: #ef4444;">*</span>
                                </label>
                                <select name="kelurahan" id="kelurahanSelect" required
                                        style="width: 100%; padding: 0.75rem 2.5rem 0.75rem 1rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.875rem; background: white; box-sizing: border-box; cursor: pointer; appearance: none; -webkit-appearance: none; background-image: url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E&quot;); background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 16px;"
                                        onfocus="this.style.borderColor='#0891b2'; this.style.boxShadow='0 0 0 3px rgba(8,145,178,0.1)'" 
                                        onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                                    <option value="">Pilih kecamatan terlebih dahulu</option>
                                </select>
                                @error('kelurahan') <p style="font-size: 0.75rem; color: #ef4444; margin-top: 0.25rem;">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 0.5rem;">
                                Alamat Lengkap <span style="color: #ef4444;">*</span>
                            </label>
                            <textarea name="alamat_lengkap" rows="3" required
                                    placeholder="Masukkan alamat lengkap kegiatan"
                                    style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.875rem; resize: vertical; box-sizing: border-box;"
                                    onfocus="this.style.borderColor='#0891b2'; this.style.boxShadow='0 0 0 3px rgba(8,145,178,0.1)'" 
                                    onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">{{ old('alamat_lengkap') }}</textarea>
                            @error('alamat_lengkap') <p style="font-size: 0.75rem; color: #ef4444; margin-top: 0.25rem;">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Deskripsi --}}
                    <div style="margin-bottom: 1.25rem;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 0.5rem;">
                            Deskripsi Kegiatan <span style="color: #ef4444;">*</span>
                        </label>
                        <textarea name="deskripsi" rows="4" placeholder="Jelaskan detail kegiatan yang dilaksanakan, peserta, hasil yang dicapai, dll..." required
                                style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.875rem; transition: all 0.2s; resize: vertical; box-sizing: border-box;" 
                                onfocus="this.style.borderColor='#0891b2'; this.style.boxShadow='0 0 0 3px rgba(8,145,178,0.1)'" 
                                onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi') <p style="font-size: 0.75rem; color: #ef4444; margin-top: 0.25rem;">{{ $message }}</p> @enderror
                    </div>

                    {{-- Dokumentasi Foto --}}
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 0.5rem;">
                            Dokumentasi Kegiatan (Foto) <span style="color: #64748b; font-weight: 400;">(Maksimal 10 foto)</span>
                        </label>
                        <div id="dropZone" style="border: 2px dashed #e2e8f0; border-radius: 0.75rem; padding: 2rem; text-align: center; cursor: pointer; transition: all 0.25s ease; background: #f8fafc;">
                            
                            {{-- Default State --}}
                            <div id="uploadPlaceholder">
                                <i class="fas fa-cloud-upload-alt" style="font-size: 3rem; color: #94a3b8; margin-bottom: 1rem;"></i>
                                <p style="font-size: 0.95rem; color: #475569; margin: 0 0 0.5rem 0; font-weight: 600;">Klik untuk memilih file atau seret foto ke sini</p>
                                <p style="font-size: 0.75rem; color: #94a3b8; margin: 0;">JPG, PNG, HEIC (Maks. 5MB per file - Maksimal 10 foto)</p>
                            </div>

                            {{-- File Preview State --}}
                            <div id="filePreview" style="display: none;">
                                <div id="fileListContainer"></div>
                                <div id="fileButtons" style="display: flex; gap: 0.5rem; margin-top: 1rem; flex-wrap: wrap;">
                                    <button type="button" id="addMoreBtn" onclick="addMoreFiles()" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1rem; background: linear-gradient(135deg, #0891b2, #14b8a6); color: white; border: none; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer; transition: all 0.2s; box-shadow: 0 2px 4px rgba(8,145,178,0.2);" onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 8px rgba(8,145,178,0.3)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(8,145,178,0.2)'">
                                        <i class="fas fa-plus"></i>
                                        Tambah Foto
                                    </button>
                                    <button type="button" onclick="changeFiles()" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1rem; background: white; border: 1px solid #e2e8f0; color: #64748b; border-radius: 0.5rem; font-size: 0.875rem; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#f8fafc'; this.style.color='#334155'" onmouseout="this.style.background='white'; this.style.color='#64748b'">
                                        <i class="fas fa-sync-alt"></i>
                                        Ganti File
                                    </button>
                                </div>
                            </div>

                            <input type="file" name="fotos[]" id="fileInput" accept="image/*, .heic" multiple style="display: none;">
                        </div>
                        
                        {{-- File Counter --}}
                        <div id="fileCounter" style="margin-top: 0.75rem; padding: 0.75rem 1rem; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 0.5rem; display: none;">
                            <p style="font-size: 0.85rem; color: #1e40af; margin: 0; font-weight: 600;">
                                <i class="fas fa-info-circle" style="margin-right: 0.5rem;"></i>
                                <span id="counterText">0 dari 10 foto dipilih</span>
                            </p>
                        </div>
                        
                        @error('fotos.*') <p style="font-size: 0.75rem; color: #ef4444; margin-top: 0.5rem;">{{ $message }}</p> @enderror
                    </div>

                    {{-- Buttons --}}
                    <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 1.25rem; border-top: 1px solid #e2e8f0;">
                        <button type="reset" 
                                style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.25rem; background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-weight: 600; cursor: pointer; transition: all 0.2s;" 
                                onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">
                            <i class="fas fa-sync-alt"></i>
                            <span>Reset</span>
                        </button>

                        <div class="btn-group" style="display: flex; gap: 0.75rem;">
                            <a href="{{ $backUrl }}" 
                            style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.25rem; background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-weight: 600; text-decoration: none; transition: all 0.2s;" 
                            onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">
                                Batal
                            </a>
                            <button type="submit" 
                                    style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #0891b2, #14b8a6); color: white; border: none; border-radius: 0.5rem; font-weight: 600; cursor: pointer; transition: all 0.2s; box-shadow: 0 2px 8px rgba(8,145,178,0.2);" 
                                    onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 12px rgba(8,145,178,0.3)'" 
                                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(8,145,178,0.2)'">
                                <i class="fas fa-paper-plane"></i>
                                <span>Kirim Laporan</span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Multi-File Upload Logic
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const uploadPlaceholder = document.getElementById('uploadPlaceholder');
    const filePreview = document.getElementById('filePreview');
    const fileListContainer = document.getElementById('fileListContainer');
    const fileCounter = document.getElementById('fileCounter');
    const counterText = document.getElementById('counterText');
    
    const MAX_FILES = 10;
    const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB
    
    let selectedFiles = [];
    
    // Click dropzone to open file picker
    if (dropZone && fileInput) {
        dropZone.addEventListener('click', (e) => {
            if (e.target.closest('#filePreview button')) return;
            fileInput.click();
        });
        
        // Drag events
        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.style.borderColor = '#0891b2';
            dropZone.style.background = '#f0f9ff';
        });
        
        dropZone.addEventListener('dragleave', () => {
            dropZone.style.borderColor = '#e2e8f0';
            dropZone.style.background = '#f8fafc';
        });
        
        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.style.borderColor = '#e2e8f0';
            dropZone.style.background = '#f8fafc';
            
            if (e.dataTransfer.files.length > 0) {
                handleFiles(e.dataTransfer.files);
            }
        });
        
        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                handleFiles(e.target.files);
            }
        });
    }
    
    function handleFiles(newFiles) {
        const remainingSlots = MAX_FILES - selectedFiles.length;
        
        if (newFiles.length > remainingSlots) {
            if (typeof Toast !== 'undefined') {
                Toast.error(`Maksimal hanya ${MAX_FILES} foto. Anda sudah memilih ${selectedFiles.length} foto.`);
            } else {
                alert(`Maksimal hanya ${MAX_FILES} foto. Anda sudah memilih ${selectedFiles.length} foto.`);
            }
            return;
        }
        
        Array.from(newFiles).forEach(file => {
            // Validate file
            const validation = validateFile(file);
            if (!validation.valid) {
                if (typeof Toast !== 'undefined') {
                    Toast.error(validation.message);
                } else {
                    alert(validation.message);
                }
                return;
            }
            
            selectedFiles.push(file);
        });
        
        updateFileDisplay();
    }
    
    function validateFile(file) {
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/heic'];
        const allowedExtensions = ['.jpg', '.jpeg', '.png', '.heic'];
        
        // Check file size
        if (file.size > MAX_FILE_SIZE) {
            const sizeInMB = (file.size / 1024 / 1024).toFixed(2);
            return {
                valid: false,
                message: `Ukuran file "${file.name}" terlalu besar (${sizeInMB}MB). Maksimal 5MB.`
            };
        }
        
        // Check file type
        if (!allowedTypes.includes(file.type)) {
            return {
                valid: false,
                message: `Format file "${file.name}" tidak diizinkan. Hanya JPG, PNG, dan HEIC yang diperbolehkan.`
            };
        }
        
        // Check extension
        const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
        if (!allowedExtensions.includes(fileExtension)) {
            return {
                valid: false,
                message: `Ekstensi file "${fileExtension}" tidak diizinkan.`
            };
        }
        
        return { valid: true, message: '' };
    }
    
    function updateFileDisplay() {
        if (selectedFiles.length === 0) {
            uploadPlaceholder.style.display = 'block';
            filePreview.style.display = 'none';
            fileCounter.style.display = 'none';
            return;
        }
        
        uploadPlaceholder.style.display = 'none';
        filePreview.style.display = 'block';
        fileCounter.style.display = 'block';
        
        // Update counter
        counterText.textContent = `${selectedFiles.length} dari ${MAX_FILES} foto dipilih`;
        
        // Sembunyikan tombol "Tambah Foto" jika sudah 10
        const addMoreBtn = document.getElementById('addMoreBtn');
        if (addMoreBtn) {
            if (selectedFiles.length >= MAX_FILES) {
                addMoreBtn.style.display = 'none';
            } else {
                addMoreBtn.style.display = 'inline-flex';
            }
        }
        
        // Render file list
        renderFileList();
        
        // Update file input
        const dataTransfer = new DataTransfer();
        selectedFiles.forEach(file => dataTransfer.items.add(file));
        fileInput.files = dataTransfer.files;
    }
    
    function renderFileList() {
        fileListContainer.innerHTML = '';
        
        selectedFiles.forEach((file, index) => {
            const fileItem = createFileItem(file, index);
            fileListContainer.appendChild(fileItem);
        });
    }
    
    function createFileItem(file, index) {
        const div = document.createElement('div');
        div.style.cssText = 'background: #f0fdf4; border: 2px solid #10b981; border-radius: 0.5rem; padding: 1rem; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 1rem;';
        
        const fileSize = (file.size / 1024).toFixed(2);
        const sizeText = fileSize >= 1024 ? `${(fileSize / 1024).toFixed(2)} MB` : `${fileSize} KB`;
        
        div.innerHTML = `
            <div style="width: 48px; height: 48px; background: white; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="fas fa-file-image" style="color: #10b981; font-size: 1.5rem;"></i>
            </div>
            <div style="flex: 1; text-align: left; overflow: hidden;">
                <p style="font-size: 0.9rem; font-weight: 600; color: #0f172a; margin: 0 0 0.25rem 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    ${index + 1}. ${file.name}
                </p>
                <p style="font-size: 0.75rem; color: #64748b; margin: 0;">${sizeText}</p>
            </div>
            <div style="flex-shrink: 0;">
                <span style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.375rem 0.875rem; background: #10b981; color: white; border-radius: 9999px; font-size: 0.8rem; font-weight: 600;">
                    <i class="fas fa-check" style="font-size: 0.7rem;"></i>
                    Siap
                </span>
            </div>
            <button type="button" onclick="removeFile(${index})" style="flex-shrink: 0; width: 2rem; height: 2rem; background: #fee2e2; color: #ef4444; border: none; border-radius: 0.375rem; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center;" onmouseover="this.style.background='#fecaca'" onmouseout="this.style.background='#fee2e2'">
                <i class="fas fa-times" style="font-size: 0.875rem;"></i>
            </button>
        `;
        
        return div;
    }
    
    function removeFile(index) {
        selectedFiles.splice(index, 1);
        updateFileDisplay();
    }
    
    function changeFiles() {
        selectedFiles = [];
        fileInput.value = '';
        updateFileDisplay();
        setTimeout(() => {
            fileInput.click();
        }, 150);
    }

    function addMoreFiles() {
        // Buka file picker tanpa menghapus file yang sudah ada
        fileInput.click();
    }

    function changeFiles() {
        // Reset semua file dan pilih file baru
        selectedFiles = [];
        fileInput.value = '';
        updateFileDisplay();
        setTimeout(() => {
            fileInput.click();
        }, 150);
    }

    // ==========================================
    // WILAYAH DROPDOWN LOGIC
    // ==========================================
    
    const provinsiSelect = document.getElementById('provinsiSelect');
    const kabupatenSelect = document.getElementById('kabupatenSelect');
    const kecamatanSelect = document.getElementById('kecamatanSelect');
    const kelurahanSelect = document.getElementById('kelurahanSelect');
    
    // Load Provinsi saat halaman dimuat
    async function loadProvinsi() {
        try {
            const response = await fetch('/api/v1/wilayah/provinces');
            const result = await response.json();
            
            if (result.success && result.data) {
                provinsiSelect.innerHTML = '<option value="">Pilih Provinsi</option>';
                result.data.forEach(prov => {
                    const option = document.createElement('option');
                        option.value = prov.name;
                        option.textContent = prov.name;
                        option.dataset.code = prov.code;
                    provinsiSelect.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error loading provinsi:', error);
            provinsiSelect.innerHTML = '<option value="">Gagal memuat data</option>';
        }
    }
    
    // Load Kabupaten saat Provinsi dipilih
    async function loadKabupaten(provinceCode) {
        if (!provinceCode) {
            kabupatenSelect.innerHTML = '<option value="">Pilih provinsi terlebih dahulu</option>';
            return;
        }
        
        kabupatenSelect.innerHTML = '<option value="">Memuat data...</option>';
        
        try {
            const response = await fetch(`/api/v1/wilayah/regencies/${provinceCode}`);
            const result = await response.json();
            
            if (result.success && result.data) {
                kabupatenSelect.innerHTML = '<option value="">Pilih Kabupaten/Kota</option>';
                result.data.forEach(kab => {
                    const option = document.createElement('option');
                        option.value = kab.name;
                        option.textContent = kab.name;
                        option.dataset.code = kab.code;
                    kabupatenSelect.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error loading kabupaten:', error);
            kabupatenSelect.innerHTML = '<option value="">Gagal memuat data</option>';
        }
    }
    
    // Load Kecamatan saat Kabupaten dipilih
    async function loadKecamatan(regencyCode) {
        if (!regencyCode) {
            kecamatanSelect.innerHTML = '<option value="">Pilih kabupaten/kota terlebih dahulu</option>';
            return;
        }
        
        kecamatanSelect.innerHTML = '<option value="">Memuat data...</option>';
        
        try {
            const response = await fetch(`/api/v1/wilayah/districts/${regencyCode}`);
            const result = await response.json();
            
            if (result.success && result.data) {
                kecamatanSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
                result.data.forEach(kec => {
                    const option = document.createElement('option');
                        option.value = kec.name;
                        option.textContent = kec.name;
                        option.dataset.code = kec.code;
                    kecamatanSelect.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error loading kecamatan:', error);
            kecamatanSelect.innerHTML = '<option value="">Gagal memuat data</option>';
        }
    }
    
    // Load Kelurahan saat Kecamatan dipilih
    async function loadKelurahan(districtCode) {
        if (!districtCode) {
            kelurahanSelect.innerHTML = '<option value="">Pilih kecamatan terlebih dahulu</option>';
            return;
        }
        
        kelurahanSelect.innerHTML = '<option value="">Memuat data...</option>';
        
        try {
            const response = await fetch(`/api/v1/wilayah/villages/${districtCode}`);
            const result = await response.json();
            
            if (result.success && result.data) {
                kelurahanSelect.innerHTML = '<option value="">Pilih Kelurahan/Desa</option>';
                result.data.forEach(kel => {
                    const option = document.createElement('option');
                        option.value = kel.name;         
                        option.textContent = kel.name;
                        option.dataset.code = kel.code;  
                    kelurahanSelect.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error loading kelurahan:', error);
            kelurahanSelect.innerHTML = '<option value="">Gagal memuat data</option>';
        }
    }
    
    // Event Listeners
    if (provinsiSelect) {
        provinsiSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const code = selectedOption ? selectedOption.dataset.code : '';
            loadKabupaten(code); // Kirim KODE, bukan nama
            // Reset kecamatan dan kelurahan
            kecamatanSelect.innerHTML = '<option value="">Pilih kabupaten/kota terlebih dahulu</option>';
            kelurahanSelect.innerHTML = '<option value="">Pilih kecamatan terlebih dahulu</option>';
        });
    }
    
    if (kabupatenSelect) {
        kabupatenSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const code = selectedOption ? selectedOption.dataset.code : '';
            loadKecamatan(code); // Kirim KODE, bukan nama
            // Reset kelurahan
            kelurahanSelect.innerHTML = '<option value="">Pilih kecamatan terlebih dahulu</option>';
        });
    }
    
    if (kecamatanSelect) {
        kecamatanSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const code = selectedOption ? selectedOption.dataset.code : '';
            loadKelurahan(code); // Kirim KODE, bukan nama
        });
    }
    
    // Load provinsi saat halaman dimuat
    loadProvinsi();
</script>
@endsection