<?php

namespace App\Livewire\Modals\Registrar;

use App\Models\Student;
use App\Models\Course;
use App\Models\Academic;
use App\Models\User;
use App\Models\Role;
use LivewireUI\Modal\ModalComponent;
use Livewire\Attributes\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AddNewStudent extends ModalComponent
{
    // Student fields
    #[Rule('required|string|max:255|unique:students,student_id')]
    public $student_id = '';

    #[Rule('required|string|max:255|unique:students,uli')]
    public $uli = '';

    #[Rule('required|exists:courses,id')]
    public $course_id = '';

    #[Rule('required|exists:academics,id')]
    public $academic_year_id = '';

    // User fields
    #[Rule('required|string|max:255')]
    public $first_name = '';

    #[Rule('nullable|string|max:255')]
    public $middle_name = '';

    #[Rule('required|string|max:255')]
    public $last_name = '';

    #[Rule('required|email|unique:users,email')]
    public $email = '';

    #[Rule('nullable|string|min:8')]
    public $password = '';

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function save()
    {
        $this->validate();

        try {
            DB::transaction(function () {
                // Get student role
                $studentRole = Role::where('name', 'Student')->first();
                
                // Get registrar's campus
                $registrarCampusId = Auth::user()->campus_id;
                
                // Create user first
                $user = User::create([
                    'first_name' => ucfirst(strtolower($this->first_name)),
                    'middle_name' => $this->middle_name ? ucfirst(strtolower($this->middle_name)) : null,
                    'last_name' => ucfirst(strtolower($this->last_name)),
                    'email' => strtolower($this->email),
                    'password' => Hash::make($this->password ?: 'password'),
                    'role_id' => $studentRole ? $studentRole->id : 1,
                    'status' => 'active',
                    'campus_id' => $registrarCampusId,
                ]);

                // Create student record
                Student::create([
                    'user_id' => $user->id,
                    'student_id' => $this->student_id,
                    'uli' => $this->uli,
                    'course_id' => $this->course_id,
                    'academic_year_id' => $this->academic_year_id,
                ]);
            });

            $this->dispatch('swal:success', [
                'title' => 'Success!',
                'text' => 'Student created successfully!',
            ]);

            $this->dispatch('student-created');
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
        $registrarCampusId = Auth::user()->campus_id;
        
        // Get courses for the registrar's campus only
        $campusCourses = Course::whereHas('campuses', function($query) use ($registrarCampusId) {
            $query->where('campuses.id', $registrarCampusId);
        })->orderBy('code', 'asc')->get();
        
        return view('livewire.modals.registrar.add-new-student', [
            'courses' => $campusCourses,
            'academicYears' => Academic::orderBy('start_year', 'desc')->get(),
            'userCampus' => Auth::user()->campus
        ]);
    }

    public static function modalMaxWidth(): string
    {
        return 'lg';
    }
}
