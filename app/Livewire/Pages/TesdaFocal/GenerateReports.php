<?php

namespace App\Livewire\Pages\TesdaFocal;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\Academic;
use App\Models\Campus;
use App\Models\Course;
use App\Models\Assessment;
use App\Models\Student;
use App\Models\Result;
use App\Models\CampusCourse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TesdaFocalReportExport;
use Barryvdh\DomPDF\Facade\Pdf;

class GenerateReports extends Component
{
    #[Layout('layouts.app')]
    #[Title('Dashboard')]
    // Filter Properties
    public $selectedAcademicYear = '';
    public $selectedCampuses = [];
    public $selectedCourses = [];
    public $selectedAssessments = [];
    public $selectedExamTypes = [];
    public $selectedQualifications = [];
    public $outputFormat = 'excel';
    
    // Report Configuration
    public $selectedColumns = [];
    public $includeCharts = true;
    public $includeStatistics = true;
    
    // Preview Properties
    public $previewData = [];
    public $previewStats = [];
    public $isLoadingPreview = false;
    public $estimatedRows = 0;
    public $estimatedFileSize = '';
    
    // UI State
    public $showAdvancedOptions = false;
    
    public $outputFormats = [
        'excel' => 'Excel (.xlsx)',
        'pdf' => 'PDF Document'
    ];

    public function mount()
    {
        // Set default academic year to active
        $activeAcademicYear = Academic::where('is_active', true)->first();
        if ($activeAcademicYear) {
            $this->selectedAcademicYear = $activeAcademicYear->id;
        }
        
        // Set default output format to excel
        $this->outputFormat = 'excel';
        
        // Initialize default columns
        $this->updateDefaultColumns();
        
        // Load initial preview
        $this->updatePreview();
    }

    public function updatedSelectedAcademicYear()
    {
        $this->selectedCourses = []; // Reset courses when academic year changes
        $this->selectedAssessments = []; // Reset assessments when academic year changes
        $this->updatePreview();
    }

    public function updatedSelectedCampuses()
    {
        $this->selectedCourses = []; // Reset courses when campuses change
        $this->selectedAssessments = []; // Reset assessments when campuses change
        $this->updatePreview();
    }

    public function updatedSelectedCourses()
    {
        $this->selectedAssessments = []; // Reset assessments when courses change
        $this->updatePreview();
    }

    public function updatedSelectedExamTypes()
    {
        $this->selectedAssessments = []; // Reset assessments when exam types change
        $this->updatePreview();
    }

    public function updatedSelectedQualifications()
    {
        $this->selectedAssessments = []; // Reset assessments when qualifications change
        $this->updatePreview();
    }

    public function updatedSelectedAssessments()
    {
        $this->updatePreview();
    }

    public function updateDefaultColumns()
    {
        // Use the exact same columns as TESDA Focal view results table
        $this->selectedColumns = [
            'campus',
            'total_assessed',
            'competent',
            'passing_percentage',
            'not_yet_competent',
            'absent',
            'total_students'
        ];
    }

    public function updatePreview()
    {
        $this->isLoadingPreview = true;
        
        // Simulate loading delay for better UX
        $this->dispatch('preview-loading');
        
        try {
            if (!empty($this->selectedAssessments)) {
                // Show assessment-specific preview
                $this->previewData = $this->getPreviewDataByAssessment();
            } else {
                // Show general preview
                $query = $this->buildBaseQuery();
                
                if ($query) {
                    // Get sample data (limit to 10 rows for preview)
                    $this->previewData = $query->limit(10)->get()->toArray();
                    
                    // Calculate statistics
                    $this->calculatePreviewStats($query);
                } else {
                    $this->previewData = [];
                    $this->previewStats = [];
                    $this->estimatedRows = 0;
                }
            }
            
            // Estimate file size
            $this->estimateFileSize();
            
        } catch (\Exception $e) {
            Log::error('Error updating preview: ' . $e->getMessage());
            $this->previewData = [];
            $this->previewStats = [];
            $this->estimatedRows = 0;
        }
        
        $this->isLoadingPreview = false;
        $this->dispatch('preview-updated');
    }

