@extends('sidongan.layouts.app')
@section('title', 'Cetak Lembar Disposisi - SIDONGAN')

@section('content')
<style>
    /* ============================================
       Layout Preview Lembar Disposisi
       ============================================ */
    .disposisi-container {
        padding: 0 1.5rem;
        max-width: 1400px;
        margin: 0 auto;
    }
    
    .action-bar {
        background: linear-gradient(135deg, #0891b2, #14b8a6);
        padding: 1.5rem 2rem;
        border-radius: 1rem;
        margin-bottom: 1.5rem;
        color: white;
        box-shadow: 0 4px 20px rgba(8, 145, 178, 0.2);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }
    
    .action-title {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .action-title-icon {
        width: 3rem;
        height: 3rem;
        background: rgba(255, 255, 255, 0.25);
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(10px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .action-title-icon i {
        font-size: 1.5rem;
        color: white;
    }
    
    .action-title-text h1 {
        font-size: 1.25rem;
        font-weight: 700;
        margin: 0 0 0.25rem 0;
        color: white;
    }
    
    .action-title-text p {
        font-size: 0.875rem;
        opacity: 0.95;
        margin: 0;
    }
    
    .action-buttons {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }
    
    .btn-action {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.25rem;
        border-radius: 0.5rem;
        font-weight: 600;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.25s ease;
        text-decoration: none;
        border: none;
        min-height: 44px;
    }
    
    .btn-print {
        background: white;
        color: #0891b2;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .btn-print:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.2);
    }
    
    .btn-back {
        background: rgba(255, 255, 255, 0.25);
        color: white;
        backdrop-filter: blur(4px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }
    
    .btn-back:hover {
        background: rgba(255, 255, 255, 0.35);
        transform: translateY(-2px);
    }
    
    .disposisi-preview-wrapper {
        max-width: 210mm;
        margin: 0 auto;
        background: white;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border-radius: 0.75rem;
        overflow: hidden;
        border: 1px solid #e2e8f0;
    }
    
    .disposisi-page {
        padding: 15mm 20mm 20mm 20mm;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 12pt;
        line-height: 1.5;
        color: #000;
    }
    
    /* LAMPIRAN 4.8 */
    .disposisi-lampiran {
        text-align: right;
        font-weight: bold;
        font-size: 11pt;
        margin-bottom: 25px;
    }
    
    /* Judul */
    .disposisi-title {
        text-align: center;
        font-weight: bold;
        font-size: 14pt;
        margin-bottom: 3px;
        letter-spacing: 0.5px;
    }
    
    .disposisi-subtitle {
        text-align: center;
        font-weight: bold;
        font-size: 12pt;
        margin-bottom: 15px;
    }
    
    /* Garis horizontal tebal */
    .disposisi-line-thick {
        border: none;
        border-top: 2px solid #000;
        margin: 0 0 8px 0;
    }
    
    /* Garis horizontal tipis (pendek) */
    .disposisi-line-short {
        border: none;
        border-top: 1px solid #000;
        width: 80px;
        margin: 0 0 6px 0;
    }
    
    /* Row NO. AGENDA & TANGGAL */
    .disposisi-row-agenda {
        display: flex;
        align-items: baseline;
        padding: 6px 0;
    }
    
    .disposisi-row-agenda .label {
        font-weight: bold;
        font-size: 11pt;
        white-space: nowrap;
    }
    
    .disposisi-row-agenda .value {
        flex: 1;
        border-bottom: 1px solid #000;
        margin: 0 8px;
        padding-bottom: 2px;
        font-size: 11pt;
    }
    
    /* Row Info Surat */
    .disposisi-row-info {
        display: flex;
        align-items: baseline;
        padding: 4px 0;
    }
    
    .disposisi-row-info .label {
        font-weight: bold;
        font-size: 11pt;
        width: 130px;
        flex-shrink: 0;
    }
    
    .disposisi-row-info .colon {
        width: 15px;
        flex-shrink: 0;
    }
    
    .disposisi-row-info .value {
        flex: 1;
        border-bottom: 1px solid #000;
        padding-bottom: 2px;
        font-size: 11pt;
    }
    
    /* Tabel SARAN SEKRETARIS & DISPOSISI */
    .disposisi-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
        border: none;
    }
    
    .disposisi-table th {
        border: none;
        border-top: 1px solid #000;
        padding: 8px 10px;
        text-align: center;
        font-weight: bold;
        font-size: 11pt;
        text-decoration: underline;
        background: white;
    }
    
    .disposisi-table th:first-child {
        border-right: 1px solid #000;
    }
    
    .disposisi-table td {
        border: none;
        padding: 12px 15px;
        vertical-align: top;
        font-size: 11pt;
        line-height: 1.7;
        height: 380px;
    }
    
    .disposisi-table td:first-child {
        border-right: 1px solid #000;
    }
    
    /* Tanda Tangan */
    .disposisi-signature {
        margin-top: 30px;
        text-align: right;
        padding-right: 10px;
        font-size: 11pt;
    }
    
    .disposisi-signature-date {
        margin-bottom: 5px;
    }
    
    .disposisi-signature-space {
        height: 80px;
    }
    
    .disposisi-signature-name {
        font-weight: bold;
        text-decoration: underline;
    }
    
    /* Info Card di bawah preview */
    .info-card {
        background: white;
        border-radius: 0.75rem;
        padding: 1.25rem 1.5rem;
        margin-top: 1.5rem;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }
    
    .info-card-icon {
        width: 2.5rem;
        height: 2.5rem;
        background: #eff6ff;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    
    .info-card-icon i {
        color: #3b82f6;
        font-size: 1.1rem;
    }
    
    .info-card-text {
        flex: 1;
        min-width: 200px;
    }
    
    .info-card-text p {
        margin: 0;
        font-size: 0.9rem;
        color: #475569;
        line-height: 1.5;
    }
    
    .info-card-text strong {
        color: #1e293b;
    }
    
    /* ============================================
    PRINT STYLES - A4 Only
    ============================================ */
    @media print {
        @page {
            size: A4 portrait !important;
            margin: 0 !important;
        }
        
        * {
            print-color-adjust: exact !important;
            -webkit-print-color-adjust: exact !important;
        }
        
        body * {
            visibility: hidden !important;
        }
        
        .disposisi-preview-wrapper,
        .disposisi-preview-wrapper * {
            visibility: visible !important;
        }
        
        .disposisi-preview-wrapper {
            position: absolute !important;
            left: 0 !important;
            top: 0 !important;
            width: 210mm !important;
            height: 297mm !important;
            max-width: 100% !important;
            box-shadow: none !important;
            border-radius: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
            background: white !important;
            border: none !important;
        }
        
        .disposisi-page {
            padding: 15mm 20mm !important;
            width: 100% !important;
            height: 100% !important;
        }
        
        .action-bar,
        .info-card,
        .no-print {
            display: none !important;
        }
    }
    
    /* Mobile Responsive */
    @media (max-width: 768px) {
        .disposisi-container {
            padding: 0 1rem;
        }
        
        .action-bar {
            padding: 1.25rem 1.5rem;
            flex-direction: column;
            align-items: stretch;
        }
        
        .action-buttons {
            width: 100%;
        }
        
        .btn-action {
            flex: 1;
            justify-content: center;
        }
        
        .info-card {
            flex-direction: column;
            align-items: flex-start;
        }
    }
    
    @media (max-width: 480px) {
        .disposisi-container {
            padding: 0 0.75rem;
        }
        
        .action-title-text h1 {
            font-size: 1.1rem;
        }
        
        .action-title-text p {
            font-size: 0.8rem;
        }
    }
</style>

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
        <div class="action-buttons">
            <button onclick="window.location.replace('{{ route('sidongan.documents.show', $document) }}')" class="btn-action btn-back">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali</span>
            </button>
            <button onclick="window.print()" class="btn-action btn-print">
                <i class="fas fa-print"></i>
                <span>Cetak</span>
            </button>
        </div>
    </div>

    {{-- Preview Lembar Disposisi --}}
    <div class="disposisi-preview-wrapper">
        <div class="disposisi-page">
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
                                <div style="margin-bottom: 12px;">
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
                                
                                <div style="margin-bottom: 12px;">
                                    <strong>Tindakan:</strong><br>
                                    {{ $disposisiData['action'] }}
                                </div>
                                
                                @if(!empty($disposisiData['comment']))
                                    <div style="margin-bottom: 12px;">
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