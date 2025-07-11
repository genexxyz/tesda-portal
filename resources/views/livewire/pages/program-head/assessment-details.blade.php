<div>
    <x-partials.header 
        title="Assessment Details" 
        breadcrumb="Assessment Details" />


    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Flash Messages -->
        @if (session()->has('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Back Button -->
        <div class="mb-6">
            <a wire:navigate href="{{ route('program-head.assessments') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <x-icon name="arrow-left" style="fas" class="w-4 h-4 mr-2" />
                Back to Assessments
            </a>
        </div>

        <!-- Assessment Overview -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h3 class="text-xl font-semibold text-gray-900 mb-6">Assessment Overview</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Exam Type</dt>
                    <dd class="mt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xl font-medium {{ $assessment->examType?->type === 'ISA' ? 'bg-indigo-100 text-indigo-800' : 'bg-red-100 text-red-800' }}">
                            <x-icon name="{{ $assessment->examType?->type === 'ISA' ? 'certificate' : 'book' }}" style="fas" class="mr-1" />
                            {{ $assessment->examType?->type }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Course</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-medium">{{ $assessment->course?->name }}</dd>
                    <dd class="text-xs text-gray-600">{{ $assessment->course?->code }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Qualification</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-medium">{{ $assessment->qualificationType?->name }}</dd>
                    <dd class="text-xs text-gray-600">{{ $assessment->qualificationType?->code }} - {{ $assessment->qualificationType?->level }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Campus</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-medium">{{ $assessment->campus?->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Academic Year</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-medium">{{ $assessment->academicYear?->description }}</dd>
                </div>
                
                <div>
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd class="mt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ ucfirst($assessment->status) }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Total Schedules</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-medium">{{ $stats['total_schedules'] }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Created Date</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $assessment->created_at?->format('M j, Y') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $assessment->updated_at?->diffForHumans() }}</dd>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-6">
            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-icon name="users" style="fas" class="h-8 w-8 text-blue-600" />
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Total Students</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_students'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-icon name="check-circle" style="fas" class="h-8 w-8 text-green-600" />
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Completed</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['completed_students'] }}</p>
                        <p class="text-xs text-gray-500">{{ $stats['completion_percentage'] }}%</p>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-icon name="award" style="fas" class="h-8 w-8 text-yellow-600" />
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Competent</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['competent_students'] }}</p>
                        <p class="text-xs text-gray-500">{{ $stats['passing_percentage'] }}% pass rate</p>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-icon name="exclamation-triangle" style="fas" class="h-8 w-8 text-orange-600" />
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Not Yet Competent</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['not_yet_competent_students'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-icon name="user-times" style="fas" class="h-8 w-8 text-red-600" />
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Absent</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['absent_students'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-icon name="clock" style="fas" class="h-8 w-8 text-gray-600" />
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Pending</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['pending_students'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assessment Schedules -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex justify-start items-center mb-6">
                <h3 class="text-xl font-semibold text-gray-900">Assessment Schedules ({{ $stats['total_schedules'] }})</h3>

            </div>
            
            @if($assessment->schedules->isEmpty())
                <div class="text-center py-12">
                    <x-icon name="calendar" class="text-gray-400 mx-auto mb-4" />
                    <h4 class="text-lg font-medium text-gray-900 mb-2">No schedules created</h4>
                </div>
            @else
                <div class="space-y-6">
                    @foreach($assessment->schedules->sortBy('assessment_date') as $schedule)
                        <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-6">
                                    <div class="flex items-center space-x-2">
                                        <x-icon name="calendar" style="fas" class="h-5 w-5 text-blue-600" />
                                        <div>
                                            <h4 class="text-lg font-medium text-gray-900">
                                                {{ $schedule->assessment_date?->format('F j, Y') }}
                                            </h4>
                                            <p class="text-sm text-gray-500">
                                                {{ $schedule->assessment_date?->format('l') }} • {{ $schedule->assessment_date?->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="border-l border-gray-200 pl-6">
                                        <div class="flex items-center space-x-2">
                                            <x-icon name="user-tie" style="fas" class="h-4 w-4 text-gray-600" />
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $schedule->assessor?->name }}</p>
                                                <p class="text-xs text-gray-500">Assessor</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="border-l border-gray-200 pl-6">
                                        <div class="flex items-center space-x-2">
                                            <x-icon name="building" style="fas" class="h-4 w-4 text-gray-600" />
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $schedule->assessmentCenter?->name }}</p>
                                                <p class="text-xs text-gray-500">Assessment Center</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    @if($schedule->assessment_date && $schedule->assessment_date->isFuture())
                                        <div class="flex items-center space-x-2 mb-2">
                                            <button wire:click="$dispatch('openModal', { component: 'modals.program-head.edit-assessment-schedule', arguments: { scheduleId: {{ $schedule->id }} } })"
                                                    class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                <x-icon name="edit" style="fas" class="w-3 h-3 mr-1" />
                                                Edit
                                            </button>
                                            <button wire:click="deleteSchedule({{ $schedule->id }})" 
                                                    wire:confirm="Are you sure you want to delete this schedule? This will also delete all related student results and cannot be undone."
                                                    class="inline-flex items-center px-3 py-1 border border-red-300 rounded-md text-sm font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                <x-icon name="trash" style="fas" class="w-3 h-3 mr-1" />
                                                Delete
                                            </button>
                                        </div>
                                    @endif
                                    <p class="text-lg font-semibold text-gray-900">{{ $schedule->results->count() }} students</p>
                                    <p class="text-sm text-gray-500">
                                        {{ $schedule->results->whereNotNull('competency_type_id')->count() }} completed
                                    </p>
                                </div>
                            </div>

                            <!-- Students in this schedule -->
                            @if($schedule->results->isNotEmpty())
                                <div class="border-t border-gray-100 pt-4">
                                    <h5 class="text-sm font-medium text-gray-700 mb-3">
                                        Students ({{ $schedule->results->count() }})
                                    </h5>
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
                                        @foreach($schedule->results->sortBy('student.user.last_name') as $result)
                                            <div class="flex items-center justify-between bg-gray-50 rounded-lg px-4 py-3 hover:bg-gray-100 transition-colors">
                                                <div class="flex items-center space-x-3">
                                                    
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900">{{ $result->student?->full_name }}</p>
                                                        <p class="text-xs text-gray-500">{{ $result->student?->student_id }}</p>
                                                    </div>
                                                </div>
                                                <div>
                                                    @if($result->competency_type_id)
                                                        @php
                                                            $competencyName = $result->competencyType?->name;
                                                            $badgeClass = match($competencyName) {
                                                                'Competent' => 'bg-green-100 text-green-800',
                                                                'Not Yet Competent' => 'bg-yellow-100 text-yellow-800',
                                                                'Absent' => 'bg-red-100 text-red-800',
                                                                'Dropped' => 'bg-gray-100 text-gray-800',
                                                                default => 'bg-gray-100 text-gray-800'
                                                            };
                                                        @endphp
                                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium {{ $badgeClass }}">
                                                            {{ $competencyName }}
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-500">
                                                            <x-icon name="clock" style="fas" class="w-3 h-3 mr-1" />
                                                            Pending
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="border-t border-gray-100 pt-4">
                                    <p class="text-sm text-gray-500 text-center">No students assigned to this schedule yet.</p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
