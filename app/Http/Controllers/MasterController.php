<?php

namespace App\Http\Controllers;

use App\Models\MasterAktivitasRutin;
use App\Models\MasterProyek;
use App\Models\Departemen;
use App\Models\HistoriMaster;  // ← TAMBAHKAN INI
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MasterController extends Controller
{
    /**
     * Menampilkan daftar semua master aktivitas (rutin & proyek)
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->is_admin) {
            $rutin = MasterAktivitasRutin::with(['departemen', 'user'])
                ->orderBy('nama_aktivitas')
                ->get();
            $proyek = MasterProyek::with(['departemen', 'user'])
                ->orderBy('nama_proyek')
                ->get();
        } else {
            $rutin = MasterAktivitasRutin::where('departemen_id', $user->departemen_id)
                ->with(['departemen', 'user'])
                ->orderBy('nama_aktivitas')
                ->get();
            $proyek = MasterProyek::where('departemen_id', $user->departemen_id)
                ->with(['departemen', 'user'])
                ->orderBy('nama_proyek')
                ->get();
        }
        
        $departemenList = Departemen::orderBy('nama')->get();
        
        return view('master.index', compact('rutin', 'proyek', 'departemenList'));
    }
    
    /**
     * Menampilkan form edit aktivitas
     */
    public function edit($id, $jenis)
    {
        $user = Auth::user();
        
        if ($jenis == 'rutin') {
            $aktivitas = MasterAktivitasRutin::findOrFail($id);
            // Cek akses: admin atau atasan departemen yang sama
            if (!$user->is_admin && $aktivitas->departemen_id != $user->departemen_id) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses ke aktivitas ini.');
            }
            return view('master.edit_rutin', compact('aktivitas'));
        } else {
            $aktivitas = MasterProyek::findOrFail($id);
            if (!$user->is_admin && $aktivitas->departemen_id != $user->departemen_id) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses ke aktivitas ini.');
            }
            return view('master.edit_proyek', compact('aktivitas'));
        }
    }
    
    /**
     * Update aktivitas rutin atau proyek
     */
    public function update(Request $request, $id, $jenis)
    {
        $user = Auth::user();
        
        if ($jenis == 'rutin') {
            $aktivitas = MasterAktivitasRutin::findOrFail($id);
            
            // Cek akses
            if (!$user->is_admin && $aktivitas->departemen_id != $user->departemen_id) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses ke aktivitas ini.');
            }
            
            $request->validate([
                'nama_aktivitas' => 'required|string|max:255',
                'periode' => 'required|in:daily,weekly,monthly',
                'bobot' => 'required|numeric|min:0|max:100',
            ]);

            // Simpan nilai lama sebelum update
            $nilaiLama = [
                'nama_aktivitas' => $aktivitas->nama_aktivitas,
                'periode' => $aktivitas->periode,
                'bobot' => $aktivitas->bobot,
            ];

            $aktivitas->update([
                'nama_aktivitas' => $request->nama_aktivitas,
                'periode' => $request->periode,
                'bobot' => $request->bobot,
            ]);

            // Catat perubahan
            $this->catatHistori(
                $aktivitas->id,
                'rutin',
                'nama_aktivitas, periode, bobot',
                json_encode($nilaiLama),
                json_encode([
                    'nama_aktivitas' => $request->nama_aktivitas,
                    'periode' => $request->periode,
                    'bobot' => $request->bobot,
                ]),
                'update',
                'Diperbarui oleh ' . $user->name
            );
            
            return redirect()->route('master.aktivitas')
                ->with('success', 'Aktivitas rutin berhasil diperbarui.');
                
        } else {
            // PROYEK
            $aktivitas = MasterProyek::findOrFail($id);
            
            if (!$user->is_admin && $aktivitas->departemen_id != $user->departemen_id) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses ke aktivitas ini.');
            }
            
            $request->validate([
                'nama_proyek' => 'required|string|max:255',
                'tgl_mulai' => 'required|date',
                'tgl_berakhir' => 'required|date|after_or_equal:tgl_mulai',
                'bobot' => 'required|numeric|min:0|max:100',
            ]);

            // Simpan nilai lama sebelum update
            $nilaiLama = [
                'nama_proyek' => $aktivitas->nama_proyek,
                'tgl_mulai' => $aktivitas->tgl_mulai,
                'tgl_berakhir' => $aktivitas->tgl_berakhir,
                'bobot' => $aktivitas->bobot,
            ];

            $aktivitas->update([
                'nama_proyek' => $request->nama_proyek,
                'tgl_mulai' => $request->tgl_mulai,
                'tgl_berakhir' => $request->tgl_berakhir,
                'bobot' => $request->bobot,
            ]);

            // Catat perubahan
            $this->catatHistori(
                $aktivitas->id,
                'proyek',
                'nama_proyek, tgl_mulai, tgl_berakhir, bobot',
                json_encode($nilaiLama),
                json_encode([
                    'nama_proyek' => $request->nama_proyek,
                    'tgl_mulai' => $request->tgl_mulai,
                    'tgl_berakhir' => $request->tgl_berakhir,
                    'bobot' => $request->bobot,
                ]),
                'update',
                'Diperbarui oleh ' . $user->name
            );
            
            return redirect()->route('master.aktivitas')
                ->with('success', 'Proyek berhasil diperbarui.');
        }
    }
    
    /**
     * Soft delete aktivitas (status jadi rejected)
     */
    public function delete($id, $jenis)
    {
        $user = Auth::user();
        
        if ($jenis == 'rutin') {
            $aktivitas = MasterAktivitasRutin::findOrFail($id);
            
            if (!$user->is_admin && $aktivitas->departemen_id != $user->departemen_id) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses ke aktivitas ini.');
            }
            
            $this->catatHistori(
                $aktivitas->id,
                'rutin',
                'status',
                $aktivitas->status,
                'rejected',
                'delete',
                'Dinonaktifkan oleh ' . $user->name
            );
            
            $aktivitas->status = 'rejected';
            $aktivitas->save();
            
            return redirect()->route('master.aktivitas')
                ->with('success', 'Aktivitas rutin dinonaktifkan.');
                
        } else {
            // PROYEK
            $aktivitas = MasterProyek::findOrFail($id);
            
            if (!$user->is_admin && $aktivitas->departemen_id != $user->departemen_id) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses ke aktivitas ini.');
            }
            
            $this->catatHistori(
                $aktivitas->id,
                'proyek',
                'status',
                $aktivitas->status,
                'rejected',
                'delete',
                'Dinonaktifkan oleh ' . $user->name
            );
            
            $aktivitas->status = 'rejected';
            $aktivitas->save();
            
            return redirect()->route('master.aktivitas')
                ->with('success', 'Proyek dinonaktifkan.');
        }
    }

    /**
     * Catat histori perubahan
     */
    private function catatHistori($aktivitasId, $jenis, $field, $nilaiLama, $nilaiBaru, $aksi, $catatan = null)
    {
        HistoriMaster::create([
            'aktivitas_id' => $aktivitasId,
            'jenis_aktivitas' => $jenis,
            'field' => $field,
            'nilai_lama' => $nilaiLama,
            'nilai_baru' => $nilaiBaru,
            'aksi' => $aksi,
            'diubah_oleh' => Auth::id(),
            'catatan' => $catatan,
        ]);
    }
}