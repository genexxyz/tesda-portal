<div>
    <x-modals.modal-header 
        title="Assign Assessment"
        subtitle="Step {{ $currentStep }} of {{ $totalSteps }}: {{ $this->getStepTitle() }}" />

    <x-modals.modal-body>
        <!-- Progress Bar -->
        <div class="mb-6">
            <div class="flex items-center justify-between mb-2">
                @for($i = 1; $i <= $totalSteps; $i++)
                    <div class="flex items-center {{ $i < $totalSteps ? 'flex-1' : '' }}">
                        <div class="flex items-center justify-center w-8 h-8 rounded-full border-2 {{ $i <= $currentStep ? 'bg-blue-600 border-blue-600 text-white' : 'border-gray-300 text-gray-400' }}">
                            {{ $i }}
                        </div>
                        @if($i < $totalSteps)
                            <div class="flex-1 h-0.5 mx-2 {{ $i < $currentStep ? 'bg-blue-600' : 'bg-gray-300' }}"></div>
                        @endif
                    </div>
                @endfor
            </div>
            <div class="flex justify-between text-xs text-gray-500">
                <span>Details</span>
                <span>Schedule</span>
                <span>Students</span>
                <span>Review</span>
            </div>
        </div>

        <!-- Display validation errors and notices -->
        @if(!empty($validationErrors))
            <div class="mb-4 space-y-3">
                @if(isset($validationErrors['partial_duplicate']))
                    <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <x-icon name="exclamation-triangle" class="h-5 w-5 text-yellow-400" />
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">
                                    Existing Assessment Schedule Found
                                </h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    {{ $validationErrors['partial_duplicate'] }}
                                </div>
                                <div class="mt-3">
                                    <button type="button" wire:click="save" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-yellow-700 bg-yellow-100 hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                        Add Students to Existing Schedule
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if(array_diff(array_keys($validationErrors), ['partial_duplicate']))
                    <div class="p-4 bg-red-50 border border-red-200 rounded-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <x-icon name="exclamation-triangle" class="h-5 w-5 text-red-400" />
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">
                                    Please fix the following errors:
                                </h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        @foreach($validationErrors as $key => $error)
                                            @if($key !== 'partial_duplicate')
                                                <li>{{ $error }}</li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- Step Content -->
        <div class="space-y-6">
            @if($currentStep === 1)
                <!-- Step 1: Assessment Details -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Assessment Details</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Exam Type -->
                        <div>
                            <label for="examTypeId" class="block text-sm font-medium text-gray-700 mb-1">
                                Exam Type <span class="text-red-500">*</span>
                            </label>
                            <select 
                                id="examTypeId"
                                wire:model="examTypeId"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Exam Type</option>
                                @foreach($examTypes as $examType)
                                    <option value="{{ $examType->id }}">{{ $examType->type }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Course -->
                        <div>
                            <label for="courseId" class="block text-sm font-medium text-gray-700 mb-1">
                                Course <span class="text-red-500">*</span>
                            </label>
                            <select 
                                id="courseId"
                                wire:model.live="courseId"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Course</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->code }} - {{ $course->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Qualification -->
                        <div>
                            <label for="qualificationTypeId" class="block text-sm font-medium text-gray-700 mb-1">
                                Qualification <span class="text-red-500">*</span>
                            </label>
                            <select 
                                id="qualificationTypeId"
                                wire:model="qualificationTypeId"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Qualification</option>
                                @foreach($qualificationTypes as $qualificationType)
                                    <option value="{{ $qualificationType->id }}">{{ $qualificationType->code }} - {{ $qualificationType->level }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Academic Year -->
                        <div>
                            <label for="academicId" class="block text-sm font-medium text-gray-700 mb-1">
                                Academic Year <span class="text-red-500">*</span>
                            </label>
                            <select 
                                id="academicId"
                                wire:model="academicId"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Academic Year</option>
                                @foreach($academics as $academic)
                                    <option value="{{ $academic->id }}">{{ $academic->formatted_description }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            @endif

            @if($currentStep === 2)
                <!-- Step 2: Assessment Center & Schedule -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Assessment Center & Schedule</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Assessment Center -->
                        <div>
                            <label for="assessmentCenterId" class="block text-sm font-medium text-gray-700 mb-1">
                                Assessment Center <span class="text-red-500">*</span>
                            </label>
                            <select 
                                id="assessmentCenterId"
                                wire:model.live="assessmentCenterId"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Assessment Center</option>
                                @foreach($assessmentCenters as $center)
                                    <option value="{{ $center->id }}">{{ $center->name }}</option>
                                @endforeach
                            </select>
                            @if($assessmentCenterId)
                                <p class="mt-1 text-xs text-gray-500">
                                    Available assessors will be filtered based on this center.
                                </p>
                            @endif
                        </div>

                        <!-- Assessor -->
                        <div>
                            <label for="assessorId" class="block text-sm font-medium text-gray-700 mb-1">
                                Assessor <span class="text-red-500">*</span>
                            </label>
                            <select 
                                id="assessorId"
                                wire:model="assessorId"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                {{ !$assessmentCenterId ? 'disabled' : '' }}>
                                <option value="">Select Assessor</option>
                                @foreach($assessors as $assessor)
                                    <option value="{{ $assessor->id }}">{{ $assessor->name }}</option>
                                @endforeach
                            </select>
                            @if(!$assessmentCenterId)
                                <p class="mt-1 text-xs text-red-500">
                                    Please select an assessment center first.
                                </p>
                            @elseif($assessors->isEmpty())
                                <p class="mt-1 text-xs text-yellow-600">
                                    No assessors available for the selected assessment center.
                                </p>
                            @endif
                        </div>

                        <!-- Assessment Date -->
                        <div class="md:col-span-2">
                            <label for="assessmentDate" class="block text-sm font-medium text-gray-700 mb-1">
                                Assessment Date <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="date"
                                id="assessmentDate"
                                wire:model.live="assessmentDate"
                                min="{{ date('Y-m-d') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>
            @endif

            @if($currentStep === 3)
                <!-- Step 3: Student Selection -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Select Students</h3>
                    
                    @if($students->isEmpty())
                        <div class="text-center py-8">
                            <x-icon name="users" class="h-12 w-12 text-gray-400 mx-auto mb-4" />
                            <h3 class="text-sm font-medium text-gray-900 mb-2">No students available</h3>
                            <p class="text-sm text-gray-500">
                                @if(!$courseId)
                                    Please select a course first.
                                @elseif(!$assessmentDate)
                                    Please select an assessment date first.
                                @else
                                    No students are available for this course on the selected date.
                                @endif
                            </p>
                        </div>
                    @else
                        <div class="space-y-2 max-h-60 overflow-y-auto border border-gray-200 rounded-md p-3">
                            @foreach($students as $student)
                                <div class="flex items-center">
                                    <input 
                                        type="checkbox"
                                        id="student_{{ $student->id }}"
                                        wire:model="selectedStudents"
                                        value="{{ $student->id }}"
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="student_{{ $student->id }}" class="ml-3 flex-1">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <div class="text-sm font-medium text-gray-700">{{ $student->full_name }}</div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $student->student_id }} • {{ $student->course->code ?? 'No Course' }}
                                                    
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        
                        <p class="mt-2 text-sm text-gray-500">
                            {{ count($selectedStudents) }} of {{ $students->count() }} student{{ $students->count() !== 1 ? 's' : '' }} selected
                        </p>
                    @endif
                </div>
            @endif

            @if($currentStep === 4)
                <!-- Step 4: Review & Confirm -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Review & Confirm</h3>
                    
                    <div class="space-y-6">
                        <!-- Assessment Details -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Assessment Details</h4>
                            <div class="bg-gray-50 rounded-md p-3">
                                <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-2 text-sm">
                                    <div>
                                        <dt class="font-medium text-gray-500">Exam Type:</dt>
                                        <dd class="text-gray-900">{{ $examTypes->firstWhere('id', $examTypeId)->type ?? 'Not selected' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="font-medium text-gray-500">Course:</dt>
                                        <dd class="text-gray-900">{{ $courses->firstWhere('id', $courseId)->name ?? 'Not selected' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="font-medium text-gray-500">Qualification Type:</dt>
                                        <dd class="text-gray-900">
                                            @php
                                                $qualType = $qualificationTypes->firstWhere('id', $qualificationTypeId);
                                            @endphp
                                            {{ $qualType ? $qualType->code . ' - ' . $qualType->level : 'Not selected' }}
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="font-medium text-gray-500">Academic Year:</dt>
                                        <dd class="text-gray-900">{{ $academics->firstWhere('id', $academicId)->formatted_description ?? 'Not selected' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="font-medium text-gray-500">Assessment Center:</dt>
                                        <dd class="text-gray-900">{{ $assessmentCenters->firstWhere('id', $assessmentCenterId)->name ?? 'Not selected' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="font-medium text-gray-500">Assessor:</dt>
                                        <dd class="text-gray-900">{{ $assessors->firstWhere('id', $assessorId)->name ?? 'Not selected' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="font-medium text-gray-500">Assessment Date:</dt>
                                        <dd class="text-gray-900">{{ $assessmentDate ? date('F j, Y', strtotime($assessmentDate)) : 'Not selected' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="font-medium text-gray-500">Campus:</dt>
                                        <dd class="text-gray-900">
                                            @if($campusId)
                                                @php
                                                    $campus = $campuses->firstWhere('id', $campusId);
                                                @endphp
                                                {{ $campus ? $campus->name : 'Derived from students' }}
                                            @else
                                                Derived from students
                                            @endif
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                        </div>

                        <!-- Selected Students -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Selected Students ({{ count($selectedStudents) }})</h4>
                            <div class="bg-gray-50 rounded-md p-3">
                                <div class="flex flex-wrap gap-2">
                                    @foreach($selectedStudents as $studentId)
                                        @php
                                            $student = $students->firstWhere('id', $studentId);
                                        @endphp
                                        @if($student)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $student->user->name }}
                                            </span>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </x-modals.modal-body>

    <x-modals.modal-footer>
        <div class="flex justify-between w-full">
            <div>
                @if($currentStep > 1)
                    <x-buttons.secondary-button wire:click="previousStep">
                        Previous
                    </x-buttons.secondary-button>
                @endif
            </div>
            
            <div class="flex space-x-2">
                <x-buttons.secondary-button wire:click="closeModal">
                    Cancel
                </x-buttons.secondary-button>
                
                @if($currentStep < $totalSteps)
                    <x-buttons.primary-button wire:click="nextStep">
                        Next
                    </x-buttons.primary-button>
                @else
                    <x-buttons.primary-button 
                        wire:click="save"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove>Assign Assessment</span>
                        <span wire:loading>Processing...</span>
                    </x-buttons.primary-button>
                @endif
            </div>
        </div>
    </x-modals.modal-footer>
</div>