    private function getPreviewDataByAssessment()
    {
        if (empty($this->selectedAssessments)) {
            return [];
        }

        // Get structured data like view results page
        $reportData = $this->getReportDataByAssessment();
        
        // Flatten for preview display - show first few campus rows from first assessment
        $previewData = [];
        $rowCount = 0;
        
        foreach ($reportData as $courseName => $courseData) {
            foreach ($courseData as $assessmentKey => $assessmentData) {
                foreach ($assessmentData['campus_data'] as $campusRow) {
                    if ($rowCount >= 10) break 3; // Break out of all loops
                    
                    $previewData[] = [
                        'course_name' => $courseName,
                        'assessment_name' => $assessmentKey,
                        'campus' => $campusRow['campus'],
                        'total_assessed' => $campusRow['total_assessed'],
                        'competent' => $campusRow['competent'],
                        'passing_percentage' => $campusRow['passing_percentage'] . '%',
                        'not_yet_competent' => $campusRow['not_yet_competent'],
                        'absent' => $campusRow['absent'],
                        'total_students' => $campusRow['total_students']
                    ];
                    $rowCount++;
                }
            }
        }
        
        // Update stats for assessment-specific preview
        $this->calculateAssessmentPreviewStats($reportData);
        
        return $previewData;
    }

    private function calculateAssessmentPreviewStats($reportData = null)
    {
        try {
            if (!$reportData) {
                $reportData = $this->getReportDataByAssessment();
            }
            
            $totalRows = 0;
            $totalCourses = count($reportData);
            $totalAssessments = 0;
            
            foreach ($reportData as $courseData) {
                foreach ($courseData as $assessmentData) {
                    $totalAssessments++;
                    $totalRows += count($assessmentData['campus_data']);
                }
            }
            
            $this->estimatedRows = $totalRows;
            
            $academicYear = 'All Years';
            if ($this->selectedAcademicYear) {
                $academic = Academic::find($this->selectedAcademicYear);
                $academicYear = $academic ? $academic->formatted_description : 'Unknown';
            }
            
            $this->previewStats = [
                'total_records' => $this->estimatedRows,
                'campuses_included' => count($this->selectedCampuses) ?: Campus::count(),
                'courses_included' => $totalCourses,
                'assessments_included' => $totalAssessments,
                'academic_year' => $academicYear
            ];
        } catch (\Exception $e) {
            Log::error('Error calculating assessment preview stats: ' . $e->getMessage());
            $this->estimatedRows = 0;
            $this->previewStats = [
                'total_records' => 0,
                'campuses_included' => 0,
                'courses_included' => 0,
                'assessments_included' => 0,
                'academic_year' => 'Unknown'
            ];
        }
    }

    private function buildBaseQuery()
    {
        try {
            // This is a simplified base query - would need to be customized based on report type
            $query = Student::with(['user', 'course', 'academicYear', 'results.assessmentSchedule.assessment'])
                            ->whereHas('user', function($q) {
                                if (!empty($this->selectedCampuses)) {
                                    $q->whereIn('campus_id', $this->selectedCampuses);
                                }
                            });

            if ($this->selectedAcademicYear) {
                $query->where('academic_year_id', $this->selectedAcademicYear);
            }

            if (!empty($this->selectedCourses)) {
                $query->whereIn('course_id', $this->selectedCourses);
            }

            // Filter by selected assessments if any
            if (!empty($this->selectedAssessments)) {
                $query->whereHas('results.assessmentSchedule', function($q) {
                    $q->whereIn('assessment_id', $this->selectedAssessments);
                });
            }

            return $query;
        } catch (\Exception $e) {
            Log::error('Error building base query: ' . $e->getMessage());
            return Student::whereRaw('1 = 0'); // Return empty query
        }
    }

