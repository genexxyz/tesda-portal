<?php

namespace App\Livewire\Pages\ProgramHead;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Student;
use App\Models\Course;
use App\Models\Campus;
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
    public $campusFilter = '';
    public $academicYearFilter = '';

    protected $queryString = ['search', 'courseFilter', 'campusFilter', 'academicYearFilter'];

    public function clearFilters()
    {
        $this->search = '';
        $this->courseFilter = '';
        $this->campusFilter = '';
        $this->academicYearFilter = '';
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

    public function getCampusesProperty()
    {
        // Get campuses that have courses assigned to this program head
        $courseIds = $this->courses->pluck('id');
        
        return Campus::whereHas('courses', function ($query) use ($courseIds) {
            $query->whereIn('courses.id', $courseIds);
        })->get();
    }

    public function getAcademicYearsProperty()
    {
        return Academic::where('status', true)->orderBy('start_year', 'desc')->get();
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
            ->whereIn('course_id', $managedCourseIds);

        // Apply search filter
        if ($this->search) {
            $query->whereHas('user', function ($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                  ->orWhere('last_name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            })->orWhere('student_id', 'like', '%' . $this->search . '%')
              ->orWhere('uli', 'like', '%' . $this->search . '%');
        }

        // Apply course filter
        if ($this->courseFilter) {
            $query->where('course_id', $this->courseFilter);
        }

        // Apply campus filter
        if ($this->campusFilter) {
            $query->whereHas('course.campuses', function ($q) {
                $q->where('campuses.id', $this->campusFilter);
            });
        }

        // Apply academic year filter
        if ($this->academicYearFilter) {
            $query->where('academic_year_id', $this->academicYearFilter);
        }

        $students = $query->orderBy('student_id', 'asc')->paginate(10);

        return view('livewire.pages.program-head.students', [
            'students' => $students,
            'courses' => $this->courses,
            'campuses' => $this->campuses,
            'academicYears' => $this->academicYears,
        ]);
    }
}
