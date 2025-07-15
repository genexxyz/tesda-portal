<?php

namespace App\Exports;

use App\Models\Student;
use App\Models\Course;
use App\Models\Academic;
use App\Models\Assessment;
use App\Models\Result;
use App\Models\CampusCourse;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;

class RegistrarStudentsExport implements WithMultipleSheets
{
    protected $academicYearId;
    protected $courseIds;
    protected $includeResults;
    protected $campusId;

    public function __construct($academicYearId, $courseIds, $includeResults, $campusId)
    {
        $this->academicYearId = $academicYearId;
        $this->courseIds = $courseIds;
        $this->includeResults = $includeResults;
        $this->campusId = $campusId;
    }

    public function sheets(): array
    {
        $sheets = [];
        
        // Verify that the courses belong to the registrar's campus through CampusCourse
        $validCourseIds = CampusCourse::where('campus_id', $this->campusId)
                                    ->whereIn('course_id', $this->courseIds)
                                    ->pluck('course_id');

        $courses = Course::whereIn('id', $validCourseIds)
                        ->orderBy('name')
                        ->get();

        foreach ($courses as $course) {
            $sheets[] = new CourseStudentsSheet(
                $course,
                $this->academicYearId,
                $this->includeResults,
                $this->campusId
            );
        }

        return $sheets;
    }
}

class CourseStudentsSheet implements FromCollection, WithTitle, WithHeadings, WithStyles, WithColumnWidths, ShouldAutoSize, WithEvents
{
    protected $course;
    protected $academicYearId;
    protected $includeResults;
    protected $campusId;
    protected $assessments;
    protected $assessmentColumns;

    public function __construct($course, $academicYearId, $includeResults, $campusId)
    {
        $this->course = $course;
        $this->academicYearId = $academicYearId;
        $this->includeResults = $includeResults;
        $this->campusId = $campusId;
        
        if ($this->includeResults) {
            $this->assessments = Assessment::where('course_id', $this->course->id)
                                         ->where('campus_id', $this->campusId)
                                         ->where('academic_year_id', $this->academicYearId)
                                         ->with(['qualificationType', 'examType', 'schedules.assessor', 'schedules.assessmentCenter'])
                                         ->orderBy('created_at')
                                         ->get();
        } else {
            $this->assessments = collect(); // Empty collection if not including results
        }
    }

    public function collection()
    {
        $students = Student::with(['user', 'academicYear'])
                          ->where('course_id', $this->course->id)
                          ->where('academic_year_id', $this->academicYearId)
                          ->whereHas('user', function($query) {
                              $query->where('campus_id', $this->campusId);
                          })
                          ->get()
                          ->sortBy('user.last_name')
                          ->values();

        $data = collect();

        foreach ($students as $index => $student) {
            $row = [
                $index + 1, // #
                $student->student_id ?: 'Not assigned',
                $student->uli ?: 'Not assigned',
                $student->user->last_name ?? '',
                $student->user->first_name ?? '',
                $student->user->middle_name ?? '',
                $student->user->email ?? '',
                ucfirst($student->user->status ?? 'Unknown'),
            ];

            if ($this->includeResults) {
                foreach ($this->assessments as $assessment) {
                    // Get student's result for this assessment
                    $result = Result::whereHas('assessmentSchedule', function($query) use ($assessment) {
                                    $query->where('assessment_id', $assessment->id);
                                })
                                ->where('student_id', $student->id)
                                ->with(['competencyType', 'assessmentSchedule.assessor', 'assessmentSchedule.assessmentCenter'])
                                ->first();

                    if ($result && $result->assessmentSchedule) {
                        // Student has a result, show the assessment details
                        $schedule = $result->assessmentSchedule;
                        $row[] = $schedule->assessor->name ?? 'TBA'; // Assessor
                        $row[] = $schedule->assessmentCenter->name ?? 'TBA'; // Assessment Center
                        $row[] = $schedule->assessment_date ? $schedule->assessment_date->format('M j, Y') : 'TBA'; // Assessment Date
                        $row[] = $result->competencyType ? $result->competencyType->name : 'Pending'; // Competency Type
                    } else {
                        // Student is not assigned to this assessment yet
                        $row[] = ''; // Assessor
                        $row[] = ''; // Assessment Center
                        $row[] = ''; // Assessment Date
                        $row[] = 'Not Assigned'; // Competency Type
                    }
                }
            }

            $data->push($row);
        }

        return $data;
    }

    public function headings(): array
    {
        $headings = [
            ['#', 'Student ID', 'ULI', 'Last Name', 'First Name', 'Middle Name', 'Email', 'Status'], // Row 1: Basic headers
        ];

        if ($this->includeResults) {
            $assessmentHeaders = [];
            $subHeaders = [];
            
            foreach ($this->assessments as $assessment) {
                $qualificationCode = $assessment->qualificationType->code ?? 'Assessment';
                $qualificationLevel = $assessment->qualificationType->level ?? '';
                $examType = $assessment->examType ? $assessment->examType->type : '';
                
                $mainHeader = $qualificationCode;
                if ($qualificationLevel) {
                    $mainHeader .= ' ' . $qualificationLevel;
                }
                if ($examType) {
                    $mainHeader .= ' (' . $examType . ')';
                }
                
                // Add 4 columns for each assessment (Assessor, Center, Date, Competency)
                $assessmentHeaders = array_merge($assessmentHeaders, [$mainHeader, '', '', '']);
                $subHeaders = array_merge($subHeaders, ['Assessor', 'Assessment Center', 'Assessment Date', 'Competency Type']);
            }
            
            // Merge basic headers with assessment headers
            $headings[0] = array_merge($headings[0], $assessmentHeaders);
            $headings[1] = array_merge(array_fill(0, 8, ''), $subHeaders); // Empty cells for basic info + sub headers
        }

        return $headings;
    }

