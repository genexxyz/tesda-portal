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
use App\Models\AssessmentSchedule;
use App\Models\Result;
use App\Models\ProgramHead;
use App\Models\ExamType;
use LivewireUI\Modal\ModalComponent;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
    
    public function updatedQualificationTypeId()
    {
        // Reload students when qualification type changes since it affects assessment filtering
        $this->loadAvailableStudents();
        $this->selectedStudents = []; // Reset selections as available students may have changed
    }
    
    public function updatedExamTypeId()
    {
        // Reload students when exam type changes since it affects assessment filtering
        $this->loadAvailableStudents();
        $this->selectedStudents = []; // Reset selections as available students may have changed
    }
    
    public function updatedAcademicId()
    {
        // Reload students when academic year changes since it affects assessment filtering
        $this->loadAvailableStudents();
        $this->selectedStudents = []; // Reset selections as available students may have changed
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
        
        // Get all students for this course, sorted by last name, excluding inactive/dropped users
        $allStudents = Student::with('user')
            ->where('course_id', $this->courseId)
            ->whereHas('user', function($query) {
                $query->orderBy('last_name', 'asc')
                      ->orderBy('first_name', 'asc');
            })
            ->get()
            ->sortBy([
                ['user.last_name', 'asc'],
                ['user.first_name', 'asc']
            ]);
        
        // Filter out students based on various criteria
        $availableStudents = $allStudents->filter(function ($student) {
            // First, check if student is already assigned to the SAME ASSESSMENT (any schedule)
            if ($this->courseId && $this->qualificationTypeId && $this->examTypeId && $this->academicId) {
                // Get campus_id - try from selected students first, fallback to current user's campus
                $campusId = $this->deriveCampusIdForFiltering($student);
                
                if ($campusId) {
                    // Check if there's an assessment with these core details
                    $existingAssessment = Assessment::where('course_id', $this->courseId)
                        ->where('qualification_type_id', $this->qualificationTypeId)
                        ->where('exam_type_id', $this->examTypeId)
                        ->where('academic_year_id', $this->academicId)
                        ->where('campus_id', $campusId)
                        ->first();
                        
                    if ($existingAssessment) {
                        // Check if student is already assigned to ANY schedule of this assessment
                        $isAlreadyInAssessment = Result::whereHas('assessmentSchedule', function($query) use ($existingAssessment) {
                            $query->where('assessment_id', $existingAssessment->id);
                        })->where('student_id', $student->id)->exists();
                        
                        if ($isAlreadyInAssessment) {
                            return false; // Student is already in this assessment, don't show them
                        }
                    }
                }
            }
            
            if (!$this->assessmentDate) {
                return true; // If no date selected yet, show remaining students
            }
            
            // Check if student is already assigned to ANY assessment on this specific date
            $hasAssessmentOnDate = Result::whereHas('assessmentSchedule', function($query) {
                $query->whereDate('assessment_date', $this->assessmentDate);
            })->where('student_id', $student->id)->exists();
            
            if ($hasAssessmentOnDate) {
                return false; // Already assigned to some assessment on this date
            }
            
            return true; // Available for assignment
        });
        
        $this->students = $availableStudents;
    }
    
    private function deriveCampusIdForFiltering($student = null)
    {
        // If we already have a campus ID set, use it
        if ($this->campusId) {
            return $this->campusId;
        }
        
        // If we have selected students, use the first student's campus
        if (!empty($this->selectedStudents)) {
            $firstStudent = Student::with('user')->find($this->selectedStudents[0]);
            if ($firstStudent && $firstStudent->user && $firstStudent->user->campus_id) {
                return $firstStudent->user->campus_id;
            }
        }
        
        // If a specific student is provided, use their campus
        if ($student && $student->user && $student->user->campus_id) {
            return $student->user->campus_id;
        }
        
        // Fallback to current user's campus
        $currentUser = Auth::user();
        if ($currentUser && $currentUser->campus_id) {
            return $currentUser->campus_id;
        }
        
        return null;
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
        $firstStudent = Student::with('user')->find($this->selectedStudents[0]);
        if ($firstStudent && $firstStudent->user && $firstStudent->user->campus_id) {
            $this->campusId = $firstStudent->user->campus_id;
            return;
        }
        
        // Fallback to current user's campus if student doesn't have one
        $currentUser = Auth::user();
        if ($currentUser && $currentUser->campus_id) {
            $this->campusId = $currentUser->campus_id;
        }
    }

    private function validateStep4()
    {
        $errors = [];
        
        // Check if assessment with core details already exists
        $existingAssessment = Assessment::where('course_id', $this->courseId)
            ->where('campus_id', $this->campusId)
            ->where('qualification_type_id', $this->qualificationTypeId)
            ->where('exam_type_id', $this->examTypeId)
            ->where('academic_year_id', $this->academicId)
            ->first();
            
        if ($existingAssessment) {
            // Check which students are already assigned to ANY schedule of this assessment
            $studentsInAssessment = [];
            $availableSlots = [];
            
            foreach ($this->selectedStudents as $studentId) {
                $existingInAssessment = Result::whereHas('assessmentSchedule', function($query) use ($existingAssessment) {
                    $query->where('assessment_id', $existingAssessment->id);
                })->where('student_id', $studentId)->first();
                
                if ($existingInAssessment) {
                    $student = Student::find($studentId);
                    $studentsInAssessment[] = $student->user->name;
                } else {
                    $availableSlots[] = $studentId;
                }
            }
            
            if (!empty($studentsInAssessment)) {
                if (!empty($availableSlots)) {
                    $errors['students_in_assessment'] = count($studentsInAssessment) . ' student(s) are already assigned to this assessment (in other schedules): ' . 
                        implode(', ', $studentsInAssessment) . '. Only ' . 
                        count($availableSlots) . ' student(s) can be added to a new schedule.';
                } else {
                    $errors['all_students_in_assessment'] = 'All selected students are already assigned to this assessment in existing schedules.';
                }
            }
            
            // If creating a new schedule, check for exact schedule match
            if (!empty($availableSlots)) {
                $exactScheduleMatch = $existingAssessment->schedules()
                    ->where('assessment_center_id', $this->assessmentCenterId)
                    ->where('assessor_id', $this->assessorId)
                    ->whereDate('assessment_date', $this->assessmentDate)
                    ->first();
                    
                if ($exactScheduleMatch) {
                    $errors['exact_schedule_exists'] = 'An identical schedule already exists for this assessment on ' . 
                        date('F j, Y', strtotime($this->assessmentDate)) . 
                        '. You can add the remaining ' . count($availableSlots) . ' student(s) to this existing schedule.';
                }
            }
        }
        
        // Check for students already assigned to OTHER assessments on the same date
        $conflictStudents = [];
        foreach ($this->selectedStudents as $studentId) {
            $conflictResult = Result::whereHas('assessmentSchedule', function($query) use ($existingAssessment) {
                $query->whereDate('assessment_date', $this->assessmentDate);
                
                // Exclude schedules from the current assessment if it exists
                if ($existingAssessment) {
                    $query->where('assessment_id', '!=', $existingAssessment->id);
                }
            })->where('student_id', $studentId)->first();
            
            if ($conflictResult) {
                $student = Student::find($studentId);
                $conflictStudents[] = $student->user->name;
            }
        }
        
        if (!empty($conflictStudents)) {
            $errors['date_conflict'] = 'The following students are already assigned to a DIFFERENT assessment on this date: ' . 
                implode(', ', $conflictStudents);
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
            
            // Find or create assessment (core details)
            $assessment = Assessment::where('course_id', $this->courseId)
                ->where('campus_id', $this->campusId)
                ->where('qualification_type_id', $this->qualificationTypeId)
                ->where('exam_type_id', $this->examTypeId)
                ->where('academic_year_id', $this->academicId)
                ->first();
                
            if (!$assessment) {
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
            
            // Find or create schedule for this assessment
            $schedule = $assessment->schedules()
                ->where('assessment_center_id', $this->assessmentCenterId)
                ->where('assessor_id', $this->assessorId)
                ->whereDate('assessment_date', $this->assessmentDate)
                ->first();
                
            if (!$schedule) {
                $schedule = $assessment->schedules()->create([
                    'assessment_center_id' => $this->assessmentCenterId,
                    'assessor_id' => $this->assessorId,
                    'assessment_date' => $this->assessmentDate
                ]);
            }
            
            // Add only new students to this schedule
            $newStudentsAdded = 0;
            $skippedStudents = [];
            
            foreach ($this->selectedStudents as $studentId) {
                // Check if student is already assigned to this exact schedule
                $existingResult = Result::where('assessment_schedule_id', $schedule->id)
                    ->where('student_id', $studentId)
                    ->first();
                    
                if (!$existingResult) {
                    Result::create([
                        'student_id' => $studentId,
                        'assessment_schedule_id' => $schedule->id,
                        'competency_type_id' => null,
                        'remarks' => null,
                        'created_by' => Auth::id()
                    ]);
                    $newStudentsAdded++;
                } else {
                    $student = Student::find($studentId);
                    $skippedStudents[] = $student->user->name;
                }
            }
            
            DB::commit();
            
            // Prepare success message
            $message = '';
            if ($newStudentsAdded > 0) {
                $message = "Successfully assigned {$newStudentsAdded} student(s) to the assessment schedule.";
            }
            
            if (!empty($skippedStudents)) {
                $message .= " " . count($skippedStudents) . " student(s) were already assigned: " . implode(', ', $skippedStudents);
            }
            
            $this->dispatch('swal:success', [
                'title' => 'Success!',
                'text' => $message,
            ]);
            
            $this->dispatch('assessment-assigned');
            $this->closeModal();
            
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Assessment assignment failed: ' . $e->getMessage(), [
                'course_id' => $this->courseId,
                'campus_id' => $this->campusId,
                'assessment_date' => $this->assessmentDate,
                'error' => $e->getMessage()
            ]);
            
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