<?php

namespace App\Livewire\Pages\ProgramHead;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Student;
use App\Models\Course;
use App\Models\Academic;
use App\Models\ProgramHead;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;

class Students extends Component
{
    use WithPagination;

    public $search = '';
    public $courseFilter = '';
    public $statusFilter = '';

    protected $queryString = ['search', 'courseFilter', 'statusFilter'];

    public function clearFilters()
    {
        $this->search = '';
        $this->courseFilter = '';
        $this->statusFilter = '';
    }

    public function getCoursesProperty()
    {
        // Get courses assigned to the current program head
        return ProgramHead::where('user_id', Auth::id())
            ->with('course')
            ->get()
            ->pluck('course')
            ->unique('id');
    }

    #[Layout('layouts.app')]
    #[Title('Dashboard')]
    #[On('student-updated')]
    public function refresh()
    {
        // Refresh the component
    }

    public function render()
    {
        // Get course IDs that this program head manages
        $managedCourseIds = $this->courses->pluck('id');

        $query = Student::with(['user', 'course', 'academicYear'])
            ->whereIn('course_id', $managedCourseIds)
            // Only show students from active academic year
            ->whereHas('academicYear', function ($q) {
                $q->where('is_active', true);
            });

        // Apply search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('user', function ($userQuery) {
                    $userQuery->where('first_name', 'like', '%' . $this->search . '%')
                              ->orWhere('last_name', 'like', '%' . $this->search . '%')
                              ->orWhere('email', 'like', '%' . $this->search . '%');
                })->orWhere('student_id', 'like', '%' . $this->search . '%')
                  ->orWhere('uli', 'like', '%' . $this->search . '%');
            });
        }

        // Apply course filter
        if ($this->courseFilter) {
            $query->where('course_id', $this->courseFilter);
        }

        // Apply status filter
        if ($this->statusFilter) {
            $query->whereHas('user', function ($q) {
                $q->where('status', $this->statusFilter);
            });
        }

        $query->join('users', 'students.user_id', '=', 'users.id')
              ->orderBy('users.last_name', 'asc')
              ->orderBy('users.first_name', 'asc')
              ->select('students.*');

        $students = $query->paginate(10);

        // Calculate dropped count for the managed courses (only from active academic year)
        $droppedCount = Student::whereIn('course_id', $managedCourseIds)
            ->whereHas('academicYear', function ($q) {
                $q->where('is_active', true);
            })
            ->whereHas('user', function ($q) {
                $q->where('status', 'dropped');
            })->count();

        return view('livewire.pages.program-head.students', [
            'students' => $students,
            'courses' => $this->courses,
            'droppedCount' => $droppedCount,
        ]);
    }
}