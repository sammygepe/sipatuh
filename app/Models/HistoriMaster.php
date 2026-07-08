<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoriMaster extends Model
{
    protected $table = 'histori_master';  // ← TAMBAHKAN INI

    protected $fillable = [
        'aktivitas_id',
        'jenis_aktivitas',
        'field',
        'nilai_lama',
        'nilai_baru',
        'aksi',
        'diubah_oleh',
        'catatan',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'diubah_oleh');
    }
}