<?php

namespace App\Livewire\Pages\Registrar;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Models\Student;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class Students extends Component
{
    use WithPagination;
    
    public $search = '';
    public $courseFilter = '';
    
    #[Layout('layouts.app')]
    #[Title('Student Management')]

    #[On('student-created')]
    #[On('student-updated')]
    #[On('student-deleted')]
    #[On('student-imported')]
    public function refreshTable()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedCourseFilter()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->courseFilter = '';
        $this->resetPage();
    }

    public function confirmDelete($id)
    {
        $student = Student::with('user')->find($id);
        
        $this->dispatch('swal:delete', [
            'title' => 'Delete Student',
            'text' => "Are you sure you want to delete {$student->user->first_name} {$student->user->last_name}?",
            'method' => 'deleteStudent',
            'params' => $id
        ]);
    }

    #[On('deleteStudent')]
    public function deleteStudent($id)
    {
        try {
            $student = Student::findOrFail($id);
            
            // Delete associated user if exists
            if ($student->user_id) {
                User::find($student->user_id)?->delete();
            }
            
            $student->delete();
            
            $this->dispatch('swal:success', [
                'title' => 'Deleted!',
                'text' => 'Student has been deleted successfully.',
            ]);
            
            $this->refreshTable();
            
        } catch (\Exception $e) {
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Failed to delete student.',
            ]);
        }
    }

    public function exportStudents()
    {
        try {
            // Add export logic here
            $this->dispatch('swal:success', [
                'title' => 'Export Started!',
                'text' => 'Student data export has been initiated.',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('swal:error', [
                'title' => 'Export Failed!',
                'text' => 'Failed to export student data.',
            ]);
        }
    }

    public function render()
    {
        $registrarCampusId = Auth::user()->campus_id;
        
        // Get courses for the registrar's campus only using the pivot table
        $campusCourses = Course::whereHas('campuses', function($query) use ($registrarCampusId) {
                                $query->where('campuses.id', $registrarCampusId);
                            })->get();
        
        $students = Student::with(['course', 'user'])
                          ->whereHas('course', function($query) use ($registrarCampusId) {
                              $query->whereHas('campuses', function($q) use ($registrarCampusId) {
                                  $q->where('campuses.id', $registrarCampusId);
                              });
                          })
                          ->when($this->search, function($query) {
                              $query->where(function($q) {
                                  $q->where('student_id', 'like', '%' . $this->search . '%')
                                    ->orWhere('uli', 'like', '%' . $this->search . '%')
                                    ->orWhereHas('user', function($userQuery) {
                                        $userQuery->where('first_name', 'like', '%' . $this->search . '%')
                                                 ->orWhere('last_name', 'like', '%' . $this->search . '%')
                                                 ->orWhere('middle_name', 'like', '%' . $this->search . '%');
                                    });
                              });
                          })
                          ->when($this->courseFilter, function($query) {
                              $query->where('course_id', $this->courseFilter);
                          })
                          ->join('users', 'students.user_id', '=', 'users.id')
                          ->orderBy('users.last_name', 'asc')
                          ->orderBy('users.first_name', 'asc')
                          ->select('students.*') // Make sure to select only student columns
                          ->paginate(10);

        return view('livewire.pages.registrar.students', [
            'students' => $students,
            'courses' => $campusCourses,
            'userCampus' => Auth::user()->campus
        ]);
    }
}