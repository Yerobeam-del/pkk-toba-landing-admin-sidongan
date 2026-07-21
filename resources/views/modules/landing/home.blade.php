@extends('modules.landing.layouts.app')

@section('title', 'Beranda - PKK Kabupaten Toba')

@section('content')
<div class="page active" id="page-beranda">
    @include('modules.landing.sections.hero')
    @include('modules.landing.sections.quick-access')
    @include('modules.landing.sections.apps-home')
    @include('modules.landing.sections.news-home')
    @include('modules.landing.sections.stats')
</div>

@include('modules.landing.sections.page-struktur')
@include('modules.landing.sections.page-aplikasi')
@include('modules.landing.sections.page-berita')
{{-- @include('modules.landing.sections.page-desa') --}}
@include('modules.landing.sections.page-sk')
@include('modules.landing.sections.page-template')
@include('modules.landing.sections.page-tentang')
@endsection
