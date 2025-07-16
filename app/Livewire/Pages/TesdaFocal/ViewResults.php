<?php

namespace App\Livewire\Pages\TesdaFocal;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Assessment;
use App\Models\Campus;
use App\Models\Course;
use App\Models\QualificationType;
use App\Models\Academic;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TesdaFocalReportExport;
use Barryvdh\DomPDF\Facade\Pdf;

class ViewResults extends Component
{
    public $activeTab = 'overall';
    public $courseFilter = '';
    public $qualificationFilter = '';
    public $academicYearFilter = '';

    #[Layout('layouts.app')]
    #[Title('Assessment Results Overview - All Campuses')]

    protected $queryString = ['activeTab', 'courseFilter', 'qualificationFilter', 'academicYearFilter'];

    public function mount(): void
    {
        // Set default academic year to active
        if (!$this->academicYearFilter) {
            $activeAcademicYear = Academic::where('is_active', true)->first();
            if ($activeAcademicYear) {
                $this->academicYearFilter = $activeAcademicYear->id;
            }
        }
    }

    public function clearFilters(): void
    {
        $this->courseFilter = '';
        $this->qualificationFilter = '';
        // Reset to active academic year instead of clearing
        $activeAcademicYear = Academic::where('is_active', true)->first();
        $this->academicYearFilter = $activeAcademicYear ? $activeAcademicYear->id : '';
    }

    public function updatedCourseFilter(): void
    {
        // Reset qualification filter when course changes
        $this->qualificationFilter = '';
    }

    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function getCampusesProperty(): Collection
    {
        return Campus::orderBy('name')->get();
    }

    public function getCoursesProperty(): Collection
    {
        return Course::orderBy('name')->get();
    }

    public function getQualificationTypesProperty(): \Illuminate\Support\Collection
    {
        $query = Assessment::with('qualificationType')
            ->whereHas('schedules', function($q) {
                $q->where('assessment_date', '<', now());
            });

        // Default to active academic year
        if ($this->academicYearFilter) {
            $query->where('academic_year_id', $this->academicYearFilter);
        } else {
            $query->whereHas('academicYear', function($q) {
                $q->where('is_active', true);
            });
        }

        // If a course is selected, filter qualifications by that course
        if ($this->courseFilter) {
            $query->where('course_id', $this->courseFilter);
        }
        
        return $query->get()
            ->pluck('qualificationType')
            ->unique('id')
            ->sortBy('name');
    }

    public function getAcademicYearsProperty(): Collection
    {
        return Academic::orderBy('description', 'desc')->get();
    }

    /**
     * Build the base query for assessments with all necessary relationships
     */
    private function buildAssessmentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = Assessment::with([
            'course', 
            'examType', 
            'qualificationType',
            'campus',
            'academicYear',
            'schedules' => function ($q) {
                $q->where('assessment_date', '<', now());
            },
            'schedules.results.student',
            'schedules.results.competencyType',
            'schedules.assessmentCenter',
            'schedules.assessor'
        ])
        ->whereHas('schedules', function($q) {
            $q->where('assessment_date', '<', now());
        });

        // Default to active academic year if no filter is set
        if ($this->academicYearFilter) {
            $query->where('academic_year_id', $this->academicYearFilter);
        } else {
            $query->whereHas('academicYear', function($q) {
                $q->where('is_active', true);
            });
        }

