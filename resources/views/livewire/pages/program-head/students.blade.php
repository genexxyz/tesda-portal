<div>
    <x-partials.header title="Student Management" breadcrumb="My Students" />
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Header Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-icon name="users" style="fas" class="h-8 w-8 text-blue-600" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Students</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $students->total() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-icon name="graduation-cap" style="fas" class="h-8 w-8 text-green-600" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Courses</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $courses->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-icon name="building" style="fas" class="h-8 w-8 text-purple-600" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Campuses</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $campuses->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-icon name="calendar" style="fas" class="h-8 w-8 text-orange-600" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Academic Years</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $academicYears->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filter Bar -->
        <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search Input -->
            <div class="md:col-span-1">
                <x-inputs.search-input 
                    id="search"
                    wire:model.live="search"
                    placeholder="Search students..." />
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

            <!-- Campus Filter -->
            <div>
                <x-inputs.filter-select 
                    id="campusFilter"
                    wire:model.live="campusFilter"
                    placeholder="All Campuses"
                    icon="building"
                    :options="$campuses" />
            </div>

            <!-- Academic Year Filter -->
            <div>
                <x-inputs.filter-select 
                    id="academicYearFilter"
                    wire:model.live="academicYearFilter"
                    placeholder="All Academic Years"
                    icon="calendar"
                    :options="$academicYears"
                    textField="description" />
            </div>
        </div>

        <!-- Active Filters Display -->
        @if($search || $courseFilter || $campusFilter || $academicYearFilter)
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

                    @if($campusFilter)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            <x-icon name="building" style="fas" class="w-3 h-3 mr-1" />
                            Campus: {{ $campuses->firstWhere('id', $campusFilter)?->name }}
                            <button wire:click="$set('campusFilter', '')" class="ml-2 text-purple-600 hover:text-purple-800">
                                <x-icon name="times" style="fas" class="w-3 h-3" />
                            </button>
                        </span>
                    @endif

                    @if($academicYearFilter)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                            <x-icon name="calendar" style="fas" class="w-3 h-3 mr-1" />
                            AY: {{ $academicYears->firstWhere('id', $academicYearFilter)?->description }}
                            <button wire:click="$set('academicYearFilter', '')" class="ml-2 text-orange-600 hover:text-orange-800">
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
            <div wire:loading.delay wire:target="search,courseFilter,campusFilter,academicYearFilter" 
                 class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-10 rounded-lg">
                <div class="flex items-center space-x-2">
                    <x-icon name="spinner" style="fas" class="w-5 h-5 text-primary animate-spin" />
                    <span class="text-sm text-gray-600">Filtering...</span>
                </div>
            </div>

            <x-tables.table :is-empty="$students->isEmpty()" empty-message="No students found matching your filters">
                <thead class="bg-gray-50">
                    <tr>
                        <x-tables.table-header>No.</x-tables.table-header>
                        
                        <x-tables.table-header>Student Name</x-tables.table-header>
                        <x-tables.table-header>Student ID</x-tables.table-header>
                        <x-tables.table-header>ULI</x-tables.table-header>
                        <x-tables.table-header>Course</x-tables.table-header>
                        
                        
                        <x-tables.table-header>Actions</x-tables.table-header>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($students as $student)
                        <x-tables.table-row wire:key="student-{{ $student->id }}">
                            <x-tables.table-cell>
                                <div class="text-sm text-gray-500">
                                    {{ $loop->iteration + ($students->currentPage() - 1) * $students->perPage() }}
                                </div>
                            </x-tables.table-cell>
                            <x-tables.table-cell>
                                <div class="text-sm text-gray-900">{{ $student->full_name }}</div>
                                <div class="text-sm text-gray-500">
                                            {{ $student->user?->email }}
                                        </div>
                            </x-tables.table-cell>
                            <x-tables.table-cell>
                                <div class="flex items-center">
                                    
                                    <div class="">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $student->student_id }}
                                        </div>
                                        
                                    </div>
                                </div>
                            </x-tables.table-cell>
                            
                            <x-tables.table-cell>
                                <div class="text-sm text-gray-900">{{ $student->uli ?? 'N/A' }}</div>
                            </x-tables.table-cell>
                            <x-tables.table-cell>
                                
                                <div class="text-sm text-gray-500">{{ $student->course?->code }}</div>
                            </x-tables.table-cell>
                            
                            
                            <x-tables.table-cell>
                                <div class="flex items-center space-x-2">
                                    <button wire:click="$dispatch('openModal', { component: 'modals.program-head.view-student-details', arguments: { studentId: {{ $student->id }} } })"
                                            class="text-blue-600 hover:text-blue-900 text-sm font-medium cursor-pointer">
                                        <x-icon name="eye" style="fas" class="w-4 h-4 mr-1" />
                                        View
                                    </button>
                                    
                                </div>
                            </x-tables.table-cell>
                        </x-tables.table-row>
                    @endforeach
                </tbody>
            </x-tables.table>
        </div>
        
        <x-tables.pagination :paginator="$students" />
    </div>
</div>
