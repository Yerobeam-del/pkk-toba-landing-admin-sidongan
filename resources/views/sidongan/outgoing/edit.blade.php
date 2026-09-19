{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
@extends('sidongan.layouts.app')
@section('title', 'Edit Surat Keluar - SIDONGAN')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/sidongan/css/sidongan-outgoing.css') }}">
@endpush

@section('content')
@include('sidongan.outgoing.form', ['document' => $letter->incomingDocument, 'letter' => $letter])
@endsection
{{-- Dikembangkan oleh Institut Teknologi Del --}}
