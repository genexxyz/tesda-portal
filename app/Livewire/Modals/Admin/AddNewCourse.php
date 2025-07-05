<?php

namespace App\Livewire\Modals\Admin;

use App\Models\Course;
use App\Models\Campus;
use LivewireUI\Modal\ModalComponent;
use Livewire\Attributes\Rule;

class AddNewCourse extends ModalComponent
{
    #[Rule('required|string|max:20|unique:courses,code')]
    public $code = '';

    #[Rule('required|string|max:255|unique:courses,name')]
    public $name = '';

    #[Rule('required|exists:campuses,id')]
    public $campus_id = '';

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function save()
    {
        $this->validate();

        try {
            Course::create([
                'code' => strtoupper($this->code),
                'name' => strtoupper($this->name),
                'campus_id' => $this->campus_id ?: null,
            ]);

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