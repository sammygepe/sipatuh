<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Departemen;
use App\Models\LogAktivitasHarian;
use App\Models\MasterAktivitasRutin;
use App\Models\MasterProyek;
use App\Models\KpiPenilaian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Exports\KpiBawahanExport;
use App\Exports\KpiSemuaExport;
use Maatwebsite\Excel\Facades\Excel;

class KPIController extends Controller
{
    public function hitungKPI($userId, $bulan)
    {
        $tahun = substr($bulan, 0, 4);
        $bulanKe = substr($bulan, 5, 2);

        $logs = LogAktivitasHarian::where('user_id', $userId)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulanKe)
            ->get();

        $totalBobotMaks = 0;
        $totalNilaiDiperoleh = 0;
        $aktivitasSudahDihitung = [];

        foreach ($logs as $log) {
            if ($log->aktivitas_rutin_id) {
                $aktivitasId = 'rutin_' . $log->aktivitas_rutin_id;
                if (in_array($aktivitasId, $aktivitasSudahDihitung)) continue;

                $aktivitas = MasterAktivitasRutin::find($log->aktivitas_rutin_id);
                if (!$aktivitas) continue;

                $bobot = $aktivitas->bobot;
                $aktivitasSudahDihitung[] = $aktivitasId;

                $logAktivitasThisMonth = LogAktivitasHarian::where('user_id', $userId)
                    ->where('aktivitas_rutin_id', $log->aktivitas_rutin_id)
                    ->whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', $bulanKe)
                    ->get();

                $nilaiStatusMax = 0;
                foreach ($logAktivitasThisMonth as $l) {
                    $nilai = match ($l->status) {
                        'selesai' => 1,
                        'progress' => 0.5,
                        default => 0
                    };
                    $nilaiStatusMax = max($nilaiStatusMax, $nilai);
                }

                $totalBobotMaks += $bobot;
                $totalNilaiDiperoleh += ($bobot * $nilaiStatusMax);

            } elseif ($log->proyek_id) {
                $aktivitasId = 'proyek_' . $log->proyek_id;
                if (in_array($aktivitasId, $aktivitasSudahDihitung)) continue;

                $aktivitas = MasterProyek::find($log->proyek_id);
                if (!$aktivitas) continue;

                $bobot = $aktivitas->bobot;
                $aktivitasSudahDihitung[] = $aktivitasId;

                if ($aktivitas->tgl_selesai && $aktivitas->tgl_selesai <= date("$tahun-$bulanKe-31")) {
                    $nilaiStatusMax = 1;
                } else {
                    $logTerakhir = LogAktivitasHarian::where('user_id', $userId)
                        ->where('proyek_id', $log->proyek_id)
                        ->whereYear('tanggal', $tahun)
                        ->whereMonth('tanggal', $bulanKe)
                        ->latest('tanggal')
                        ->first();
                    $nilaiStatusMax = $logTerakhir ? match ($logTerakhir->status) {
                        'selesai' => 1,
                        'progress' => 0.5,
                        default => 0
                    } : 0;
                }

                $totalBobotMaks += $bobot;
                $totalNilaiDiperoleh += ($bobot * $nilaiStatusMax);
            }
        }

        $nilaiAkhir = $totalBobotMaks > 0 ? ($totalNilaiDiperoleh / $totalBobotMaks) * 100 : 0;
        $kategori = match (true) {
            $nilaiAkhir >= 85 => 'A',
            $nilaiAkhir >= 70 => 'B',
            $nilaiAkhir >= 55 => 'C',
            default => 'D'
        };

