<?php

namespace App\Livewire\Modals\Admin;

use App\Models\User;
use App\Models\Campus;
use App\Models\Course;
use App\Models\ProgramHead;
use LivewireUI\Modal\ModalComponent;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Hash;

class AddNewProgramHead extends ModalComponent
{
    #[Rule('required|string|max:255')]
    public $first_name = '';

    #[Rule('nullable|string|max:255')]
    public $middle_name = '';

    #[Rule('required|string|max:255')]
    public $last_name = '';

    #[Rule('required|email|unique:users,email')]
    public $email = '';

    #[Rule('required|exists:campuses,id')] 
    public $campus_id = '';

    #[Rule('required|array|min:1')]
    public $course_ids = [];

    public $availableCourses = [];

    #[Computed]
    public function selectedCount()
    {
        return count($this->course_ids);
    }

    #[Computed]
    public function availableCount()
    {
        return count($this->availableCourses);
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
        
        if ($propertyName === 'campus_id') {
            $this->loadCourses();
            $this->course_ids = []; // Reset selected courses when campus changes
        }
    }

    public function mount()
    {
        $this->availableCourses = [];
    }

    public function loadCourses()
    {
        if ($this->campus_id) {
            // Get courses that are available for the selected campus using pivot table
            // and are NOT already assigned to any program head
            $this->availableCourses = Course::whereHas('campuses', function($query) {
                                            $query->where('campuses.id', $this->campus_id);
                                        })
                                        ->whereDoesntHave('programHead')
                                        ->orderBy('code', 'asc')
                                        ->get()
                                        ->toArray();
        } else {
            $this->availableCourses = [];
        }
    }

    public function save()
    {
        $this->validate();

        try {
            // Create the user
            $user = User::create([
                'first_name' => ucfirst(strtolower($this->first_name)),
                'middle_name' => $this->middle_name ? ucfirst(strtolower($this->middle_name)) : null,
                'last_name' => ucfirst(strtolower($this->last_name)),
                'email' => strtolower($this->email),
                'campus_id' => $this->campus_id,
                'role_id' => 3, // Program Head role
                'status' => 'active',
                'password' => Hash::make('password'),
            ]);

            // Assign courses to the program head
            foreach ($this->course_ids as $courseId) {
                ProgramHead::create([
                    'user_id' => $user->id,
                    'course_id' => $courseId,
                ]);
            }

            $this->dispatch('swal:success', [
                'title' => 'Success!',
                'text' => 'Program Head created successfully!',
            ]);

            $this->dispatch('program-head-created');
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
        return view('livewire.modals.admin.add-new-program-head', [
            'campuses' => Campus::orderBy('name', 'asc')->get()
        ]);
    }

    public static function modalMaxWidth(): string
    {
        return '2xl';
    }
}