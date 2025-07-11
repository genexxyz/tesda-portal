<?php

namespace App\Livewire\Pages\ProgramHead;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;
use App\Models\Assessment;
use App\Models\AssessmentSchedule;
use App\Models\Result;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AssessmentDetails extends Component
{
    public $assessment;
    public $stats = [];
    #[Layout('layouts.app')]
    #[Title('Assessments Details')]

    public function mount($assessmentId)
    {
        $this->assessment = Assessment::with([
            'course',
            'campus',
            'academicYear',
            'qualificationType',
            'examType',
            'schedules' => function($query) {
                $query->orderBy('assessment_date', 'asc');
            },
            'schedules.assessmentCenter',
            'schedules.assessor',
            'schedules.results' => function($query) {
                $query->join('students', 'results.student_id', '=', 'students.id')
                      ->join('users', 'students.user_id', '=', 'users.id')
                      ->orderBy('users.last_name', 'asc')
                      ->orderBy('users.first_name', 'asc')
                      ->select('results.*');
            },
            'schedules.results.student.user',
            'schedules.results.competencyType'
        ])->findOrFail($assessmentId);

        $this->calculateStats();
    }

    private function calculateStats()
    {
        $totalStudents = 0;
        $completedStudents = 0;
        $competentStudents = 0;
        $notYetCompetentStudents = 0;
        $absentStudents = 0;
        $droppedStudents = 0;

        foreach ($this->assessment->schedules as $schedule) {
            foreach ($schedule->results as $result) {
                // Only count students who have been assessed (not dropped, not null)
                if ($result->competency_type_id && ($result->competencyType?->name ?? '') !== 'Dropped') {
                    $totalStudents++;
                    $completedStudents++;
                    
                    switch ($result->competencyType?->name) {
                        case 'Competent':
                            $competentStudents++;
                            break;
                        case 'Not Yet Competent':
                            $notYetCompetentStudents++;
                            break;
                        case 'Absent':
                            $absentStudents++;
                            break;
                    }
                } elseif ($result->competency_type_id && ($result->competencyType?->name ?? '') === 'Dropped') {
                    // Count dropped students separately but don't include in totals
                    $droppedStudents++;
                }
                // Students with null competency_type_id are not counted at all
            }
        }

        $this->stats = [
            'total_students' => $totalStudents,
            'completed_students' => $completedStudents,
            'pending_students' => 0, // No pending since we only count assessed students
            'competent_students' => $competentStudents,
            'not_yet_competent_students' => $notYetCompetentStudents,
            'absent_students' => $absentStudents,
            'dropped_students' => $droppedStudents,
            'completion_percentage' => $totalStudents > 0 ? round(($completedStudents / $totalStudents) * 100, 1) : 0,
            'passing_percentage' => ($competentStudents + $notYetCompetentStudents) > 0 ? 
                round(($competentStudents / ($competentStudents + $notYetCompetentStudents)) * 100, 1) : 0,
            'total_schedules' => $this->assessment->schedules->count()
        ];
    }

    #[On('assessment-schedule-updated')]
    public function refreshData()
    {
        // Reload the assessment with fresh data
        $this->assessment = Assessment::with([
            'course',
            'campus',
            'academicYear',
            'qualificationType',
            'examType',
            'schedules.assessmentCenter',
            'schedules.assessor',
            'schedules.results.student.user',
            'schedules.results.competencyType'
        ])->findOrFail($this->assessment->id);

        $this->calculateStats();
    }

    public function deleteSchedule($scheduleId)
    {
        try {
            DB::beginTransaction();

            $schedule = AssessmentSchedule::findOrFail($scheduleId);
            
            // Check if the schedule is in the future
            if ($schedule->assessment_date && $schedule->assessment_date < now()) {
                session()->flash('error', 'Cannot delete past assessment schedules.');
                return;
            }

            // Delete all results related to this schedule
            Result::where('assessment_schedule_id', $scheduleId)->delete();
            
            // Delete the schedule
            $schedule->delete();

            DB::commit();

            // Refresh the data
            $this->refreshData();

            

            $this->dispatch('swal:alert', 
                type: 'success',
                text: 'Assessment schedule and all related results have been deleted successfully.'
            );
            
            Log::info('Assessment schedule deleted', [
                'schedule_id' => $scheduleId,
                'assessment_id' => $this->assessment->id,
                'deleted_by' => Auth::id()
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete assessment schedule', [
                'schedule_id' => $scheduleId,
                'error' => $e->getMessage()
            ]);
            session()->flash('error', 'Failed to delete assessment schedule. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.pages.program-head.assessment-details');
    }
}
