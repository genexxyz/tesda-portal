<?php

namespace App\Livewire\Modals\Admin;

use App\Models\User;
use App\Models\Campus;
use App\Models\Course;
use App\Models\ProgramHead;
use LivewireUI\Modal\ModalComponent;
use Livewire\Attributes\Rule;
use Illuminate\Support\Facades\Hash;

class EditProgramHead extends ModalComponent
{
    public $programHeadId;
    public $programHead;

    #[Rule('required|string|max:255')]
    public $first_name = '';

    #[Rule('nullable|string|max:255')]
    public $middle_name = '';

    #[Rule('required|string|max:255')]
    public $last_name = '';

    public $email = '';

    #[Rule('required|exists:campuses,id')] 
    public $campus_id = '';

    #[Rule('array')]
    public $course_ids = [];

    public $availableCourses = [];

    public function mount($programHeadId)
    {
        $this->programHeadId = $programHeadId;
        $this->programHead = User::with(['campus'])->findOrFail($programHeadId);
        
        // Load user data
        $this->first_name = $this->programHead->first_name;
        $this->middle_name = $this->programHead->middle_name;
        $this->last_name = $this->programHead->last_name;
        $this->email = $this->programHead->email;
        $this->campus_id = $this->programHead->campus_id;
        
        // Load assigned courses using direct query to avoid null relationship
        $this->course_ids = ProgramHead::where('user_id', $programHeadId)
                                      ->pluck('course_id')
                                      ->toArray();
        
        $this->loadCourses();
    }

    public function rules()
    {
        return [
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->programHeadId,
            'campus_id' => 'required|exists:campuses,id',
            'course_ids' => 'required|array|min:1',
        ];
    }

    public function messages()
    {
        return [
            'course_ids.required' => 'Please select at least one course.',
            'course_ids.min' => 'Please select at least one course.',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
        
        if ($propertyName === 'campus_id') {
            $this->loadCourses();
            $this->course_ids = []; // Reset selected courses when campus changes
        }
    }

    public function loadCourses()
    {
        if ($this->campus_id) {
            // Get all courses for this campus (including assigned ones for editing)
            $this->availableCourses = Course::where('campus_id', $this->campus_id)
                                          ->orderBy('code', 'asc')
                                          ->get()
                                          ->toArray();
        } else {
            $this->availableCourses = [];
        }
    }

    public function getCourseAssignmentStatus($courseId)
    {
        // Check if this course is assigned to another program head
        $assignedTo = ProgramHead::where('course_id', $courseId)
                                ->where('user_id', '!=', $this->programHeadId)
                                ->with('user')
                                ->first();
        
        return $assignedTo;
    }

    public function save()
    {
        $this->validate();

        try {
            // Update the user
            $this->programHead->update([
                'first_name' => ucfirst(strtolower($this->first_name)),
                'middle_name' => $this->middle_name ? ucfirst(strtolower($this->middle_name)) : null,
                'last_name' => ucfirst(strtolower($this->last_name)),
                'email' => strtolower($this->email),
                'campus_id' => $this->campus_id,
            ]);

            // Update course assignments
            // First, remove all existing assignments for this user
            ProgramHead::where('user_id', $this->programHeadId)->delete();
            
            // Then add the new assignments
            foreach ($this->course_ids as $courseId) {
                ProgramHead::create([
                    'user_id' => $this->programHeadId,
                    'course_id' => $courseId,
                ]);
            }

            $this->dispatch('swal:success', [
                'title' => 'Success!',
                'text' => 'Program Head updated successfully!',
            ]);

            $this->dispatch('program-head-updated');
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
        return view('livewire.modals.admin.edit-program-head', [
            'campuses' => Campus::orderBy('name', 'asc')->get()
        ]);
    }

    public static function modalMaxWidth(): string
    {
        return '2xl';
    }
}