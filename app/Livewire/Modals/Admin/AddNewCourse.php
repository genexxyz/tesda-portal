<?php

namespace App\Livewire\Modals\Admin;

use App\Models\Course;
use App\Models\Campus;
use App\Models\QualificationType;
use LivewireUI\Modal\ModalComponent;
use Livewire\Attributes\Rule;

class AddNewCourse extends ModalComponent
{
    #[Rule('required|string|max:20|unique:courses,code')]
    public $code = '';

    #[Rule('required|string|max:255|unique:courses,name')]
    public $name = '';

    #[Rule('nullable|array')]
    public $selectedCampuses = [];

    public function mount()
    {
        $this->selectedCampuses = [];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function save()
    {
        $this->validate();

        try {
            $course = Course::create([
                'code' => strtoupper($this->code),
                'name' => strtoupper($this->name),
            ]);

            // Attach selected campuses if any
            if (!empty($this->selectedCampuses)) {
                $course->campuses()->attach($this->selectedCampuses);
            }

            $this->dispatch('swal:success', [
                'title' => 'Success!',
                'text' => 'Course created successfully!',
            ]);

            $this->dispatch('course-created');
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
        return view('livewire.modals.admin.add-new-course', [
            'campuses' => Campus::orderBy('name', 'asc')->get()
        ]);
    }

    public static function modalMaxWidth(): string
    {
        return 'lg';
    }
}