<div>
    <x-partials.header title="Assessment Management" breadcrumb="My Assessments" />
    
    <x-buttons.floating-add-button 
        wire-click="$dispatch('openModal', { component: 'modals.program-head.assign-assessment' })"
        tooltip="Assign new assessment" 
        icon="plus"
    />
    
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

        <!-- Header Stats -->
        <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-6">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-icon name="clipboard-list" style="fas" class="h-8 w-8 text-blue-600" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total</dt>
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
                            <x-icon name="clock" style="fas" class="h-8 w-8 text-yellow-600" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Upcoming</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['upcoming'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-icon name="calendar-day" style="fas" class="h-8 w-8 text-purple-600" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Today</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['today'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-icon name="calendar-check" style="fas" class="h-8 w-8 text-green-600" />
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
                            <x-icon name="certificate" style="fas" class="h-8 w-8 text-indigo-600" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">ISA</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['isa'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-icon name="book" style="fas" class="h-8 w-8 text-red-600" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Mandatory</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['mandatory'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filter Bar -->
        <div class="mb-6 grid grid-cols-1 md:grid-cols-5 gap-4">
            <!-- Search Input -->
            <div class="md:col-span-1">
                <x-inputs.search-input 
                    id="search"
                    wire:model.live="search"
                    placeholder="Search assessments..." />
            </div>
            
            <!-- Course Filter -->
            <div>
                <x-inputs.filter-select 
                    id="courseFilter"
                    wire:model.live="courseFilter"
                    placeholder="All Courses"
                    icon="graduation-cap"
                    :options="$courses" />
            </div>

            <!-- Assessment Type Filter -->
            <div>
                <x-inputs.filter-select 
                    id="assessmentTypeFilter"
                    wire:model.live="assessmentTypeFilter"
                    placeholder="All Types"
                    icon="certificate"
                    :options="[
                        ['id' => 'ISA', 'name' => 'ISA'],
                        ['id' => 'MANDATORY', 'name' => 'Mandatory']
                    ]" />
            </div>

            <!-- Date Filter -->
            <div>
                <x-inputs.filter-select 
                    id="dateFilter"
                    wire:model.live="dateFilter"
                    placeholder="All Dates"
                    icon="calendar"
                    :options="$assessmentDates"
                    valueField="value" />
            </div>

            <!-- Status Filter -->
            <div>
                <x-inputs.filter-select 
                    id="statusFilter"
                    wire:model.live="statusFilter"
                    placeholder="All Status"
                    icon="filter"
                    :options="[
                        ['id' => 'upcoming', 'name' => 'Upcoming'],
                        ['id' => 'today', 'name' => 'Today'],
                        ['id' => 'completed', 'name' => 'Completed']
                    ]" />
            </div>
        </div>

        <!-- Active Filters Display -->
        @if($search || $courseFilter || $assessmentTypeFilter || $dateFilter || $statusFilter)
            <div class="mb-4">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-sm font-medium text-gray-700">Active filters:</span>
                    
                    @if($search)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <x-icon name="search" style="fas" class="w-3 h-3 mr-1" />
                            Search: "{{ $search }}"
                            <button wire:click="$set('search', '')" class="ml-2 text-blue-600 hover:text-blue-800">
                                <x-icon name="times" style="fas" class="w-3 h-3" />
                            </button>
                        </span>
                    @endif

                    @if($courseFilter)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <x-icon name="graduation-cap" style="fas" class="w-3 h-3 mr-1" />
                            Course: {{ $courses->firstWhere('id', $courseFilter)?->name }}
                            <button wire:click="$set('courseFilter', '')" class="ml-2 text-green-600 hover:text-green-800">
                                <x-icon name="times" style="fas" class="w-3 h-3" />
                            </button>
                        </span>
                    @endif

                    @if($assessmentTypeFilter)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            <x-icon name="certificate" style="fas" class="w-3 h-3 mr-1" />
                            Type: {{ $assessmentTypeFilter }}
                            <button wire:click="$set('assessmentTypeFilter', '')" class="ml-2 text-purple-600 hover:text-purple-800">
                                <x-icon name="times" style="fas" class="w-3 h-3" />
                            </button>
                        </span>
                    @endif

                    @if($dateFilter)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                            <x-icon name="calendar" style="fas" class="w-3 h-3 mr-1" />
                            Date: {{ date('M j, Y', strtotime($dateFilter)) }}
                            <button wire:click="$set('dateFilter', '')" class="ml-2 text-orange-600 hover:text-orange-800">
                                <x-icon name="times" style="fas" class="w-3 h-3" />
                            </button>
                        </span>
                    @endif

                    @if($statusFilter)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                            <x-icon name="filter" style="fas" class="w-3 h-3 mr-1" />
                            Status: {{ ucfirst($statusFilter) }}
                            <button wire:click="$set('statusFilter', '')" class="ml-2 text-indigo-600 hover:text-indigo-800">
                                <x-icon name="times" style="fas" class="w-3 h-3" />
                            </button>
                        </span>
                    @endif

                    <!-- Clear All Filters -->
                    <button wire:click="clearFilters"
                            class="inline-flex items-center px-3 py-1 text-xs font-medium text-gray-600 bg-gray-100 rounded-full hover:bg-gray-200 transition-colors">
                        <x-icon name="times" style="fas" class="w-3 h-3 mr-1" />
                        Clear all
                    </button>
                </div>
            </div>
        @endif

        <!-- Table with Loading State -->
        <div class="relative">
            <!-- Loading Overlay -->
            <div wire:loading.delay wire:target="search,courseFilter,assessmentTypeFilter,dateFilter,statusFilter" 
                 class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-10 rounded-lg">
                <div class="flex items-center space-x-2">
                    <x-icon name="spinner" style="fas" class="w-5 h-5 text-primary animate-spin" />
                    <span class="text-sm text-gray-600">Filtering...</span>
                </div>
            </div>

            <x-tables.table :is-empty="$assessments->isEmpty()" empty-message="No assessments found matching your filters">
                <thead class="bg-gray-50">
                    <tr>
                        <x-tables.table-header>Assessment Details</x-tables.table-header>
                        <x-tables.table-header>Course</x-tables.table-header>
                        <x-tables.table-header>Qualification</x-tables.table-header>
                        <x-tables.table-header>Assessment Dates</x-tables.table-header>
                        <x-tables.table-header>Students</x-tables.table-header>
                        <x-tables.table-header>Status</x-tables.table-header>
                        <x-tables.table-header>Actions</x-tables.table-header>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($assessments as $assessment)
                        <x-tables.table-row wire:key="assessment-{{ $assessment->id }}">
                            <x-tables.table-cell>
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full {{ $assessment->examType?->type === 'ISA' ? 'bg-indigo-100' : 'bg-red-100' }} flex items-center justify-center">
                                            <x-icon name="{{ $assessment->examType?->type === 'ISA' ? 'certificate' : 'book' }}" style="fas" class=" {{ $assessment->examType?->type === 'ISA' ? 'text-indigo-600' : 'text-red-600' }}" />
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $assessment->examType?->type ?? 'N/A' }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $assessment->academicYear?->formatted_description }}
                                        </div>
                                    </div>
                                </div>
                            </x-tables.table-cell>
                            <x-tables.table-cell>
                                <div class="text-sm text-gray-900">{{ $assessment->course?->code }}</div>
                            </x-tables.table-cell>
                            <x-tables.table-cell>
                                <div class="text-sm text-gray-900">{{ $assessment->qualificationType?->code }} - {{ $assessment->qualificationType?->level }}</div>
                            </x-tables.table-cell>
                            <x-tables.table-cell>
                                @if($assessment->schedules->isNotEmpty())
                                    @if($assessment->schedules->count() === 1)
                                        @php $schedule = $assessment->schedules->first(); @endphp
                                        <div class="text-sm text-gray-900 font-medium">
                                            {{ $schedule->assessment_date?->format('M j, Y') }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $schedule->assessment_date?->diffForHumans() }}
                                        </div>
                                    @else
                                        <div class="text-sm text-gray-900 font-medium">
                                            {{ $assessment->schedules->count() }} Dates
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            @foreach($assessment->schedules->sortBy('assessment_date')->take(3) as $schedule)
                                                {{ $schedule->assessment_date?->format('M j') }}@if(!$loop->last), @endif
                                            @endforeach
                                            @if($assessment->schedules->count() > 3)
                                                <span class="text-gray-400">...</span>
                                            @endif
                                        </div>
                                    @endif
                                @else
                                    <div class="text-sm text-gray-500">Not scheduled</div>
                                @endif
                            </x-tables.table-cell>
                            <x-tables.table-cell>
                                @php
                                    $totalStudents = $assessment->schedules->sum(function($schedule) {
                                        return $schedule->results->filter(function($result) {
                                            return !$result->competencyType || $result->competencyType->name !== 'Dropped';
                                        })->count();
                                    });
                                @endphp
                                <div class="text-sm text-gray-900">{{ $totalStudents }} students</div>
                                @if($assessment->schedules->count() > 1)
                                    <div class="text-xs text-gray-500">Across {{ $assessment->schedules->count() }} schedules</div>
                                @endif
                            </x-tables.table-cell>
                            <x-tables.table-cell>
                                @php
                                    $latestSchedule = $assessment->schedules->sortByDesc('assessment_date')->first();
                                    $isUpcoming = $latestSchedule && $latestSchedule->assessment_date?->isFuture();
                                    $isToday = $latestSchedule && $latestSchedule->assessment_date?->isToday();
                                    $isCompleted = $latestSchedule && $latestSchedule->assessment_date?->isPast();
                                @endphp
                                @if($isToday)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <x-icon name="calendar-day" style="fas" class="w-3 h-3 mr-1" />
                                        Today
                                    </span>
                                @elseif($isUpcoming)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <x-icon name="clock" style="fas" class="w-3 h-3 mr-1" />
                                        Upcoming
                                    </span>
                                @elseif($isCompleted)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <x-icon name="check" style="fas" class="w-3 h-3 mr-1" />
                                        Completed
                                    </span>
                                @endif
                            </x-tables.table-cell>
                            <x-tables.table-cell>
    <div class="flex items-center space-x-2 w-35">
        <a wire:navigate href="{{ route('program-head.assessment-details', $assessment->id) }}"
           class="text-blue-600 hover:text-blue-900 text-sm font-medium">
            <x-icon name="eye" style="fas" class="w-4 h-4 mr-1" />
            View
        </a>
        @if($isToday || $isCompleted)
            <a wire:navigate href="{{ route('program-head.submit-results', $assessment->id) }}"
               class="text-purple-600 hover:text-purple-900 text-sm font-medium">
                <x-icon name="clipboard-check" style="fas" />
                Results
            </a>
        @endif
        @if($assessment->schedules->count() === 0)
            <button wire:click="deleteAssessment({{ $assessment->id }})" 
                    wire:confirm="Are you sure you want to delete this assessment? This action cannot be undone."
                    class="text-red-600 hover:text-red-900 text-sm font-medium cursor-pointer">
                <x-icon name="trash" style="fas" class="w-4 h-4 mr-1" />
                Delete
            </button>
        @endif
    </div>
</x-tables.table-cell>
                        </x-tables.table-row>
                    @endforeach
                </tbody>
            </x-tables.table>
        </div>
        
        <x-tables.pagination :paginator="$assessments" />
    </div>
</div>