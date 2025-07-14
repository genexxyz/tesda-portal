<div>
    <x-modals.modal-header 
        title="Edit Assessment Schedule"
        subtitle="Modify schedule details and manage students" />

    <x-modals.modal-body>
        <div class="space-y-6">
            <!-- Validation Errors -->
            @if(!empty($validationErrors))
                <div class="p-4 bg-red-50 border border-red-200 rounded-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <x-icon name="exclamation-circle" style="fas" class="h-5 w-5 text-red-400" />
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Please fix the following issues:</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach($validationErrors as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Assessment Info Display -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h4 class="text-sm font-medium text-blue-900 mb-3">Assessment Information</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
                    <div>
                        <span class="text-blue-700 font-medium">Course:</span>
                        <div class="text-blue-800">{{ $schedule->assessment->course->name }}</div>
                        <div class="text-blue-600 text-xs">{{ $schedule->assessment->course->code }}</div>
                    </div>
                    <div>
                        <span class="text-blue-700 font-medium">Qualification:</span>
                        <div class="text-blue-800">{{ $schedule->assessment->qualificationType->name }}</div>
                        <div class="text-blue-600 text-xs">{{ $schedule->assessment->qualificationType->code }}</div>
                    </div>
                    <div>
                        <span class="text-blue-700 font-medium">Exam Type:</span>
                        <div class="text-blue-800">{{ $schedule->assessment->examType->type }}</div>
                    </div>
                    <div>
                        <span class="text-blue-700 font-medium">Academic Year:</span>
                        <div class="text-blue-800">{{ $schedule->assessment->academicYear->formatted_description }}</div>
                    </div>
                </div>
            </div>

            <!-- Schedule Details Form -->
            <div class="space-y-4">
                <h3 class="text-lg font-medium text-gray-900">Schedule Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Assessment Center -->
                    <x-inputs.select-input 
                        id="assessmentCenterId"
                        wire:model.live="assessmentCenterId" 
                        label="Assessment Center"
                        placeholder="Select Assessment Center"
                        :options="$assessmentCenters"
                        value-field="id"
                        text-field="name"
                        required />

                    <!-- Assessor -->
                    <div>
                        <x-inputs.select-input 
                            id="assessorId"
                            wire:model="assessorId" 
                            label="Assessor"
                            placeholder="{{ $assessors->isEmpty() ? 'Select Assessment Center first' : 'Select Assessor' }}"
                            :options="$assessors"
                            value-field="id"
                            text-field="name"
                            :disabled="$assessors->isEmpty()"
                            required />
                        @if($assessors->isEmpty() && $assessmentCenterId)
                            <p class="mt-1 text-xs text-yellow-600">
                                No assessors available for the selected assessment center.
                            </p>
                        @endif
                    </div>

                    <!-- Assessment Date -->
                    <x-inputs.text-input 
                        id="assessmentDate"
                        wire:model.live="assessmentDate"
                        label="Assessment Date"
                        type="date"
                        min="{{ date('Y-m-d') }}"
                        required />
                </div>
            </div>

            <!-- Student Management -->
            <div class="space-y-4">
                <h3 class="text-lg font-medium text-gray-900">Manage Students</h3>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Selected Students -->
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-sm font-medium text-gray-700">
                                Selected Students
                            </h4>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ count($selectedStudents) }} selected
                            </span>
                        </div>
                        
                        <div class="border border-gray-300 rounded-lg bg-white max-h-64 overflow-y-auto">
                            @if(empty($selectedStudents))
                                <div class="text-center py-8">
                                    <x-icon name="users" style="fas" class="h-8 w-8 text-gray-400 mx-auto mb-2" />
                                    <p class="text-sm text-gray-500">No students selected</p>
                                </div>
                            @else
                                <div class="p-3 space-y-2">
                                    @foreach($selectedStudents as $studentId)
                                        @php
                                            $student = $availableStudents->firstWhere('id', $studentId);
                                        @endphp
                                        @if($student)
                                            <div class="flex items-center justify-between p-3 bg-blue-50 rounded border border-blue-200 hover:bg-blue-100 transition-colors">
                                                <div class="flex items-center space-x-3">
                                                    <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                                        <span class="text-sm font-medium text-blue-600">
                                                            {{ substr($student->user->first_name, 0, 1) }}{{ substr($student->user->last_name, 0, 1) }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900">{{ $student->user->name }}</p>
                                                        <p class="text-xs text-gray-500">{{ $student->student_id }}</p>
                                                    </div>
                                                </div>
                                                <button wire:click="removeStudent({{ $studentId }})" 
                                                        class="p-1 text-red-600 hover:text-red-800 hover:bg-red-100 rounded transition-colors">
                                                    <x-icon name="times" style="fas" class="w-4 h-4" />
                                                </button>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Available Students -->
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-sm font-medium text-gray-700">
                                Available Students
                            </h4>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $availableStudents->whereNotIn('id', $selectedStudents)->count() }} available
                            </span>
                        </div>
                        
                        <div class="border border-gray-300 rounded-lg bg-white max-h-64 overflow-y-auto">
                            @php
                                $unselectedStudents = $availableStudents->whereNotIn('id', $selectedStudents);
                            @endphp
                            
                            @if($unselectedStudents->isEmpty())
                                <div class="text-center py-8">
                                    <x-icon name="user-check" style="fas" class="h-8 w-8 text-gray-400 mx-auto mb-2" />
                                    <p class="text-sm text-gray-500">No additional students available</p>
                                </div>
                            @else
                                <div class="p-3 space-y-2">
                                    @foreach($unselectedStudents as $student)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded border border-gray-200 hover:bg-gray-100 transition-colors">
                                            <div class="flex items-center space-x-3">
                                                <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-gray-600">
                                                        {{ substr($student->user->first_name, 0, 1) }}{{ substr($student->user->last_name, 0, 1) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900">{{ $student->user->name }}</p>
                                                    <p class="text-xs text-gray-500">{{ $student->student_id }}</p>
                                                </div>
                                            </div>
                                            <button wire:click="addStudent({{ $student->id }})" 
                                                    class="p-1 text-blue-600 hover:text-blue-800 hover:bg-blue-100 rounded transition-colors">
                                                <x-icon name="plus" style="fas" class="w-4 h-4" />
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-modals.modal-body>

    <x-modals.modal-footer>
        <div class="flex justify-between w-full">
            <x-buttons.secondary-button wire:click="closeModal">
                Cancel
            </x-buttons.secondary-button>
            
            <button wire:click="save"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition ease-in-out duration-150">
                <x-icon name="save" style="fas" class="w-4 h-4 mr-2" />
                Update Schedule
            </button>
        </div>
    </x-modals.modal-footer>
</div>
