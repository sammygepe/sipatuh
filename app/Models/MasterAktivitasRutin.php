<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterAktivitasRutin extends Model
{
    protected $table = 'master_aktivitas_rutin';
    
    protected $fillable = [
        'nama_aktivitas', 'periode', 'bobot', 'departemen_id', 'user_id',
        'created_by', 'status', 'approved_by'
    ];

    public function departemen()
    {
        return $this->belongsTo(Departemen::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}