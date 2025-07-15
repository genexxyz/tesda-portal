<div>
    <x-partials.header 
        title="Submit Assessment Results" 
        breadcrumb="Assessments > Submit Results" />
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Back Button -->
        <div class="mb-6">
            <a wire:navigate href="{{ route('program-head.assessments') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <x-icon name="arrow-left" style="fas" class="w-4 h-4 mr-2" />
                Back to Assessments
            </a>
        </div>

        <!-- Assessment Details Card -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Assessment Details</h3>
            </div>
            <div class="px-6 py-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Assessment Type</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $assessment->examType?->type ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Course</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $assessment->course?->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Campus</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $assessment->campus?->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Qualification Type</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $assessment->qualificationType?->name }} ({{ $assessment->qualificationType?->level }})</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Assessment Date</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $assessment->assessment_date?->format('F j, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Assessor</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $assessment->assessor?->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Assessment Center</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $assessment->assessmentCenter?->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Academic Year</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $assessment->academicYear?->formatted_description }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Total Students</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $assessment->results->count() }}</dd>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Stats -->
        <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-6">
            <div class="bg-white overflow-hidden shadow rounded-lg">
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

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-icon name="check-circle" style="fas" class="h-8 w-8 text-green-600" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Completed</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['completed'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
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

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-icon name="certificate" style="fas" class="h-8 w-8 text-green-600" />
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

            <div class="bg-white overflow-hidden shadow rounded-lg">
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

            <div class="bg-white overflow-hidden shadow rounded-lg">
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
        </div>

        <!-- Progress Bar -->
        <div class="bg-white shadow rounded-lg mb-6 p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-700">Completion Progress</span>
                <span class="text-sm text-gray-500">{{ $stats['completed'] }}/{{ $stats['total'] }} ({{ $stats['completion_percentage'] }}%)</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: {{ $stats['completion_percentage'] }}%"></div>
            </div>
        </div>

        <!-- Save Results Button -->
        <div class="bg-white shadow rounded-lg mb-6 p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <x-icon name="info-circle" style="fas" class="w-5 h-5 text-blue-600 mr-2" />
                    <span class="text-sm text-gray-600">Save all assessment results across all schedule dates</span>
                </div>
                <button wire:click="saveResults"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50">
                    <span wire:loading.remove wire:target="saveResults">
                        <x-icon name="save" style="fas" class="w-4 h-4 mr-2" />
                        Save All Results
                    </span>
                    <span wire:loading wire:target="saveResults">
                        <x-icon name="spinner" style="fas" class="w-4 h-4 mr-2 animate-spin" />
                        Saving...
                    </span>
                </button>
            </div>
            
            @if($isSaved)
                <div class="mt-4 flex items-center text-sm text-green-700 bg-green-50 rounded-lg p-3">
                    <x-icon name="check-circle" style="fas" class="h-4 w-4 mr-2 text-green-500" />
                    <span>All assessment results have been saved successfully!</span>
                </div>
            @endif
        </div>

        <!-- Students Results by Schedule -->
        @foreach($resultsBySchedule as $scheduleGroup)
            <div class="bg-white shadow rounded-lg mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <h3 class="text-lg font-medium text-gray-900">
                                Assessment Date: {{ $scheduleGroup['schedule']->assessment_date?->format('F j, Y') }}
                            </h3>
                            @if($scheduleGroup['schedule']->assessment_date)
                                @if($scheduleGroup['schedule']->assessment_date->isToday())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <x-icon name="calendar-day" style="fas" class="w-3 h-3 mr-1" />
                                        Today
                                    </span>
                                @elseif($scheduleGroup['schedule']->assessment_date->isPast())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <x-icon name="check" style="fas" class="w-3 h-3 mr-1" />
                                        Completed
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <x-icon name="clock" style="fas" class="w-3 h-3 mr-1" />
                                        Upcoming
                                    </span>
                                @endif
                            @endif
                        </div>
                        
                        <div class="flex items-center space-x-4 text-sm text-gray-600">
                            <div class="flex items-center">
                                <x-icon name="users" style="fas" class="w-4 h-4 mr-1 text-blue-500" />
                                <span>{{ $scheduleGroup['results']->count() }} Students</span>
                            </div>
                            @if($scheduleGroup['schedule']->assessmentCenter)
                                <div class="flex items-center">
                                    <x-icon name="building" style="fas" class="w-4 h-4 mr-1 text-purple-500" />
                                    <span>{{ $scheduleGroup['schedule']->assessmentCenter->name }}</span>
                                </div>
                            @endif
                            @if($scheduleGroup['schedule']->assessor)
                                <div class="flex items-center">
                                    <x-icon name="user-tie" style="fas" class="w-4 h-4 mr-1 text-green-500" />
                                    <span>{{ $scheduleGroup['schedule']->assessor->name }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    @if(!$scheduleGroup['is_editable'])
                        <div class="mt-2 flex items-center text-sm text-amber-600 bg-amber-50 rounded-lg p-3">
                            <x-icon name="info-circle" style="fas" class="w-4 h-4 mr-2" />
                            <span>Results for future dates cannot be edited until the assessment date arrives.</span>
                        </div>
                    @endif
                </div>
                
                <!-- Table Container with Fixed Header -->
                <div class="overflow-hidden">
                    <!-- Fixed Header -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-fixed">
                            <thead class="bg-gray-50 sticky top-0 z-10">
                                <tr>
                                    <th class="w-80 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                    <th class="w-32 px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="w-24 px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Competent</th>
                                    <th class="w-32 px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Not Yet</th>
                                    <th class="w-20 px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Absent</th>
                                    <th class="flex-1 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remarks</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    
                    <!-- Scrollable Body -->
                    <div class="overflow-y-auto max-h-96 overflow-x-auto">
                        <table class="min-w-full table-fixed">
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($scheduleGroup['results'] as $result)
                                    <tr wire:key="result-{{ $result->id }}-{{ $scheduleGroup['schedule']->id }}">
                                        <!-- Student Column -->
                                        <td class="w-80 px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="ml-2 min-w-0 flex-1">
                                                    <div class="text-sm font-medium text-gray-900 truncate">
                                                        {{ $result->student?->user?->last_name }}, {{ $result->student?->user?->first_name }}
                                                        @if($result->student?->user?->middle_name)
                                                            {{ substr($result->student?->user?->middle_name, 0, 1) }}.
                                                        @endif
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $result->student?->student_id }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <!-- Status Column -->
                                        <td class="w-32 px-6 py-4 whitespace-nowrap text-center">
                                            @if($studentResults[$result->id]['competency_type_id'])
                                                @php
                                                    $selectedType = $competencyTypes->find($studentResults[$result->id]['competency_type_id']);
                                                @endphp
                                                @if($selectedType)
                                                    @if($selectedType->name === 'Competent')
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            <x-icon name="check" style="fas" class="w-3 h-3 mr-1" />
                                                            Competent
                                                        </span>
                                                    @elseif($selectedType->name === 'Not Yet Competent')
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                            <x-icon name="times" style="fas" class="w-3 h-3 mr-1" />
                                                            Not Yet
                                                        </span>
                                                    @elseif($selectedType->name === 'Absent')
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                            <x-icon name="user-times" style="fas" class="w-3 h-3 mr-1" />
                                                            Absent
                                                        </span>
                                                    @elseif($selectedType->name === 'Dropped')
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                            <x-icon name="user-times" style="fas" class="w-3 h-3 mr-1" />
                                                            Dropped
                                                        </span>
                                                    @endif
                                                @endif
                                            @else
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    <x-icon name="clock" style="fas" class="w-3 h-3 mr-1" />
                                                    Pending
                                                </span>
                                            @endif
                                        </td>
                                        
                                        <!-- Competent Radio Button -->
                                        <td class="w-24 px-6 py-4 whitespace-nowrap text-center">
                                            @php
                                                $competentType = $competencyTypes->where('name', 'Competent')->first();
                                                $isDropped = $this->isStudentDropped($result->id);
                                                $isDisabled = $isDropped || !$scheduleGroup['is_editable'];
                                            @endphp
                                            @if($competentType)
                                                <input type="radio"
                                                       name="result_{{ $result->id }}"
                                                       wire:click="updateCompetencyType({{ $result->id }}, {{ $competentType->id }})"
                                                       {{ $studentResults[$result->id]['competency_type_id'] == $competentType->id ? 'checked' : '' }}
                                                       {{ $isDisabled ? 'disabled' : '' }}
                                                       class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 {{ $isDisabled ? 'opacity-50 cursor-not-allowed' : '' }}">
                                            @endif
                                        </td>
                                        
                                        <!-- Not Yet Competent Radio Button -->
                                        <td class="w-32 px-6 py-4 whitespace-nowrap text-center">
                                            @php
                                                $notCompetentType = $competencyTypes->where('name', 'Not Yet Competent')->first();
                                            @endphp
                                            @if($notCompetentType)
                                                <input type="radio"
                                                       name="result_{{ $result->id }}"
                                                       wire:click="updateCompetencyType({{ $result->id }}, {{ $notCompetentType->id }})"
                                                       {{ $studentResults[$result->id]['competency_type_id'] == $notCompetentType->id ? 'checked' : '' }}
                                                       {{ $isDisabled ? 'disabled' : '' }}
                                                       class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 {{ $isDisabled ? 'opacity-50 cursor-not-allowed' : '' }}">
                                            @endif
                                        </td>
                                        
                                        <!-- Absent Radio Button -->
                                        <td class="w-20 px-6 py-4 whitespace-nowrap text-center">
                                            @php
                                                $absentType = $competencyTypes->where('name', 'Absent')->first();
                                            @endphp
                                            @if($absentType)
                                                <input type="radio"
                                                       name="result_{{ $result->id }}"
                                                       wire:click="updateCompetencyType({{ $result->id }}, {{ $absentType->id }})"
                                                       {{ $studentResults[$result->id]['competency_type_id'] == $absentType->id ? 'checked' : '' }}
                                                       {{ $isDisabled ? 'disabled' : '' }}
                                                       class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 {{ $isDisabled ? 'opacity-50 cursor-not-allowed' : '' }}">
                                            @endif
                                        </td>
                                        
                                        <!-- Remarks Column -->
                                        <td class="flex-1 px-6 py-4">
                                            <input type="text"
                                                   wire:model="studentResults.{{ $result->id }}.remarks"
                                                   placeholder="{{ $isDisabled ? ($isDropped ? $result->remarks : 'Cannot edit future dates') : 'Add remarks...' }}"
                                                   {{ $isDisabled ? 'disabled readonly' : '' }}
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm {{ $isDisabled ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : '' }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>