<div>
    <x-partials.header title="My Profile" breadcrumb="Profile" />
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @if($user)
            <!-- User Information Card -->
            <div class="bg-white shadow rounded-lg mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Personal Information</h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center space-x-6 mb-6">
                        <!-- Avatar -->
                        <div class="flex-shrink-0">
                            <div class="h-20 w-20 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                                <span class="text-2xl font-bold text-white">
                                    {{ substr($user->first_name ?? '', 0, 1) }}{{ substr($user->last_name ?? '', 0, 1) }}
                                </span>
                            </div>
                        </div>
                        
                        <!-- Basic Info -->
                        <div class="flex-1">
                            <h2 class="text-2xl font-bold text-gray-900">
                                {{ $user->first_name }} {{ $user->middle_name }} {{ $user->last_name }}
                            </h2>
                            <p class="text-lg text-gray-600">{{ $user->email }}</p>
                            <div class="flex items-center space-x-4 mt-2">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                    {{ $user->status === 'active' ? 'bg-green-100 text-green-800' : 
                                       ($user->status === 'dropped' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                                    <x-icon name="{{ $user->status === 'active' ? 'check-circle' : ($user->status === 'dropped' ? 'times-circle' : 'clock') }}" 
                                            style="fas" class="w-4 h-4 mr-2" />
                                    {{ ucfirst($user->status) }}
                                </span>
                                @if($user->role)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        <x-icon name="user-tag" style="fas" class="w-4 h-4 mr-2" />
                                        {{ $user->role->name }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Information Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-3">Contact Information</h4>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-900">Email Address</dt>
                                    <dd class="text-sm text-gray-600">{{ $user->email }}</dd>
                                </div>
                                @if($user->campus)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-900">Campus</dt>
                                        <dd class="text-sm text-gray-600">
                                            <span class="inline-flex items-center">
                                                <div class="w-2 h-2 rounded-full mr-2 bg-blue-500"></div>
                                                {{ $user->campus->name }}
                                            </span>
                                        </dd>
                                    </div>
                                @endif
                            </dl>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-3">Account Information</h4>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-900">User ID</dt>
                                    <dd class="text-sm text-gray-600">#{{ $user->id }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-900">Role</dt>
                                    <dd class="text-sm text-gray-600">{{ $user->role?->name ?? 'No role assigned' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-900">Account Status</dt>
                                    <dd class="text-sm text-gray-600">{{ ucfirst($user->status) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-900">Member Since</dt>
                                    <dd class="text-sm text-gray-600">{{ $user->created_at->format('F j, Y') }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Student-Specific Information -->
            @if($student)
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Student Details Card -->
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                                <x-icon name="graduation-cap" style="fas" class="w-5 h-5 mr-2 text-green-600" />
                                Student Information
                            </h3>
                        </div>
                        <div class="p-6">
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-900">Student ID</dt>
                                    <dd class="text-sm text-gray-600">
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
                                    <dt class="text-sm font-medium text-gray-900">ULI (Unique Learner Identifier)</dt>
                                    <dd class="text-sm text-gray-600">
                                        @if($student->uli)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                {{ $student->uli }}
                                            </span>
                                        @else
                                            <span class="text-gray-400 italic">Not assigned</span>
                                        @endif
                                    </dd>
                                </div>
                                @if($student->course)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-900">Course</dt>
                                        <dd class="text-sm text-gray-600">
                                            <div class="font-medium">{{ $student->course->code }}</div>
                                            <div class="text-gray-500">{{ $student->course->name }}</div>
                                        </dd>
                                    </div>
                                @endif
                                @if($student->academicYear)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-900">Academic Year</dt>
                                        <dd class="text-sm text-gray-600">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                {{ $student->academicYear->formatted_description }}
                                            </span>
                                        </dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    </div>

                    <!-- Assessment Statistics Card -->
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                                <x-icon name="chart-bar" style="fas" class="w-5 h-5 mr-2 text-blue-600" />
                                Assessment Overview
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-gray-900">{{ $assessmentStats['total'] }}</div>
                                    <div class="text-sm text-gray-500">Total Assessments</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-green-600">{{ $assessmentStats['competent'] }}</div>
                                    <div class="text-sm text-gray-500">Competent</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-red-600">{{ $assessmentStats['not_yet_competent'] }}</div>
                                    <div class="text-sm text-gray-500">Not Yet Competent</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-orange-600">{{ $assessmentStats['absent'] }}</div>
                                    <div class="text-sm text-gray-500">Absent</div>
                                </div>
                            </div>
                            
                            @if($assessmentStats['pending'] > 0)
                                <div class="mt-4 p-3 bg-yellow-50 rounded-lg">
                                    <div class="flex items-center">
                                        <x-icon name="clock" style="fas" class="w-5 h-5 text-yellow-600 mr-2" />
                                        <span class="text-sm font-medium text-yellow-800">
                                            {{ $assessmentStats['pending'] }} assessment(s) pending results
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Recent Assessment Results -->
                @if($assessmentResults && $assessmentResults->isNotEmpty())
                    <div class="bg-white shadow rounded-lg mb-6">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                                <x-icon name="clipboard-list" style="fas" class="w-5 h-5 mr-2 text-purple-600" />
                                Recent Assessment Results
                            </h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assessment</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assessor</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Result</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($assessmentResults->take(5) as $result)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $result->assessmentSchedule?->assessment?->qualificationType?->name ?? 'Unknown' }}
                                                </div>
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
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $result->created_at->format('M j, Y') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($assessmentResults->count() > 5)
                            <div class="px-6 py-3 bg-gray-50 text-center">
                                <span class="text-sm text-gray-500">
                                    Showing 5 of {{ $assessmentResults->count() }} total assessments
                                </span>
                            </div>
                        @endif
                    </div>
                @endif
            @endif

            <!-- Program Head-Specific Information -->
            @if($programHead)
                <div class="bg-white shadow rounded-lg mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 flex items-center">
                            <x-icon name="user-tie" style="fas" class="w-5 h-5 mr-2 text-indigo-600" />
                            Program Head Information
                        </h3>
                    </div>
                    <div class="p-6">
                        <dl class="space-y-4">
                            @if($programHead->course)
                                <div>
                                    <dt class="text-sm font-medium text-gray-900">Assigned Course</dt>
                                    <dd class="text-sm text-gray-600">
                                        <div class="font-medium">{{ $programHead->course->code }}</div>
                                        <div class="text-gray-500">{{ $programHead->course->name }}</div>
                                    </dd>
                                </div>
                            @endif
                            <div>
                                <dt class="text-sm font-medium text-gray-900">Position Start Date</dt>
                                <dd class="text-sm text-gray-600">{{ $programHead->created_at->format('F j, Y') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            @endif

            <!-- Generic Role Information for other roles -->
            @if(!$student && !$programHead && $user->role)
                <div class="bg-white shadow rounded-lg mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 flex items-center">
                            <x-icon name="briefcase" style="fas" class="w-5 h-5 mr-2 text-gray-600" />
                            Role Information
                        </h3>
                    </div>
                    <div class="p-6">
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-900">Current Role</dt>
                                <dd class="text-sm text-gray-600">{{ $user->role->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-900">Role Assigned</dt>
                                <dd class="text-sm text-gray-600">{{ $user->created_at->format('F j, Y') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            @endif

        @else
            <!-- Error State -->
            <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                <div class="flex items-center">
                    <x-icon name="exclamation-triangle" style="fas" class="h-8 w-8 text-red-600 mr-3" />
                    <div>
                        <h3 class="text-lg font-medium text-red-800">Unable to Load Profile</h3>
                        <p class="text-sm text-red-700 mt-1">There was an issue loading your profile information. Please try refreshing the page.</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
