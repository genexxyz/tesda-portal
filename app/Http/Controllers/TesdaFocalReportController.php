<?php

namespace App\Http\Controllers;

use App\Exports\TesdaFocalReportExport;
use App\Models\Academic;
use App\Models\Assessment;
use App\Models\Campus;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\School;

class TesdaFocalReportController extends Controller
{
    public function downloadReport(Request $request)
    {
        try {
            // Get filter parameters from session
            $filters = session('tesda_report_filters');
            
            if (!$filters) {
                return response()->json(['error' => 'No filter parameters found'], 400);
            }
            
            // Get the report data using the filters
            $reportData = $this->getReportDataByAssessment($filters);
            
            if (empty($reportData)) {
                return response()->json(['error' => 'No data found for the selected filters'], 404);
            }
            
            // Generate report based on format
            switch ($filters['outputFormat']) {
                case 'excel':
                    return $this->generateExcelReport($reportData, $filters);
                case 'pdf':
                    return $this->generatePdfReport($reportData, $filters);
                default:
                    return response()->json(['error' => 'Invalid output format'], 400);
            }
            
        } catch (\Exception $e) {
            Log::error('Error downloading TESDA report: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate report: ' . $e->getMessage()], 500);
        }
    }
    
    private function generateExcelReport($reportData, $filters)
    {
        $filename = 'tesda-report-' . date('Y-m-d-H-i-s') . '.xlsx';
        
        return Excel::download(
            new TesdaFocalReportExport($reportData, $filters['selectedColumns']), 
            $filename
        );
    }
    
    private function generatePdfReport($reportData, $filters)
    {
        $filename = 'tesda-focal-report-' . date('Y-m-d-H-i-s') . '.pdf';
        
        // Get academic year info
        $academicYear = 'All Years';
        if ($filters['selectedAcademicYear']) {
            $academic = Academic::find($filters['selectedAcademicYear']);
            $academicYear = $academic ? $academic->formatted_description : 'Unknown';
        }
        
        // Generate PDF
        $pdf = Pdf::loadView('exports.tesda-focal-report-pdf', [
            'reportData' => $reportData,
            'selectedColumns' => $filters['selectedColumns'],
            'academicYear' => $academicYear,
            'generatedAt' => now()->format('F j, Y g:i A'),
            'getColumnLabel' => function($column) {
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
        ]);
        
        $pdf->setPaper('A4', 'landscape');
        
        return $pdf->download($filename);
    }
    
    private function getReportDataByAssessment($filters)
    {
        // Get data organized exactly like the view results page:
        // Grouped by Course -> Assessment -> Campus data
        if (empty($filters['selectedAssessments'])) {
            return [];
        }

        $reportData = [];
        
        // Get assessments with relationships
        $assessments = Assessment::with(['course', 'qualificationType', 'examType', 'campus'])
                                ->whereIn('id', $filters['selectedAssessments'])
                                ->get();

        foreach ($assessments as $assessment) {
            $courseName = $assessment->course->name ?? 'Unknown Course';
            $assessmentKey = $assessment->examType->type . ' - ' . ' ' . 
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
            $campusData = $this->getAssessmentCampusData($assessment->id, $filters);
            
            foreach ($campusData as $data) {
                $reportData[$courseName][$assessmentKey]['campus_data'][] = $data;
            }
        }

        return $reportData;
    }
    
    private function getAssessmentCampusData($assessmentId, $filters)
    {
        try {
            Log::info('Getting assessment campus data', [
                'assessmentId' => $assessmentId,
                'filters' => $filters
            ]);
            
            // Get the assessment with all schedules and results - same as ViewResults
            $assessment = Assessment::with([
                'course', 
                'examType', 
                'qualificationType',
                'campus',
                'schedules' => function ($q) {
                    $q->where('assessment_date', '<', now());
                },
                'schedules.results.student.user.campus',
                'schedules.results.competencyType'
            ])->find($assessmentId);

            if (!$assessment) {
                Log::warning('Assessment not found', ['assessmentId' => $assessmentId]);
                return [];
            }

            $totalResults = 0;
            foreach ($assessment->schedules as $schedule) {
                $totalResults += $schedule->results->count();
            }
            
            Log::info('Assessment loaded', [
                'schedules_count' => $assessment->schedules->count(),
                'total_results' => $totalResults
            ]);

            // Group results by campus - exactly like ViewResults does
            $campusResults = [];

            foreach ($assessment->schedules as $schedule) {
                foreach ($schedule->results as $result) {
                    if (!$result->student || !$result->student->user || !$result->student->user->campus) {
                        continue;
                    }

                    $campus = $result->student->user->campus;
                    $campusName = $campus->name;

                    // Apply campus filter if specified
                    if (!empty($filters['selectedCampuses']) && !in_array($campus->id, $filters['selectedCampuses'])) {
                        continue;
                    }

                    // Apply academic year filter if specified
                    if (!empty($filters['selectedAcademicYear']) && $result->student->academic_year_id != $filters['selectedAcademicYear']) {
                        continue;
                    }

                    // Apply course filter if specified
                    if (!empty($filters['selectedCourses']) && !in_array($result->student->course_id, $filters['selectedCourses'])) {
                        continue;
                    }

                    // Initialize campus data if not exists
                    if (!isset($campusResults[$campusName])) {
                        $campusResults[$campusName] = [
                            'competent' => 0,
                            'not_yet_competent' => 0,
                            'absent' => 0,
                            'total_assessed' => 0,
                            'total_students' => 0
                        ];
                    }

                    // Process result exactly like ViewResults does
                    if ($result->competency_type_id) {
                        switch ($result->competencyType->name ?? '') {
                            case 'Competent':
                                $campusResults[$campusName]['competent']++;
                                $campusResults[$campusName]['total_assessed']++;
                                $campusResults[$campusName]['total_students']++;
                                break;
                            case 'Not Yet Competent':
                                $campusResults[$campusName]['not_yet_competent']++;
                                $campusResults[$campusName]['total_assessed']++;
                                $campusResults[$campusName]['total_students']++;
                                break;
                            case 'Absent':
                                $campusResults[$campusName]['absent']++;
                                $campusResults[$campusName]['total_students']++;
                                break;
                            case 'Dropped':
                                // Don't count dropped students in any category - same as ViewResults
                                break;
                        }
                    }
                    // Don't count students with null competency_type_id - same as ViewResults
                }
            }

            Log::info('Campus results processed', [
                'campuses_found' => array_keys($campusResults),
                'campus_counts' => $campusResults
            ]);

            // Convert to the format expected by the export
            $campusData = [];
            foreach ($campusResults as $campusName => $counts) {
                $passingPercentage = $counts['total_assessed'] > 0 
                    ? round(($counts['competent'] / $counts['total_assessed']) * 100, 1) 
                    : 0;

                $campusData[] = [
                    'campus' => $campusName,
                    'total_assessed' => $counts['total_assessed'],
                    'competent' => $counts['competent'],
                    'passing_percentage' => $passingPercentage,
                    'not_yet_competent' => $counts['not_yet_competent'],
                    'absent' => $counts['absent'],
                    'total_students' => $counts['total_students']
                ];
            }

            Log::info('Final campus data for export', ['campus_data' => $campusData]);

            return $campusData;
            
        } catch (\Exception $e) {
            Log::error('Error generating campus data for assessment: ' . $e->getMessage());
            return [];
        }
    }

    public function downloadTable(Request $request)
    {
        try {
            $filters = session('table_export_filters');
            
            if (!$filters) {
                return response()->json(['error' => 'No export filters found'], 400);
            }
            
            $format = $filters['format'];
            $exportType = $filters['export_type'];
            
            Log::info('Table export started', ['filters' => $filters]);
            
            if ($exportType === 'specific_table') {
                $data = $this->getSpecificTableData($filters);
                $filename = $this->generateTableFilename($filters);
            } else {
                $data = $this->getCampusTableData($filters);
                $filename = $this->generateCampusFilename($filters);
            }
            
            Log::info('Table export data generated', [
                'data_count' => count($data), 
                'data_structure' => array_keys($data),
                'sample_data' => !empty($data) ? json_encode(array_slice($data, 0, 1, true)) : 'No data'
            ]);
            
            if (empty($data)) {
                return response()->json(['error' => 'No data found for export'], 404);
            }
            
            if ($format === 'excel') {
                Log::info('Excel export data structure', [
                    'full_data_structure' => json_encode($data, JSON_PRETTY_PRINT)
                ]);
                
                return Excel::download(new TesdaFocalReportExport($data, [
                    'campus', 'total_assessed', 'competent', 'passing_percentage', 
                    'not_yet_competent', 'absent', 'total_students'
                ]), $filename . '.xlsx');
            } else {
                return $this->generateTablePdf($data, $filters, $filename);
            }
            
        } catch (\Exception $e) {
            Log::error('Error downloading table: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate export: ' . $e->getMessage()], 500);
        }
    }
    
    private function getSpecificTableData($filters)
    {
        try {
            // Get data for a specific assessment table using the exact same logic as ViewResults
            $courseName = $filters['courseName'];
            $examType = $filters['examType'];
            $courseCode = $filters['courseCode'];
            $qualificationName = $filters['qualificationName'];
            $qualificationLevel = $filters['qualificationLevel'];
            
            Log::info('Getting specific table data using ViewResults logic', [
                'courseName' => $courseName,
                'examType' => $examType,
                'courseCode' => $courseCode,
                'qualificationName' => $qualificationName,
                'qualificationLevel' => $qualificationLevel
            ]);
            
            // Build query exactly like ViewResults does
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

            // Apply academic year filter
            if (!empty($filters['academicYearFilter'])) {
                $query->where('academic_year_id', $filters['academicYearFilter']);
            } else {
                $query->whereHas('academicYear', function($q) {
                    $q->where('is_active', true);
                });
            }

            // Apply filters exactly like ViewResults
            $query->whereHas('course', function($q) use ($courseCode) {
                $q->where('code', $courseCode);
            })
            ->whereHas('examType', function($q) use ($examType) {
                $q->where('type', $examType);
            })
            ->whereHas('qualificationType', function($q) use ($qualificationName, $qualificationLevel) {
                $q->where('name', $qualificationName);
                if ($qualificationLevel) {
                    $q->where('level', $qualificationLevel);
                }
            });
            
            $assessments = $query->get();
            
            Log::info('Found assessments for specific table', ['count' => $assessments->count()]);
            
            // Process assessments exactly like ViewResults does
            $resultsData = [];
            
            foreach ($assessments as $assessment) {
                $campusName = $assessment->campus->name ?? 'Unknown';
                
                // Use the same key format as ViewResults
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

                // Store assessment IDs for this group
                $resultsData[$key]['campuses'][$campusName]['assessment_ids'][] = $assessment->id;
                $resultsData[$key]['assessment_count']++;
            }
            
            $this->calculatePercentages($resultsData);
            
            // Convert to the format needed for export
            $exportData = [];
            foreach ($resultsData as $data) {
                $campusData = [];
                foreach ($data['campuses'] as $campusName => $campusStats) {
                    $campusData[] = [
                        'campus' => $campusName,
                        'total_assessed' => $campusStats['total_assessed'],
                        'competent' => $campusStats['competent'],
                        'passing_percentage' => $campusStats['passing_percentage'],
                        'not_yet_competent' => $campusStats['not_yet_competent'],
                        'absent' => $campusStats['absent'],
                        'total_students' => $campusStats['total_students']
                    ];
                }
                
                // Add totals row
                $campusData[] = [
                    'campus' => 'TOTAL',
                    'total_assessed' => $data['totals']['total_assessed'],
                    'competent' => $data['totals']['competent'],
                    'passing_percentage' => $data['totals']['passing_percentage'],
                    'not_yet_competent' => $data['totals']['not_yet_competent'],
                    'absent' => $data['totals']['absent'],
                    'total_students' => $data['totals']['total_students']
                ];
                
                $assessmentKey = $data['exam_type'] . ' - ' . ' ' . 
                               $data['qualification_name'] . ' ' . $data['qualification_level'];
                
                $exportData[$data['course_name']] = [
                    $assessmentKey => [
                        'assessment_info' => [
                            'exam_type' => $data['exam_type'],
                            'course_code' => $data['course_code'],
                            'qualification_name' => $data['qualification_name'],
                            'qualification_level' => $data['qualification_level'],
                            'course_name' => $data['course_name']
                        ],
                        'campus_data' => $campusData
                    ]
                ];
            }
            
            Log::info('Specific table data generated using ViewResults logic', [
                'exportDataKeys' => array_keys($exportData),
                'dataStructure' => !empty($exportData) ? array_keys(reset($exportData)) : []
            ]);
            
            return $exportData;
            
        } catch (\Exception $e) {
            Log::error('Error in getSpecificTableData: ' . $e->getMessage());
            return [];
        }
    }
    
    // Copy the helper methods from ViewResults component
    private function processAssessmentResults($assessment)
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
    
    private function initializeResultData($assessment)
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
            'assessment_count' => 0
        ];
    }
    
    private function initializeCampusData()
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
    
    private function updateStatistics(&$resultsData, $key, $campusName, $counts)
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
    
    private function calculatePercentages(&$resultsData)
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
    }
    
    private function getCampusTableData($filters)
    {
        try {
            // This should replicate the EXACT ViewResults logic for a specific campus view
            $courseName = $filters['courseName'];
            $campusName = $filters['campusName'];
            
            Log::info('Getting campus table data - replicating ViewResults campus view', [
                'courseName' => $courseName,
                'campusName' => $campusName,
                'filters' => $filters
            ]);
            
            // Build the EXACT same query as ViewResults.getResultsDataProperty()
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

            // Apply academic year filter exactly like ViewResults
            if (!empty($filters['academicYearFilter'])) {
                $query->where('academic_year_id', $filters['academicYearFilter']);
            } else {
                $query->whereHas('academicYear', function($q) {
                    $q->where('is_active', true);
                });
            }

            // Apply course filter exactly like ViewResults
            if (!empty($filters['courseFilter'])) {
                $query->where('course_id', $filters['courseFilter']);
            }

            // Apply qualification filter exactly like ViewResults
            if (!empty($filters['qualificationFilter'])) {
                $query->where('qualification_type_id', $filters['qualificationFilter']);
            }

            // Get ALL assessments for the course (like ViewResults does)
            $assessments = $query->get();
            
            Log::info('Found all assessments for campus filtering', ['count' => $assessments->count()]);

            // Process assessments exactly like ViewResults.getResultsDataProperty()
            $resultsData = [];

            foreach ($assessments as $assessment) {
                $assessmentCampusName = $assessment->campus->name ?? 'Unknown';
                
                Log::info('Processing assessment', [
                    'assessment_id' => $assessment->id,
                    'course_code' => $assessment->course->code ?? 'Unknown',
                    'course_name' => $assessment->course->name ?? 'Unknown',
                    'exam_type' => $assessment->examType->type ?? 'Unknown',
                    'qualification_name' => $assessment->qualificationType->name ?? 'Unknown',
                    'campus_name' => $assessmentCampusName,
                    'schedules_count' => $assessment->schedules->count()
                ]);
                
                // Use the same key format as ViewResults
                $key = ($assessment->course->code ?? 'Unknown') . '_' .
                       $assessment->qualification_type_id . '_' .
                       ($assessment->examType->type ?? 'Unknown') . '_' .
                       $assessment->campus_id;

                if (!isset($resultsData[$key])) {
                    $resultsData[$key] = $this->initializeResultData($assessment);
                    Log::info('Initialized new result data', ['key' => $key]);
                }

                if (!isset($resultsData[$key]['campuses'][$assessmentCampusName])) {
                    $resultsData[$key]['campuses'][$assessmentCampusName] = $this->initializeCampusData();
                    Log::info('Initialized campus data', ['campus' => $assessmentCampusName]);
                }

                $counts = $this->processAssessmentResults($assessment);
                Log::info('Assessment counts', ['counts' => $counts]);
                
                $this->updateStatistics($resultsData, $key, $assessmentCampusName, $counts);

                $resultsData[$key]['campuses'][$assessmentCampusName]['assessment_ids'][] = $assessment->id;
                $resultsData[$key]['assessment_count']++;
            }

            $this->calculatePercentages($resultsData);

            // Group by course name like ViewResults does
            $groupedByCourse = [];
            foreach ($resultsData as $data) {
                $groupedByCourse[$data['course_name']][] = $data;
            }

            Log::info('Grouped by course', [
                'available_courses' => array_keys($groupedByCourse),
                'looking_for_course' => $courseName,
                'course_match_found' => isset($groupedByCourse[$courseName])
            ]);

            // Filter to show only the specific course and apply campus filtering like getFilteredResultsProperty()
            $courseData = $groupedByCourse[$courseName] ?? [];
            
            Log::info('Course data found', [
                'course_items_count' => count($courseData),
                'course_items' => array_map(function($item) {
                    return [
                        'exam_type' => $item['exam_type'],
                        'course_code' => $item['course_code'],
                        'qualification_name' => $item['qualification_name'],
                        'campuses' => array_keys($item['campuses'])
                    ];
                }, $courseData)
            ]);
            
            // Apply campus filtering exactly like ViewResults.getFilteredResultsProperty()
            $filteredCourseData = [];
            foreach ($courseData as $item) {
                $campusData = $item['campuses'][$campusName] ?? [
                    'total_assessed' => 0,
                    'competent' => 0,
                    'not_yet_competent' => 0,
                    'absent' => 0,
                    'total_students' => 0,
                    'passing_percentage' => 0
                ];
                
                Log::info('Processing campus data for item', [
                    'exam_type' => $item['exam_type'],
                    'course_code' => $item['course_code'],
                    'qualification_name' => $item['qualification_name'],
                    'available_campuses' => array_keys($item['campuses']),
                    'looking_for_campus' => $campusName,
                    'campus_data' => $campusData,
                    'has_students' => $campusData['total_students'] > 0
                ]);
                
                // Include ALL items, even if no students (to match ViewResults behavior)
                $filteredItem = array_merge($item, ['campus_data' => $campusData]);
                $filteredCourseData[] = $filteredItem;
            }

            Log::info('Filtered course data for campus', [
                'originalItemsCount' => count($courseData),
                'filteredItemsCount' => count($filteredCourseData)
            ]);

            // Convert to export format exactly like what the view displays
            $tableData = [];
            foreach ($filteredCourseData as $data) {
                $assessmentKey = $data['exam_type'] . ' - ' . ' ' . 
                               $data['qualification_name'] . ' ' . 
                               $data['qualification_level'];

                Log::info('Creating table data entry', [
                    'assessment_key' => $assessmentKey,
                    'campus_data' => $data['campus_data']
                ]);

                // Create campus data row - this matches exactly what the view shows
                $campusData = [[
                    'campus' => $campusName,
                    'total_assessed' => $data['campus_data']['total_assessed'],
                    'competent' => $data['campus_data']['competent'],
                    'passing_percentage' => $data['campus_data']['passing_percentage'],
                    'not_yet_competent' => $data['campus_data']['not_yet_competent'],
                    'absent' => $data['campus_data']['absent'],
                    'total_students' => $data['campus_data']['total_students']
                ]];

                $tableData[$assessmentKey] = [
                    'assessment_info' => [
                        'exam_type' => $data['exam_type'],
                        'course_code' => $data['course_code'],
                        'qualification_name' => $data['qualification_name'],
                        'qualification_level' => $data['qualification_level'],
                        'course_name' => $data['course_name']
                    ],
                    'campus_data' => $campusData
                ];
            }
            
            Log::info('Campus table data generated - ViewResults replica', [
                'tableDataCount' => count($tableData),
                'tableDataKeys' => array_keys($tableData)
            ]);
            
            // Return in the format expected by the export
            return [
                $courseName => $tableData
            ];
            
        } catch (\Exception $e) {
            Log::error('Error in getCampusTableData: ' . $e->getMessage());
            return [];
        }
    }
    
    private function generateTableFilename($filters)
    {
        $courseName = str_replace(' ', '-', strtolower($filters['courseName']));
        $examType = str_replace(' ', '-', strtolower($filters['examType']));
        $courseCode = strtolower($filters['courseCode']);
        
        return "tesda-table-{$courseName}-{$examType}-{$courseCode}-" . date('Y-m-d-H-i-s');
    }
    
    private function generateCampusFilename($filters)
    {
        $courseName = str_replace(' ', '-', strtolower($filters['courseName']));
        $campusName = str_replace(' ', '-', strtolower($filters['campusName']));
        
        return "tesda-campus-{$courseName}-{$campusName}-" . date('Y-m-d-H-i-s');
    }
    
    private function generateTablePdf($data, $filters, $filename)
    {
        $academicYear = 'All Years';
        if ($filters['academicYearFilter']) {
            $academic = Academic::find($filters['academicYearFilter']);
            $academicYear = $academic ? $academic->formatted_description : 'Unknown';
        }
        
        Log::info('Generating table PDF', [
            'data_structure' => json_encode($data, JSON_PRETTY_PRINT),
            'filters' => $filters,
            'filename' => $filename
        ]);

        $schoolInfo = School::first();
        
        $pdf = Pdf::loadView('exports.tesda-focal-table-pdf', [
            'data' => $data,
            'filters' => $filters,
            'academicYear' => $academicYear,
            'schoolInfo' => $schoolInfo,
            'generatedAt' => now()->format('F j, Y g:i A')
        ]);
        
        $pdf->setPaper('A4', 'portrait');
        
        return response()->streamDownload(function() use ($pdf) {
            echo $pdf->output();
        }, $filename . '.pdf', [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
