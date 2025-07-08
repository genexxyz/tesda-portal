<?php

namespace App\Livewire\Modals\Admin;

use App\Models\Course;
use App\Models\Campus;
use LivewireUI\Modal\ModalComponent;
use Livewire\Attributes\Rule;

class EditCourse extends ModalComponent
{
    public $courseId;
    public $course;

    #[Rule('required|string|max:20')]
    public $code = '';

    #[Rule('required|string|max:255')]
    public $name = '';

    #[Rule('nullable|array')]
    public $selectedCampuses = [];

    public function mount($courseId)
    {
        $this->courseId = $courseId;
        $this->course = Course::with('campuses')->findOrFail($courseId);
        
        // Load course data
        $this->code = $this->course->code;
        $this->name = $this->course->name;
        $this->selectedCampuses = $this->course->campuses->pluck('id')->toArray();
    }

    public function rules()
    {
        return [
            'code' => 'required|string|max:20|unique:courses,code,' . $this->courseId,
            'name' => 'required|string|max:255|unique:courses,name,' . $this->courseId,
            'selectedCampuses' => 'nullable|array',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function save()
    {
        $this->validate();

        try {
            $this->course->update([
                'code' => strtoupper($this->code),
                'name' => strtoupper($this->name),
            ]);

            // Sync selected campuses
            $this->course->campuses()->sync($this->selectedCampuses);

            $this->dispatch('swal:success', [
                'title' => 'Success!',
                'text' => 'Course updated successfully!',
            ]);

            $this->dispatch('course-updated');
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
        return view('livewire.modals.admin.edit-course', [
            'campuses' => Campus::orderBy('name', 'asc')->get()
        ]);
    }

    public static function modalMaxWidth(): string
    {
        return 'lg';
    }
}