    private function calculatePreviewStats($query)
    {
        try {
            $this->estimatedRows = $query ? $query->count() : 0;
            
            $academicYear = 'All Years';
            if ($this->selectedAcademicYear) {
                $academic = Academic::find($this->selectedAcademicYear);
                $academicYear = $academic ? $academic->formatted_description : 'Unknown';
            }
            
            $this->previewStats = [
                'total_records' => $this->estimatedRows,
                'campuses_included' => count($this->selectedCampuses) ?: Campus::count(),
                'courses_included' => count($this->selectedCourses) ?: Course::count(),
                'assessments_included' => count($this->selectedAssessments),
                'academic_year' => $academicYear
            ];
        } catch (\Exception $e) {
            Log::error('Error calculating preview stats: ' . $e->getMessage());
            $this->estimatedRows = 0;
            $this->previewStats = [
                'total_records' => 0,
                'campuses_included' => 0,
                'courses_included' => 0,
                'assessments_included' => 0,
                'academic_year' => 'Unknown'
            ];
        }
    }

    private function estimateFileSize()
    {
        $avgRowSize = count($this->selectedColumns) * 15; // Average 15 chars per cell
        $totalSize = $this->estimatedRows * $avgRowSize;
        
        if ($totalSize < 1024) {
            $this->estimatedFileSize = $totalSize . ' B';
        } elseif ($totalSize < 1024 * 1024) {
            $this->estimatedFileSize = round($totalSize / 1024, 1) . ' KB';
        } else {
            $this->estimatedFileSize = round($totalSize / (1024 * 1024), 1) . ' MB';
        }
    }

    public function generateReport()
    {
        $this->validate([
            'selectedAcademicYear' => 'required',
            'outputFormat' => 'required'
        ]);

        try {
            // Store the current filter parameters in session for the download route
            session([
                'tesda_report_filters' => [
                    'selectedAcademicYear' => $this->selectedAcademicYear,
                    'selectedCampuses' => $this->selectedCampuses,
                    'selectedCourses' => $this->selectedCourses,
                    'selectedExamTypes' => $this->selectedExamTypes,
                    'selectedQualifications' => $this->selectedQualifications,
                    'selectedAssessments' => $this->selectedAssessments,
                    'selectedColumns' => $this->selectedColumns,
                    'outputFormat' => $this->outputFormat
                ]
            ]);
            
            // Trigger download using JavaScript
            $this->dispatch('download-report', [
                'url' => route('tesda-focal.download-report'),
                'format' => $this->outputFormat
            ]);
            
        } catch (\Exception $e) {
            $this->dispatch('swal:error', [
                'title' => 'Error',
                'text' => 'Failed to generate report: ' . $e->getMessage()
            ]);
        }
    }

    private function getReportData()
    {
        try {
            // Build the query to get actual report data
            $campusQuery = Campus::with(['users.student.results.assessmentSchedule.assessment.qualificationType', 
                                  'users.student.results.assessmentSchedule.assessment.examType',
                                  'users.student.results.competencyType',
                                  'users.student.course',
                                  'users.student.academicYear']);

            // Filter by selected campuses if any
            if (!empty($this->selectedCampuses)) {
                $campusQuery->whereIn('id', $this->selectedCampuses);
            }

            // Only get campuses that have students matching our criteria
            $campusQuery->whereHas('users.student', function($q) {
                if ($this->selectedAcademicYear) {
                    $q->where('academic_year_id', $this->selectedAcademicYear);
                }
                if (!empty($this->selectedCourses)) {
                    $q->whereIn('course_id', $this->selectedCourses);
                }
            });

            $campuses = $campusQuery->get();
            
            if ($campuses->isEmpty()) {
                return [];
            }

            $reportData = [];

            foreach ($campuses as $campus) {
                // Get users for this campus, ensuring they have students
                $users = $campus->users ?? collect();
                
                $students = $users->filter(function($user) {
                    return $user->student !== null;
                })->map(function($user) {
                    return $user->student;
                })->filter(function($student) {
                    // Apply filters
                    if ($this->selectedAcademicYear && $student->academic_year_id != $this->selectedAcademicYear) {
                        return false;
                    }
                    if (!empty($this->selectedCourses) && !in_array($student->course_id, $this->selectedCourses)) {
                        return false;
                    }
                    return true;
                });

                if ($students->isEmpty()) continue;

                // Filter students by selected assessments if any
                if (!empty($this->selectedAssessments)) {
                    $students = $students->filter(function($student) {
                        return $student->results && $student->results->filter(function($result) {
                            return $result->assessmentSchedule && 
                                   in_array($result->assessmentSchedule->assessment_id, $this->selectedAssessments);
                        })->isNotEmpty();
                    });
                }

                $totalStudents = $students->count();
                if ($totalStudents === 0) continue;

                // Calculate statistics for this campus
                $assessedStudents = $students->filter(function($student) {
                    return $student->results && $student->results->isNotEmpty();
                });

                $competentStudents = $students->filter(function($student) {
                    if (!$student->results) return false;
                    return $student->results->filter(function($result) {
                        return $result->competencyType && 
                               strtolower($result->competencyType->name) === 'competent';
                    })->isNotEmpty();
                });

                $notYetCompetentStudents = $students->filter(function($student) {
                    if (!$student->results) return false;
                    return $student->results->filter(function($result) {
                        return $result->competencyType && 
                               strtolower($result->competencyType->name) === 'not yet competent';
                    })->isNotEmpty();
                });

                $totalAssessed = $assessedStudents->count();
                $competentCount = $competentStudents->count();
                $notYetCompetentCount = $notYetCompetentStudents->count();
                $absentCount = $totalStudents - $totalAssessed;
                
                $passingPercentage = $totalAssessed > 0 ? round(($competentCount / $totalAssessed) * 100, 1) : 0;

                $reportData[] = [
                    'campus' => $campus->name,
                    'total_assessed' => $totalAssessed,
                    'competent' => $competentCount,
                    'passing_percentage' => $passingPercentage . '%',
                    'not_yet_competent' => $notYetCompetentCount,
                    'absent' => $absentCount,
                    'total_students' => $totalStudents
                ];
            }

            return $reportData;
            
        } catch (\Exception $e) {
            // Log the error and return empty array
            Log::error('Error generating report data: ' . $e->getMessage());
            return [];
        }
    }

