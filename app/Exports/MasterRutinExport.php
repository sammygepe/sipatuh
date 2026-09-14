<?php

namespace App\Exports;

use App\Models\MasterAktivitasRutin;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MasterRutinExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = MasterAktivitasRutin::with(['departemen', 'user']);

        // Sama persis dengan filter di controller
        if (!empty($this->filters['departemen_id'])) {
            $query->where('departemen_id', $this->filters['departemen_id']);
        }

        if (!empty($this->filters['search_rutin'])) {
            $query->where('nama_aktivitas', 'like', '%' . $this->filters['search_rutin'] . '%');
        }
        if (!empty($this->filters['periode'])) {
            $query->where('periode', $this->filters['periode']);
        }
        if (!empty($this->filters['status_rutin'])) {
            $query->where('status', $this->filters['status_rutin']);
        }
        if (!empty($this->filters['user_rutin'])) {
            $query->where('user_id', $this->filters['user_rutin']);
        }

        return $query->orderBy('nama_aktivitas')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama Aktivitas',
            'Periode',
            'Bobot',
            'Departemen',
            'Dikerjakan Oleh',
            'Status',
        ];
    }

    public function map($item): array
    {
        return [
            $item->id,
            $item->nama_aktivitas,
            ucfirst($item->periode),
            $item->bobot,
            $item->departemen->nama ?? '-',
            $item->user->name ?? 'Belum ditugaskan',
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