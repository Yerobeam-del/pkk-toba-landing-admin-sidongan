{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
@extends('sidongan.layouts.app')
@section('title', 'Buat Surat Keluar - SIDONGAN')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/sidongan/css/sidongan-outgoing.css') }}">
@endpush

@section('content')
@include('sidongan.outgoing.form', ['document' => $document, 'letter' => $letter])
@endsection

@push('scripts')
<script src="{{ asset('assets/sidongan/js/outgoing-form.js') }}"></script>
@endpush
{{-- Dikembangkan oleh Institut Teknologi Del --}}
