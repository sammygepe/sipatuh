<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KpiBawahanExport implements FromCollection, WithHeadings, WithMapping, WithStyles
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
            ['LAPORAN KPI BAWAHAN'],
            ["Periode: {$bulanFormatted}"],
            [],
            ['No', 'Nama Bawahan', 'Nilai Auto', 'Grade Auto', 'Nilai Akhir', 'Grade Akhir', 'Catatan Atasan']
        ];
    }
    
    public function map($kpi): array
    {
        static $no = 0;
        $no++;
        
        return [
            $no,
            $kpi['nama'],
            number_format($kpi['nilai_auto'], 2),
            $kpi['kategori'],
            number_format($kpi['nilai_akhir'], 2),
            $kpi['kategori_akhir'],
            $kpi['catatan'] ?? '-',
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        // Merge title cells
        $sheet->mergeCells('A1:G1');
        $sheet->mergeCells('A2:G2');
        
        // Style title
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2')->getFont()->setBold(true);
        
        // Style header
        $sheet->getStyle('A4:G4')->getFont()->setBold(true);
        $sheet->getStyle('A4:G4')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle('A4:G4')->getFill()->getStartColor()->setARGB('FFE0E0E0');
        
        // Auto size columns
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        return [];
    }
}