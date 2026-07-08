<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    public function baca($id)
    {
        $notif = Notifikasi::findOrFail($id);
        
        // Hanya user pemilik notifikasi yang bisa menandai
        if ($notif->user_id == auth()->id()) {
            $notif->is_dibaca = true;
            $notif->save();
        }
        
        return redirect()->back();
    }
}