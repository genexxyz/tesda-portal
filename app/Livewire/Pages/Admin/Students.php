<?php

namespace App\Livewire\Pages\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Models\Student;
use App\Models\Campus;
use App\Models\Course;
use App\Models\Academic;
use App\Models\User;

class Students extends Component
{
    use WithPagination;
    
    public $search = '';
    public $campusFilter = '';
    public $courseFilter = '';
    public $academicYearFilter = '';
    public $statusFilter = '';
    
    #[Layout('layouts.app')]
    #[Title('Student Management')]

    #[On('student-created')]
    #[On('student-updated')]
    #[On('student-deleted')]
    public function refreshTable()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedCampusFilter()
    {
        $this->resetPage();
    }

    public function updatedCourseFilter()
    {
        $this->resetPage();
    }

    public function updatedAcademicYearFilter()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->campusFilter = '';
        $this->courseFilter = '';
        $this->academicYearFilter = '';
        $this->statusFilter = '';
        $this->resetPage();
    }

    public function toggleStatus($id)
    {
        try {
            $user = User::findOrFail($id);
            $newStatus = $user->status === 'active' ? 'inactive' : 'active';
            
            $user->update(['status' => $newStatus]);
            
            $this->dispatch('swal:success', [
                'title' => 'Success!',
                'text' => 'Student status updated successfully!',
            ]);
            
            $this->refreshTable();
            
        } catch (\Exception $e) {
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Failed to update status.',
            ]);
        }
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
            $student = Student::with('user')->findOrFail($id);
            
            // Delete user and student record
            if ($student->user) {
                $student->user->delete();
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
        // Build query for students with filters
        $query = Student::with(['user.campus', 'course', 'academicYear', 'results.competencyType'])
            ->whereHas('user');

        // Apply search filter
        if ($this->search) {
            $query->where(function($q) {
                $q->whereHas('user', function($userQuery) {
                    $userQuery->where('first_name', 'like', '%' . $this->search . '%')
                             ->orWhere('last_name', 'like', '%' . $this->search . '%')
                             ->orWhere('email', 'like', '%' . $this->search . '%');
                })
                ->orWhere('student_id', 'like', '%' . $this->search . '%')
                ->orWhere('uli', 'like', '%' . $this->search . '%');
            });
        }

        // Apply status filter
        if ($this->statusFilter) {
            $query->whereHas('user', function($q) {
                $q->where('status', $this->statusFilter);
            });
        }

        // Apply campus filter
        if ($this->campusFilter) {
            $query->whereHas('user', function($q) {
                $q->where('campus_id', $this->campusFilter);
            });
        }

        // Apply course filter
        if ($this->courseFilter) {
            $query->where('course_id', $this->courseFilter);
        }

        // Apply academic year filter
        if ($this->academicYearFilter) {
            $query->where('academic_year_id', $this->academicYearFilter);
        }

        // Order by user's last name, then first name
        $query->join('users', 'students.user_id', '=', 'users.id')
              ->orderBy('users.last_name', 'asc')
              ->orderBy('users.first_name', 'asc')
              ->select('students.*');

        $students = $query->paginate(10);

        // Get filter options
        $campuses = Campus::orderBy('name', 'asc')->get();
        $courses = Course::orderBy('code', 'asc')->get();
        $academicYears = Academic::orderBy('start_year', 'desc')->get();

        // Calculate statistics
        $totalStudents = Student::count();
        $activeStudents = Student::whereHas('user', function($q) {
            $q->where('status', 'active');
        })->count();
        $inactiveStudents = Student::whereHas('user', function($q) {
            $q->where('status', 'inactive');
        })->count();
        $droppedStudents = Student::whereHas('user', function($q) {
            $q->where('status', 'dropped');
        })->count();

        return view('livewire.pages.admin.students', [
            'students' => $students,
            'campuses' => $campuses,
            'courses' => $courses,
            'academicYears' => $academicYears,
            'stats' => [
                'total' => $totalStudents,
                'active' => $activeStudents,
                'inactive' => $inactiveStudents,
                'dropped' => $droppedStudents
            ]
        ]);
    }
}
