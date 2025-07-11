<?php

namespace App\Livewire\Modals\TesdaFocal;

use App\Models\Assessment;
use LivewireUI\Modal\ModalComponent;

class ViewAssessmentResults extends ModalComponent
{
    public $assessment;
    public $stats = [];
    public $assessmentDates = [];
    public $selectedDate = null;
    public $groupCriteria = [];
    public $assessmentCenters = [];
    public $assessors = [];
    public $selectedAssessmentCenter = null;
    public $selectedAssessor = null;
    public $allAssessments;
    public $filteredAssessments;
    public $allResults;
    public $sortField = 'student_name';
    public $sortDirection = 'asc';

    public function mount($assessmentId, $groupCriteria = [])
    {
        // Find the assessment for initial display
        $this->assessment = Assessment::with([
            'course', 'campus', 'academicYear', 'qualificationType', 'examType',
            'schedules.assessmentCenter', 'schedules.assessor',
            'schedules.results.student.user', 'schedules.results.competencyType'
        ])->findOrFail($assessmentId);

        // Set group criteria for merging (course_id, exam_type_id, qualification_type_id, campus_id)
        $this->groupCriteria = $groupCriteria ?: [
            'course_id' => $this->assessment->course_id,
            'exam_type_id' => $this->assessment->exam_type_id,
            'qualification_type_id' => $this->assessment->qualification_type_id,
            'campus_id' => $this->assessment->campus_id,
        ];

        // Get all assessments for this group
        $this->allAssessments = Assessment::with([
            'course', 'campus', 'academicYear', 'qualificationType', 'examType',
            'schedules.assessmentCenter', 'schedules.assessor',
            'schedules.results.student.user', 'schedules.results.competencyType'
        ])->where($this->groupCriteria)->get();

        // Load available filters from schedules
        $allSchedules = collect();
        foreach ($this->allAssessments as $assessment) {
            foreach ($assessment->schedules as $schedule) {
                $allSchedules->push($schedule);
            }
        }

        $this->assessmentCenters = $allSchedules->pluck('assessmentCenter')
            ->unique('id')->filter()->values();
        $this->assessors = $allSchedules->pluck('assessor')
            ->unique('id')->filter()->values();
        $this->assessmentDates = $allSchedules->pluck('assessment_date')
            ->filter()->map(function ($date) {
                if ($date instanceof \Carbon\Carbon) {
                    return $date->format('Y-m-d');
                }
                return null;
            })->filter()->unique()->values()->toArray();
            
        // Set a default date if there's only one date available
        if (count($this->assessmentDates) == 1) {
            $this->selectedDate = $this->assessmentDates[0];
        }

        // Load filtered assessments based on initial values
        $this->loadAssessmentByFilters();
    }

    public function loadAssessmentByFilters()
    {
        // Collect all schedules that match the filter criteria
        $filteredSchedules = collect();
        
        foreach ($this->allAssessments as $assessment) {
            foreach ($assessment->schedules as $schedule) {
                $matchesCenter = $this->selectedAssessmentCenter 
                    ? $schedule->assessment_center_id == $this->selectedAssessmentCenter
                    : true;
                    
                $matchesAssessor = $this->selectedAssessor 
                    ? $schedule->assessor_id == $this->selectedAssessor 
                    : true;
                    
                $matchesDate = $this->selectedDate 
                    ? ($schedule->assessment_date && $schedule->assessment_date instanceof \Carbon\Carbon 
                        ? $schedule->assessment_date->format('Y-m-d') == $this->selectedDate 
                        : false)
                    : true;
                    
                if ($matchesCenter && $matchesAssessor && $matchesDate) {
                    $filteredSchedules->push($schedule);
                }
            }
        }

        // Get assessments that have matching schedules
        $this->filteredAssessments = $this->allAssessments->filter(function ($assessment) use ($filteredSchedules) {
            return $filteredSchedules->contains(function ($schedule) use ($assessment) {
                return $schedule->assessment_id === $assessment->id;
            });
        })->values();

        // Set the primary assessment for display
        $this->assessment = $this->filteredAssessments->first() ?: $this->allAssessments->first();
        
        // Merge all results from filtered schedules
        $this->allResults = collect();
        foreach ($filteredSchedules as $schedule) {
            foreach ($schedule->results as $result) {
                $this->allResults->push($result);
            }
        }
        
        $this->calculateStats();
        
        // Apply sorting
        if ($this->sortField) {
            $this->sort($this->sortField);
        }
    }

    private function calculateStats()
    {
        // Only count students with a valid result (not dropped, not null)
        $validResults = $this->allResults->filter(function($result) {
            return $result->competency_type_id && 
                   ($result->competencyType?->name ?? '') !== 'Dropped';
        });
        
        $competent = $validResults->filter(function($result) {
            return $result->competencyType?->name === 'Competent';
        })->count();
        
        $notYetCompetent = $validResults->filter(function($result) {
            return $result->competencyType?->name === 'Not Yet Competent';
        })->count();
        
        $absent = $validResults->filter(function($result) {
            return $result->competencyType?->name === 'Absent';
        })->count();
        
        $evaluated = $competent + $notYetCompetent + $absent;
        
        $this->stats = [
            'total' => $validResults->count(),
            'completed' => $evaluated,
            'competent' => $competent,
            'not_yet_competent' => $notYetCompetent,
            'absent' => $absent,
            'pending' => $validResults->count() - $evaluated,
            'completion_percentage' => $validResults->count() > 0 ? round(($evaluated / $validResults->count()) * 100, 1) : 0,
            'passing_percentage' => ($competent + $notYetCompetent) > 0 ? round(($competent / ($competent + $notYetCompetent)) * 100, 1) : 0,
            'assessment_count' => $this->filteredAssessments->count()
        ];
    }

    public function sort($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        
        // Re-sort the allResults collection
        $this->allResults = $this->allResults->sortBy(function($result) {
            switch($this->sortField) {
                case 'student_name':
                    return $result->student->user->last_name . ', ' . $result->student->user->first_name;
                case 'student_id':
                    return $result->student->student_id ?? '';
                case 'uli':
                    return $result->student->uli ?? '';
                case 'competency':
                    return $result->competencyType->name ?? 'ZZZ'; // Put null values at the end
                case 'remarks':
                    return $result->remarks ?? '';
                default:
                    return $result->student->user->last_name . ', ' . $result->student->user->first_name;
            }
        }, SORT_REGULAR, $this->sortDirection === 'desc')->values();
    }
    
    public function render()
    {
        // Apply default sorting when first rendering
        if (count($this->allResults) > 0 && $this->sortField) {
            $this->sort($this->sortField);
        }
        
        return view('livewire.modals.tesda-focal.view-assessment-results');
    }

    public static function modalMaxWidth(): string
    {
        return '7xl';
    }
}
