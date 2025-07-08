<?php

namespace App\Livewire\Pages\ProgramHead;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Assessment;
use App\Models\Course;
use App\Models\Campus;
use App\Models\Academic;
use App\Models\ProgramHead;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;

class Assessments extends Component
{
    

    use WithPagination;

    public $search = '';
    public $courseFilter = '';
    public $campusFilter = '';
    public $academicYearFilter = '';
    public $statusFilter = '';
#[Layout('layouts.app')]
    #[Title('Dashboard')]
    protected $queryString = ['search', 'courseFilter', 'campusFilter', 'academicYearFilter', 'statusFilter'];

    public function clearFilters()
    {
        $this->search = '';
        $this->courseFilter = '';
        $this->campusFilter = '';
        $this->academicYearFilter = '';
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

    #[On('assessment-assigned')]
    public function refresh()
    {
        // Refresh the component
    }

    public function render()
    {
        // Get course IDs that this program head manages
        $managedCourseIds = $this->courses->pluck('id');

        $query = Assessment::with(['course', 'campus', 'academicYear', 'qualificationType', 'examType', 'assessmentCenter', 'assessor', 'results.student.user'])
            ->whereIn('course_id', $managedCourseIds);

        // Apply search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('course', function ($subQ) {
                    $subQ->where('name', 'like', '%' . $this->search . '%')
                         ->orWhere('code', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('qualificationType', function ($subQ) {
                    $subQ->where('name', 'like', '%' . $this->search . '%')
                         ->orWhere('code', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('assessor', function ($subQ) {
                    $subQ->where('name', 'like', '%' . $this->search . '%');
                });
            });
        }

        // Apply course filter
        if ($this->courseFilter) {
            $query->where('course_id', $this->courseFilter);
        }

        // Apply campus filter
        if ($this->campusFilter) {
            $query->where('campus_id', $this->campusFilter);
        }

        // Apply academic year filter
        if ($this->academicYearFilter) {
            $query->where('academic_year_id', $this->academicYearFilter);
        }

        // Apply status filter
        if ($this->statusFilter) {
            $currentDate = now();
            if ($this->statusFilter === 'upcoming') {
                $query->where('assessment_date', '>', $currentDate);
            } elseif ($this->statusFilter === 'completed') {
                $query->where('assessment_date', '<', $currentDate);
            } elseif ($this->statusFilter === 'today') {
                $query->whereDate('assessment_date', $currentDate);
            }
        }

        $assessments = $query->orderBy('assessment_date', 'desc')->paginate(10);

        return view('livewire.pages.program-head.assessments', [
            'assessments' => $assessments,
            'courses' => $this->courses,
            'campuses' => $this->campuses,
            'academicYears' => $this->academicYears,
        ]);
    }
}