    private function getReportDataByAssessment()
    {
        // Get data organized exactly like the view results page:
        // Grouped by Course -> Assessment -> Campus data
        if (empty($this->selectedAssessments)) {
            return [];
        }

        $reportData = [];
        
        // Get assessments with relationships
        $assessments = Assessment::with(['course', 'qualificationType', 'examType', 'campus'])
                                ->whereIn('id', $this->selectedAssessments)
                                ->get();

        foreach ($assessments as $assessment) {
            $courseName = $assessment->course->name ?? 'Unknown Course';
            $assessmentKey = $assessment->examType->type . ' - ' . 
                           $assessment->course->code . ' ' . 
                           $assessment->qualificationType->name . ' ' . 
                           ($assessment->qualificationType->level ?? '');

            // Initialize course group if not exists
            if (!isset($reportData[$courseName])) {
                $reportData[$courseName] = [];
            }

            // Initialize assessment group if not exists
            if (!isset($reportData[$courseName][$assessmentKey])) {
                $reportData[$courseName][$assessmentKey] = [
                    'assessment_info' => [
                        'exam_type' => $assessment->examType->type ?? 'Unknown',
                        'course_code' => $assessment->course->code ?? 'Unknown',
                        'qualification_name' => $assessment->qualificationType->name ?? 'Unknown',
                        'qualification_level' => $assessment->qualificationType->level ?? '',
                        'course_name' => $courseName
                    ],
                    'campus_data' => []
                ];
            }

            // Get campus data for this assessment
            $campusData = $this->getAssessmentCampusData($assessment->id);
            
            foreach ($campusData as $data) {
                $reportData[$courseName][$assessmentKey]['campus_data'][] = $data;
            }
        }

        return $reportData;
    }

