<?php

namespace App\Livewire\Pages\Student;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\Student;
use App\Models\Academic;
use App\Models\Result;
use Illuminate\Support\Facades\Auth;

class Home extends Component
{
    public $student;
    public $currentAcademicYear;
    public $assessmentResults;

    public function mount()
    {
        // Get the current student record
        $this->student = Student::with(['course', 'user', 'academicYear'])
            ->where('user_id', Auth::id())
            ->first();

        // Get the current active academic year
        $this->currentAcademicYear = Academic::where('is_active', true)->first();

        // Get assessment results for the current academic year (excluding dropped results)
        if ($this->student && $this->currentAcademicYear) {
            $this->assessmentResults = Result::with([
                'assessmentSchedule.assessment.qualificationType',
                'assessmentSchedule.assessment.examType',
                'assessmentSchedule.assessment.course',
                'assessmentSchedule.assessor',
                'assessmentSchedule.assessmentCenter',
                'competencyType'
            ])
            ->where('student_id', $this->student->id)
            ->whereHas('assessmentSchedule.assessment', function($query) {
                $query->where('academic_year_id', $this->currentAcademicYear->id);
            })
            ->whereHas('competencyType', function($query) {
                $query->where('name', '!=', 'Dropped');
            })
            ->orWhere(function($query) {
                $query->where('student_id', $this->student->id)
                      ->whereNull('competency_type_id')
                      ->whereHas('assessmentSchedule.assessment', function($q) {
                          $q->where('academic_year_id', $this->currentAcademicYear->id);
                      });
            })
            ->orderBy('created_at', 'desc')
            ->get();
        }
    }

    public function getAssessmentStatsProperty()
    {
        if (!$this->assessmentResults) {
            return [
                'total' => 0,
                'competent' => 0,
                'not_yet_competent' => 0,
                'absent' => 0,
                'pending' => 0
            ];
        }

        return [
            'total' => $this->assessmentResults->count(),
            'competent' => $this->assessmentResults->where('competencyType.name', 'Competent')->count(),
            'not_yet_competent' => $this->assessmentResults->where('competencyType.name', 'Not Yet Competent')->count(),
            'absent' => $this->assessmentResults->where('competencyType.name', 'Absent')->count(),
            'pending' => $this->assessmentResults->whereNull('competency_type_id')->count()
        ];
    }

    #[Layout('layouts.app')]
    #[Title('Student Dashboard')]
    public function render()
    {
        return view('livewire.pages.student.home', [
            'assessmentStats' => $this->assessmentStats
        ]);
    }
}
