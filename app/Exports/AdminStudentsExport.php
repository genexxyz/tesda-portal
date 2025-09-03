<?php

namespace App\Exports;

use App\Models\Student;
use App\Models\Campus;
use App\Models\Course;
use App\Models\Academic;
use App\Models\Assessment;
use App\Models\Result;
use Maatwebsite\Excel\Concerns\FromCollection;
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

class AdminStudentsExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, ShouldAutoSize, WithEvents
{
    protected $filters;
    protected $includeAssessments;
    
    public function __construct($filters = [], $includeAssessments = false)
    {
        $this->filters = $filters;
        $this->includeAssessments = $includeAssessments;
    }

    public function collection()
    {
        // Build query for students with filters
        $query = Student::with(['user.campus', 'course', 'academicYear', 'results.competencyType'])
            ->whereHas('user');

        // Apply filters
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('first_name', 'like', '%' . $search . '%')
                             ->orWhere('last_name', 'like', '%' . $search . '%')
                             ->orWhere('email', 'like', '%' . $search . '%');
                })
                ->orWhere('student_id', 'like', '%' . $search . '%')
                ->orWhere('uli', 'like', '%' . $search . '%');
            });
        }

        if (!empty($this->filters['status'])) {
            $query->whereHas('user', function($q) {
                $q->where('status', $this->filters['status']);
            });
        }

        if (!empty($this->filters['campus'])) {
            $query->whereHas('user', function($q) {
                $q->where('campus_id', $this->filters['campus']);
            });
        }

        if (!empty($this->filters['course'])) {
            $query->where('course_id', $this->filters['course']);
        }

        if (!empty($this->filters['academic_year'])) {
            $query->where('academic_year_id', $this->filters['academic_year']);
        }

        // Order by user's last name, then first name
        $query->join('users', 'students.user_id', '=', 'users.id')
              ->orderBy('users.last_name', 'asc')
              ->orderBy('users.first_name', 'asc')
              ->select('students.*');

        $students = $query->get();

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
                $student->user->campus->name ?? 'Not assigned',
                $student->course->code ?? 'Not assigned',
                $student->course->name ?? 'Not assigned',
                $student->academicYear->formatted_description ?? 'Not assigned',
                ucfirst($student->user->status ?? 'Unknown'),
            ];

            if ($this->includeAssessments) {
                // Get assessment results for this student
                $results = Result::where('student_id', $student->id)
                    ->with(['competencyType', 'assessmentSchedule.assessment.qualificationType', 'assessmentSchedule.assessment.examType'])
                    ->get();

                $assessmentData = [];
                foreach ($results as $result) {
                    $assessment = $result->assessmentSchedule->assessment;
                    $qualification = $assessment->qualificationType->code ?? 'Unknown';
                    $examType = $assessment->examType->type ?? 'Unknown';
                    $competency = $result->competencyType->name ?? 'Unknown';
                    $assessmentData[] = "{$qualification} - {$examType}: {$competency}";
                }

                $row[] = implode('; ', $assessmentData) ?: 'No assessments';
            }

            $data->push($row);
        }

        return $data;
    }

    public function headings(): array
    {
        $headings = [
            '#',
            'Student ID',
            'ULI',
            'Last Name',
            'First Name',
            'Middle Name',
            'Email',
            'Campus',
            'Course Code',
            'Course Name',
            'Academic Year',
            'Status',
        ];

        if ($this->includeAssessments) {
            $headings[] = 'Assessment Results';
        }

        return $headings;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Header row styling
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();
                
                // Apply alternating row colors for better readability
                for ($row = 2; $row <= $highestRow; $row++) {
                    if ($row % 2 == 0) {
                        $sheet->getStyle("A{$row}:{$highestColumn}{$row}")
                              ->getFill()
                              ->setFillType(Fill::FILL_SOLID)
                              ->getStartColor()
                              ->setRGB('F9FAFB'); // Light gray for even rows
                    }
                }

                // Apply status colors to the Status column
                $statusColumn = 'L'; // Assuming Status is in column L
                for ($row = 2; $row <= $highestRow; $row++) {
                    $cell = $sheet->getCell($statusColumn . $row);
                    $status = $cell->getValue();
                    
                    $bgColor = 'FFFFFF'; // Default white
                    $textColor = '000000'; // Default black
                    
                    switch (strtolower($status)) {
                        case 'active':
                            $bgColor = 'D1FAE5'; // Light green
                            $textColor = '059669'; // Dark green
                            break;
                        case 'inactive':
                            $bgColor = 'FED7AA'; // Light orange
                            $textColor = 'EA580C'; // Dark orange
                            break;
                        case 'dropped':
                            $bgColor = 'FEE2E2'; // Light red
                            $textColor = 'DC2626'; // Dark red
                            break;
                    }
                    
                    $cell->getStyle()->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB($bgColor);
                    $cell->getStyle()->getFont()->getColor()->setRGB($textColor);
                    $cell->getStyle()->getFont()->setBold(true);
                }
                
                // Auto-fit columns
                foreach (range('A', $highestColumn) as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
                
                // Set minimum height for header row
                $sheet->getRowDimension(1)->setRowHeight(25);
            },
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // #
            'B' => 15,  // Student ID
            'C' => 15,  // ULI
            'D' => 15,  // Last Name
            'E' => 15,  // First Name
            'F' => 15,  // Middle Name
            'G' => 25,  // Email
            'H' => 15,  // Campus
            'I' => 12,  // Course Code
            'J' => 30,  // Course Name
            'K' => 15,  // Academic Year
            'L' => 12,  // Status
            'M' => 50,  // Assessment Results (if included)
        ];
    }
}
