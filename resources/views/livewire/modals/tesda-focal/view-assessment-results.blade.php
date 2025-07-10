<div>
    <x-modals.modal-header 
        title="Assessment Results - {{ $assessment->qualificationType?->name }} ({{ $assessment->qualificationType?->level }})"
        subtitle="View detailed results for {{ $assessment->course?->name }} - {{ $assessment->examType?->type }}" />

    <x-modals.modal-body>
        <div class="space-y-6">
            <!-- Filter Controls -->
            <div class="flex flex-wrap gap-4 mb-4">
                @if(count($assessmentCenters) > 1)
                    <div>
                        <label for="assessment-center" class="block text-xs font-medium text-gray-700">Assessment Center:</label>
                        <select id="assessment-center" wire:model="selectedAssessmentCenter" wire:change="loadAssessmentByFilters" class="mt-1 block w-48 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <option value="">All</option>
                            @foreach($assessmentCenters as $centerId)
                                <option value="{{ $centerId }}">{{ optional(\App\Models\AssessmentCenter::find($centerId))->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                @if(count($assessors) > 1)
                    <div>
                        <label for="assessor" class="block text-xs font-medium text-gray-700">Assessor:</label>
                        <select id="assessor" wire:model="selectedAssessor" wire:change="loadAssessmentByFilters" class="mt-1 block w-48 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <option value="">All</option>
                            @foreach($assessors as $assessorId)
                                <option value="{{ $assessorId }}">{{ optional(\App\Models\Assessor::find($assessorId))->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                @if(count($assessmentDates) > 1)
                    <div>
                        <label for="assessment-date" class="block text-xs font-medium text-gray-700">Assessment Date:</label>
                        <select id="assessment-date" wire:model="selectedDate" wire:change="loadAssessmentByFilters" class="mt-1 block w-48 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <option value="">All Dates</option>
                            @foreach($assessmentDates as $date)
                                <option value="{{ $date }}">{{ \Carbon\Carbon::parse($date)->format('F j, Y') }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>

            <!-- Assessment Details Card -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <x-icon name="clipboard-list" style="fas" class="w-5 h-5 mr-2 text-gray-600" />
                    Assessment Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Assessment Type</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-medium">{{ $assessment->examType?->type ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Course</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-medium">{{ $assessment->course?->name }}</dd>
                        <dd class="text-xs text-gray-600">{{ $assessment->course?->code }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Campus</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-medium">{{ $assessment->campus?->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Qualification Type</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-medium">{{ $assessment->qualificationType?->name }} ({{ $assessment->qualificationType?->level }})</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Assessment Date</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-medium">{{ $assessment->assessment_date?->format('F j, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Assessor</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-medium">{{ $assessment->assessor?->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Assessment Center</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-medium">{{ $assessment->assessmentCenter?->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Academic Year</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-medium">{{ $assessment->academicYear?->description }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Total Students</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-medium">{{ $stats['total'] }}</dd>
                    </div>
                </div>
            </div>

            <!-- Progress Stats -->
            <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
                <div class="bg-white border border-gray-200 shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <x-icon name="users" style="fas" class="h-8 w-8 text-blue-600" />
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Students</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $stats['total'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <x-icon name="check-circle" style="fas" class="h-8 w-8 text-green-600" />
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Competent</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $stats['competent'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <x-icon name="times-circle" style="fas" class="h-8 w-8 text-red-600" />
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Not Yet</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $stats['not_yet_competent'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <x-icon name="user-times" style="fas" class="h-8 w-8 text-orange-600" />
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Absent</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $stats['absent'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <x-icon name="clock" style="fas" class="h-8 w-8 text-yellow-600" />
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Pending</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $stats['pending'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <x-icon name="percentage" style="fas" class="h-8 w-8 text-purple-600" />
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Pass Rate</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $stats['passing_percentage'] }}%</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Assessment Completion Progress</span>
                    <span class="text-sm text-gray-500">{{ $stats['completed'] }}/{{ $stats['total'] }} ({{ $stats['completion_percentage'] }}%)</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-blue-600 h-3 rounded-full transition-all duration-300" style="width: {{ $stats['completion_percentage'] }}%"></div>
                </div>
            </div>

            <!-- Students Results Table -->
            <div class="bg-white border border-gray-200 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900 flex items-center">
                            <x-icon name="table" style="fas" class="w-5 h-5 mr-2 text-gray-600" />
                            Student Results
                        </h3>
                        @if($stats['assessment_count'] > 1)
                            <span class="text-sm text-blue-600 bg-blue-50 px-3 py-1 rounded-full">
                                Showing combined results from {{ $stats['assessment_count'] }} assessments
                            </span>
                        @endif
                    </div>
                </div>
                
                <!-- Table Container with Fixed Header -->
                <div class="overflow-hidden">
                    <!-- Fixed Header -->
                    <div class="overflow-x-auto max-h-96">
                        <table class="min-w-full table-fixed">
                            <thead class="bg-gray-50 sticky top-0 z-10">
                                <tr>
                                    <th wire:click="sort('student_name')" class="w-80 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                                        Student
                                        @if($sortField === 'student_name')
                                            <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </th>
                                    <th wire:click="sort('student_id')" class="w-40 px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                                        Student ID
                                        @if($sortField === 'student_id')
                                            <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </th>
                                    <th wire:click="sort('uli')" class="w-32 px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                                        ULI
                                        @if($sortField === 'uli')
                                            <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </th>
                                    <th wire:click="sort('competency')" class="w-48 px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                                        Result
                                        @if($sortField === 'competency')
                                            <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </th>
                                    <th wire:click="sort('remarks')" class="flex-1 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                                        Remarks
                                        @if($sortField === 'remarks')
                                            <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($allResults as $result)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                                                        <span class="text-sm font-medium text-white">
                                                            {{ substr($result->student->user->first_name ?? '', 0, 1) }}{{ substr($result->student->user->last_name ?? '', 0, 1) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <button 
                                                        wire:click="$dispatch('openModal', { component: 'modals.tesda-focal.view-student-details', arguments: { studentId: {{ $result->student->id }} } })"
                                                        class="text-sm font-medium text-blue-600 hover:text-blue-900 hover:underline cursor-pointer">
                                                        {{ $result->student->user->first_name ?? 'N/A' }} {{ $result->student->user->last_name ?? '' }}
                                                    </button>
                                                    <div class="text-sm text-gray-500">{{ $result->student->user->email ?? 'N/A' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div class="text-sm text-gray-900">
                                                @if($result->student->student_id)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        {{ $result->student->student_id }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-400">Not assigned</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div class="text-sm text-gray-900">
                                                @if($result->student->uli)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        {{ $result->student->uli }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-400">Not assigned</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @if($result->competencyType)
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                                    {{ $result->competencyType->name === 'Competent' ? 'bg-green-100 text-green-800' : 
                                                       ($result->competencyType->name === 'Not Yet Competent' ? 'bg-red-100 text-red-800' : 
                                                        ($result->competencyType->name === 'Absent' ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 text-gray-800')) }}">
                                                    {{ $result->competencyType->name }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                                    Pending
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900">
                                                @if($result->remarks)
                                                    <span class="text-gray-800">{{ $result->remarks }}</span>
                                                @else
                                                    <span class="text-gray-400 italic">No remarks</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </x-modals.modal-body>

    <x-modals.modal-footer>
        <x-buttons.secondary-button wire:click="closeModal">
            Close
        </x-buttons.secondary-button>
    </x-modals.modal-footer>
</div>