    private function getAssessmentCampusData($assessmentId)
    {
        try {
            // Get campuses that have students with this assessment
            $campusQuery = Campus::whereHas('users.student.results.assessmentSchedule', function($q) use ($assessmentId) {
                $q->where('assessment_id', $assessmentId);
            });

            // Filter by selected campuses if any
            if (!empty($this->selectedCampuses)) {
                $campusQuery->whereIn('id', $this->selectedCampuses);
            }

            $campuses = $campusQuery->get();
            
            $campusData = [];

            foreach ($campuses as $campus) {
                // Get students for this campus who have results for this specific assessment
                $students = $campus->users()
                    ->whereHas('student', function($q) use ($assessmentId) {
                        // Apply academic year filter
                        if ($this->selectedAcademicYear) {
                            $q->where('academic_year_id', $this->selectedAcademicYear);
                        }
                        
                        // Apply course filter
                        if (!empty($this->selectedCourses)) {
                            $q->whereIn('course_id', $this->selectedCourses);
                        }
                        
                        // Must have results for this assessment
                        $q->whereHas('results.assessmentSchedule', function($subQ) use ($assessmentId) {
                            $subQ->where('assessment_id', $assessmentId);
                        });
                    })
                    ->with(['student.results.assessmentSchedule', 'student.results.competencyType'])
                    ->get()
                    ->pluck('student');

                if ($students->isEmpty()) continue;

                // Calculate statistics for this campus and assessment
                $studentsWithResults = $students->filter(function($student) use ($assessmentId) {
                    return $student->results->filter(function($result) use ($assessmentId) {
                        return $result->assessmentSchedule && 
                               $result->assessmentSchedule->assessment_id == $assessmentId;
                    })->isNotEmpty();
                });

                $competentStudents = $students->filter(function($student) use ($assessmentId) {
                    return $student->results->filter(function($result) use ($assessmentId) {
                        return $result->assessmentSchedule && 
                               $result->assessmentSchedule->assessment_id == $assessmentId &&
                               $result->competencyType && 
                               strtolower($result->competencyType->name) === 'competent';
                    })->isNotEmpty();
                });

                $notYetCompetentStudents = $students->filter(function($student) use ($assessmentId) {
                    return $student->results->filter(function($result) use ($assessmentId) {
                        return $result->assessmentSchedule && 
                               $result->assessmentSchedule->assessment_id == $assessmentId &&
                               $result->competencyType && 
                               strtolower($result->competencyType->name) === 'not yet competent';
                    })->isNotEmpty();
                });

                $totalAssessed = $studentsWithResults->count();
                $competentCount = $competentStudents->count();
                $notYetCompetentCount = $notYetCompetentStudents->count();
                
                $totalStudents = $students->count();
                $absentCount = $totalStudents - $totalAssessed;
                
                $passingPercentage = $totalAssessed > 0 ? round(($competentCount / $totalAssessed) * 100, 1) : 0;

                $campusData[] = [
                    'campus' => $campus->name,
                    'total_assessed' => $totalAssessed,
                    'competent' => $competentCount,
                    'passing_percentage' => $passingPercentage,
                    'not_yet_competent' => $notYetCompetentCount,
                    'absent' => $absentCount,
                    'total_students' => $totalStudents
                ];
            }

            return $campusData;
            
        } catch (\Exception $e) {
            Log::error('Error generating campus data for assessment: ' . $e->getMessage());
            return [];
        }
    }

