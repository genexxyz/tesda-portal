<div>
    <x-modals.modal-header 
        title="Student Details"
        subtitle="View detailed information about {{ $student->full_name }}" />

    <x-modals.modal-body>
        <div class="space-y-6">
            <!-- Student Basic Information -->
            <div class="bg-gray-50 border border-gray-200 rounded-md p-4">
                <h3 class="text-lg font-medium text-gray-900 mb-3">Basic Information</h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                        <dd class="text-sm text-gray-900">{{ $student->full_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Student ID</dt>
                        <dd class="text-sm text-gray-900">{{ $student->student_id }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                        <dd class="text-sm text-gray-900">{{ $student->user?->email ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">ULI</dt>
                        <dd class="text-sm text-gray-900">{{ $student->uli ?? 'N/A' }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Academic Information -->
            <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                <h3 class="text-lg font-medium text-gray-900 mb-3">Academic Information</h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Course</dt>
                        <dd class="text-sm text-gray-900">{{ $student->course?->name ?? 'N/A' }}</dd>
                        <dd class="text-xs text-gray-500">{{ $student->course?->code ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Academic Year</dt>
                        <dd class="text-sm text-gray-900">{{ $student->academicYear?->description ?? 'N/A' }}</dd>
                    </div>
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Campus</dt>
                        <dd class="text-sm text-gray-900">
                            @if($student->course && $student->course->campuses->isNotEmpty())
                                <div class="flex flex-wrap gap-2">
                                    @foreach($student->course->campuses as $campus)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                                              style="background-color: {{ $campus->color }}20; color: {{ $campus->color }}; border: 1px solid {{ $campus->color }}40;">
                                            <div class="w-2 h-2 rounded-full mr-1" style="background-color: {{ $campus->color }}"></div>
                                            {{ $campus->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-gray-500">No campus assigned</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Assessment History -->
            <div class="bg-green-50 border border-green-200 rounded-md p-4">
                <h3 class="text-lg font-medium text-gray-900 mb-3">Assessment History</h3>
                @if($student->results && $student->results->isNotEmpty())
                    <div class="space-y-2">
                        @foreach($student->results as $result)
                            <div class="flex items-center justify-between p-2 bg-white rounded border">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $result->assessment?->qualificationType?->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $result->assessment?->assessmentDate?->format('F j, Y') }}</p>
                                </div>
                                <div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $result->competencyType?->name === 'Competent' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $result->competencyType?->name ?? 'Pending' }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">No assessment history available.</p>
                @endif
            </div>
        </div>
    </x-modals.modal-body>

    <x-modals.modal-footer>
        <x-buttons.secondary-button wire:click="closeModal">
            Close
        </x-buttons.secondary-button>
        
        <x-buttons.primary-button 
            wire:click="$dispatch('openModal', { component: 'modals.program-head.assign-assessment', arguments: { studentId: {{ $student->id }} } })">
            <x-icon name="clipboard-list" style="fas" class="w-4 h-4 mr-1" />
            Assign Assessment
        </x-buttons.primary-button>
    </x-modals.modal-footer>
</div>