        return [
            'nilai' => round($nilaiAkhir, 2),
            'kategori' => $kategori,
            'total_bobot' => $totalBobotMaks,
            'total_diperoleh' => $totalNilaiDiperoleh
        ];
    }

    public function laporanSaya()
    {
        $user = Auth::user();
        $laporan = [];
        
        // Looping dari bulan ini ke belakang (5 bulan terakhir)
        // Tapi kita balik urutannya jadi terbaru di atas
        $bulanTerbaru = [];
        for ($i = 5; $i >= 0; $i--) {
            $bulan = date('Y-m', strtotime("-$i months"));
            $bulanTerbaru[] = $bulan;
        }
        
        // Urutkan dari terbaru ke terlama (descending)
        rsort($bulanTerbaru);
        
        foreach ($bulanTerbaru as $bulan) {
            $kpi = KpiPenilaian::where('user_id', $user->id)
                ->where('bulan', $bulan)
                ->first();
            
            if ($kpi) {
                $laporan[] = [
                    'bulan' => $bulan,
                    'nilai' => $kpi->nilai_total,
                    'kategori' => $kpi->kategori,
                    'catatan' => $kpi->catatan_atasan
                ];
            } else {
                $hitung = $this->hitungKPI($user->id, $bulan);
                $laporan[] = [
                    'bulan' => $bulan,
                    'nilai' => $hitung['nilai'],
                    'kategori' => $hitung['kategori'],
                    'catatan' => 'Belum dinilai atasan',
                    'is_sementara' => true
                ];
            }
        }
        
        return view('kpi_laporan_saya', compact('laporan'));
    }

    public function listBawahan(Request $request)
    {
        $bulan = $request->get('bulan', date('Y-m'));
        $bawahan = User::where('departemen_id', auth()->user()->departemen_id)
            ->where('id', '!=', auth()->id())
            ->get();
        
        $hitungKPI = [];
        foreach ($bawahan as $user) {
            // HITUNG ULANG SETIAP SAAT, jangan hanya baca dari database
            $hitung = $this->hitungKPI($user->id, $bulan);
            $hitungKPI[$user->id] = $hitung;
        }
        
        return view('kpi_bawahan', compact('bawahan', 'bulan', 'hitungKPI'));
    }

    public function simpanNilai(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'bulan' => 'required|date_format:Y-m',
            'nilai' => 'required|numeric|min:0|max:100',
            'kategori' => 'required|in:A,B,C,D',
            'catatan' => 'nullable|string'
        ]);

        KpiPenilaian::updateOrCreate(
            ['user_id' => $request->user_id, 'bulan' => $request->bulan],
            [
                'nilai_total' => $request->nilai,
                'kategori' => $request->kategori,
                'catatan_atasan' => $request->catatan,
                'dinilai_oleh' => Auth::id()
            ]
        );

        return redirect()->back()->with('success', 'Penilaian KPI berhasil disimpan.');
    }

    /**
     * Atasan: Lihat laporan KPI semua bawahan (read only)
     */
    public function laporanBawahan(Request $request)
    {
        $atasan = Auth::user();
        
        $bulan = $request->get('bulan', date('Y-m'));
        $cariNama = $request->get('nama', ''); // ← TAMBAHKAN INI
        
        // Ambil semua bawahan
        $bawahan = User::where('departemen_id', $atasan->departemen_id)
            ->where('id', '!=', $atasan->id);
        
        // Filter berdasarkan nama jika ada
        if (!empty($cariNama)) {
            $bawahan = $bawahan->where('name', 'like', '%' . $cariNama . '%');
        }
        
        $bawahan = $bawahan->get();
        
        // Daftar periode untuk dropdown (6 bulan terakhir)
        $daftarPeriode = [];
        for ($i = 5; $i >= 0; $i--) {
            $periode = date('Y-m', strtotime("-$i months"));
            $daftarPeriode[$periode] = date('F Y', strtotime($periode . '-01'));
        }
        
        // Hitung KPI setiap bawahan
        $dataKpi = [];
        foreach ($bawahan as $user) {
            $hitung = $this->hitungKPI($user->id, $bulan);
            $kpiExisting = KpiPenilaian::where('user_id', $user->id)
                ->where('bulan', $bulan)
                ->first();
                
            $dataKpi[$user->id] = [
                'id' => $user->id,
                'nama' => $user->name,
                'nilai_auto' => $hitung['nilai'],
                'kategori' => $hitung['kategori'],
                'nilai_akhir' => $kpiExisting->nilai_total ?? $hitung['nilai'],
                'kategori_akhir' => $kpiExisting->kategori ?? $hitung['kategori'],
                'catatan' => $kpiExisting->catatan_atasan ?? '',
            ];
        }
        
        // Urutkan berdasarkan nilai (desc)
        uasort($dataKpi, function ($a, $b) {
            return $b['nilai_auto'] <=> $a['nilai_auto'];
        });
        
        return view('kpi_laporan_bawahan', compact('dataKpi', 'bulan', 'daftarPeriode', 'cariNama'));
    }

    /**
     * Admin: Lihat KPI semua user (read only)
     */
    public function kpiSemua(Request $request)
    {
        $bulan = $request->get('bulan', date('Y-m'));
        $departemenId = $request->get('departemen_id', '');
        $cariNama = $request->get('nama', '');
        
        // Query user
        $query = User::with('departemen');
        
        // Filter berdasarkan departemen
        if (!empty($departemenId)) {
            $query->where('departemen_id', $departemenId);
        }
        
        // Filter berdasarkan nama
        if (!empty($cariNama)) {
            $query->where('name', 'like', '%' . $cariNama . '%');
        }
        
        $semuaUser = $query->get();
        
        // Daftar periode untuk dropdown (12 bulan terakhir)
        $daftarPeriode = [];
        for ($i = 11; $i >= 0; $i--) {
            $periode = date('Y-m', strtotime("-$i months"));
            $daftarPeriode[$periode] = date('F Y', strtotime($periode . '-01'));
        }
        
        // Daftar departemen untuk dropdown filter
        $departemenList = Departemen::orderBy('nama')->get();
        
        $dataKpi = [];
        foreach ($semuaUser as $user) {
            $hitung = $this->hitungKPI($user->id, $bulan);
            $kpiExisting = KpiPenilaian::where('user_id', $user->id)
                ->where('bulan', $bulan)
                ->first();
                
            $dataKpi[] = [
                'id' => $user->id,
                'nama' => $user->name,
                'departemen' => $user->departemen->nama ?? '-',
                'departemen_id' => $user->departemen_id,
                'role' => $user->is_admin ? 'Admin' : ($user->is_atasan ? 'Atasan' : 'Staff'),
                'nilai_auto' => $hitung['nilai'],
                'kategori' => $hitung['kategori'],
                'nilai_akhir' => $kpiExisting->nilai_total ?? $hitung['nilai'],
                'kategori_akhir' => $kpiExisting->kategori ?? $hitung['kategori'],
                'catatan' => $kpiExisting->catatan_atasan ?? '',
            ];
        }
        
        // Urutkan berdasarkan departemen, lalu nilai
        $dataKpi = collect($dataKpi)->sortBy([
            ['departemen', 'asc'],
            ['nilai_auto', 'desc']
        ])->values()->toArray();
        
        return view('admin.kpi_semua', compact('dataKpi', 'bulan', 'daftarPeriode', 'departemenList', 'departemenId', 'cariNama'));
    }

    public function exportBawahan(Request $request)
    {
        $atasan = Auth::user();
        $bulan = $request->get('bulan', date('Y-m'));
        $cariNama = $request->get('nama', '');
        
        $bawahan = User::where('departemen_id', $atasan->departemen_id)
            ->where('id', '!=', $atasan->id);
        
        if (!empty($cariNama)) {
            $bawahan = $bawahan->where('name', 'like', '%' . $cariNama . '%');
        }
        
        $bawahan = $bawahan->get();
        
        $dataKpi = [];
        foreach ($bawahan as $user) {
            $hitung = $this->hitungKPI($user->id, $bulan);
            $kpiExisting = KpiPenilaian::where('user_id', $user->id)
                ->where('bulan', $bulan)
                ->first();
                
            $dataKpi[] = [
                'nama' => $user->name,
                'nilai_auto' => $hitung['nilai'],
                'kategori' => $hitung['kategori'],
                'nilai_akhir' => $kpiExisting->nilai_total ?? $hitung['nilai'],
                'kategori_akhir' => $kpiExisting->kategori ?? $hitung['kategori'],
                'catatan' => $kpiExisting->catatan_atasan ?? '',
            ];
        }
        
        $fileName = 'KPI_Bawahan_' . date('Y-m') . '.xlsx';
        return Excel::download(new KpiBawahanExport($dataKpi, $bulan), $fileName);
    }

    public function exportSemua(Request $request)
    {
        $bulan = $request->get('bulan', date('Y-m'));
        $departemenId = $request->get('departemen_id', '');
        $cariNama = $request->get('nama', '');
        
        $query = User::with('departemen');
        
        if (!empty($departemenId)) {
            $query->where('departemen_id', $departemenId);
        }
        
        if (!empty($cariNama)) {
            $query->where('name', 'like', '%' . $cariNama . '%');
        }
        
        $semuaUser = $query->get();
        
        $dataKpi = [];
        foreach ($semuaUser as $user) {
            $hitung = $this->hitungKPI($user->id, $bulan);
            $dataKpi[] = [
                'nama' => $user->name,
                'departemen' => $user->departemen->nama ?? '-',
                'role' => $user->is_admin ? 'Admin' : ($user->is_atasan ? 'Atasan' : 'Staff'),
                'nilai_auto' => $hitung['nilai'],
                'kategori' => $hitung['kategori'],
            ];
        }
        
        $dataKpi = collect($dataKpi)->sortBy([
            ['departemen', 'asc'],
            ['nilai_auto', 'desc']
        ])->values()->toArray();
        
        $fileName = 'KPI_Semua_User_' . date('Y-m-d') . '.xlsx';
        return Excel::download(new KpiSemuaExport($dataKpi, $bulan), $fileName);
    } 
}