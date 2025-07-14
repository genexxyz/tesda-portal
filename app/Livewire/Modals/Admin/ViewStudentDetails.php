<?php

namespace App\Livewire\Modals\Admin;

use App\Models\Student;
use LivewireUI\Modal\ModalComponent;

class ViewStudentDetails extends ModalComponent
{
    public $student;

    public function mount($studentId)
    {
        $this->student = Student::with([
            'user.campus', 
            'course', 
            'academicYear',
            'results.assessmentSchedule.assessment.qualificationType',
            'results.assessmentSchedule.assessment.examType',
            'results.assessmentSchedule.assessment.assessmentCenter',
            'results.assessmentSchedule.assessor',
            'results.competencyType'
        ])->findOrFail($studentId);
    }

    public function render()
    {
        return view('livewire.modals.admin.view-student-details');
    }

    public static function modalMaxWidth(): string
    {
        return 'xl';
    }
}
