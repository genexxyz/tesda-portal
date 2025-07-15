<?php

namespace App\Livewire\Modals\Admin;

use App\Models\Student;
use App\Models\Course;
use App\Models\Academic;
use App\Models\User;
use App\Models\Campus;
use App\Models\Result;
use App\Models\CompetencyType;
use LivewireUI\Modal\ModalComponent;
use Livewire\Attributes\Rule;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;

class EditStudent extends ModalComponent
{
    public $studentId;
    public $student;

    // Student fields
    #[Rule('required|string|max:255')]
    public $student_id = '';

    #[Rule('required|string|max:255')]
    public $uli = '';

    #[Rule('required|exists:courses,id')]
    public $course_id = '';

    #[Rule('required|exists:academics,id')]
    public $academic_year_id = '';

    #[Rule('required|exists:campuses,id')]
    public $campus_id = '';

    // User fields
    #[Rule('required|string|max:255')]
    public $first_name = '';

    #[Rule('nullable|string|max:255')]
    public $middle_name = '';

    #[Rule('required|string|max:255')]
    public $last_name = '';

    #[Rule('required|email')]
    public $email = '';

    public function mount($studentId)
    {
        $this->studentId = $studentId;
        $this->student = Student::with(['user.campus', 'course', 'academicYear'])->findOrFail($studentId);
        
        // Load student data
        $this->student_id = $this->student->student_id;
        $this->uli = $this->student->uli;
        $this->course_id = $this->student->course_id;
        $this->academic_year_id = $this->student->academic_year_id;
        
        // Load user data
        if ($this->student->user) {
            $this->first_name = $this->student->user->first_name;
            $this->middle_name = $this->student->user->middle_name;
            $this->last_name = $this->student->user->last_name;
            $this->email = $this->student->user->email;
            $this->campus_id = $this->student->user->campus_id;
        }
    }

    public function rules()
    {
        $emailRule = 'required|email';
        if ($this->student->user_id) {
            $emailRule .= '|unique:users,email,' . $this->student->user_id;
        }

        $studentIdRule = 'nullable|string|max:255';
        if ($this->student_id) {
            $studentIdRule .= '|unique:students,student_id,' . $this->studentId;
        }

        $uliRule = 'nullable|string|max:255';
        if ($this->uli) {
            $uliRule .= '|unique:students,uli,' . $this->studentId;
        }

        return [
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => $emailRule,
            'student_id' => $studentIdRule,
            'uli' => $uliRule,
            'course_id' => 'required|exists:courses,id',
            'academic_year_id' => 'required|exists:academics,id',
            'campus_id' => 'required|exists:campuses,id',
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
            // Update user information
            if ($this->student->user) {
                $this->student->user->update([
                    'first_name' => ucfirst(strtolower($this->first_name)),
                    'middle_name' => $this->middle_name ? ucfirst(strtolower($this->middle_name)) : null,
                    'last_name' => ucfirst(strtolower($this->last_name)),
                    'email' => strtolower($this->email),
                    'campus_id' => $this->campus_id,
                ]);
            } else {
                // Create user if doesn't exist
                $user = User::create([
                    'first_name' => ucfirst(strtolower($this->first_name)),
                    'middle_name' => $this->middle_name ? ucfirst(strtolower($this->middle_name)) : null,
                    'last_name' => ucfirst(strtolower($this->last_name)),
                    'email' => strtolower($this->email),
                    'password' => bcrypt('defaultpassword'), // Should be changed
                    'role_id' => 1, // Student role
                    'status' => 'active',
                    'campus_id' => $this->campus_id,
                ]);
                
                $this->student->update(['user_id' => $user->id]);
            }

            // Update student information
            $this->student->update([
                'student_id' => $this->student_id,
                'uli' => $this->uli,
                'course_id' => $this->course_id,
                'academic_year_id' => $this->academic_year_id,
            ]);

            $this->dispatch('swal:success', [
                'title' => 'Success!',
                'text' => 'Student updated successfully!',
            ]);

            $this->dispatch('student-updated');
            $this->closeModal();

        } catch (\Exception $e) {
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Something went wrong. Please try again.',
            ]);
        }
    }

    public function markAsDropped()
    {
        try {
            DB::transaction(function () {
                // Mark the user as dropped
                if ($this->student->user) {
                    $this->student->user->update(['status' => 'dropped']);
                }

                // Get the "Dropped" competency type
                $droppedCompetencyType = CompetencyType::where('name', 'Dropped')->first();

                if ($droppedCompetencyType) {
                    // Update all results with null competency_type_id for this student to "Dropped"
                    Result::where('student_id', $this->student->id)
                          ->whereNull('competency_type_id')
                          ->update(['competency_type_id' => $droppedCompetencyType->id, 'remarks' => 'Dropped']);
                }
            });

            $this->dispatch('swal:alert', 
                type: 'success',
                text:  'The student has been marked as dropped and all pending assessments have been updated.',
            );

            $this->dispatch('student-updated');
            $this->closeModal();

        } catch (\Exception $e) {
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Failed to mark student as dropped. Please try again.',
            ]);
        }
    }

    public function render()
    {
        return view('livewire.modals.admin.edit-student', [
            'courses' => Course::orderBy('code', 'asc')->get(),
            'academicYears' => Academic::orderBy('start_year', 'desc')->get(),
            'campuses' => Campus::orderBy('name', 'asc')->get()
        ]);
    }

    public static function modalMaxWidth(): string
    {
        return 'lg';
    }
}
