<?php

namespace App\Exports;

use App\Models\LogAktivitasHarian;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RiwayatBawahanExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        // Ambil bawahan (satu departemen, bukan diri sendiri)
        $bawahanQuery = User::where('departemen_id', $this->filters['departemen_id'])
            ->where('id', '!=', $this->filters['atasan_id']);

        if (!empty($this->filters['user_id'])) {
            $bawahanQuery = $bawahanQuery->where('id', $this->filters['user_id']);
        }

        $bawahanIds = $bawahanQuery->pluck('id');

        $query = LogAktivitasHarian::whereIn('user_id', $bawahanIds)
            ->whereYear('tanggal', $this->filters['tahun'])
            ->whereMonth('tanggal', $this->filters['bulan'])
            ->with(['user', 'user.departemen', 'aktivitasRutin', 'proyek']);

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['jenis'])) {
            if ($this->filters['jenis'] == 'rutin') {
                $query->whereNotNull('aktivitas_rutin_id');
            } elseif ($this->filters['jenis'] == 'proyek') {
                $query->whereNotNull('proyek_id');
            }
        }

        // Filter pencarian nama
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->whereHas('aktivitasRutin', function ($sub) use ($search) {
                    $sub->where('nama_aktivitas', 'like', '%' . $search . '%');
                })->orWhereHas('proyek', function ($sub) use ($search) {
                    $sub->where('nama_proyek', 'like', '%' . $search . '%');
                });
            });
        }

        return $query->orderBy('tanggal', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'User',
            'Jenis',
            'Aktivitas',
            'Status',
            'Detail',
        ];
    }

    public function map($log): array
    {
        $namaAktivitas = $log->aktivitas_rutin_id
            ? ($log->aktivitasRutin->nama_aktivitas ?? '-')
            : ($log->proyek->nama_proyek ?? '-');

        $jenis = $log->aktivitas_rutin_id ? 'Rutin' : 'Proyek';

        $status = $log->status == 'selesai'
            ? 'Selesai'
            : ($log->status == 'progress' ? 'Progress' : 'Belum');

        return [
            \Carbon\Carbon::parse($log->tanggal)->format('d-m-Y'),
            $log->user->name ?? '-',
            $jenis,
            $namaAktivitas,
            $status,
            $log->detail ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}