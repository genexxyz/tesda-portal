<div>
    <x-partials.header title="Student Dashboard" breadcrumb="Dashboard" />
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @if(!$student)
            <!-- No Student Record Alert -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6">
                <div class="flex items-center">
                    <x-icon name="exclamation-triangle" style="fas" class="h-8 w-8 text-yellow-600 mr-3" />
                    <div>
                        <h3 class="text-lg font-medium text-yellow-800">No Student Record Found</h3>
                        <p class="text-sm text-yellow-700 mt-1">Your account is not associated with any student record. Please contact the registrar.</p>
                    </div>
                </div>
            </div>
        @else
            <!-- Student Information Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Current Academic Year -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <x-icon name="calendar" style="fas" class="h-8 w-8 text-blue-600" />
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Current Academic Year</dt>
                                    <dd class="text-lg font-medium text-gray-900">
                                        @if($currentAcademicYear)
                                            {{ $currentAcademicYear->formatted_description }}
                                        @else
                                            <span class="text-gray-400 italic">No active academic year</span>
                                        @endif
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Course Information -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <x-icon name="graduation-cap" style="fas" class="h-8 w-8 text-green-600" />
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Your Course</dt>
                                    <dd class="text-lg font-medium text-gray-900">
                                        @if($student->course)
                                            {{ $student->course->code }}
                                        @else
                                            <span class="text-gray-400 italic">No course assigned</span>
                                        @endif
                                    </dd>
                                    @if($student->course)
                                        <dd class="text-sm text-gray-600">{{ $student->course->name }}</dd>
                                    @endif
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Student Status -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <x-icon name="user-check" style="fas" class="h-8 w-8 text-purple-600" />
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Status</dt>
                                    <dd class="text-lg font-medium text-gray-900">
                                        @if($student->user)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                                {{ $student->user->status === 'active' ? 'bg-green-100 text-green-800' : 
                                                   ($student->user->status === 'dropped' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                                                {{ ucfirst($student->user->status) }}
                                            </span>
                                        @else
                                            <span class="text-gray-400 italic">Unknown</span>
                                        @endif
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assessment Statistics -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <x-icon name="clipboard-list" style="fas" class="h-8 w-8 text-blue-600" />
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $assessmentStats['total'] }}</dd>
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
                                    <dt class="text-sm font-medium text-gray-500 truncate">Competent</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $assessmentStats['competent'] }}</dd>
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
                                    <dd class="text-lg font-medium text-gray-900">{{ $assessmentStats['not_yet_competent'] }}</dd>
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
                                    <dd class="text-lg font-medium text-gray-900">{{ $assessmentStats['absent'] }}</dd>
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
                                    <dd class="text-lg font-medium text-gray-900">{{ $assessmentStats['pending'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assessment History -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Your Assessment History</h3>
                    <p class="text-sm text-gray-600">Assessments taken in {{ $currentAcademicYear ? $currentAcademicYear->formatted_description : 'current academic year' }}</p>
                </div>
                
                @if($assessmentResults && $assessmentResults->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assessment</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assessor</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Result</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remarks</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($assessmentResults as $result)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $result->assessmentSchedule?->assessment?->qualificationType?->name ?? 'Unknown' }}
                                            </div>
                                            @if($result->assessmentSchedule?->assessment?->qualificationType?->level)
                                                <div class="text-sm text-gray-500">
                                                    Level {{ $result->assessmentSchedule->assessment->qualificationType->level }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $result->assessmentSchedule?->assessment_date ? $result->assessmentSchedule->assessment_date->format('M j, Y') : 'Not scheduled' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                {{ $result->assessmentSchedule?->assessment?->examType?->type === 'ISA' ? 'bg-indigo-100 text-indigo-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $result->assessmentSchedule?->assessment?->examType?->type ?? 'Unknown' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $result->assessmentSchedule?->assessor?->name ?? 'Not assigned' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($result->competencyType)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                    {{ $result->competencyType->name === 'Competent' ? 'bg-green-100 text-green-800' : 
                                                       ($result->competencyType->name === 'Not Yet Competent' ? 'bg-red-100 text-red-800' : 
                                                       ($result->competencyType->name === 'Absent' ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 text-gray-800')) }}">
                                                    {{ $result->competencyType->name }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    Pending
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ $result->remarks ?? '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-12">
                        <x-icon name="clipboard-list" style="fas" class="w-12 h-12 text-gray-300 mx-auto mb-4" />
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Assessments Yet</h3>
                        <p class="text-sm text-gray-500">You haven't taken any assessments in the current academic year.</p>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
