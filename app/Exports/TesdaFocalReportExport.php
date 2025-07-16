<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Facades\Log;

class TesdaFocalReportExport implements WithMultipleSheets
{
    protected $reportData;
    protected $selectedColumns;

    public function __construct($reportData, $selectedColumns)
    {
        $this->reportData = $reportData;
        $this->selectedColumns = $selectedColumns;
    }

    public function sheets(): array
    {
        $sheets = [];
        
        foreach ($this->reportData as $courseName => $courseData) {
            $sheets[] = new TesdaFocalCourseSheet($courseName, $courseData, $this->selectedColumns);
        }
        
        return $sheets;
    }
}

class TesdaFocalCourseSheet implements FromArray, WithTitle, WithHeadings, WithStyles, WithColumnWidths
{
    protected $courseName;
    protected $courseData;
    protected $selectedColumns;

    public function __construct($courseName, $courseData, $selectedColumns)
    {
        $this->courseName = $courseName;
        $this->courseData = $courseData;
        $this->selectedColumns = $selectedColumns;
    }

    public function title(): string
    {
        // Clean the course name for use as a sheet title (Excel has character limits)
        return substr(preg_replace('/[^A-Za-z0-9 ]/', '', $this->courseName), 0, 30);
    }

    public function array(): array
    {
        $data = [];
        // Gather all campus names
        $campusNames = [];
        foreach ($this->courseData as $assessmentName => $assessmentData) {
            if (isset($assessmentData['campus_data']) && is_array($assessmentData['campus_data'])) {
                foreach ($assessmentData['campus_data'] as $campusData) {
                    $campusNames[] = $campusData['campus'];
                }
            }
        }
        $campusNames = array_unique($campusNames);
        sort($campusNames);

        // Build header row
        $header = ['Exam Type', 'Qualification', 'Course Code', 'Qualification Level'];
        foreach ($campusNames as $campus) {
            $header = array_merge($header, [
                $campus . ' - Total Assessed',
                $campus . ' - Competent',
                $campus . ' - % of Passing',
                $campus . ' - Not Yet Competent',
                $campus . ' - Absent',
                $campus . ' - Total Students'
            ]);
        }
        $data[] = $header;

        // Build rows: each row is exam type + qualification + course, with stats for each campus
        foreach ($this->courseData as $assessmentName => $assessmentData) {
            $info = $assessmentData['assessment_info'] ?? [];
            $row = [
                $info['exam_type'] ?? $assessmentName,
                $info['qualification_name'] ?? '',
                $info['course_code'] ?? '',
                $info['qualification_level'] ?? ''
            ];
            // Map campus stats
            $campusStats = [];
            if (isset($assessmentData['campus_data']) && is_array($assessmentData['campus_data'])) {
                foreach ($campusNames as $campus) {
                    $campusRow = array_filter($assessmentData['campus_data'], function($cd) use ($campus) {
                        return $cd['campus'] === $campus;
                    });
                    $campusRow = $campusRow ? array_values($campusRow)[0] : null;
                    $row = array_merge($row, [
                        $campusRow['total_assessed'] ?? 0,
                        $campusRow['competent'] ?? 0,
                        isset($campusRow['passing_percentage']) ? ($campusRow['passing_percentage'] . '%') : '0%',
                        $campusRow['not_yet_competent'] ?? 0,
                        $campusRow['absent'] ?? 0,
                        $campusRow['total_students'] ?? 0
                    ]);
                }
            } else {
                // No campus data, fill with zeros
                foreach ($campusNames as $campus) {
                    $row = array_merge($row, [0, 0, '0%', 0, 0, 0]);
                }
            }
            $data[] = $row;
        }
        return $data;
    }

    public function headings(): array
    {
        return []; // We handle headings in the array() method
    }

    public function styles(Worksheet $sheet)
    {
        $rowNumber = 1;
        
        foreach ($this->courseData as $assessmentName => $assessmentData) {
            // Style assessment header
            $sheet->getStyle("A{$rowNumber}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'E3F2FD']],
            ]);
            $sheet->mergeCells("A{$rowNumber}:" . chr(64 + count($this->selectedColumns)) . $rowNumber);
            
            $rowNumber += 2; // Skip empty row
            
            // Style column headers
            $sheet->getStyle("A{$rowNumber}:" . chr(64 + count($this->selectedColumns)) . $rowNumber)
                  ->applyFromArray([
                      'font' => ['bold' => true],
                      'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'F5F5F5']],
                      'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                      'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                  ]);
            
            $rowNumber++; // Move to data rows
            
            // Style data rows
            $dataRowCount = count($assessmentData['campus_data']);
            if ($dataRowCount > 0) {
                $endRow = $rowNumber + $dataRowCount - 1;
                $sheet->getStyle("A{$rowNumber}:" . chr(64 + count($this->selectedColumns)) . $endRow)
                      ->applyFromArray([
                          'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                          'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                      ]);
                
                // Left align campus column
                $sheet->getStyle("A{$rowNumber}:A{$endRow}")
                      ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            }
            
            $rowNumber += $dataRowCount + 1; // Skip to next assessment
        }
        
        return $sheet;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20, // Campus
            'B' => 15, // Total Assessed
            'C' => 12, // Competent
            'D' => 15, // % of Passing
            'E' => 18, // Not Yet Competent
            'F' => 20, // Absent
            'G' => 15, // Total Students
        ];
    }

    private function getColumnLabel($column): string
    {
        $labels = [
            'campus' => 'Campus',
            'total_assessed' => 'Total Assessed',
            'competent' => 'Competent',
            'passing_percentage' => '% of Passing',
            'not_yet_competent' => 'Not Yet Competent',
            'absent' => 'No Assessment Yet / Absent',
            'total_students' => 'Total Students'
        ];
        
        return $labels[$column] ?? $column;
    }
}
