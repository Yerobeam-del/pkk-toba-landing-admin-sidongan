<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityReport extends Model
{
    use HasFactory;

    protected $table = 'activity_reports';

    protected $fillable = [
        'document_id',
        'created_by',
        'kegiatan_nama',
        'kegiatan_tanggal',
        'start_time',
        'end_time',
        'provinsi',
        'kabupaten',
        'kecamatan',
        'kelurahan',
        'alamat_lengkap',
        'deskripsi',
        'fotos',
        'status',
        'catatan_verifikasi',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'kegiatan_tanggal' => 'date',
        'fotos' => 'array',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class, 'document_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}