    public function title(): string
    {
        return substr($this->course->code, 0, 31); // Excel sheet name limit
    }

    public function styles(Worksheet $sheet)
    {
        $styles = [];
        
        // Header styles for rows 1 and 2
        $styles[1] = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ];
        
        if ($this->includeResults) {
            $styles[2] = [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '6366F1']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ];
        }

        return $styles;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();
                
                if ($this->includeResults) {
                    // Apply colors to assessment type headers (row 1)
                    $currentCol = 9; // Start after basic student info columns (A-H)
                    foreach ($this->assessments as $assessment) {
                        $examType = $assessment->examType ? $assessment->examType->type : '';
                        
                        if ($examType === 'ISA') {
                            $color = '059669'; // Green for ISA
                        } elseif ($examType === 'MANDATORY') {
                            $color = 'DC2626'; // Red for MANDATORY
                        } else {
                            $color = '6366F1'; // Blue for others
                        }
                        
                        // Apply color to the 4 columns for this assessment
                        for ($i = 0; $i < 4; $i++) {
                            $cell = $sheet->getCellByColumnAndRow($currentCol + $i, 1);
                            $cell->getStyle()->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setRGB($color);
                        }
                        
                        $currentCol += 4; // Move to next assessment (4 columns per assessment)
                    }
                    
                    // Apply colors to competency type values (data rows)
                    for ($row = 3; $row <= $highestRow; $row++) { // Start from row 3 (data rows)
                        $currentCol = 9; // Start after basic student info
                        foreach ($this->assessments as $assessment) {
                            $competencyCol = $currentCol + 3; // Competency Type is the 4th column in each assessment group
                            $cell = $sheet->getCellByColumnAndRow($competencyCol, $row);
                            $competencyValue = $cell->getValue();
                            
                            $bgColor = 'FFFFFF'; // Default white
                            $textColor = '000000'; // Default black
                            
                            switch ($competencyValue) {
                                case 'Competent':
                                    $bgColor = 'D1FAE5'; // Light green
                                    $textColor = '059669'; // Dark green
                                    break;
                                case 'Not Yet Competent':
                                    $bgColor = 'FEE2E2'; // Light red
                                    $textColor = 'DC2626'; // Dark red
                                    break;
                                case 'Absent':
                                    $bgColor = 'FED7AA'; // Light orange
                                    $textColor = 'EA580C'; // Dark orange
                                    break;
                                case 'Dropped':
                                    $bgColor = 'F3F4F6'; // Light gray
                                    $textColor = '6B7280'; // Dark gray
                                    break;
                                case 'Pending':
                                    $bgColor = 'DBEAFE'; // Light blue
                                    $textColor = '2563EB'; // Dark blue
                                    break;
                                case 'Not Assigned':
                                    $bgColor = 'F9FAFB'; // Very light gray
                                    $textColor = '9CA3AF'; // Medium gray
                                    break;
                            }
                            
                            $cell->getStyle()->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setRGB($bgColor);
                            $cell->getStyle()->getFont()->getColor()->setRGB($textColor);
                            $cell->getStyle()->getFont()->setBold(true);
                            
                            $currentCol += 4; // Move to next assessment
                        }
                    }
                    
                    // Merge cells for assessment headers (row 1)
                    $currentCol = 9;
                    foreach ($this->assessments as $assessment) {
                        $startCell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($currentCol);
                        $endCell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($currentCol + 3);
                        $sheet->mergeCells($startCell . '1:' . $endCell . '1');
                        $currentCol += 4;
                    }
                }
                
                // Auto-fit columns
                foreach (range('A', $highestColumn) as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
                
                // Set minimum height for header rows
                $sheet->getRowDimension(1)->setRowHeight(25);
                if ($this->includeResults) {
                    $sheet->getRowDimension(2)->setRowHeight(20);
                }
            },
        ];
    }

    public function columnWidths(): array
    {
        $widths = [
            'A' => 5,   // #
            'B' => 15,  // Student ID
            'C' => 15,  // ULI
            'D' => 15,  // Last Name
            'E' => 15,  // First Name
            'F' => 15,  // Middle Name
            'G' => 25,  // Email
            'H' => 12,  // Status
        ];

        if ($this->includeResults && $this->assessments) {
            // Add widths for assessment columns (4 columns per assessment)
            $assessmentCount = $this->assessments->count();
            for ($i = 0; $i < $assessmentCount * 4; $i++) {
                $columnIndex = 8 + $i; // Start after basic columns (A-H = 0-7)
                $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex + 1);
                $widths[$columnLetter] = 18; // Assessment columns width
            }
        }

        return $widths;
    }
}