        return $query;
    }

    /**
     * Apply filters to the assessment query
     */
    private function applyFilters(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        if ($this->courseFilter) {
            $query->where('course_id', $this->courseFilter);
        }

        if ($this->qualificationFilter) {
            $query->where('qualification_type_id', $this->qualificationFilter);
        }

        return $query;
    }

    /**
     * Process a single assessment and count its results
     */
    private function processAssessmentResults(Assessment $assessment): array
    {
        $competentCount = 0;
        $notYetCompetentCount = 0;
        $absentCount = 0;
        $totalAssessed = 0;
        $totalStudents = 0;

        // Process results from all schedules for this assessment
        foreach ($assessment->schedules as $schedule) {
            foreach ($schedule->results as $result) {
                if ($result->competency_type_id) {
                    switch ($result->competencyType->name ?? '') {
                        case 'Competent':
                            $competentCount++;
                            $totalAssessed++;
                            $totalStudents++;
                            break;
                        case 'Not Yet Competent':
                            $notYetCompetentCount++;
                            $totalAssessed++;
                            $totalStudents++;
                            break;
                        case 'Absent':
                            $absentCount++;
                            $totalStudents++;
                            break;
                        case 'Dropped':
                            // Don't count dropped students in any category
                            break;
                    }
                }
                // Don't count students with null competency_type_id - they haven't been assessed yet
            }
        }

        return [
            'competent' => $competentCount,
            'not_yet_competent' => $notYetCompetentCount,
            'absent' => $absentCount,
            'total_assessed' => $totalAssessed,
            'total_students' => $totalStudents
        ];
    }

    /**
     * Initialize result data structure for a new assessment combination
     */
    private function initializeResultData(Assessment $assessment): array
    {
        return [
            'course_code' => $assessment->course->code ?? 'Unknown',
            'course_name' => $assessment->course->name ?? 'Unknown',
            'qualification_name' => $assessment->qualificationType->name ?? 'Unknown',
            'qualification_level' => $assessment->qualificationType->level ?? '',
            'exam_type' => $assessment->examType->type ?? 'Unknown',
            'campuses' => [],
            'totals' => [
                'total_assessed' => 0,
                'competent' => 0,
                'not_yet_competent' => 0,
                'absent' => 0,
                'total_students' => 0,
                'passing_percentage' => 0
            ],
            'assessment_count' => 0 // Initialize assessment count
        ];
    }

    /**
     * Initialize campus data structure
     */
    private function initializeCampusData(): array
    {
        return [
            'total_assessed' => 0,
            'competent' => 0,
            'not_yet_competent' => 0,
            'absent' => 0,
            'total_students' => 0,
            'passing_percentage' => 0,
            'assessment_ids' => []
        ];
    }

    /**
     * Update campus and total statistics with assessment results
     */
    private function updateStatistics(array &$resultsData, string $key, string $campusName, array $counts): void
    {
        // Update campus data
        $resultsData[$key]['campuses'][$campusName]['total_assessed'] += $counts['total_assessed'];
        $resultsData[$key]['campuses'][$campusName]['competent'] += $counts['competent'];
        $resultsData[$key]['campuses'][$campusName]['not_yet_competent'] += $counts['not_yet_competent'];
        $resultsData[$key]['campuses'][$campusName]['absent'] += $counts['absent'];
        $resultsData[$key]['campuses'][$campusName]['total_students'] += $counts['total_students'];

        // Update totals
        $resultsData[$key]['totals']['total_assessed'] += $counts['total_assessed'];
        $resultsData[$key]['totals']['competent'] += $counts['competent'];
        $resultsData[$key]['totals']['not_yet_competent'] += $counts['not_yet_competent'];
        $resultsData[$key]['totals']['absent'] += $counts['absent'];
        $resultsData[$key]['totals']['total_students'] += $counts['total_students'];
    }

    /**
     * Calculate passing percentages for all data
     */
    private function calculatePercentages(array &$resultsData): void
    {
        foreach ($resultsData as &$data) {
            foreach ($data['campuses'] as &$campus) {
                $campus['passing_percentage'] = $campus['total_assessed'] > 0
                    ? round(($campus['competent'] / $campus['total_assessed']) * 100, 2)
                    : 0;
            }

            $data['totals']['passing_percentage'] = $data['totals']['total_assessed'] > 0
                ? round(($data['totals']['competent'] / $data['totals']['total_assessed']) * 100, 2)
                : 0;
        }
    } // Closing the calculatePercentages method

    public function getResultsDataProperty(): \Illuminate\Support\Collection
    {
        $query = $this->buildAssessmentQuery();
        $query = $this->applyFilters($query);
        $assessments = $query->get();

        $resultsData = [];

        foreach ($assessments as $assessment) {
            $campusName = $assessment->campus->name ?? 'Unknown';
            // Merge by course, exam type, qualification, and campus only
            $key = ($assessment->course->code ?? 'Unknown') . '_' .
                   $assessment->qualification_type_id . '_' .
                   ($assessment->examType->type ?? 'Unknown') . '_' .
                   $assessment->campus_id;

            if (!isset($resultsData[$key])) {
                $resultsData[$key] = $this->initializeResultData($assessment);
            }

            if (!isset($resultsData[$key]['campuses'][$campusName])) {
                $resultsData[$key]['campuses'][$campusName] = $this->initializeCampusData();
            }

            $counts = $this->processAssessmentResults($assessment);
            $this->updateStatistics($resultsData, $key, $campusName, $counts);

            // Store assessment IDs for this group for filtering in modal
            $resultsData[$key]['campuses'][$campusName]['assessment_ids'][] = $assessment->id;

            // Increment the assessment count for the group
            $resultsData[$key]['assessment_count']++;
        }

        $this->calculatePercentages($resultsData);
        $sorted = collect($resultsData)->sortBy(['course_name', 'qualification_name', 'exam_type']);
        return $sorted->groupBy('course_name');
    }

    public function getFilteredResultsProperty(): \Illuminate\Support\Collection
    {
        $results = $this->resultsData;
        
        if ($this->activeTab !== 'overall') {
            // Filter results to show only the selected campus
            $results = $results->map(function($courseGroup) {
                return $courseGroup->map(function($item) {
                    $campusData = $item['campuses'][$this->activeTab] ?? [
                        'total_assessed' => 0,
                        'competent' => 0,
                        'not_yet_competent' => 0,
                        'absent' => 0,
                        'total_students' => 0,
                        'passing_percentage' => 0
                    ];
                    
                    return array_merge($item, ['campus_data' => $campusData]);
                })->filter(function($item) {
                    return $item['campus_data']['total_students'] > 0;
                });
            })->filter(function($courseGroup) {
                return $courseGroup->count() > 0;
            });
        }
        
        return $results;
    }

    public function getOverallStatsProperty(): array
    {
        $results = $this->resultsData;

        $totalStudents = 0;
        $totalAssessed = 0;
        $totalCompetent = 0;
        $totalNotYetCompetent = 0;
        $totalAbsent = 0;

        foreach ($results as $courseGroup) {
            foreach ($courseGroup as $result) {
                if ($this->activeTab === 'overall') {
                    $totalStudents += $result['totals']['total_students'];
                    $totalAssessed += $result['totals']['total_assessed'];
                    $totalCompetent += $result['totals']['competent'];
                    $totalNotYetCompetent += $result['totals']['not_yet_competent'];
                    $totalAbsent += $result['totals']['absent'];
                } else {
                    $campusData = $result['campuses'][$this->activeTab] ?? [];
                    $totalStudents += $campusData['total_students'] ?? 0;
                    $totalAssessed += $campusData['total_assessed'] ?? 0;
                    $totalCompetent += $campusData['competent'] ?? 0;
                    $totalNotYetCompetent += $campusData['not_yet_competent'] ?? 0;
                    $totalAbsent += $campusData['absent'] ?? 0;
                }
            }
        }

        $overallPassingRate = $totalAssessed > 0
            ? round(($totalCompetent / $totalAssessed) * 100, 2)
            : 0;

        return [
            'total_students' => $totalStudents,
            'total_assessed' => $totalAssessed,
            'total_competent' => $totalCompetent,
            'total_not_yet_competent' => $totalNotYetCompetent,
            'total_absent' => $totalAbsent,
            'overall_passing_rate' => $overallPassingRate,
            'assessment_completion_rate' => $totalStudents > 0
                ? round(($totalAssessed / $totalStudents) * 100, 2)
                : 0
        ];
    } // Closing the getOverallStatsProperty method

    /**
     * Get assessment ID for view results modal
     */
    public function getAssessmentId($courseCode, $qualificationTypeId, $examType, $campusName)
    {
        $query = $this->buildAssessmentQuery();
        $query = $this->applyFilters($query);
        
        $assessment = $query->whereHas('course', function($q) use ($courseCode) {
                $q->where('code', $courseCode);
            })
            ->where('qualification_type_id', $qualificationTypeId)
            ->whereHas('examType', function($q) use ($examType) {
                $q->where('type', $examType);
            })
            ->whereHas('campus', function($q) use ($campusName) {
                $q->where('name', $campusName);
            })
            ->first();
            
        return $assessment ? $assessment->id : null;
    }

    

    /**
     * Get the count of assessments for each group
     */
    private function getAssessmentCountForGroup($courseCode, $qualificationName, $examType, $campusName): int
    {
        $academicYearId = $this->academicYearFilter ?: Academic::where('is_active', true)->first()?->id;
        
        $query = Assessment::whereHas('course', function($q) use ($courseCode) {
                $q->where('code', $courseCode);
            })
            ->whereHas('qualificationType', function($q) use ($qualificationName) {
                $q->where('name', $qualificationName);
            })
            ->whereHas('examType', function($q) use ($examType) {
                $q->where('type', $examType);
            })
            ->whereHas('campus', function($q) use ($campusName) {
                $q->where('name', $campusName);
            });
            
        if ($academicYearId) {
            $query->where('academic_year_id', $academicYearId);
        }
        
        return $query->count();
    }
    
    public function render()
    {
        return view('livewire.pages.tesda-focal.view-results', [
            'campuses' => $this->campuses,
            'courses' => $this->courses,
            'qualificationTypes' => $this->qualificationTypes,
            'academicYears' => $this->academicYears,
            'resultsData' => $this->filteredResults,
            'overallStats' => $this->overallStats
        ]);
    }

    public function exportTable($format, $courseName, $examType, $courseCode, $qualificationName, $qualificationLevel)
    {
        try {
            // Store the export parameters in session for the download route
            session([
                'table_export_filters' => [
                    'format' => $format,
                    'courseName' => $courseName,
                    'examType' => $examType,
                    'courseCode' => $courseCode,
                    'qualificationName' => $qualificationName,
                    'qualificationLevel' => $qualificationLevel,
                    'activeTab' => $this->activeTab,
                    'courseFilter' => $this->courseFilter,
                    'qualificationFilter' => $this->qualificationFilter,
                    'academicYearFilter' => $this->academicYearFilter,
                    'export_type' => 'specific_table'
                ]
            ]);
            
            // Trigger download using JavaScript
            $this->dispatch('download-table', [
                'url' => route('tesda-focal.download-table'),
                'format' => $format
            ]);
            
        } catch (\Exception $e) {
            $this->dispatch('swal:error', [
                'title' => 'Export Failed',
                'text' => 'Failed to export table: ' . $e->getMessage()
            ]);
        }
    }

    public function exportCampusTable($format, $courseName, $campusName)
    {
        try {
            // Store the export parameters in session for the download route
            session([
                'table_export_filters' => [
                    'format' => $format,
                    'courseName' => $courseName,
                    'campusName' => $campusName,
                    'activeTab' => $this->activeTab,
                    'courseFilter' => $this->courseFilter,
                    'qualificationFilter' => $this->qualificationFilter,
                    'academicYearFilter' => $this->academicYearFilter,
                    'export_type' => 'campus_table'
                ]
            ]);
            
            // Trigger download using JavaScript
            $this->dispatch('download-table', [
                'url' => route('tesda-focal.download-table'),
                'format' => $format
            ]);
            
        } catch (\Exception $e) {
            $this->dispatch('swal:error', [
                'title' => 'Export Failed',
                'text' => 'Failed to export table: ' . $e->getMessage()
            ]);
        }
    }
}