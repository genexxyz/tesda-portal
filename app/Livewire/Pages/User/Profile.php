<?php

namespace App\Livewire\Pages\User;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\User;
use App\Models\Result;
use Illuminate\Support\Facades\Auth;

class Profile extends Component
{
    public $user;
    public $student;
    public $programHead;
    public $assessmentResults;
    public $assessmentStats;

    public function mount()
    {
        // Get current authenticated user with all relationships
        $this->user = User::with([
            'role',
            'campus',
            'student.course',
            'student.academicYear',
            'student.results.competencyType',
            'student.results.assessmentSchedule.assessment.qualificationType',
            'student.results.assessmentSchedule.assessment.examType',
            'student.results.assessmentSchedule.assessor',
            'programHead.course'
        ])->find(Auth::id());

        // Get student-specific data if user is a student
        if ($this->user->student) {
            $this->student = $this->user->student;
            
            // Get assessment results for the student (excluding dropped results)
            $this->assessmentResults = Result::with([
                'assessmentSchedule.assessment.qualificationType',
                'assessmentSchedule.assessment.examType',
                'assessmentSchedule.assessor',
                'competencyType'
            ])
            ->where('student_id', $this->student->id)
            ->whereHas('competencyType', function($query) {
                $query->where('name', '!=', 'Dropped');
            })
            ->orWhere(function($query) {
                $query->where('student_id', $this->student->id)
                      ->whereNull('competency_type_id');
            })
            ->orderBy('created_at', 'desc')
            ->get();

            // Calculate assessment statistics
            $this->assessmentStats = [
                'total' => $this->assessmentResults->count(),
                'competent' => $this->assessmentResults->where('competencyType.name', 'Competent')->count(),
                'not_yet_competent' => $this->assessmentResults->where('competencyType.name', 'Not Yet Competent')->count(),
                'absent' => $this->assessmentResults->where('competencyType.name', 'Absent')->count(),
                'pending' => $this->assessmentResults->whereNull('competency_type_id')->count()
            ];
        }

        // Get program head-specific data if user is a program head
        if ($this->user->programHead) {
            $this->programHead = $this->user->programHead;
        }
    }

    #[Layout('layouts.app')]
    #[Title('My Profile')]
    public function render()
    {
        return view('livewire.pages.user.profile');
    }
}
