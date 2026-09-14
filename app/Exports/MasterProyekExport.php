<?php

namespace App\Exports;

use App\Models\MasterProyek;
use App\Models\LogAktivitasHarian;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MasterProyekExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = MasterProyek::with(['departemen', 'user']);

        if (!empty($this->filters['departemen_id'])) {
            $query->where('departemen_id', $this->filters['departemen_id']);
        }

        if (!empty($this->filters['search_proyek'])) {
            $query->where('nama_proyek', 'like', '%' . $this->filters['search_proyek'] . '%');
        }
        if (!empty($this->filters['status_proyek'])) {
            $query->where('status', $this->filters['status_proyek']);
        }
        if (!empty($this->filters['user_proyek'])) {
            $query->where('user_id', $this->filters['user_proyek']);
        }

        // Filter status proyek terakhir
        if (!empty($this->filters['status_proyek_akhir'])) {
            $query->whereIn('id', function ($sub) {
                $sub->select('proyek_id')
                    ->from('log_aktivitas_harian')
                    ->whereIn('id', function ($sub2) {
                        $sub2->selectRaw('MAX(id)')
                            ->from('log_aktivitas_harian')
                            ->whereNotNull('proyek_id')
                            ->groupBy('proyek_id');
                    })
                    ->where('status', $this->filters['status_proyek_akhir']);
            });
        }

        return $query->orderBy('nama_proyek')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama Proyek',
            'Tgl Mulai',
            'Tgl Berakhir',
            'Bobot',
            'Departemen',
            'Dikerjakan Oleh',
            'Status Proyek',
            'Status',
        ];
    }

    public function map($item): array
    {
        // Ambil log terakhir proyek ini
        $log = LogAktivitasHarian::where('proyek_id', $item->id)
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        $statusProyek = '-';
        if ($log) {
            if ($log->status == 'selesai') {
                $statusProyek = 'Selesai' . ($item->tgl_selesai ? ' (' . \Carbon\Carbon::parse($item->tgl_selesai)->format('d-m-Y') . ')' : '');
            } elseif ($log->status == 'progress') {
                $statusProyek = 'Progress';
            } else {
                $statusProyek = 'Belum';
            }
        }

        return [
            $item->id,
            $item->nama_proyek,
            $item->tgl_mulai ? \Carbon\Carbon::parse($item->tgl_mulai)->format('d-m-Y') : '-',
            $item->tgl_berakhir ? \Carbon\Carbon::parse($item->tgl_berakhir)->format('d-m-Y') : '-',
            $item->bobot,
            $item->departemen->nama ?? '-',
            $item->user->name ?? 'Belum ditugaskan',
            $statusProyek,
            $item->status == 'approved' ? 'Aktif' : ($item->status == 'pending' ? 'Pending' : 'Nonaktif'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}