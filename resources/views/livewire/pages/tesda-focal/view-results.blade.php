<div>
    <x-partials.header 
        title="Assessment Results Overview - All Campuses" 
        breadcrumb="TESDA Focal > Results Overview" />
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Filters Section -->
        <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Course Filter -->
            <div>
                <x-inputs.filter-select 
                    id="courseFilter"
                    wire:model.live="courseFilter"
                    placeholder="All Courses"
                    icon="graduation-cap"
                    :options="$courses" />
            </div>

            <!-- Qualification Type Filter -->
            <div>
                <x-inputs.filter-select 
                    id="qualificationFilter"
                    wire:model.live="qualificationFilter"
                    placeholder="{{ $courseFilter ? 'All Qualification Types' : 'Select Course First' }}"
                    icon="award"
                    :options="$qualificationTypes"
                    nameField="name"
                    :disabled="!$courseFilter" />
            </div>

            <!-- Academic Year Filter -->
            <div>
                <x-inputs.filter-select 
                    id="academicYearFilter"
                    wire:model.live="academicYearFilter"
                    placeholder="Select Academic Year"
                    icon="calendar"
                    :options="$academicYears"
                    textField="description" />
            </div>

        
        </div>

        <!-- Active Filters Display -->
        @if($courseFilter || $qualificationFilter || $academicYearFilter)
            <div class="mb-6">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-sm font-medium text-gray-700">Active filters:</span>
                    
                    @if($courseFilter)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <x-icon name="graduation-cap" style="fas" class="w-3 h-3 mr-1" />
                            Course: {{ $courses->firstWhere('id', $courseFilter)?->name }}
                            <button wire:click="$set('courseFilter', '')" class="ml-2 text-blue-600 hover:text-blue-800">
                                <x-icon name="times" style="fas" class="w-3 h-3" />
                            </button>
                        </span>
                    @endif

                    @if($qualificationFilter)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            <x-icon name="award" style="fas" class="w-3 h-3 mr-1" />
                            Qualification: {{ $qualificationTypes->firstWhere('id', $qualificationFilter)?->name }}
                            <button wire:click="$set('qualificationFilter', '')" class="ml-2 text-purple-600 hover:text-purple-800">
                                <x-icon name="times" style="fas" class="w-3 h-3" />
                            </button>
                        </span>
                    @endif

                    @if($academicYearFilter)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <x-icon name="calendar" style="fas" class="w-3 h-3 mr-1" />
                            Academic Year: {{ $academicYears->firstWhere('id', $academicYearFilter)?->description }}
                            <button wire:click="$set('academicYearFilter', '')" class="ml-2 text-green-600 hover:text-green-800">
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

        

        <!-- Campus Tabs -->
        <div class="mb-6">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8 overflow-x-auto" aria-label="Tabs">
                    <!-- Overall Tab -->
                    <button wire:click="setActiveTab('overall')"
                            class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'overall' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        <x-icon name="chart-bar" style="fas" class="w-4 h-4 mr-2 inline" />
                        Overall
                    </button>
                    
                    <!-- Campus Tabs -->
                    @foreach($campuses as $campus)
                        <button wire:click="setActiveTab('{{ $campus->name }}')"
                                class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === $campus->name ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            <x-icon name="building" style="fas" class="w-4 h-4 mr-2 inline" />
                            {{ $campus->name }}
                        </button>
                    @endforeach
                </nav>
            </div>
        </div>

        <!-- Overall Statistics Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-6">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-icon name="users" style="fas" class="h-8 w-8 text-blue-600" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Students</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($overallStats['total_students']) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-icon name="clipboard-check" style="fas" class="h-8 w-8 text-indigo-600" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Assessed</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($overallStats['total_assessed']) }}</dd>
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
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($overallStats['total_competent']) }}</dd>
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
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($overallStats['total_not_yet_competent']) }}</dd>
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
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($overallStats['total_absent']) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-icon name="percentage" style="fas" class="h-8 w-8 text-purple-600" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Pass Rate</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $overallStats['overall_passing_rate'] }}%</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Results Tables -->
        @if($activeTab === 'overall')
            <!-- Overall View - Shows all campuses for each course/qualification/exam type grouped by course -->
            @foreach($resultsData as $courseName => $courseData)
                <!-- Course Title -->
                <div class="mb-4">
                    <h2 class="text-2xl font-bold text-gray-900 bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text ">
                        {{ $courseName }}
                    </h2>
                    <hr class="border-gray-300 mt-2 mb-4">
                </div>

                @foreach($courseData as $data)
                    <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">
                                {{ $data['exam_type'] }} - {{ $data['course_code'] }} {{ $data['qualification_name'] }} {{ $data['qualification_level'] }}
                            </h3>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Campus</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total Assessed</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Competent</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">% of Passing</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Not Yet Competent</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">No Assessment Yet / Absent</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total Students</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($data['campuses'] as $campusName => $campusData)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $campusName }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">{{ number_format($campusData['total_assessed']) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    {{ number_format($campusData['competent']) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-semibold text-gray-900">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                                    {{ $campusData['passing_percentage'] >= 70 ? 'bg-green-100 text-green-800' : 
                                                       ($campusData['passing_percentage'] >= 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                    {{ $campusData['passing_percentage'] }}%
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    {{ number_format($campusData['not_yet_competent']) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                    {{ number_format($campusData['absent']) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-900">{{ number_format($campusData['total_students']) }}</td>
                                        </tr>
                                    @endforeach
                                    
                                    <!-- Totals Row -->
                                    <tr class="bg-gray-100 font-semibold">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">TOTAL</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">{{ number_format($data['totals']['total_assessed']) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                {{ number_format($data['totals']['competent']) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                                {{ $data['totals']['passing_percentage'] >= 70 ? 'bg-green-100 text-green-800' : 
                                                   ($data['totals']['passing_percentage'] >= 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                {{ $data['totals']['passing_percentage'] }}%
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                {{ number_format($data['totals']['not_yet_competent']) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                {{ number_format($data['totals']['absent']) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">{{ number_format($data['totals']['total_students']) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            @endforeach
        @else
            <!-- Campus-specific View - Shows courses/qualifications for selected campus grouped by course -->
            @foreach($resultsData as $courseName => $courseData)
                @if($courseData->count() > 0)
                    <!-- Course Title -->
                    <div class="mb-4">
                        <h2 class="text-2xl font-bold text-gray-900 bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text">
                            {{ $courseName }} - {{ $activeTab }} Campus
                        </h2>
                        <hr class="border-gray-300 mt-2 mb-4">
                    </div>

                    <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Assessment Results</h3>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qualification / Exam Type</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total Assessed</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Competent</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">% of Passing</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Not Yet Competent</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">No Assessment Yet / Absent</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total Students</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($courseData as $data)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $data['exam_type'] }} - {{ $data['course_code'] }}</div>
                                                <div class="text-xs text-blue-600 mt-1">{{ $data['qualification_name'] }} {{ $data['qualification_level'] }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">{{ number_format($data['campus_data']['total_assessed']) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    {{ number_format($data['campus_data']['competent']) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-semibold text-gray-900">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                                    {{ $data['campus_data']['passing_percentage'] >= 70 ? 'bg-green-100 text-green-800' : 
                                                       ($data['campus_data']['passing_percentage'] >= 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                    {{ $data['campus_data']['passing_percentage'] }}%
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    {{ number_format($data['campus_data']['not_yet_competent']) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                    {{ number_format($data['campus_data']['absent']) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-900">{{ number_format($data['campus_data']['total_students']) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            @endforeach

            @if($resultsData->flatten()->isEmpty())
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-6 py-12 text-center text-gray-500">
                        <div class="flex flex-col items-center">
                            <x-icon name="chart-bar" style="fas" class="h-12 w-12 text-gray-400 mb-4" />
                            <p class="text-lg font-medium">No assessment results found for {{ $activeTab }}</p>
                            <p class="text-sm">Try adjusting your filters or check if assessments have been conducted.</p>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>