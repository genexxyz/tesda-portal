<div>
    <x-modals.modal-header 
        title="Student Details"
        subtitle="Comprehensive information for {{ $student->full_name }}" />

    <x-modals.modal-body>
        <div class="space-y-6">
            <!-- Student Status Alert -->
            @if($student->user && $student->user->status === 'dropped')
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <x-icon name="user-times" style="fas" class="w-5 h-5 text-red-600 mr-2" />
                        <div>
                            <h3 class="text-sm font-medium text-red-800">Student Dropped</h3>
                            <p class="text-sm text-red-700">This student has been marked as dropped from the program.</p>
                        </div>
                    </div>
                </div>
            @elseif($student->user && $student->user->status === 'inactive')
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <x-icon name="user-clock" style="fas" class="w-5 h-5 text-yellow-600 mr-2" />
                        <div>
                            <h3 class="text-sm font-medium text-yellow-800">Student Inactive</h3>
                            <p class="text-sm text-yellow-700">This student account is currently inactive.</p>
                        </div>
                    </div>
                </div>
            @endif

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
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="text-sm text-gray-900">
                            @if($student->user)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $student->user->status === 'active' ? 'bg-green-100 text-green-800' : 
                                       ($student->user->status === 'dropped' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                                    @if($student->user->status === 'dropped')
                                        <x-icon name="user-times" style="fas" class="w-3 h-3 mr-1" />
                                    @elseif($student->user->status === 'active')
                                        <x-icon name="user-check" style="fas" class="w-3 h-3 mr-1" />
                                    @else
                                        <x-icon name="user-clock" style="fas" class="w-3 h-3 mr-1" />
                                    @endif
                                    {{ ucfirst($student->user->status) }}
                                </span>
                            @else
                                <span class="text-gray-400">No user data</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Campus</dt>
                        <dd class="text-sm text-gray-900">
                            @if($student->user?->campus)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                                      style="background-color: {{ $student->user->campus->color }}20; color: {{ $student->user->campus->color }}; border: 1px solid {{ $student->user->campus->color }}40;">
                                    <div class="w-2 h-2 rounded-full mr-1" style="background-color: {{ $student->user->campus->color }}"></div>
                                    {{ $student->user->campus->name }}
                                </span>
                            @else
                                N/A
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
                        <dt class="text-sm font-medium text-gray-500">Student ID</dt>
                        <dd class="text-sm text-gray-900 font-medium">{{ $student->student_id ?: 'Not assigned' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">ULI</dt>
                        <dd class="text-sm text-gray-900">{{ $student->uli ?: 'Not assigned' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Course</dt>
                        <dd class="text-sm text-gray-900">
                            @if($student->course)
                                <div>
                                    <div class="font-medium">{{ $student->course->code }}</div>
                                    <div class="text-xs text-gray-600">{{ $student->course->name }}</div>
                                </div>
                            @else
                                Not assigned
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Academic Year</dt>
                        <dd class="text-sm text-gray-900">
                            @if($student->academicYear)
                                {{ $student->academicYear->formatted_description }}
                            @else
                                Not assigned
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Assessment History -->
            @if($student->results->isNotEmpty())
                <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <x-icon name="clipboard-check" style="fas" class="w-5 h-5 mr-2 text-green-600" />
                        Assessment History
                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $student->results->count() }} assessments
                        </span>
                    </h3>
                    
                    @php
                        // Filter out dropped results for statistics
                        $validResults = $student->results->filter(function($result) {
                            return !$result->competencyType || $result->competencyType->name !== 'Dropped';
                        });
                        
                        $competentCount = $validResults->where('competencyType.name', 'Competent')->count();
                        $notYetCompetentCount = $validResults->where('competencyType.name', 'Not Yet Competent')->count();
                        $absentCount = $validResults->where('competencyType.name', 'Absent')->count();
                        $pendingCount = $validResults->whereNull('competency_type_id')->count();
                    @endphp

                    <!-- Assessment Statistics -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">{{ $competentCount }}</div>
                            <div class="text-xs text-gray-600">Competent</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-red-600">{{ $notYetCompetentCount }}</div>
                            <div class="text-xs text-gray-600">Not Yet</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-orange-600">{{ $absentCount }}</div>
                            <div class="text-xs text-gray-600">Absent</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-600">{{ $pendingCount }}</div>
                            <div class="text-xs text-gray-600">Pending</div>
                        </div>
                    </div>

                    <!-- Assessment List -->
                    <div class="space-y-3 max-h-64 overflow-y-auto">
                        @foreach($student->results->sortByDesc('created_at') as $result)
                            <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-gray-200">
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $result->assessmentSchedule?->assessment?->qualificationType?->description ?? 'Unknown Assessment' }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $result->assessmentSchedule?->assessment_date?->format('M j, Y') ?? 'No date' }}
                                        @if($result->assessmentSchedule?->assessment?->examType)
                                            • {{ $result->assessmentSchedule->assessment->examType->type }}
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    @if($result->competencyType)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            {{ $result->competencyType->name === 'Competent' ? 'bg-green-100 text-green-800' : 
                                               ($result->competencyType->name === 'Not Yet Competent' ? 'bg-red-100 text-red-800' : 
                                               ($result->competencyType->name === 'Absent' ? 'bg-orange-100 text-orange-800' : 
                                               ($result->competencyType->name === 'Dropped' ? 'bg-gray-100 text-gray-800' : 'bg-blue-100 text-blue-800'))) }}">
                                            {{ $result->competencyType->name }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            Pending
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                    <div class="text-center">
                        <x-icon name="clipboard-list" style="fas" class="w-12 h-12 text-gray-300 mx-auto mb-4" />
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Assessment History</h3>
                        <p class="text-sm text-gray-500">This student hasn't taken any assessments yet.</p>
                    </div>
                </div>
            @endif
        </div>
    </x-modals.modal-body>

    <x-modals.modal-footer>
        <div class="flex justify-end space-x-3">
            <x-buttons.secondary-button wire:click="$dispatch('closeModal')">
                Close
            </x-buttons.secondary-button>
            <x-buttons.primary-button 
                wire:click="$dispatch('openModal', { component: 'modals.admin.edit-student', arguments: { studentId: {{ $student->id }} } })">
                <x-icon name="edit" style="fas" class="w-4 h-4 mr-2" />
                Edit Student
            </x-buttons.primary-button>
        </div>
    </x-modals.modal-footer>
</div>
