<?php

namespace App\Livewire\Modals\Registrar;

use App\Models\Student;
use LivewireUI\Modal\ModalComponent;

class ViewStudent extends ModalComponent
{
    public $student;

    public function mount($studentId)
    {
        $this->student = Student::with([
            'user', 
            'course.campuses', 
            'academicYear',
            'results.assessment.qualificationType',
            'results.assessment.examType',
            'results.assessment.assessor',
            'results.assessment.assessmentCenter',
            'results.competencyType'
        ])->findOrFail($studentId);
    }

    public function render()
    {
        return view('livewire.modals.registrar.view-student');
    }

    public static function modalMaxWidth(): string
    {
        return 'xl';
    }
}
