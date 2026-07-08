<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KpiSemuaExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $dataKpi;
    protected $bulan;
    
    public function __construct($dataKpi, $bulan)
    {
        $this->dataKpi = $dataKpi;
        $this->bulan = $bulan;
    }
    
    public function collection()
    {
        return collect($this->dataKpi);
    }
    
    public function headings(): array
    {
        $bulanFormatted = date('F Y', strtotime($this->bulan . '-01'));
        return [
            ['LAPORAN KPI SEMUA USER'],
            ["Periode: {$bulanFormatted}"],
            [],
            ['No', 'Nama', 'Departemen', 'Role', 'Nilai KPI', 'Grade']
        ];
    }
    
    public function map($kpi): array
    {
        static $no = 0;
        $no++;
        
        return [
            $no,
            $kpi['nama'],
            $kpi['departemen'],
            $kpi['role'],
            number_format($kpi['nilai_auto'], 2),
            $kpi['kategori'],
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:F1');
        $sheet->mergeCells('A2:F2');
        
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2')->getFont()->setBold(true);
        $sheet->getStyle('A4:F4')->getFont()->setBold(true);
        $sheet->getStyle('A4:F4')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle('A4:F4')->getFill()->getStartColor()->setARGB('FFE0E0E0');
        
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        return [];
    }
}