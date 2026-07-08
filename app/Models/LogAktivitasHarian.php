<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogAktivitasHarian extends Model
{
    protected $table = 'log_aktivitas_harian';

    protected $fillable = [
        'user_id', 'aktivitas_rutin_id', 'proyek_id',
        'tanggal', 'status', 'detail'
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function aktivitasRutin()
    {
        return $this->belongsTo(MasterAktivitasRutin::class);
    }

    public function proyek()
    {
        return $this->belongsTo(MasterProyek::class);
    }
}