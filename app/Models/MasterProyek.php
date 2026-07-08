<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterProyek extends Model
{
    protected $table = 'master_proyek';

    protected $fillable = [
        'nama_proyek', 'tgl_mulai', 'tgl_berakhir', 'tgl_selesai',
        'bobot', 'departemen_id', 'user_id', 'created_by', 'status', 'approved_by'
    ];

    protected $casts = [
        'tgl_mulai' => 'date',
        'tgl_berakhir' => 'date',
        'tgl_selesai' => 'date',
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