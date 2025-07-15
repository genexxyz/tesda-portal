<div>
    <x-modals.modal-header 
        title="Student Details"
        subtitle="Comprehensive information for {{ $student->full_name }}" />

    <x-modals.modal-body>
        <div class="space-y-6">
            <!-- Student Basic Information -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <x-icon name="user" style="fas" class="w-5 h-5 mr-2 text-gray-600" />
                    Personal Information
                </h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                        <dd class="text-sm text-gray-900 font-medium">{{ $student->full_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Email Address</dt>
                        <dd class="text-sm text-gray-900">{{ $student->user?->email ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Student ID</dt>
                        <dd class="text-sm text-gray-900">
                            @if($student->student_id)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $student->student_id }}
                                </span>
                            @else
                                <span class="text-gray-400 italic">Not assigned</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">ULI (Unique Learner Identifier)</dt>
                        <dd class="text-sm text-gray-900">
                            @if($student->uli)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ $student->uli }}
                                </span>
                            @else
                                <span class="text-gray-400 italic">Not assigned</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Academic Information -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <x-icon name="graduation-cap" style="fas" class="w-5 h-5 mr-2 text-blue-600" />
                    Academic Information
                </h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Course</dt>
                        <dd class="text-sm text-gray-900">
                            @if($student->course)
                                <div>
                                    <p class="font-medium">{{ $student->course->name }}</p>
                                    <p class="text-xs text-gray-600">Code: {{ $student->course->code }}</p>
                                </div>
                            @else
                                <span class="text-gray-400 italic">No course assigned</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Academic Year</dt>
                        <dd class="text-sm text-gray-900">
                            @if($student->academicYear)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                                    {{ $student->academicYear->formatted_description }}
                                </span>
                            @else
                                <span class="text-gray-400 italic">Not specified</span>
                            @endif
                        </dd>
                    </div>
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Campus</dt>
                        <dd class="text-sm text-gray-900">
                            @if($student->course && $student->course->campuses->isNotEmpty())
                                <div class="flex flex-wrap gap-2 mt-1">
                                    @foreach($student->course->campuses as $campus)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium" 
                                              style="background-color: {{ $campus->color }}20; color: {{ $campus->color }}; border: 1px solid {{ $campus->color }}40;">
                                            <div class="w-2 h-2 rounded-full mr-2" style="background-color: {{ $campus->color }}"></div>
                                            {{ $campus->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-gray-400 italic">No campus assigned</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Assessment History -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <x-icon name="clipboard-list" style="fas" class="w-5 h-5 mr-2 text-green-600" />
                    Assessment History
                    @if($student->results && $student->results->count() > 0)
                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ $student->results->count() }} {{ $student->results->count() === 1 ? 'Assessment' : 'Assessments' }}
                        </span>
                    @endif
                </h3>
                
                @if($student->results && $student->results->isNotEmpty())
                    <div class="space-y-4">
                        @foreach($student->results as $result)
                            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-2">
                                            <div class="flex-1">
                                                <h4 class="text-sm font-medium text-gray-900">
                                                    {{ $result->assessmentSchedule?->assessment?->qualificationType?->name ?? 'Unknown Qualification' }}
                                                </h4>
                                                @if($result->assessmentSchedule?->assessment?->qualificationType?->level)
                                                    <p class="text-xs text-blue-600">
                                                        {{ $result->assessmentSchedule->assessment->qualificationType->level }}
                                                    </p>
                                                @endif
                                            </div>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                {{ $result->competencyType?->name === 'Competent' ? 'bg-green-100 text-green-800' : 
                                                   ($result->competencyType?->name === 'Not Yet Competent' ? 'bg-red-100 text-red-800' : 
                                                   ($result->competencyType?->name === 'Absent' ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 text-gray-800')) }}">
                                                {{ $result->competencyType?->name ?? 'Pending' }}
                                            </span>
                                        </div>
                                        
                                        <div class="text-xs text-gray-500 mb-2">
                                            <strong>Course:</strong> {{ $result->assessmentSchedule?->assessment?->course?->name ?? 'Not specified' }} 
                                            ({{ $result->assessmentSchedule?->assessment?->course?->code ?? 'N/A' }})
                                        </div>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs text-gray-600">
                                            <div>
                                                <strong>Assessment Date:</strong> 
                                                {{ $result->assessmentSchedule?->assessment_date ? $result->assessmentSchedule->assessment_date->format('F j, Y') : 'Not specified' }}
                                            </div>
                                            <div>
                                                <strong>Exam Type:</strong> 
                                                {{ $result->assessmentSchedule?->assessment?->examType?->type ?? 'Not specified' }}
                                            </div>
                                            <div>
                                                <strong>Assessor:</strong> 
                                                {{ $result->assessmentSchedule?->assessor?->name ?? 'Not specified' }}
                                            </div>
                                            <div>
                                                <strong>Assessment Center:</strong> 
                                                {{ $result->assessmentSchedule?->assessmentCenter?->name ?? 'Not specified' }}
                                            </div>
                                        </div>

                                        @if($result->remarks)
                                            <div class="mt-3">
                                                <strong class="text-xs text-gray-600">Remarks:</strong>
                                                <p class="text-sm text-gray-800 bg-gray-50 rounded p-2 mt-1">{{ $result->remarks }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <x-icon name="clipboard-list" style="fas" class="w-12 h-12 text-gray-300 mx-auto mb-3" />
                        <p class="text-sm text-gray-500">No assessment history available.</p>
                        <p class="text-xs text-gray-400 mt-1">This student has not taken any assessments yet.</p>
                    </div>
                @endif
            </div>

            <!-- Quick Stats -->
            @if($student->results && $student->results->isNotEmpty())
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <x-icon name="chart-bar" style="fas" class="w-5 h-5 mr-2 text-yellow-600" />
                        Assessment Summary
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-900">{{ $student->results->count() }}</div>
                            <div class="text-sm text-gray-600">Total Assessments</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">
                                {{ $student->results->where('competencyType.name', 'Competent')->count() }}
                            </div>
                            <div class="text-sm text-gray-600">Competent</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-red-600">
                                {{ $student->results->where('competencyType.name', 'Not Yet Competent')->count() }}
                            </div>
                            <div class="text-sm text-gray-600">Not Yet Competent</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-orange-600">
                                {{ $student->results->where('competencyType.name', 'Absent')->count() }}
                            </div>
                            <div class="text-sm text-gray-600">Absent</div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </x-modals.modal-body>

    <x-modals.modal-footer>
        <x-buttons.secondary-button wire:click="closeModal">
            Close
        </x-buttons.secondary-button>
        
        
    </x-modals.modal-footer>
</div>
