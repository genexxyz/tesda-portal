<?php

namespace App\Livewire\Modals\ProgramHead;

use App\Models\Student;
use LivewireUI\Modal\ModalComponent;

class ViewStudentDetails extends ModalComponent
{
    public $student;

    public function mount($studentId)
    {
        $this->student = Student::with([
            'user', 
            'course.campuses', 
            'academicYear',
            'results.assessmentSchedule.assessment.qualificationType',
            'results.assessmentSchedule.assessment.examType',
            'results.assessmentSchedule.assessment.course',
            'results.assessmentSchedule.assessor',
            'results.assessmentSchedule.assessmentCenter',
            'results.competencyType'
        ])->findOrFail($studentId);
    }

    public function render()
    {
        return view('livewire.modals.program-head.view-student-details');
    }

    public static function modalMaxWidth(): string
    {
        return 'xl';
    }
}
