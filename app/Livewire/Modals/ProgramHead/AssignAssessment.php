<?php

namespace App\Livewire\Modals\ProgramHead;

use App\Models\Student;
use App\Models\Course;
use App\Models\Campus;
use App\Models\QualificationType;
use App\Models\Academic;
use App\Models\AssessmentCenter;
use App\Models\Assessor;
use App\Models\Assessment;
use App\Models\Result;
use App\Models\ProgramHead;
use App\Models\ExamType;
use LivewireUI\Modal\ModalComponent;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AssignAssessment extends ModalComponent
{
    public $currentStep = 1;
    public $totalSteps = 4;
    
    // Step 1: Assessment Details
    public $examTypeId = null;
    public $courseId = null;
    public $qualificationTypeId = null;
    public $academicId = null;
    
    // Step 2: Assessment Center & Schedule
    public $assessmentCenterId = null;
    public $assessorId = null;
    public $assessmentDate = null;
    
    // Step 3: Student Selection
    public $selectedStudents = [];
    public $studentId = null; // For single student assignment
    public $isMultipleStudents = false;
    public $campusId = null; // Derived from selected students
    
    // Collections
    public $students;
    public $courses;
    public $campuses;
    public $qualificationTypes;
    public $academics;
    public $examTypes;
    public $assessmentCenters;
    public $assessors;
    
    // Validation errors
    public $validationErrors = [];

    public function mount($studentId = null)
    {
        $this->studentId = $studentId;
        $this->isMultipleStudents = !$studentId;
        
        // Load initial data
        $this->loadInitialData();
    }

    public function loadInitialData()
    {
        // Get current program head's courses
        $programHead = Auth::user()->programHead;
        $courseIds = $programHead ? ProgramHead::where('user_id', Auth::id())->pluck('course_id') : [];
        
        // Load courses managed by this program head
        $this->courses = Course::whereIn('id', $courseIds)->get();
        
        // Initialize other collections
        $this->students = collect(); // Will be loaded when course is selected
        $this->campuses = collect();
        $this->qualificationTypes = collect();
        $this->academics = Academic::all();
        $this->examTypes = ExamType::all();
        $this->assessmentCenters = AssessmentCenter::all();
        $this->assessors = collect(); // Will be loaded when assessment center is selected
        
        // If single student, pre-select them for step 3
        if ($this->studentId) {
            $this->selectedStudents = [$this->studentId];
        }
    }

    public function updatedCourseId()
    {
        if ($this->courseId) {
            $course = Course::with('campuses', 'qualificationTypes')->find($this->courseId);
            if ($course) {
                $this->campuses = $course->campuses;
                $this->qualificationTypes = $course->qualificationTypes;
                
                // Load students for this course that are not already assigned to any assessment
                $this->loadAvailableStudents();
            }
        } else {
            $this->campuses = collect();
            $this->qualificationTypes = collect();
            $this->students = collect();
        }
        
        // Reset dependent fields
        $this->qualificationTypeId = null;
        $this->selectedStudents = [];
    }
    
    public function updatedAssessmentCenterId()
    {
        if ($this->assessmentCenterId) {
            // Load assessors that are associated with this assessment center
            $assessmentCenter = AssessmentCenter::with('assessors')->find($this->assessmentCenterId);
            if ($assessmentCenter) {
                $this->assessors = $assessmentCenter->assessors;
            }
        } else {
            $this->assessors = collect();
        }
        
        // Reset assessor selection when assessment center changes
        $this->assessorId = null;
    }
    
    public function updatedAssessorId()
    {
        if ($this->assessorId && !$this->assessmentCenterId) {
            // If assessor is selected but no assessment center, load available centers for this assessor
            $assessor = Assessor::with('assessmentCenters')->find($this->assessorId);
            if ($assessor) {
                $this->assessmentCenters = $assessor->assessmentCenters;
            }
        }
    }
    
    public function loadAvailableStudents()
    {
        if (!$this->courseId) {
            $this->students = collect();
            return;
        }
        
        // Get all students for this course
        $allStudents = Student::where('course_id', $this->courseId)->get()->sortBy('user.first_name');
        
        // Filter out students already assigned to assessments on the selected date
        $availableStudents = $allStudents->filter(function ($student) {
            if (!$this->assessmentDate) {
                return true; // If no date selected yet, show all students
            }
            
            // Check if student is already assigned to an assessment on this date
            $hasAssessment = Result::whereHas('assessmentSchedule', function($query) {
                $query->whereDate('assessment_date', $this->assessmentDate);
            })->where('student_id', $student->id)->exists();
            
            return !$hasAssessment;
        });
        
        $this->students = $availableStudents;
    }
    
    public function updatedAssessmentDate()
    {
        // Reload available students when assessment date changes
        $this->loadAvailableStudents();
        
        // Reset selected students if any are no longer available
        if ($this->selectedStudents) {
            $availableStudentIds = $this->students->pluck('id')->toArray();
            $this->selectedStudents = array_intersect($this->selectedStudents, $availableStudentIds);
        }
    }

    public function nextStep()
    {
        if ($this->validateCurrentStep()) {
            $this->currentStep++;
            
            // Load students when moving to step 3
            if ($this->currentStep === 3) {
                $this->loadAvailableStudents();
            }
        }
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function validateCurrentStep()
    {
        $this->validationErrors = [];
        
        switch ($this->currentStep) {
            case 1:
                return $this->validateStep1();
            case 2:
                return $this->validateStep2();
            case 3:
                return $this->validateStep3();
            case 4:
                return $this->validateStep4();
        }
        
        return true;
    }

    private function validateStep1()
    {
        $errors = [];
        
        if (!$this->examTypeId) {
            $errors['examTypeId'] = 'Please select an exam type.';
        }
        
        if (!$this->courseId) {
            $errors['courseId'] = 'Please select a course.';
        }
        
        if (!$this->qualificationTypeId) {
            $errors['qualificationTypeId'] = 'Please select a qualification.';
        }
        
        if (!$this->academicId) {
            $errors['academicId'] = 'Please select an academic year.';
        }
        
        $this->validationErrors = $errors;
        return empty($errors);
    }

    private function validateStep2()
    {
        $errors = [];
        
        if (!$this->assessmentCenterId) {
            $errors['assessmentCenterId'] = 'Please select an assessment center.';
        }
        
        if (!$this->assessorId) {
            $errors['assessorId'] = 'Please select an assessor.';
        } elseif ($this->assessmentCenterId) {
            // Validate that the selected assessor is associated with the selected assessment center
            $assessmentCenter = AssessmentCenter::with('assessors')->find($this->assessmentCenterId);
            if ($assessmentCenter && !$assessmentCenter->assessors->contains('id', $this->assessorId)) {
                $errors['assessorId'] = 'Selected assessor is not associated with the selected assessment center.';
            }
        }
        
        if (!$this->assessmentDate) {
            $errors['assessmentDate'] = 'Please select an assessment date.';
        } elseif (strtotime($this->assessmentDate) < strtotime('today')) {
            $errors['assessmentDate'] = 'Assessment date cannot be in the past.';
        }
        
        $this->validationErrors = $errors;
        return empty($errors);
    }

    private function validateStep3()
    {
        if (empty($this->selectedStudents)) {
            $this->validationErrors['selectedStudents'] = 'Please select at least one student.';
            return false;
        }
        
        // Derive campus from selected students
        $this->deriveCampusFromStudents();
        
        return true;
    }
    
    private function deriveCampusFromStudents()
    {
        if (empty($this->selectedStudents)) {
            $this->campusId = null;
            return;
        }
        
        // Get the campus from the first selected student
        $firstStudent = Student::find($this->selectedStudents[0]);
        if ($firstStudent && $firstStudent->user) {
            $this->campusId = $firstStudent->user->campus_id;
        }
    }

    private function validateStep4()
    {
        $errors = [];
        
        // Check for duplicate assessments on the same day
        $existingAssessment = Assessment::where('course_id', $this->courseId)
            ->where('campus_id', $this->campusId)
            ->where('qualification_type_id', $this->qualificationTypeId)
            ->whereHas('schedules', function($query) {
                $query->where('assessment_center_id', $this->assessmentCenterId)
                      ->where('assessor_id', $this->assessorId)
                      ->whereDate('assessment_date', $this->assessmentDate);
            })
            ->first();
            
        if ($existingAssessment) {
            $errors['duplicate_assessment'] = 'An assessment with the same details already exists on this date.';
        }
        
        // Check for students already assigned to assessments on the same date
        $duplicateStudents = [];
        foreach ($this->selectedStudents as $studentId) {
            $existingResult = Result::whereHas('assessmentSchedule', function($query) {
                $query->whereDate('assessment_date', $this->assessmentDate);
            })->where('student_id', $studentId)->first();
            
            if ($existingResult) {
                $student = Student::find($studentId);
                $duplicateStudents[] = $student->user->name;
            }
        }
        
        if (!empty($duplicateStudents)) {
            $errors['duplicate_students'] = 'The following students are already assigned to an assessment on this date: ' . implode(', ', $duplicateStudents);
        }
        
        // Check for students already assigned to the same assessment schedule
        if ($existingAssessment) {
            $alreadyAssignedStudents = [];
            
            // Get the existing schedule
            $existingSchedule = $existingAssessment->schedules()
                ->where('assessment_center_id', $this->assessmentCenterId)
                ->where('assessor_id', $this->assessorId)
                ->whereDate('assessment_date', $this->assessmentDate)
                ->first();
                
            if ($existingSchedule) {
                foreach ($this->selectedStudents as $studentId) {
                    $existingResult = Result::where('assessment_schedule_id', $existingSchedule->id)
                        ->where('student_id', $studentId)
                        ->first();
                        
                    if ($existingResult) {
                        $student = Student::find($studentId);
                        $alreadyAssignedStudents[] = $student->user->name;
                    }
                }
                
                if (!empty($alreadyAssignedStudents)) {
                    $errors['already_assigned'] = 'The following students are already assigned to this assessment: ' . implode(', ', $alreadyAssignedStudents);
                }
            }
        }
        
        $this->validationErrors = $errors;
        return empty($errors);
    }

    public function save()
    {
        if (!$this->validateCurrentStep()) {
            return;
        }
        
        try {
            DB::beginTransaction();
            
            // Check if assessment already exists (based on core details)
            $assessment = Assessment::where('course_id', $this->courseId)
                ->where('campus_id', $this->campusId)
                ->where('qualification_type_id', $this->qualificationTypeId)
                ->where('exam_type_id', $this->examTypeId)
                ->whereHas('schedules', function($query) {
                    $query->where('assessment_center_id', $this->assessmentCenterId)
                          ->where('assessor_id', $this->assessorId)
                          ->whereDate('assessment_date', $this->assessmentDate);
                })
                ->first();
                
            // Get or create assessment schedule
            $schedule = null;
            
            // Create new assessment if it doesn't exist
            if (!$assessment) {
                // First check if an assessment with these core details exists
                $assessment = Assessment::where('course_id', $this->courseId)
                    ->where('campus_id', $this->campusId)
                    ->where('qualification_type_id', $this->qualificationTypeId)
                    ->where('exam_type_id', $this->examTypeId)
                    ->where('academic_year_id', $this->academicId)
                    ->first();
                
                if (!$assessment) {
                    // Create new assessment if no matching core assessment exists
                    $assessment = Assessment::create([
                        'exam_type_id' => $this->examTypeId,
                        'course_id' => $this->courseId,
                        'campus_id' => $this->campusId,
                        'qualification_type_id' => $this->qualificationTypeId,
                        'academic_year_id' => $this->academicId,
                        'created_by' => Auth::id(),
                        'status' => 'scheduled'
                    ]);
                }
                
                // Create new schedule
                $schedule = $assessment->schedules()->create([
                    'assessment_center_id' => $this->assessmentCenterId,
                    'assessor_id' => $this->assessorId,
                    'assessment_date' => $this->assessmentDate
                ]);
            } else {
                // Get the existing schedule for this assessment
                $schedule = $assessment->schedules()
                    ->where('assessment_center_id', $this->assessmentCenterId)
                    ->where('assessor_id', $this->assessorId)
                    ->whereDate('assessment_date', $this->assessmentDate)
                    ->first();
                
                // Create a new schedule if none exists
                if (!$schedule) {
                    $schedule = $assessment->schedules()->create([
                        'assessment_center_id' => $this->assessmentCenterId,
                        'assessor_id' => $this->assessorId,
                        'assessment_date' => $this->assessmentDate
                    ]);
                }
            }
            
            // Create results for each selected student (without competency_type_id)
            foreach ($this->selectedStudents as $studentId) {
                // Double-check that student isn't already assigned to this schedule
                $existingResult = Result::where('assessment_schedule_id', $schedule->id)
                    ->where('student_id', $studentId)
                    ->first();
                    
                if (!$existingResult) {
                    Result::create([
                        'student_id' => $studentId,
                        'assessment_schedule_id' => $schedule->id,
                        'competency_type_id' => null, // Will be determined after assessment
                        'remarks' => null,
                        'created_by' => Auth::id()
                    ]);
                }
            }
            
            DB::commit();
            
            $this->dispatch('swal:success', [
                'title' => 'Success!',
                'text' => 'Assessment assigned successfully to ' . count($this->selectedStudents) . ' student(s).',
            ]);
            
            $this->dispatch('assessment-assigned');
            $this->closeModal();
            
        } catch (\Exception $e) {
            DB::rollback();
            
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Failed to assign assessment: ' . $e->getMessage(),
            ]);
        }
    }

    public function getStepTitle()
    {
        return match($this->currentStep) {
            1 => 'Assessment Details',
            2 => 'Assessment Center & Schedule',
            3 => 'Select Students',
            4 => 'Review & Confirm',
            default => 'Unknown Step'
        };
    }

    public function render()
    {
        return view('livewire.modals.program-head.assign-assessment');
    }

    public static function modalMaxWidth(): string
    {
        return '4xl';
    }
}