{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
@extends('sidongan.layouts.app')
@section('title', 'Cetak Lembar Disposisi - SIDONGAN')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/sidongan/css/sidongan-documents-disposisi-print.css') }}">


<div class="disposisi-container">
    {{-- Action Bar --}}
    <div class="action-bar no-print">
        <div class="action-title">
            <div class="action-title-icon">
                <i class="fas fa-print"></i>
            </div>
            <div class="action-title-text">
                <h1>Preview Lembar Disposisi</h1>
                <p>Surat No. Agenda: {{ $document->agenda_number }}</p>
            </div>
        </div>
        <div class="action-buttons sd-header-actions">
            <button data-back-url="{{ route('sidongan.documents.show', $document) }}" class="btn-action btn-back sd-btn-back">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali</span>
            </button>
            <button data-action="print-page" class="btn-action btn-print">
                <i class="fas fa-print"></i>
                <span>Cetak</span>
            </button>
        </div>
    </div>

    {{-- Penanda geser: hanya tampil di layar kecil, tidak ikut tercetak --}}
    <div class="sd-scroll-hint no-print">
        <i class="fas fa-arrows-left-right"></i>
        <span>Geser lembar ke samping untuk melihat seluruh isi</span>
    </div>

    {{-- Preview Lembar Disposisi --}}
    <div class="disposisi-preview-wrapper sd-sheet-scroll">
        <div class="disposisi-page sd-sheet-a4">
            {{-- LAMPIRAN 4.8 --}}
            <div class="disposisi-lampiran">LAMPIRAN 4.8</div>
            
            {{-- Judul --}}
            <div class="disposisi-title">LEMBAR DISPOSISI</div>
            <div class="disposisi-subtitle">KETUA UMUM TIM PENGGERAK PKK</div>
            
            {{-- Garis tebal atas --}}
            <hr class="disposisi-line-thick">
            
            {{-- NO. AGENDA & TANGGAL --}}
            <div class="disposisi-row-agenda">
                <span class="label">NO. AGENDA :</span>
                <span class="value">{{ $document->agenda_number }}</span>
                <span class="label" style="width: 90px; margin-left: 30px;">TANGGAL:</span>
                <span class="value">{{ $document->created_at->format('d/m/Y') }}</span>
            </div>
            
            {{-- Garis tebal bawah --}}
            <hr class="disposisi-line-thick">
            
            {{-- Garis tipis pendek di atas SURAT DARI --}}
            <hr class="disposisi-line-short">
            
            {{-- SURAT DARI --}}
            <div class="disposisi-row-info">
                <span class="label">SURAT DARI</span>
                <span class="colon">:</span>
                <span class="value">{{ $document->sender }}</span>
            </div>
            
            {{-- TANGGAL --}}
            <div class="disposisi-row-info">
                <span class="label">TANGGAL</span>
                <span class="colon">:</span>
                <span class="value">{{ $document->document_date->format('d/m/Y') }}</span>
            </div>
            
            {{-- NOMOR SURAT --}}
            <div class="disposisi-row-info">
                <span class="label">NOMOR SURAT</span>
                <span class="colon">:</span>
                <span class="value">{{ $document->document_number }}</span>
            </div>
            
            {{-- PERIHAL --}}
            <div class="disposisi-row-info">
                <span class="label">PERIHAL</span>
                <span class="colon">:</span>
                <span class="value">{{ $document->subject }}</span>
            </div>
            
            {{-- Tabel SARAN SEKRETARIS & DISPOSISI --}}
            <table class="disposisi-table">
                <thead>
                    <tr>
                        <th style="width: 50%;">SARAN SEKRETARIS</th>
                        <th style="width: 50%;">DISPOSISI</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        {{-- Kolom SARAN SEKRETARIS --}}
                        <td>
                            {{ $document->suggestion ?? '-' }}
                        </td>
                        
                        {{-- Kolom DISPOSISI --}}
                        <td>
                            @php
                                $disposisiData = is_string($document->disposisi_data ?? '') ? json_decode($document->disposisi_data, true) : $document->disposisi_data;
                            @endphp
                            
                            @if(is_array($disposisiData) && isset($disposisiData['action']))
                                <div class="u-a88">
                                    <strong>Didisposisikan kepada:</strong><br>
                                    @php
                                        $rolesMap = [
                                            'sekretaris' => 'Sekretaris PKK',
                                            'bendahara' => 'Bendahara PKK',
                                            'staf_ahli_1' => 'Staf Ahli I',
                                            'staf_ahli_2' => 'Staf Ahli II',
                                            'pengurus_1' => 'Ketua Pengurus I',
                                            'pengurus_2' => 'Ketua Pengurus II',
                                            'pengurus_3' => 'Ketua Pengurus III',
                                            'pengurus_4' => 'Ketua Pengurus IV',
                                        ];
                                    @endphp
                                    @foreach($disposisiData['target_roles'] as $role)
                                        - {{ $rolesMap[$role] ?? ucfirst(str_replace('_', ' ', $role)) }}<br>
                                    @endforeach
                                </div>
                                
                                <div class="u-a88">
                                    <strong>Tindakan:</strong><br>
                                    {{ $disposisiData['action'] }}
                                </div>
                                
                                @if(!empty($disposisiData['comment']))
                                    <div class="u-a88">
                                        <strong>Komentar:</strong><br>
                                        {{ $disposisiData['comment'] }}
                                    </div>
                                @endif
                                
                                {{-- Tanda Tangan dengan Ruang Kosong --}}
                                <div class="disposisi-signature">
                                    <div class="disposisi-signature-date">
                                        {{ isset($disposisiData['disposed_at']) ? \Carbon\Carbon::parse($disposisiData['disposed_at'])->format('d/m/Y') : $document->updated_at->format('d/m/Y') }}
                                    </div>
                                    <div class="disposisi-signature-space"></div>
                                    <div class="disposisi-signature-name">
                                        @php
                                            $disposedBy = null;
                                            if (isset($disposisiData['disposed_by'])) {
                                                $disposedBy = \App\Models\User::find($disposisiData['disposed_by']);
                                            }
                                        @endphp
                                        {{ $disposedBy->name ?? 'Ketua PKK' }}
                                    </div>
                                </div>
                            @else
                                <em style="color: #64748b;">Belum ada disposisi</em>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    {{-- Info Card --}}
    <div class="info-card no-print">
        <div class="info-card-icon">
            <i class="fas fa-info-circle"></i>
        </div>
        <div class="info-card-text">
            <p>
                <strong>Tips:</strong> Gunakan tombol <strong>Cetak</strong> untuk mencetak lembar disposisi ini. 
                Tombol <strong>Kembali</strong> akan membawa Anda ke halaman detail surat. 
                Format cetakan sudah disesuaikan dengan ukuran kertas <strong>A4 Portrait</strong>.
            </p>
        </div>
    </div>
</div>
@endsection
{{-- Dikembangkan oleh Institut Teknologi Del --}}
