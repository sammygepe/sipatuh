<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $table = 'notifikasi';
    
    protected $fillable = ['user_id', 'pesan', 'is_dibaca'];

    protected $casts = [
        'is_dibaca' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}