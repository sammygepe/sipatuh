<?php

namespace App\Http\Controllers;

use App\Models\MasterAktivitasRutin;
use App\Models\MasterProyek;
use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AtasanController extends Controller
{
    /**
     * Menampilkan daftar usulan pending dari bawahan
     */
    public function pendingList()
    {
        $user = Auth::user();

        $pendingRutin = MasterAktivitasRutin::where('departemen_id', $user->departemen_id)
            ->where('status', 'pending')
            ->with('creator')
            ->get();

        $pendingProyek = MasterProyek::where('departemen_id', $user->departemen_id)
            ->where('status', 'pending')
            ->with('creator')
            ->get();

        return view('approval_list', compact('pendingRutin', 'pendingProyek'));
    }

    /**
     * Menyetujui usulan aktivitas dari bawahan
     */
    public function approve($id, $jenis)
    {
        if ($jenis == 'rutin') {
            $aktivitas = MasterAktivitasRutin::findOrFail($id);
            $aktivitas->status = 'approved';
            $aktivitas->approved_by = Auth::id();
            $aktivitas->save();

            Notifikasi::create([
                'user_id' => $aktivitas->created_by,
                'pesan' => 'Aktivitas rutin "' . $aktivitas->nama_aktivitas . '" telah disetujui.',
                'is_dibaca' => false
            ]);
        } else {
            $aktivitas = MasterProyek::findOrFail($id);
            $aktivitas->status = 'approved';
            $aktivitas->approved_by = Auth::id();
            $aktivitas->save();

            Notifikasi::create([
                'user_id' => $aktivitas->created_by,
                'pesan' => 'Proyek "' . $aktivitas->nama_proyek . '" telah disetujui.',
                'is_dibaca' => false
            ]);
        }

        return redirect()->back()->with('success', 'Aktivitas berhasil disetujui.');
    }

    /**
     * Menolak usulan aktivitas dari bawahan
     */
    public function reject($id, $jenis)
    {
        if ($jenis == 'rutin') {
            $aktivitas = MasterAktivitasRutin::findOrFail($id);
            $aktivitas->status = 'rejected';
            $aktivitas->approved_by = Auth::id();
            $aktivitas->save();

            Notifikasi::create([
                'user_id' => $aktivitas->created_by,
                'pesan' => 'Aktivitas rutin "' . $aktivitas->nama_aktivitas . '" ditolak.',
                'is_dibaca' => false
            ]);
        } else {
            $aktivitas = MasterProyek::findOrFail($id);
            $aktivitas->status = 'rejected';
            $aktivitas->approved_by = Auth::id();
            $aktivitas->save();

            Notifikasi::create([
                'user_id' => $aktivitas->created_by,
                'pesan' => 'Proyek "' . $aktivitas->nama_proyek . '" ditolak.',
                'is_dibaca' => false
            ]);
        }

        return redirect()->back()->with('error', 'Aktivitas ditolak.');
    }

    /**
     * Menampilkan form assign aktivitas untuk atasan (ke bawahan)
     */
    public function formAssignAktivitas()
    {
        // Ambil semua bawahan (user satu departemen yang bukan atasan sendiri)
        $bawahan = User::where('departemen_id', auth()->user()->departemen_id)
            ->where('id', '!=', auth()->id())
            ->get();
        
        return view('atasan.assign_aktivitas', compact('bawahan'));
    }

    /**
     * Proses assign aktivitas dari atasan ke bawahan
     */
    public function assignAktivitas(Request $request)
    {
        // Validasi dasar
        $request->validate([
            'jenis' => 'required|in:rutin,proyek',
            'nama' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
            'bobot' => 'nullable|numeric|min:0|max:100',
        ]);

        // Validasi bahwa user yang dipilih adalah bawahan atasan ini
        $targetUser = User::findOrFail($request->user_id);
        if ($targetUser->departemen_id !== auth()->user()->departemen_id) {
            return redirect()->back()->with('error', 'Anda hanya bisa memberikan tugas ke bawahan Anda sendiri.');
        }

        // Proses berdasarkan jenis aktivitas
        if ($request->jenis === 'rutin') {
            // Validasi tambahan untuk rutin
            $request->validate([
                'periode' => 'required|in:daily,weekly,monthly'
            ]);

            MasterAktivitasRutin::create([
                'nama_aktivitas' => $request->nama,
                'periode' => $request->periode,
                'bobot' => $request->bobot ?? 10,
                'departemen_id' => $targetUser->departemen_id,
                'user_id' => $targetUser->id,
                'created_by' => auth()->id(),
                'status' => 'approved',
                'approved_by' => auth()->id(),
            ]);

            $message = 'Aktivitas rutin "' . $request->nama . '" berhasil diberikan ke ' . $targetUser->name;

        } else {
            // Validasi tambahan untuk proyek
            $request->validate([
                'tgl_mulai' => 'required|date',
                'tgl_berakhir' => 'required|date|after_or_equal:tgl_mulai',
            ]);

            MasterProyek::create([
                'nama_proyek' => $request->nama,
                'tgl_mulai' => $request->tgl_mulai,
                'tgl_berakhir' => $request->tgl_berakhir,
                'bobot' => $request->bobot ?? 50,
                'departemen_id' => $targetUser->departemen_id,
                'user_id' => $targetUser->id,
                'created_by' => auth()->id(),
                'status' => 'approved',
                'approved_by' => auth()->id(),
            ]);

            $message = 'Proyek "' . $request->nama . '" berhasil diberikan ke ' . $targetUser->name;
        }

        // Kirim notifikasi ke bawahan (opsional)
        Notifikasi::create([
            'user_id' => $targetUser->id,
            'pesan' => 'Anda mendapatkan tugas baru: "' . $request->nama . '" dari atasan.',
            'is_dibaca' => false
        ]);

        return redirect()->route('dashboard')->with('success', $message);
    }
}