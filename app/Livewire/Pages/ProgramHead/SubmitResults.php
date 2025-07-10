<?php

namespace App\Livewire\Pages\ProgramHead;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Assessment;
use App\Models\Result;
use App\Models\CompetencyType;
use App\Models\ProgramHead;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubmitResults extends Component
{
    public $assessment;
    public $results = [];
    public $studentResults = [];
    public $isSaved = false;

    #[Layout('layouts.app')]
    #[Title('Submit Assessment Results')]

    public function mount(Assessment $assessment)
    {
        // Verify this assessment belongs to the program head
        $user = Auth::user();
        $programHeadCampusId = $user->campus_id;
        
        // Get managed course IDs for this program head
        $managedCourseIds = ProgramHead::where('user_id', $user->id)
                                     ->pluck('course_id');
        
        if (!$managedCourseIds->contains($assessment->course_id) || 
            $assessment->campus_id !== $programHeadCampusId) {
            abort(403, 'Unauthorized access to this assessment.');
        }

        $this->assessment = $assessment->load([
            'course', 
            'campus', 
            'academicYear', 
            'qualificationType', 
            'examType', 
            'schedules.assessmentCenter', 
            'schedules.assessor',
            'schedules.results.student.user',
            'schedules.results.competencyType'
        ]);

        $this->initializeResults();
    }

    public function initializeResults()
    {
        // Initialize results for each student from all schedules
        foreach ($this->assessment->schedules as $schedule) {
            foreach ($schedule->results as $result) {
                $this->studentResults[$result->id] = [
                    'competency_type_id' => $result->competency_type_id,
                    'remarks' => $result->remarks ?? ''
                ];
            }
        }
    }

    public function updateCompetencyType($resultId, $competencyTypeId)
    {
        $this->studentResults[$resultId]['competency_type_id'] = $competencyTypeId;
    }

    public function saveResults()
    {
        $this->validate([
            'studentResults.*.competency_type_id' => 'nullable|exists:competency_types,id',
            'studentResults.*.remarks' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            foreach ($this->studentResults as $resultId => $data) {
                $result = Result::find($resultId);
                
                if ($result) {
                    $result->update([
                        'competency_type_id' => $data['competency_type_id'],
                        'remarks' => $data['remarks'],
                        'created_by' => Auth::id()
                    ]);
                }
            }

            // Update assessment status if all students have results
            $this->updateAssessmentStatus();

            DB::commit();

            $this->isSaved = true;
            
            $this->dispatch('swal:success', [
                'title' => 'Success!',
                'text' => 'Assessment results have been saved successfully.',
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Failed to save results: ' . $e->getMessage(),
            ]);
        }
    }

    private function updateAssessmentStatus()
    {
        $totalStudents = $this->assessment->results->count();
        $completedResults = collect($this->studentResults)->filter(function ($result) {
            return !is_null($result['competency_type_id']);
        })->count();

        if ($completedResults === $totalStudents) {
            $this->assessment->update(['status' => 'completed']);
        }
    }

    public function getCompetencyTypesProperty()
    {
        // Exclude "Dropped" competency type
        return CompetencyType::where('name', '!=', 'Dropped')
                           ->orderBy('name')
                           ->get();
    }

    public function getCompletionStatsProperty()
    {
        $total = count($this->studentResults);
        $completed = collect($this->studentResults)->filter(function ($result) {
            return !is_null($result['competency_type_id']);
        })->count();
        
        // Get competency type IDs
        $competentTypeId = $this->competencyTypes->where('name', 'Competent')->first()?->id;
        $notCompetentTypeId = $this->competencyTypes->where('name', 'Not Yet Competent')->first()?->id;
        $absentTypeId = $this->competencyTypes->where('name', 'Absent')->first()?->id;
        
        $competent = collect($this->studentResults)->filter(function ($result) use ($competentTypeId) {
            return $result['competency_type_id'] == $competentTypeId;
        })->count();
        
        $notYetCompetent = collect($this->studentResults)->filter(function ($result) use ($notCompetentTypeId) {
            return $result['competency_type_id'] == $notCompetentTypeId;
        })->count();
        
        $absent = collect($this->studentResults)->filter(function ($result) use ($absentTypeId) {
            return $result['competency_type_id'] == $absentTypeId;
        })->count();

        return [
            'total' => $total,
            'completed' => $completed,
            'pending' => $total - $completed,
            'competent' => $competent,
            'not_yet_competent' => $notYetCompetent,
            'absent' => $absent,
            'completion_percentage' => $total > 0 ? round(($completed / $total) * 100, 1) : 0
        ];
    }

    public function render()
    {
        return view('livewire.pages.program-head.submit-results', [
            'competencyTypes' => $this->competencyTypes,
            'stats' => $this->completionStats
        ]);
    }
}