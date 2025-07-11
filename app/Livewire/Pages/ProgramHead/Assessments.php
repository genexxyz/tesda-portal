<?php

namespace App\Livewire\Pages\ProgramHead;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Assessment;
use App\Models\Course;
use App\Models\Campus;
use App\Models\Academic;
use App\Models\ProgramHead;
use App\Models\AssessmentSchedule;
use App\Models\Result;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

    public function deleteAssessment($assessmentId)
    {
        try {
            DB::beginTransaction();

            $assessment = Assessment::with('schedules.results')->findOrFail($assessmentId);
            
            // Check if the assessment has no schedules
            if ($assessment->schedules->count() > 0) {
                session()->flash('error', 'Cannot delete assessment with existing schedules. Please delete all schedules first.');
                return;
            }

            // Delete the assessment
            $assessment->delete();

            DB::commit();

            $this->dispatch('swal:alert', 
                type: 'success',
                text: 'Assessment has been deleted successfully.'
            );
            
            Log::info('Assessment deleted', [
                'assessment_id' => $assessmentId,
                'deleted_by' => Auth::id()
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete assessment', [
                'assessment_id' => $assessmentId,
                'error' => $e->getMessage()
            ]);
            $this->dispatch('swal:alert', 
                type: 'error',
                text: 'Assessment has been deleted successfully.'
            );
        }
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
        
        $assessments = Assessment::whereIn('course_id', $managedCourseIds)
            ->where('campus_id', $programHeadCampusId)
            ->whereHas('academicYear', function($query) {
                $query->where('is_active', true);
            })
            ->with('schedules')
            ->get();
            
        $dates = collect();
        foreach ($assessments as $assessment) {
            foreach ($assessment->schedules as $schedule) {
                if ($schedule->assessment_date) {
                    $dates->push([
                        'id' => $schedule->assessment_date->format('Y-m-d'),
                        'name' => $schedule->assessment_date->format('F j, Y'),
                        'value' => $schedule->assessment_date->format('Y-m-d')
                    ]);
                }
            }
        }
        
        return $dates->unique('id')->sortByDesc('id')->values();
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

        $query = Assessment::with([
                'course', 
                'campus', 
                'academicYear', 
                'qualificationType', 
                'examType', 
                'schedules.assessmentCenter', 
                'schedules.assessor', 
                'schedules.results.student.user'
            ])
            ->whereIn('course_id', $managedCourseIds)
            ->where('campus_id', $programHeadCampusId) // Only show assessments from program head's campus
            ->whereHas('academicYear', function($q) {
                $q->where('is_active', true); // Only show assessments from active academic year
            });                // Apply search filter
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
                ->orWhereHas('schedules.assessor', function ($subQ) {
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
            $query->whereHas('schedules', function($q) {
                $q->whereDate('assessment_date', $this->dateFilter);
            });
        }

        // Apply status filter
        if ($this->statusFilter) {
            $currentDate = now();
            if ($this->statusFilter === 'upcoming') {
                $query->whereHas('schedules', function($q) use ($currentDate) {
                    $q->where('assessment_date', '>', $currentDate);
                });
            } elseif ($this->statusFilter === 'completed') {
                $query->whereHas('schedules', function($q) use ($currentDate) {
                    $q->where('assessment_date', '<', $currentDate);
                });
            } elseif ($this->statusFilter === 'today') {
                $query->whereHas('schedules', function($q) use ($currentDate) {
                    $q->whereDate('assessment_date', $currentDate);
                });
            }
        }

        $assessments = $query->with('schedules')->orderBy('created_at', 'desc')->paginate(10);

        // Get stats for the current filtered results
        $allAssessments = Assessment::with('schedules')
            ->whereIn('course_id', $managedCourseIds)
            ->where('campus_id', $programHeadCampusId)
            ->whereHas('academicYear', function($q) {
                $q->where('is_active', true);
            })
            ->get();

        // Count upcoming, completed and today's assessments based on schedules
        $currentDate = now();
        $upcoming = 0;
        $completed = 0;
        $today = 0;

        foreach ($allAssessments as $assessment) {
            $latestSchedule = $assessment->schedules()->latest()->first();
            if ($latestSchedule) {
                if ($latestSchedule->assessment_date > $currentDate) {
                    $upcoming++;
                } elseif ($latestSchedule->assessment_date < $currentDate) {
                    $completed++;
                } elseif ($latestSchedule->assessment_date instanceof \Carbon\Carbon && $latestSchedule->assessment_date->isToday()) {
                    $today++;
                }
            }
        }

        $stats = [
            'total' => $allAssessments->count(),
            'upcoming' => $upcoming,
            'completed' => $completed,
            'today' => $today,
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