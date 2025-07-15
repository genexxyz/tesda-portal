<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class StudentImportTemplate implements FromArray, WithHeadings, WithStyles, WithColumnWidths, ShouldAutoSize
{
    public function array(): array
    {
        return [
            ['STU001', '1234567890', 'Cruz', 'Juan', 'Dela', 'EIM'],
            ['STU002', '1234567891', 'Santos', 'Maria', 'Garcia', 'HRS'],
            ['STU003', '1234567892', 'Garcia', 'Pedro', 'Lopez', 'EIM'],
        ];
    }

    public function headings(): array
    {
        return [
            'Student ID *',
            'ULI *',
            'Last Name *',
            'First Name *',
            'Middle Name',
            'Course Code *'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            '2:4' => [
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3F4F6']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, // Student ID
            'B' => 20, // ULI
            'C' => 20, // Last Name
            'D' => 20, // First Name
            'E' => 20, // Middle Name
            'F' => 15, // Course Code
        ];
    }
}