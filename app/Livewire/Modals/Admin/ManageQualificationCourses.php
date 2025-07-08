<?php

namespace App\Livewire\Modals\Admin;

use App\Models\QualificationType;
use App\Models\Course;
use LivewireUI\Modal\ModalComponent;

class ManageQualificationCourses extends ModalComponent
{
    public $qualificationTypeId;
    public $qualificationType;
    public $selectedCourses = [];
    public $availableCourses = [];

    public function mount($qualificationTypeId)  // Changed from $id to $qualificationTypeId
    {
        $this->qualificationTypeId = $qualificationTypeId;
        $this->qualificationType = QualificationType::with('courses')->findOrFail($qualificationTypeId);
        
        $this->selectedCourses = $this->qualificationType->courses->pluck('id')->toArray();
        $this->availableCourses = Course::orderBy('code', 'asc')->get()->toArray();
    }

    public function save()
    {
        try {
            // Sync selected courses
            $this->qualificationType->courses()->sync($this->selectedCourses);

            $this->dispatch('swal:success', [
                'title' => 'Success!',
                'text' => 'Course assignments updated successfully!',
            ]);

            $this->dispatch('qualification-courses-updated');
            $this->closeModal();

        } catch (\Exception $e) {
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Something went wrong. Please try again.',
            ]);
        }
    }

    public function render()
    {
        return view('livewire.modals.admin.manage-qualification-courses');
    }

    public static function modalMaxWidth(): string
    {
        return 'lg';
    }
}