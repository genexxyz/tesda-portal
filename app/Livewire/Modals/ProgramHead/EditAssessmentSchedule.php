<?php

namespace App\Livewire\Modals\ProgramHead;

use App\Models\AssessmentSchedule;
use App\Models\Student;
use App\Models\Result;
use App\Models\AssessmentCenter;
use App\Models\Assessor;
use LivewireUI\Modal\ModalComponent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class EditAssessmentSchedule extends ModalComponent
{
    public $schedule;
    public $assessmentCenterId;
    public $assessorId;
    public $assessmentDate;
    public $selectedStudents = [];
    public $currentStudents = [];
    public $availableStudents;
    public $assessmentCenters;
    public $assessors;
    public $validationErrors = [];

    public function mount($scheduleId)
    {
        $this->schedule = AssessmentSchedule::with([
            'assessment',
            'assessmentCenter',
            'assessor',
            'results' => function($query) {
                $query->join('students', 'results.student_id', '=', 'students.id')
                      ->join('users', 'students.user_id', '=', 'users.id')
                      ->where('users.status', 'active')
                      ->orderBy('users.last_name', 'asc')
                      ->orderBy('users.first_name', 'asc')
                      ->select('results.*');
            },
            'results.student.user'
        ])->findOrFail($scheduleId);

        // Load current values
        $this->assessmentCenterId = $this->schedule->assessment_center_id;
        $this->assessorId = $this->schedule->assessor_id;
        $this->assessmentDate = $this->schedule->assessment_date ? Carbon::parse($this->schedule->assessment_date)->format('Y-m-d') : null;

        // Get current students in this schedule
        $this->currentStudents = $this->schedule->results->pluck('student_id')->toArray();
        $this->selectedStudents = $this->currentStudents;

        $this->loadInitialData();
        $this->loadAvailableStudents();
    }

    public function loadInitialData()
    {
        $this->assessmentCenters = AssessmentCenter::all();
        $this->assessors = collect(); // Will be loaded when assessment center is selected
        
        // Load assessors for current assessment center
        if ($this->assessmentCenterId) {
            $this->updatedAssessmentCenterId();
        }
    }

    public function updatedAssessmentCenterId()
    {
        if ($this->assessmentCenterId) {
            $assessmentCenter = AssessmentCenter::with('assessors')->find($this->assessmentCenterId);
            if ($assessmentCenter) {
                $this->assessors = $assessmentCenter->assessors;
            }
        } else {
            $this->assessors = collect();
            $this->assessorId = null;
        }
    }

    public function updatedAssessmentDate()
    {
        $this->loadAvailableStudents();
    }

    public function loadAvailableStudents()
    {
        if (!$this->schedule || !$this->schedule->assessment) {
            $this->availableStudents = collect();
            return;
        }

        $assessment = $this->schedule->assessment;

        // Get all students for this course, sorted by last name, excluding inactive/dropped users
        $allStudents = Student::with('user')
            ->where('course_id', $assessment->course_id)
            ->whereHas('user', function($query) {
                $query->where('status', 'active');
            })
            ->get()
            ->sortBy([
                ['user.last_name', 'asc'],
                ['user.first_name', 'asc']
            ]);

        // Filter out students based on various criteria
        $this->availableStudents = $allStudents->filter(function ($student) use ($assessment) {
            // Include students already in this schedule
            if (in_array($student->id, $this->currentStudents)) {
                return true;
            }

            // Check if student is already assigned to ANY other schedule of this assessment
            $isInOtherSchedule = Result::whereHas('assessmentSchedule', function($query) use ($assessment) {
                $query->where('assessment_id', $assessment->id)
                      ->where('id', '!=', $this->schedule->id); // Exclude current schedule
            })->where('student_id', $student->id)->exists();

            if ($isInOtherSchedule) {
                return false; // Student is in another schedule of this assessment
            }

            // If assessment date is set, check for conflicts on the same date
            if ($this->assessmentDate) {
                $hasConflictOnDate = Result::whereHas('assessmentSchedule', function($query) {
                    $query->whereDate('assessment_date', $this->assessmentDate)
                          ->where('id', '!=', $this->schedule->id); // Exclude current schedule
                })->where('student_id', $student->id)->exists();

                if ($hasConflictOnDate) {
                    return false; // Student has another assessment on this date
                }
            }

            return true; // Available for assignment
        });
    }

    public function removeStudent($studentId)
    {
        $this->selectedStudents = array_filter($this->selectedStudents, function($id) use ($studentId) {
            return $id != $studentId;
        });
    }

    public function addStudent($studentId)
    {
        if (!in_array($studentId, $this->selectedStudents)) {
            $this->selectedStudents[] = $studentId;
        }
    }

    public function save()
    {
        $this->validateSchedule();

        if (!empty($this->validationErrors)) {
            return;
        }

        try {
            DB::beginTransaction();

            // Update schedule details
            $this->schedule->update([
                'assessment_center_id' => $this->assessmentCenterId,
                'assessor_id' => $this->assessorId,
                'assessment_date' => $this->assessmentDate
            ]);

            // Get students to remove and add
            $studentsToRemove = array_diff($this->currentStudents, $this->selectedStudents);
            $studentsToAdd = array_diff($this->selectedStudents, $this->currentStudents);

            // Remove students
            if (!empty($studentsToRemove)) {
                Result::where('assessment_schedule_id', $this->schedule->id)
                    ->whereIn('student_id', $studentsToRemove)
                    ->delete();
            }

            // Add new students
            foreach ($studentsToAdd as $studentId) {
                Result::create([
                    'student_id' => $studentId,
                    'assessment_schedule_id' => $this->schedule->id,
                    'competency_type_id' => null,
                    'remarks' => null,
                    'created_by' => Auth::id()
                ]);
            }

            DB::commit();

            $this->dispatch('swal:success', [
                'title' => 'Success!',
                'text' => 'Assessment schedule updated successfully.',
            ]);

            $this->dispatch('assessment-schedule-updated');
            $this->closeModal();

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Assessment schedule update failed: ' . $e->getMessage(), [
                'schedule_id' => $this->schedule->id,
                'error' => $e->getMessage()
            ]);

            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Failed to update assessment schedule: ' . $e->getMessage(),
            ]);
        }
    }

    private function validateSchedule()
    {
        $this->validationErrors = [];

        if (!$this->assessmentCenterId) {
            $this->validationErrors['assessmentCenterId'] = 'Please select an assessment center.';
        }

        if (!$this->assessorId) {
            $this->validationErrors['assessorId'] = 'Please select an assessor.';
        }

        if (!$this->assessmentDate) {
            $this->validationErrors['assessmentDate'] = 'Please select an assessment date.';
        } elseif (strtotime($this->assessmentDate) < strtotime('today')) {
            $this->validationErrors['assessmentDate'] = 'Assessment date cannot be in the past.';
        }

        if (empty($this->selectedStudents)) {
            $this->validationErrors['selectedStudents'] = 'Please select at least one student.';
        }

        // Check for duplicate schedule (excluding current schedule)
        if ($this->assessmentCenterId && $this->assessorId && $this->assessmentDate) {
            $duplicateSchedule = AssessmentSchedule::where('assessment_id', $this->schedule->assessment_id)
                ->where('assessment_center_id', $this->assessmentCenterId)
                ->where('assessor_id', $this->assessorId)
                ->whereDate('assessment_date', $this->assessmentDate)
                ->where('id', '!=', $this->schedule->id)
                ->first();

            if ($duplicateSchedule) {
                $this->validationErrors['duplicate'] = 'A schedule with the same details already exists for this assessment.';
            }
        }
    }

    public function render()
    {
        return view('livewire.modals.program-head.edit-assessment-schedule');
    }

    public static function modalMaxWidth(): string
    {
        return '5xl';
    }
}
