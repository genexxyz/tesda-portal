<div>
    <x-partials.header 
        title="Assessment Results Overview" 
        breadcrumb="Results Overview" />
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Filters Section -->
        <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
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
                    placeholder="{{ $courseFilter ? 'All Qualification ' : 'Select Course First' }}"
                    icon="award"
                    :options="$qualificationTypes"
                    textField="description"
                    :disabled="!$courseFilter" />
            </div>

            
        </div>

        <!-- Active Filters Display -->
        @if($courseFilter || $qualificationFilter)
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

                    <!-- Clear All Filters -->
                    <button wire:click="clearFilters"
                            class="inline-flex items-center px-3 py-1 text-xs font-medium text-gray-600 bg-gray-100 rounded-full hover:bg-gray-200 transition-colors">
                        <x-icon name="times" style="fas" class="w-3 h-3 mr-1" />
                        Clear all
                    </button>
                </div>
            </div>
        @endif


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
                            <x-icon name="circle-check" style="fas" class="h-8 w-8 text-green-600" />
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

        <!-- Results Table -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Assessment Results by Course and Qualification</h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th rowspan="2" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">Course / Qualification</th>
                            <th colspan="6" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200 bg-indigo-50">ISA</th>
                            <th colspan="6" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-red-50">MANDATORY</th>
                        </tr>
                        <tr>
                            <!-- ISA Headers -->
                            <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-indigo-50">Total Assessed</th>
                            <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-indigo-50">Competent</th>
                            <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-indigo-50">% Passing</th>
                            <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-indigo-50">Not Yet</th>
                            <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-indigo-50">Absent</th>
                            <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-indigo-50 border-r border-gray-200">Total Students</th>
                            
                            <!-- MANDATORY Headers -->
                            <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-red-50">Total Assessed</th>
                            <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-red-50">Competent</th>
                            <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-red-50">% Passing</th>
                            <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-red-50">Not Yet</th>
                            <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-red-50">Absent</th>
                            <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-red-50">Total Students</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($resultsData as $data)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">
                                    <div class="text-sm font-medium text-gray-900">{{ $data['course_code'] }}</div>
                                    <div class="text-sm text-gray-500">{{ $data['course_name'] }}</div>
                                    <div class="text-xs text-blue-600 mt-1">{{ $data['qualification_name'] }} {{ $data['qualification_level'] }}</div>
                                </td>
                                
                                <!-- ISA Columns -->
                                <td class="px-3 py-4 whitespace-nowrap text-center text-sm text-gray-900 bg-indigo-50">
                                    {{ number_format($data['isa']['total_assessed']) }}
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap text-center text-sm text-gray-900 bg-indigo-50">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ number_format($data['isa']['competent']) }}
                                    </span>
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap text-center text-sm font-semibold text-gray-900 bg-indigo-50">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                        {{ $data['isa']['passing_percentage'] >= 70 ? 'bg-green-100 text-green-800' : 
                                           ($data['isa']['passing_percentage'] >= 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ $data['isa']['passing_percentage'] }}%
                                    </span>
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap text-center text-sm text-gray-900 bg-indigo-50">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        {{ number_format($data['isa']['not_yet_competent']) }}
                                    </span>
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap text-center text-sm text-gray-900 bg-indigo-50">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                        {{ number_format($data['isa']['absent']) }}
                                    </span>
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-900 bg-indigo-50 border-r border-gray-200">
                                    {{ number_format($data['isa']['total_students']) }}
                                </td>
                                
                                <!-- MANDATORY Columns -->
                                <td class="px-3 py-4 whitespace-nowrap text-center text-sm text-gray-900 bg-red-50">
                                    {{ number_format($data['mandatory']['total_assessed']) }}
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap text-center text-sm text-gray-900 bg-red-50">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ number_format($data['mandatory']['competent']) }}
                                    </span>
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap text-center text-sm font-semibold text-gray-900 bg-red-50">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                        {{ $data['mandatory']['passing_percentage'] >= 70 ? 'bg-green-100 text-green-800' : 
                                           ($data['mandatory']['passing_percentage'] >= 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ $data['mandatory']['passing_percentage'] }}%
                                    </span>
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap text-center text-sm text-gray-900 bg-red-50">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        {{ number_format($data['mandatory']['not_yet_competent']) }}
                                    </span>
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap text-center text-sm text-gray-900 bg-red-50">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                        {{ number_format($data['mandatory']['absent']) }}
                                    </span>
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-900 bg-red-50">
                                    {{ number_format($data['mandatory']['total_students']) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="13" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <x-icon name="chart-bar" style="fas" class="h-12 w-12 text-gray-400 mb-4" />
                                        <p class="text-lg font-medium">No assessment results found</p>
                                        <p class="text-sm">Try adjusting your filters or check if assessments have been conducted.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>