    private function getReportDataForAssessment($assessmentId)
    {
        try {
            // Build the query to get data for specific assessment
            $campusQuery = Campus::with(['users.student.results.assessmentSchedule.assessment.qualificationType', 
                                  'users.student.results.assessmentSchedule.assessment.examType',
                                  'users.student.results.competencyType',
                                  'users.student.course',
                                  'users.student.academicYear']);

            // Filter by selected campuses if any
            if (!empty($this->selectedCampuses)) {
                $campusQuery->whereIn('id', $this->selectedCampuses);
            }

            // Only get campuses that have students with results for this assessment
            $campusQuery->whereHas('users.student.results.assessmentSchedule', function($q) use ($assessmentId) {
                $q->where('assessment_id', $assessmentId);
                if ($this->selectedAcademicYear) {
                    $q->whereHas('assessment', function($subQ) {
                        $subQ->where('academic_year_id', $this->selectedAcademicYear);
                    });
                }
            });

            $campuses = $campusQuery->get();
            
            if ($campuses->isEmpty()) {
                return [];
            }

            $reportData = [];

            foreach ($campuses as $campus) {
                // Get students for this campus who have results for this specific assessment
                $users = $campus->users ?? collect();
                
                $students = $users->filter(function($user) use ($assessmentId) {
                    if (!$user->student) return false;
                    
                    // Apply academic year filter
                    if ($this->selectedAcademicYear && $user->student->academic_year_id != $this->selectedAcademicYear) {
                        return false;
                    }
                    
                    // Apply course filter
                    if (!empty($this->selectedCourses) && !in_array($user->student->course_id, $this->selectedCourses)) {
                        return false;
                    }
                    
                    // Check if student has results for this assessment
                    return $user->student->results && $user->student->results->filter(function($result) use ($assessmentId) {
                        return $result->assessmentSchedule && 
                               $result->assessmentSchedule->assessment_id == $assessmentId;
                    })->isNotEmpty();
                    
                })->map(function($user) {
                    return $user->student;
                });

                if ($students->isEmpty()) continue;

                // Calculate statistics for this campus and assessment
                $studentsWithResults = $students->filter(function($student) use ($assessmentId) {
                    return $student->results && $student->results->filter(function($result) use ($assessmentId) {
                        return $result->assessmentSchedule && 
                               $result->assessmentSchedule->assessment_id == $assessmentId;
                    })->isNotEmpty();
                });

                $competentStudents = $students->filter(function($student) use ($assessmentId) {
                    if (!$student->results) return false;
                    return $student->results->filter(function($result) use ($assessmentId) {
                        return $result->assessmentSchedule && 
                               $result->assessmentSchedule->assessment_id == $assessmentId &&
                               $result->competencyType && 
                               strtolower($result->competencyType->name) === 'competent';
                    })->isNotEmpty();
                });

                $notYetCompetentStudents = $students->filter(function($student) use ($assessmentId) {
                    if (!$student->results) return false;
                    return $student->results->filter(function($result) use ($assessmentId) {
                        return $result->assessmentSchedule && 
                               $result->assessmentSchedule->assessment_id == $assessmentId &&
                               $result->competencyType && 
                               strtolower($result->competencyType->name) === 'not yet competent';
                    })->isNotEmpty();
                });

                $totalAssessed = $studentsWithResults->count();
                $competentCount = $competentStudents->count();
                $notYetCompetentCount = $notYetCompetentStudents->count();
                
                // For assessment-specific reports, total students should be those who were supposed to take this assessment
                $totalStudents = $students->count();
                $absentCount = $totalStudents - $totalAssessed;
                
                $passingPercentage = $totalAssessed > 0 ? round(($competentCount / $totalAssessed) * 100, 1) : 0;

                $reportData[] = [
                    'campus' => $campus->name,
                    'total_assessed' => $totalAssessed,
                    'competent' => $competentCount,
                    'passing_percentage' => $passingPercentage . '%',
                    'not_yet_competent' => $notYetCompetentCount,
                    'absent' => $absentCount,
                    'total_students' => $totalStudents
                ];
            }

            return $reportData;
            
        } catch (\Exception $e) {
            Log::error('Error generating assessment report data: ' . $e->getMessage());
            return [];
        }
    }

    public function toggleColumn($column)
    {
        if (in_array($column, $this->selectedColumns)) {
            $this->selectedColumns = array_filter($this->selectedColumns, fn($col) => $col !== $column);
        } else {
            $this->selectedColumns[] = $column;
        }
        $this->updatePreview();
    }

    public function getAvailableAcademicYearsProperty()
    {
        return Academic::orderBy('start_year', 'desc')->get();
    }

    public function getAvailableCampusesProperty()
    {
        return Campus::orderBy('name')->get();
    }

    public function getAvailableCoursesProperty()
    {
        if (empty($this->selectedCampuses)) {
            return Course::orderBy('code')->get();
        }
        
        $courseIds = CampusCourse::whereIn('campus_id', $this->selectedCampuses)
                                ->pluck('course_id');
        
        return Course::whereIn('id', $courseIds)->orderBy('code')->get();
    }

