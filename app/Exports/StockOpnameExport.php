<?php

namespace App\Exports;

use App\Models\StockOpname;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class StockOpnameExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithEvents
{
    protected $opname;
    protected $rowNumber = 0;

    public function __construct(StockOpname $opname)
    {
        $this->opname = $opname;
    }

    /**
     * Return collection of items
     */
    public function collection()
    {
        return $this->opname->items;
    }

    /**
     * Define the headings
     */
    public function headings(): array
    {
        return [
            'No',
            'Product Code',
            'Product Name',
            'Storage Bin',
            'Batch Number',
            'Serial Number',
            'System Qty',
            'Physical Qty',
            'Variance',
            'Status',
            'Counted By',
            'Counted At',
            'Notes'
        ];
    }

    /**
     * Map data for each row
     */
    public function map($item): array
    {
        $this->rowNumber++;
        
        return [
            $this->rowNumber,
            $item->product->product_code ?? '-',
            $item->product->product_name ?? '-',
            $item->storageBin->bin_code ?? '-',
            $item->batch_number ?? '-',
            $item->serial_number ?? '-',
            $item->system_quantity,
            $item->physical_quantity ?? '-',
            $item->variance ?? '-',
            ucfirst($item->status),
            $item->countedBy->name ?? '-',
            $item->counted_at ? $item->counted_at->format('Y-m-d H:i') : '-',
            $item->notes ?? '-'
        ];
    }

    /**
     * Apply styles
     */
    public function styles(Worksheet $sheet)
    {
        $lastRow = $this->rowNumber + 8; // Header info rows + data rows

        // Style header info
        $sheet->mergeCells('A1:M1');
        $sheet->setCellValue('A1', 'STOCK OPNAME REPORT');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);

        // Opname details
        $sheet->setCellValue('A3', 'Opname Number:');
        $sheet->setCellValue('B3', $this->opname->opname_number);
        $sheet->setCellValue('A4', 'Warehouse:');
        $sheet->setCellValue('B4', $this->opname->warehouse->name ?? '-');
        $sheet->setCellValue('A5', 'Opname Date:');
        $sheet->setCellValue('B5', $this->opname->opname_date->format('Y-m-d'));
        $sheet->setCellValue('A6', 'Type:');
        $sheet->setCellValue('B6', strtoupper($this->opname->opname_type));
        $sheet->setCellValue('A7', 'Status:');
        $sheet->setCellValue('B7', strtoupper($this->opname->status));

        // Make labels bold
        $sheet->getStyle('A3:A7')->getFont()->setBold(true);

        // Style table header
        $sheet->getStyle('A8:M8')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);

        // Apply borders to all data
        $sheet->getStyle('A8:M' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        // Center align specific columns
        $sheet->getStyle('A9:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('G9:I' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('J9:J' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Auto-size columns
        foreach (range('A', 'M') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return [];
    }

    /**
     * Sheet title
     */
    public function title(): string
    {
        return 'Stock Opname';
    }

    /**
     * Register events
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Set row height for header
                $event->sheet->getDelegate()->getRowDimension(1)->setRowHeight(30);
                $event->sheet->getDelegate()->getRowDimension(8)->setRowHeight(20);
            },
        ];
    }
}