@extends('admin.layouts.app')

@section('title', 'Dashboard - Admin PKK Toba')
@section('page-title', 'Dashboard Beranda')

@section('content')
<div class="form-grid card">
    <div class="form-group full">
        <label>Judul Utama Hero</label>
        <input class="form-control" value="Selamat Datang di Portal PKK Kabupaten Toba">
    </div>
    <div class="form-group full">
        <label>Subjudul</label>
        <textarea class="form-control">Melayani masyarakat Kabupaten Toba melalui transformasi digital untuk pemberdayaan keluarga.</textarea>
    </div>
    <div class="form-group">
        <label>Gambar Latar 1</label>
        <input class="form-control" type="text" value="images/Background/Background_1.jpg">
    </div>
    <div class="form-group">
        <label>Gambar Latar 2</label>
        <input class="form-control" type="text" value="images/Background/Background_2.jpg">
    </div>
    <div class="form-group">
        <label>Gambar Latar 3</label>
        <input class="form-control" type="text" value="images/Background/Background_3.jpg">
    </div>
    <div class="form-group">
        <label>Gambar Latar 4</label>
        <input class="form-control" type="text" value="images/Background/Background_5.jpg">
    </div>
    <div class="form-group full">
        <button class="btn btn-primary">Simpan Perubahan Hero</button>
    </div>
</div>

<div class="card">
    <h3 style="margin-bottom:1rem">Statistik Cepat</h3>
    <div class="form-grid">
        <div class="form-group">
            <label>Kecamatan</label>
            <input class="form-control" type="number" value="16">
        </div>
        <div class="form-group">
            <label>Desa/Kelurahan</label>
            <input class="form-control" type="number" value="231">
        </div>
        <div class="form-group">
            <label>Ribu+ Penduduk</label>
            <input class="form-control" type="number" value="222">
        </div>
        <div class="form-group">
            <label>Aplikasi Aktif</label>
            <input class="form-control" type="number" value="2">
        </div>
        <div class="form-group full">
            <button class="btn btn-primary">Update Statistik</button>
        </div>
    </div>
</div>
@endsection