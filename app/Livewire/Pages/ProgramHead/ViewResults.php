<?php

namespace App\Livewire\Pages\ProgramHead;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Assessment;
use App\Models\Course;
use App\Models\Result;
use App\Models\Student;
use App\Models\ProgramHead;
use App\Models\QualificationType;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ViewResults extends Component
{
    use WithPagination;

    public $courseFilter = '';
    public $qualificationFilter = '';

    #[Layout('layouts.app')]
    #[Title('Assessment Results Overview')]

    protected $queryString = ['courseFilter', 'qualificationFilter'];

    public function clearFilters()
    {
        $this->courseFilter = '';
        $this->qualificationFilter = '';
    }

    public function updatedCourseFilter()
    {
        // Reset qualification filter when course changes
        $this->qualificationFilter = '';
    }

    public function getCoursesProperty()
    {
        // Get courses assigned to the current program head
        return ProgramHead::where('user_id', Auth::id())
            ->with('course')
            ->get()
            ->pluck('course')
            ->unique('id');
    }

    public function getQualificationTypesProperty()
    {
        $managedCourseIds = $this->courses->pluck('id');
        $programHeadCampusId = Auth::user()->campus_id;
        
        $query = Assessment::whereIn('course_id', $managedCourseIds)
            ->where('campus_id', $programHeadCampusId)
            ->with('qualificationType');

        // If a course is selected, filter qualifications by that course
        if ($this->courseFilter) {
            $query->where('course_id', $this->courseFilter);
        }
        
        return $query->get()
            ->pluck('qualificationType')
            ->unique('id')
            ->sortBy('name');
    }

    public function getResultsDataProperty()
    {
        $programHeadCampusId = Auth::user()->campus_id;
        $managedCourseIds = $this->courses->pluck('id');

        $query = Assessment::with([
            'course', 
            'examType', 
            'qualificationType',
            'academicYear',
            'schedules.results.student',
            'schedules.results.competencyType',
            'schedules.assessor',
            'schedules.assessmentCenter'
        ])
        ->whereIn('course_id', $managedCourseIds)
        ->where('campus_id', $programHeadCampusId)
        ->whereHas('academicYear', function($q) {
            $q->where('is_active', true);
        });

        // Apply filters
        if ($this->courseFilter) {
            $query->where('course_id', $this->courseFilter);
        }

        if ($this->qualificationFilter) {
            $query->where('qualification_type_id', $this->qualificationFilter);
        }

        $assessments = $query->get();

        // Group by course and qualification type
        $resultsData = [];

        foreach ($assessments as $assessment) {
            $courseCode = $assessment->course->code ?? 'Unknown';
            $courseName = $assessment->course->name ?? 'Unknown';
            $qualificationName = $assessment->qualificationType->name ?? 'Unknown';
            $qualificationLevel = $assessment->qualificationType->level ?? '';
            $examType = $assessment->examType->type ?? 'Unknown';
            
            $key = $courseCode . '_' . $assessment->qualification_type_id;
            
            if (!isset($resultsData[$key])) {
                $resultsData[$key] = [
                    'course_code' => $courseCode,
                    'course_name' => $courseName,
                    'qualification_name' => $qualificationName,
                    'qualification_level' => $qualificationLevel,
                    'isa' => [
                        'total_assessed' => 0,
                        'competent' => 0,
                        'not_yet_competent' => 0,
                        'absent' => 0,
                        'total_students' => 0,
                        'passing_percentage' => 0
                    ],
                    'mandatory' => [
                        'total_assessed' => 0,
                        'competent' => 0,
                        'not_yet_competent' => 0,
                        'absent' => 0,
                        'total_students' => 0,
                        'passing_percentage' => 0
                    ]
                ];
            }

            // Count results for this assessment
            $assessmentResults = $assessment->results;
            $competentCount = 0;
            $notYetCompetentCount = 0;
            $absentCount = 0;
            $totalAssessed = 0; // Only Competent + Not Yet Competent

            foreach ($assessmentResults as $result) {
                if ($result->competency_type_id) {
                    switch ($result->competencyType->name ?? '') {
                        case 'Competent':
                            $competentCount++;
                            $totalAssessed++; // Count as assessed
                            break;
                        case 'Not Yet Competent':
                            $notYetCompetentCount++;
                            $totalAssessed++; // Count as assessed
                            break;
                        case 'Absent':
                            $absentCount++;
                            // Don't count as assessed
                            break;
                        case 'Dropped':
                            // Don't count dropped students in any category
                            break;
                    }
                } else {
                    $absentCount++; // No assessment yet - treat as absent
                }
            }

            // Add to appropriate exam type
            $examTypeKey = strtolower($examType);
            if ($examTypeKey === 'isa' || $examTypeKey === 'mandatory') {
                $resultsData[$key][$examTypeKey]['total_assessed'] += $totalAssessed;
                $resultsData[$key][$examTypeKey]['competent'] += $competentCount;
                $resultsData[$key][$examTypeKey]['not_yet_competent'] += $notYetCompetentCount;
                $resultsData[$key][$examTypeKey]['absent'] += $absentCount;
                $resultsData[$key][$examTypeKey]['total_students'] += $assessmentResults->count();
            }
        }

        // Calculate percentages
        foreach ($resultsData as &$data) {
            // Calculate ISA percentage
            $data['isa']['passing_percentage'] = $data['isa']['total_assessed'] > 0 
                ? round(($data['isa']['competent'] / $data['isa']['total_assessed']) * 100, 2) 
                : 0;
            
            // Calculate MANDATORY percentage
            $data['mandatory']['passing_percentage'] = $data['mandatory']['total_assessed'] > 0 
                ? round(($data['mandatory']['competent'] / $data['mandatory']['total_assessed']) * 100, 2) 
                : 0;
        }

        return collect($resultsData)->values();
    }

    public function getOverallStatsProperty()
    {
        $resultsData = $this->resultsData;
        
        $totalStudents = 0;
        $totalAssessed = 0;
        $totalCompetent = 0;
        $totalNotYetCompetent = 0;
        $totalAbsent = 0;

        foreach ($resultsData as $data) {
            $totalStudents += $data['isa']['total_students'] + $data['mandatory']['total_students'];
            $totalAssessed += $data['isa']['total_assessed'] + $data['mandatory']['total_assessed'];
            $totalCompetent += $data['isa']['competent'] + $data['mandatory']['competent'];
            $totalNotYetCompetent += $data['isa']['not_yet_competent'] + $data['mandatory']['not_yet_competent'];
            $totalAbsent += $data['isa']['absent'] + $data['mandatory']['absent'];
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
    }

    public function render()
    {
        return view('livewire.pages.program-head.view-results', [
            'courses' => $this->courses,
            'qualificationTypes' => $this->qualificationTypes,
            'resultsData' => $this->resultsData,
            'overallStats' => $this->overallStats
        ]);
    }
}