    public function getAvailableAssessmentsProperty()
    {
        if (!$this->selectedAcademicYear) {
            return collect();
        }
        
        $assessmentsQuery = Assessment::with(['qualificationType', 'examType'])
                                    ->where('academic_year_id', $this->selectedAcademicYear);
        
        // Filter by selected campuses if any
        if (!empty($this->selectedCampuses)) {
            $assessmentsQuery->whereIn('campus_id', $this->selectedCampuses);
        }
        
        // Filter by selected courses if any
        if (!empty($this->selectedCourses)) {
            $assessmentsQuery->whereIn('course_id', $this->selectedCourses);
        }
        
        // Filter by selected exam types if any
        if (!empty($this->selectedExamTypes)) {
            $assessmentsQuery->whereHas('examType', function($q) {
                $q->whereIn('id', $this->selectedExamTypes);
            });
        }
        
        // Filter by selected qualifications if any
        if (!empty($this->selectedQualifications)) {
            $assessmentsQuery->whereHas('qualificationType', function($q) {
                $q->whereIn('id', $this->selectedQualifications);
            });
        }
        
        return $assessmentsQuery->get()->map(function ($assessment) {
            $qualificationName = $assessment->qualificationType->name ?? 'Unknown Qualification';
            $examType = $assessment->examType->type ?? 'Unknown Type';
            $level = $assessment->qualificationType->level ?? '';
            
            return (object) [
                'id' => $assessment->id,
                'name' => $examType . ' - ' . $qualificationName . ($level ? ' ' . $level : ''),
                'exam_type' => $examType,
                'qualification_name' => $qualificationName
            ];
        })->sortBy('name');
    }

    public function getAvailableExamTypesProperty()
    {
        if (!$this->selectedAcademicYear) {
            return collect();
        }
        
        $query = Assessment::with('examType')->where('academic_year_id', $this->selectedAcademicYear);
        
        if (!empty($this->selectedCampuses)) {
            $query->whereIn('campus_id', $this->selectedCampuses);
        }
        
        if (!empty($this->selectedCourses)) {
            $query->whereIn('course_id', $this->selectedCourses);
        }
        
        return $query->get()
                    ->pluck('examType')
                    ->unique('id')
                    ->sortBy('type');
    }

    public function getAvailableQualificationsProperty()
    {
        if (!$this->selectedAcademicYear) {
            return collect();
        }
        
        $query = Assessment::with('qualificationType')->where('academic_year_id', $this->selectedAcademicYear);
        
        if (!empty($this->selectedCampuses)) {
            $query->whereIn('campus_id', $this->selectedCampuses);
        }
        
        if (!empty($this->selectedCourses)) {
            $query->whereIn('course_id', $this->selectedCourses);
        }
        
        if (!empty($this->selectedExamTypes)) {
            $query->whereHas('examType', function($q) {
                $q->whereIn('id', $this->selectedExamTypes);
            });
        }
        
        return $query->get()
                    ->pluck('qualificationType')
                    ->unique('id')
                    ->sortBy('name');
    }

    public function getAvailableColumnsProperty()
    {
        // Match the exact columns from TESDA Focal view results table
        return [
            'campus' => 'Campus',
            'total_assessed' => 'Total Assessed',
            'competent' => 'Competent',
            'passing_percentage' => '% of Passing',
            'not_yet_competent' => 'Not Yet Competent',
            'absent' => 'No Assessment Yet / Absent',
            'total_students' => 'Total Students'
        ];
    }

    public function render()
    {
        return view('livewire.pages.tesda-focal.generate-reports', [
            'academicYears' => $this->availableAcademicYears,
            'campuses' => $this->availableCampuses,
            'courses' => $this->availableCourses,
            'examTypes' => $this->availableExamTypes,
            'qualifications' => $this->availableQualifications,
            'assessments' => $this->availableAssessments,
            'availableColumns' => $this->availableColumns
        ]);
    }

    public function getPreviewValue($row, $column)
    {
        // Convert array to object if needed
        $row = is_array($row) ? (object) $row : $row;
        
        switch ($column) {
            case 'campus':
                return isset($row->user['campus']) ? $row->user['campus']['name'] : 'Sample Campus';
            case 'total_assessed':
                return rand(50, 200);
            case 'competent':
                return rand(35, 180);
            case 'passing_percentage':
                $percentage = rand(60, 95);
                return $percentage . '%';
            case 'not_yet_competent':
                return rand(5, 40);
            case 'absent':
                return rand(0, 20);
            case 'total_students':
                return rand(80, 250);
            default:
                return 'Sample Data';
        }
    }
}
