<?php

namespace App\Livewire\Pages\TesdaFocal\Partials;

use Livewire\Component;
use App\Models\Assessment;
use App\Models\Course;
use App\Models\QualificationType;
use App\Models\ExamType;

class OverviewTable extends Component
{
    public $examTypeFilter = '';
    public $courseFilter = '';
    public $qualificationFilter = '';

    public function getExamTypesProperty()
    {
        return ExamType::orderBy('type')->get()->pluck('type', 'id');
    }

    public function getCoursesProperty()
    {
        return Course::orderBy('name')->get()->pluck('name', 'id');
    }

    public function getQualificationTypesProperty()
    {
        $query = Assessment::with('qualificationType');

        if ($this->courseFilter) {
            $query->where('course_id', $this->courseFilter);
        }
        if ($this->examTypeFilter) {
            $query->whereHas('examType', function($q) {
                $q->where('type', $this->examTypeFilter);
            });
        }

        return $query->get()
            ->pluck('qualificationType')
            ->unique('id')
            ->pluck('name', 'id');
    }

    public function getFilteredAssessmentsProperty()
    {
        $query = Assessment::query();

        if ($this->courseFilter) {
            $query->where('course_id', $this->courseFilter);
        }
        if ($this->qualificationFilter) {
            $query->where('qualification_type_id', $this->qualificationFilter);
        }
        if ($this->examTypeFilter) {
            $query->whereHas('examType', function($q) {
                $q->where('type', $this->examTypeFilter);
            });
        }

        return $query->get();
    }

    public function getOverviewDataProperty()
{
    $query = Assessment::with([
        'course',
        'qualificationType',
        'examType',
        'campus',
        'schedules.results.competencyType',
    ])->whereHas('schedules', function($q) {
        $q->where('assessment_date', '<', now());
    });

    // Apply filters
    if ($this->courseFilter) {
        $query->where('course_id', $this->courseFilter);
    }
    if ($this->qualificationFilter) {
        $query->where('qualification_type_id', $this->qualificationFilter);
    }
    if ($this->examTypeFilter) {
        $query->whereHas('examType', function($q) {
            $q->where('type', $this->examTypeFilter);
        });
    }

    // Only active academic year
    $activeAcademicYear = \App\Models\Academic::where('is_active', true)->first();
    if ($activeAcademicYear) {
        $query->where('academic_year_id', $activeAcademicYear->id);
    }

    $assessments = $query->get();

    // Group by course, qualification, exam type
    $groups = [];
    foreach ($assessments as $assessment) {
        $groupKey = implode('|', [
            $assessment->course->name ?? 'Unknown',
            $assessment->qualificationType->name ?? 'Unknown',
            $assessment->examType->type ?? 'Unknown',
        ]);
        if (!isset($groups[$groupKey])) {
            $groups[$groupKey] = [
                'course' => $assessment->course->name ?? 'Unknown',
                'qualification_type' => $assessment->qualificationType->name ?? 'Unknown',
                'exam_type' => $assessment->examType->type ?? 'Unknown',
                'campuses' => [],
                'totals' => [
                    'total_assessed' => 0,
                    'competent' => 0,
                    'not_yet_competent' => 0,
                    'absent' => 0,
                    'total_students' => 0,
                    'pass_rate' => 0,
                ],
            ];
        }

        $campus = $assessment->campus->name ?? 'Unknown';
        if (!isset($groups[$groupKey]['campuses'][$campus])) {
            $groups[$groupKey]['campuses'][$campus] = [
                'total_assessed' => 0,
                'competent' => 0,
                'not_yet_competent' => 0,
                'absent' => 0,
                'total_students' => 0,
                'pass_rate' => 0,
            ];
        }

        foreach ($assessment->schedules as $schedule) {
            foreach ($schedule->results as $result) {
                if ($result->competencyType) {
                    if ($result->competencyType->name === 'Competent') {
                        $groups[$groupKey]['campuses'][$campus]['competent']++;
                        $groups[$groupKey]['campuses'][$campus]['total_assessed']++;
                        $groups[$groupKey]['campuses'][$campus]['total_students']++;
                        $groups[$groupKey]['totals']['competent']++;
                        $groups[$groupKey]['totals']['total_assessed']++;
                        $groups[$groupKey]['totals']['total_students']++;
                    } elseif ($result->competencyType->name === 'Not Yet Competent') {
                        $groups[$groupKey]['campuses'][$campus]['not_yet_competent']++;
                        $groups[$groupKey]['campuses'][$campus]['total_assessed']++;
                        $groups[$groupKey]['campuses'][$campus]['total_students']++;
                        $groups[$groupKey]['totals']['not_yet_competent']++;
                        $groups[$groupKey]['totals']['total_assessed']++;
                        $groups[$groupKey]['totals']['total_students']++;
                    } elseif ($result->competencyType->name === 'Absent') {
                        $groups[$groupKey]['campuses'][$campus]['absent']++;
                        $groups[$groupKey]['campuses'][$campus]['total_students']++;
                        $groups[$groupKey]['totals']['absent']++;
                        $groups[$groupKey]['totals']['total_students']++;
                    }
                }
            }
        }
    }

    // Calculate pass rates
    foreach ($groups as &$group) {
        foreach ($group['campuses'] as &$campusData) {
            $campusData['pass_rate'] = $campusData['total_assessed'] > 0
                ? round(($campusData['competent'] / $campusData['total_assessed']) * 100, 2)
                : 0;
        }
        $group['totals']['pass_rate'] = $group['totals']['total_assessed'] > 0
            ? round(($group['totals']['competent'] / $group['totals']['total_assessed']) * 100, 2)
            : 0;
    }

    return collect($groups)->values();
}

    public function render()
    {
        return view('livewire.pages.tesda-focal.partials.overview-table', [
            'examTypes' => $this->examTypes,
            'courses' => $this->courses,
            'qualificationTypes' => $this->qualificationTypes,
            'filteredAssessments' => $this->filteredAssessments,
            'overviewData' => $this->overviewData,
        ]);
    }
}