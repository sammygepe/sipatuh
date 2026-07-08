<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KpiPenilaian extends Model
{
    protected $table = 'kpi_penilaian';

    protected $fillable = [
        'user_id', 'bulan', 'nilai_total', 'kategori',
        'catatan_atasan', 'dinilai_oleh'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function penilai()
    {
        return $this->belongsTo(User::class, 'dinilai_oleh');
    }
}