<?php

namespace App\Http\Controllers;

use App\Models\MasterAktivitasRutin;
use App\Models\MasterProyek;
use App\Models\LogAktivitasHarian;
use App\Models\User;
use App\Models\Notifikasi;
use App\Models\Departemen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AktivitasController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $hariIni = date('Y-m-d');

        // Aktivitas Rutin (HANYA milik user ini)
        $rutinHariIni = MasterAktivitasRutin::where(function($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhereNull('user_id'); // Aktivitas umum (belum ditugaskan ke siapa)
            })
            ->where('departemen_id', $user->departemen_id)
            ->where('status', 'approved')
            ->get();

        // Aktivitas Proyek (HANYA milik user ini)
        $proyekAktif = MasterProyek::where(function($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhereNull('user_id'); // Aktivitas umum (belum ditugaskan ke siapa)
            })
            ->where('departemen_id', $user->departemen_id)
            ->where('status', 'approved')
            ->where('tgl_mulai', '<=', $hariIni)
            ->where('tgl_berakhir', '>=', $hariIni)
            ->whereNull('tgl_selesai')
            ->get();

        // Log hari ini
        $logRutin = LogAktivitasHarian::where('user_id', $user->id)
            ->where('tanggal', $hariIni)
            ->whereNotNull('aktivitas_rutin_id')
            ->get()->keyBy('aktivitas_rutin_id');

        $logProyek = LogAktivitasHarian::where('user_id', $user->id)
            ->where('tanggal', $hariIni)
            ->whereNotNull('proyek_id')
            ->get()->keyBy('proyek_id');

        return view('dashboard', compact('rutinHariIni', 'proyekAktif', 'logRutin', 'logProyek'));
    }
    
    public function simpanLog(Request $request)
    {
        $user = Auth::user();
        $tanggal = date('Y-m-d');

        if ($request->has('status_rutin')) {
            foreach ($request->status_rutin as $id => $status) {
                LogAktivitasHarian::updateOrCreate(
                    ['user_id' => $user->id, 'aktivitas_rutin_id' => $id, 'tanggal' => $tanggal],
                    ['status' => $status, 'detail' => $request->detail_rutin[$id] ?? null]
                );
            }
        }

        if ($request->has('status_proyek')) {
            foreach ($request->status_proyek as $id => $status) {
                LogAktivitasHarian::updateOrCreate(
                    ['user_id' => $user->id, 'proyek_id' => $id, 'tanggal' => $tanggal],
                    ['status' => $status, 'detail' => $request->detail_proyek[$id] ?? null]
                );

                if ($status == 'selesai') {
                    MasterProyek::where('id', $id)->update(['tgl_selesai' => $tanggal]);
                }
            }
        }

        return redirect()->back()->with('success', 'Aktivitas hari ini berhasil dicatat!');
    }

    public function formUsul()
    {
        return view('usul_aktivitas');
    }

    public function usulAktivitas(Request $request)
    {
        // Validasi berdasarkan jenis
        if ($request->jenis == 'rutin') {
            $request->validate([
                'jenis' => 'required|in:rutin,proyek',
                'nama' => 'required|string|max:255',
                'periode' => 'required|in:daily,weekly,monthly',
                'bobot' => 'nullable|numeric|min:0|max:100'
            ]);
        } else {
            $request->validate([
                'jenis' => 'required|in:rutin,proyek',
                'nama' => 'required|string|max:255',
                'tgl_mulai' => 'required|date',
                'tgl_berakhir' => 'required|date|after_or_equal:tgl_mulai',
                'bobot' => 'nullable|numeric|min:0|max:100'
            ]);
        }

        $user = Auth::user();

        if ($request->jenis == 'rutin') {
            MasterAktivitasRutin::create([
                'nama_aktivitas' => $request->nama,
                'periode' => $request->periode,
                'bobot' => $request->bobot ?? 10,
                'departemen_id' => $user->departemen_id,
                'user_id' => $user->id,  // ← TAMBAHKAN
                'created_by' => $user->id,
                'status' => 'pending'
            ]);

            $message = 'Aktivitas rutin berhasil diusulkan ke atasan.';
        } else {
            MasterProyek::create([
                'nama_proyek' => $request->nama,
                'tgl_mulai' => $request->tgl_mulai,
                'tgl_berakhir' => $request->tgl_berakhir,
                'bobot' => $request->bobot ?? 50,
                'departemen_id' => $user->departemen_id,
                'user_id' => $user->id,  // ← TAMBAHKAN
                'created_by' => $user->id,
                'status' => 'pending'
            ]);

            $message = 'Proyek berhasil diusulkan ke atasan.';
        }

        // Kirim notifikasi ke atasan
        $atasan = User::where('departemen_id', $user->departemen_id)
            ->where('is_atasan', true)
            ->first();

        if ($atasan) {
            Notifikasi::create([
                'user_id' => $atasan->id,
                'pesan' => '📋 Usulan baru dari ' . $user->name . ': "' . $request->nama . '"',
                'is_dibaca' => false
            ]);
        }

        return redirect()->route('dashboard')->with('success', $message);
    }

    public function riwayat(Request $request)
    {
        $user = Auth::user();
        
        // Ambil bulan dan tahun dari request (default: bulan ini)
        $bulan = (int) $request->get('bulan', date('m'));
        $tahun = (int) $request->get('tahun', date('Y'));
        
        // Query log aktivitas user
        $riwayat = LogAktivitasHarian::where('user_id', $user->id)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->orderBy('tanggal', 'desc')
            ->paginate(20);
        
        // Untuk keperluan dropdown (bulan 1-12)
        $daftarBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        $daftarTahun = range(date('Y') - 1, date('Y') + 1); // tahun lalu, tahun ini, tahun depan
        
        return view('riwayat', compact('riwayat', 'bulan', 'tahun', 'daftarBulan', 'daftarTahun'));
    }

    /**
     * Riwayat untuk Atasan (melihat riwayat bawahan)
     */
    public function riwayatBawahan(Request $request)
    {
        $atasan = Auth::user();
        $bulan = (int) $request->get('bulan', date('m'));
        $tahun = (int) $request->get('tahun', date('Y'));
        $userId = $request->get('user_id', ''); // Ganti 'user' jadi 'user_id'
        $status = $request->get('status', '');
        $jenis = $request->get('jenis', '');
        
        // Ambil daftar bawahan untuk dropdown
        $bawahanList = User::where('departemen_id', $atasan->departemen_id)
            ->where('id', '!=', $atasan->id)
            ->orderBy('name')
            ->get();
        
        // Ambil bawahan (satu departemen) - untuk query riwayat
        $bawahanQuery = User::where('departemen_id', $atasan->departemen_id)
            ->where('id', '!=', $atasan->id);
        
        if (!empty($userId)) {
            $bawahanQuery = $bawahanQuery->where('id', $userId);
        }
        
        $bawahanIds = $bawahanQuery->pluck('id');
        
        // Ambil log aktivitas bawahan
        $query = LogAktivitasHarian::whereIn('user_id', $bawahanIds)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->with(['user', 'user.departemen']);
        
        // Filter status
        if (!empty($status)) {
            $query->where('status', $status);
        }
        
        // Filter jenis
        if ($jenis == 'rutin') {
            $query->whereNotNull('aktivitas_rutin_id');
        } elseif ($jenis == 'proyek') {
            $query->whereNotNull('proyek_id');
        }
        
        $riwayat = $query->orderBy('tanggal', 'desc')
            ->paginate(20);
        
        $daftarBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $daftarTahun = range(date('Y') - 1, date('Y'));
        
        $statusList = [
            'belum' => 'Belum Mulai',
            'progress' => 'On Progress',
            'selesai' => 'Selesai'
        ];
        
        return view('riwayat_bawahan', compact('riwayat', 'bulan', 'tahun', 'daftarBulan', 'daftarTahun', 'userId', 'status', 'jenis', 'statusList', 'bawahanList'));
    }

    /**
     * Riwayat untuk Admin (melihat semua user)
     */
    public function riwayatSemua(Request $request)
    {
        $bulan = (int) $request->get('bulan', date('m'));
        $tahun = (int) $request->get('tahun', date('Y'));
        $userId = $request->get('user_id', ''); // Ganti 'user' jadi 'user_id'
        $departemenId = $request->get('departemen_id', '');
        $status = $request->get('status', '');
        $jenis = $request->get('jenis', '');
        
        // Ambil daftar user untuk dropdown
        $userList = User::with('departemen')
            ->orderBy('name')
            ->get();
        
        // Query riwayat
        $query = LogAktivitasHarian::whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->with(['user', 'user.departemen']);
        
        // Filter user
        if (!empty($userId)) {
            $query->where('user_id', $userId);
        }
        
        // Filter departemen
        if (!empty($departemenId)) {
            $query->whereHas('user', function($q) use ($departemenId) {
                $q->where('departemen_id', $departemenId);
            });
        }
        
        // Filter status
        if (!empty($status)) {
            $query->where('status', $status);
        }
        
        // Filter jenis
        if ($jenis == 'rutin') {
            $query->whereNotNull('aktivitas_rutin_id');
        } elseif ($jenis == 'proyek') {
            $query->whereNotNull('proyek_id');
        }
        
        $riwayat = $query->orderBy('tanggal', 'desc')
            ->paginate(20);
        
        $daftarBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $daftarTahun = range(date('Y') - 1, date('Y'));
        $departemenList = Departemen::orderBy('nama')->get();
        
        $statusList = [
            'belum' => 'Belum Mulai',
            'progress' => 'On Progress',
            'selesai' => 'Selesai'
        ];
        
        return view('riwayat_semua', compact('riwayat', 'bulan', 'tahun', 'daftarBulan', 'daftarTahun', 'userId', 'departemenId', 'departemenList', 'status', 'jenis', 'statusList', 'userList'));
    }
}