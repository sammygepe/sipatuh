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
     * dengan tab, filter, dan pagination.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Tab aktif: default 'rutin'
        $tab = $request->get('tab', 'rutin');

        // ============ DAFTAR USER UNTUK DROPDOWN ============
        if ($user->is_admin) {
            $userList = \App\Models\User::orderBy('name')->get();
        } else {
            $userList = \App\Models\User::where('departemen_id', $user->departemen_id)
                ->orderBy('name')->get();
        }

        // ============ FILTER RUTIN ============
        $searchRutin  = $request->get('search_rutin', '');
        $periode      = $request->get('periode', '');
        $statusRutin  = $request->get('status_rutin', '');
        $userRutin    = $request->get('user_rutin', '');
        $departemenId = $request->get('departemen_id', '');

        $queryRutin = MasterAktivitasRutin::with(['departemen', 'user']);

        if (!$user->is_admin) {
            $queryRutin->where('departemen_id', $user->departemen_id);
        } elseif (!empty($departemenId)) {
            $queryRutin->where('departemen_id', $departemenId);
        }

        if (!empty($searchRutin)) {
            $queryRutin->where('nama_aktivitas', 'like', '%' . $searchRutin . '%');
        }
        if (!empty($periode)) {
            $queryRutin->where('periode', $periode);
        }
        if (!empty($statusRutin)) {
            $queryRutin->where('status', $statusRutin);
        }
        if (!empty($userRutin)) {
            $queryRutin->where('user_id', $userRutin);
        }

        $rutin = $queryRutin->orderBy('nama_aktivitas')
            ->paginate(15, ['*'], 'page_rutin')
            ->appends($request->query());

        // ============ FILTER PROYEK ============
        $searchProyek      = $request->get('search_proyek', '');
        $statusProyek      = $request->get('status_proyek', '');
        $statusProyekAkhir = $request->get('status_proyek_akhir', '');
        $userProyek        = $request->get('user_proyek', '');

        $queryProyek = MasterProyek::with(['departemen', 'user']);

        if (!$user->is_admin) {
            $queryProyek->where('departemen_id', $user->departemen_id);
        } elseif (!empty($departemenId)) {
            $queryProyek->where('departemen_id', $departemenId);
        }

        if (!empty($searchProyek)) {
            $queryProyek->where('nama_proyek', 'like', '%' . $searchProyek . '%');
        }
        if (!empty($statusProyek)) {
            $queryProyek->where('status', $statusProyek);
        }
        if (!empty($userProyek)) {
            $queryProyek->where('user_id', $userProyek);
        }

        // Filter Status Proyek Terakhir
        if (!empty($statusProyekAkhir)) {
            $queryProyek->whereIn('id', function ($sub) use ($statusProyekAkhir) {
                $sub->select('proyek_id')
                    ->from('log_aktivitas_harian')
                    ->whereIn('id', function ($sub2) {
                        $sub2->selectRaw('MAX(id)')
                            ->from('log_aktivitas_harian')
                            ->whereNotNull('proyek_id')
                            ->groupBy('proyek_id');
                    })
                    ->where('status', $statusProyekAkhir);
            });
        }

        $proyek = $queryProyek->orderBy('nama_proyek')
            ->paginate(15, ['*'], 'page_proyek')
            ->appends($request->query());

        // Log terakhir per proyek
        $proyekIds = $proyek->pluck('id');
        $logTerakhir = \App\Models\LogAktivitasHarian::whereIn('proyek_id', $proyekIds)
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->get()
            ->groupBy('proyek_id')
            ->map(fn($logs) => $logs->first());

        $departemenList = Departemen::orderBy('nama')->get();

        return view('master.index', compact(
            'rutin', 'proyek', 'departemenList', 'logTerakhir', 'userList',
            'tab',
            'searchRutin', 'periode', 'statusRutin', 'userRutin',
            'searchProyek', 'statusProyek', 'statusProyekAkhir', 'userProyek',
            'departemenId'
        ));
    }

    /**
     * Export data master rutin ke Excel (mengikuti filter aktif)
     */
    public function exportRutin(Request $request)
    {
        $user = Auth::user();

        // Ambil filter dari request
        $filters = [
            'departemen_id' => $user->is_admin
                ? $request->get('departemen_id', '')
                : $user->departemen_id,
            'search_rutin'  => $request->get('search_rutin', ''),
            'periode'       => $request->get('periode', ''),
            'status_rutin'  => $request->get('status_rutin', ''),
            'user_rutin'    => $request->get('user_rutin', ''),
        ];

        $namaFile = 'master_rutin_' . date('Y-m-d_H-i-s') . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\MasterRutinExport($filters),
            $namaFile
        );
    }

    /**
     * Export data master proyek ke Excel (mengikuti filter aktif)
     */
    public function exportProyek(Request $request)
    {
        $user = Auth::user();

        $filters = [
            'departemen_id'      => $user->is_admin
                ? $request->get('departemen_id', '')
                : $user->departemen_id,
            'search_proyek'      => $request->get('search_proyek', ''),
            'status_proyek'      => $request->get('status_proyek', ''),
            'status_proyek_akhir'=> $request->get('status_proyek_akhir', ''),
            'user_proyek'        => $request->get('user_proyek', ''),
        ];

        $namaFile = 'master_proyek_' . date('Y-m-d_H-i-s') . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\MasterProyekExport($filters),
            $namaFile
        );
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