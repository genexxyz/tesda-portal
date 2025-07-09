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
    public $assessmentTypeFilter = '';
    public $dateFilter = '';
    public $statusFilter = '';

    #[Layout('layouts.app')]
    #[Title('Assessments')]

    protected $queryString = ['search', 'courseFilter', 'assessmentTypeFilter', 'dateFilter', 'statusFilter'];

    public function clearFilters()
    {
        $this->search = '';
        $this->courseFilter = '';
        $this->assessmentTypeFilter = '';
        $this->dateFilter = '';
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

    public function getAssessmentDatesProperty()
    {
        // Get unique assessment dates for this program head's assessments
        $managedCourseIds = $this->courses->pluck('id');
        $programHeadCampusId = Auth::user()->campus_id;
        
        return Assessment::whereIn('course_id', $managedCourseIds)
            ->where('campus_id', $programHeadCampusId)
            ->whereHas('academicYear', function($query) {
                $query->where('is_active', true);
            })
            ->distinct()
            ->orderBy('assessment_date', 'desc')
            ->pluck('assessment_date')
            ->map(function($date) {
                return [
                    'id' => $date->format('Y-m-d'),
                    'name' => $date->format('F j, Y'),
                    'value' => $date->format('Y-m-d')
                ];
            })
            ->unique('id')
            ->values();
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
        $programHeadCampusId = Auth::user()->campus_id;

        $query = Assessment::with(['course', 'campus', 'academicYear', 'qualificationType', 'examType', 'assessmentCenter', 'assessor', 'results.student.user'])
            ->whereIn('course_id', $managedCourseIds)
            ->where('campus_id', $programHeadCampusId) // Only show assessments from program head's campus
            ->whereHas('academicYear', function($q) {
                $q->where('is_active', true); // Only show assessments from active academic year
            });

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

        // Apply assessment type filter (ISA/MANDATORY)
        if ($this->assessmentTypeFilter) {
            $query->whereHas('examType', function($q) {
                $q->where('type', $this->assessmentTypeFilter);
            });
        }

        // Apply date filter
        if ($this->dateFilter) {
            $query->whereDate('assessment_date', $this->dateFilter);
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

        // Get stats for the current filtered results
        $allAssessments = Assessment::whereIn('course_id', $managedCourseIds)
            ->where('campus_id', $programHeadCampusId)
            ->whereHas('academicYear', function($q) {
                $q->where('is_active', true);
            })
            ->get();

        $stats = [
            'total' => $allAssessments->count(),
            'upcoming' => $allAssessments->where('assessment_date', '>', now())->count(),
            'completed' => $allAssessments->where('assessment_date', '<', now())->count(),
            'today' => $allAssessments->filter(fn($a) => $a->assessment_date?->isToday())->count(),
            'isa' => $allAssessments->filter(fn($a) => $a->examType?->type === 'ISA')->count(),
            'mandatory' => $allAssessments->filter(fn($a) => $a->examType?->type === 'MANDATORY')->count(),
        ];

        return view('livewire.pages.program-head.assessments', [
            'assessments' => $assessments,
            'courses' => $this->courses,
            'assessmentDates' => $this->assessmentDates,
            'stats' => $stats,
        ]);
    }
}