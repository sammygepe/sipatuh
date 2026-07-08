<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departemen extends Model
{
    protected $table = 'departemen';  // ← TAMBAHKAN BARIS INI
    
    protected $fillable = ['nama', 'bobot_default'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function aktivitasRutin()
    {
        return $this->hasMany(MasterAktivitasRutin::class);
    }

    public function proyek()
    {
        return $this->hasMany(MasterProyek::class);
    }
}