<div>
    <x-partials.header title="Student Management" breadcrumb="Students" />
    
    <x-buttons.floating-add-button 
        wire-click="$dispatch('openModal', { component: 'modals.admin.add-new-student' })"
        tooltip="Add new student" 
        icon="user-plus"
    />
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-icon name="users" style="fas" class="h-8 w-8 text-blue-600" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Students</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['total']) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-icon name="user-check" style="fas" class="h-8 w-8 text-green-600" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Active</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['active']) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-icon name="user-clock" style="fas" class="h-8 w-8 text-yellow-600" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Inactive</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['inactive']) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-icon name="user-times" style="fas" class="h-8 w-8 text-red-600" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Dropped</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['dropped']) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filter Bar -->
        <div class="mb-6 grid grid-cols-1 md:grid-cols-1 gap-4">
            <!-- Search Input -->
            <div class="md:col-span-2">
                <x-inputs.search-input 
                    id="search"
                    wire:model.live="search"
                    placeholder="Search by name, email, student ID, or ULI..." />
            </div>
            
            
        </div>

        <!-- Academic Year Filter and Actions -->
        <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <x-inputs.filter-select 
                    id="academicYearFilter"
                    wire:model.live="academicYearFilter"
                    placeholder="All Academic Years"
                    icon="calendar"
                    :options="$academicYears" 
                    value-field="id"
                    text-field="formatted_description" />
            </div>
            <!-- Campus Filter -->
            <div>
                <x-inputs.filter-select 
                    id="campusFilter"
                    wire:model.live="campusFilter"
                    placeholder="All Campuses"
                    icon="building"
                    :options="$campuses" 
                    value-field="id"
                    text-field="name" />
            </div>

            <!-- Course Filter -->
            <div>
                <x-inputs.filter-select 
                    id="courseFilter"
                    wire:model.live="courseFilter"
                    placeholder="All Courses"
                    icon="graduation-cap"
                    :options="$courses" 
                    value-field="id"
                    text-field="code" />
            </div>

            <!-- Status Filter -->
            <div>
                <x-inputs.filter-select 
                    id="statusFilter"
                    wire:model.live="statusFilter"
                    placeholder="All Status"
                    icon="user-check"
                    :options="[
                        (object)['value' => 'active', 'label' => 'Active'],
                        (object)['value' => 'inactive', 'label' => 'Inactive'],
                        (object)['value' => 'dropped', 'label' => 'Dropped']
                    ]"
                    value-field="value"
                    text-field="label" />
            </div>

        </div>

        <!-- Active Filters Display -->
        @if($search || $campusFilter || $courseFilter || $academicYearFilter || $statusFilter)
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
                    
                    @if($campusFilter)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <x-icon name="building" style="fas" class="w-3 h-3 mr-1" />
                            Campus: {{ $campuses->firstWhere('id', $campusFilter)?->name }}
                            <button wire:click="$set('campusFilter', '')" class="ml-2 text-green-600 hover:text-green-800">
                                <x-icon name="times" style="fas" class="w-3 h-3" />
                            </button>
                        </span>
                    @endif

                    @if($courseFilter)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            <x-icon name="graduation-cap" style="fas" class="w-3 h-3 mr-1" />
                            Course: {{ $courses->firstWhere('id', $courseFilter)?->code }}
                            <button wire:click="$set('courseFilter', '')" class="ml-2 text-purple-600 hover:text-purple-800">
                                <x-icon name="times" style="fas" class="w-3 h-3" />
                            </button>
                        </span>
                    @endif

                    @if($academicYearFilter)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                            <x-icon name="calendar" style="fas" class="w-3 h-3 mr-1" />
                            Year: {{ $academicYears->firstWhere('id', $academicYearFilter)?->formatted_description }}
                            <button wire:click="$set('academicYearFilter', '')" class="ml-2 text-indigo-600 hover:text-indigo-800">
                                <x-icon name="times" style="fas" class="w-3 h-3" />
                            </button>
                        </span>
                    @endif

                    @if($statusFilter)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            <x-icon name="user-check" style="fas" class="w-3 h-3 mr-1" />
                            Status: {{ ucfirst($statusFilter) }}
                            <button wire:click="$set('statusFilter', '')" class="ml-2 text-gray-600 hover:text-gray-800">
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

        <!-- Results Summary -->
        <div class="mb-4 flex items-center justify-end">
            <!-- Stats -->
            <div class="flex items-center space-x-4 text-sm text-gray-600">
                <div class="flex items-center">
                    <x-icon name="users" style="fas" class="w-4 h-4 mr-1 text-blue-500" />
                    <span>{{ $students->total() }} Total</span>
                </div>
                <div class="flex items-center">
                    <x-icon name="building" style="fas" class="w-4 h-4 mr-1 text-green-500" />
                    <span>{{ $campuses->count() }} Campuses</span>
                </div>
                <div class="flex items-center">
                    <x-icon name="graduation-cap" style="fas" class="w-4 h-4 mr-1 text-purple-500" />
                    <span>{{ $courses->count() }} Courses</span>
                </div>
            </div>
        </div>

        <!-- Table with Loading State -->
        <div class="relative">
            <!-- Loading Overlay -->
            <div wire:loading.delay wire:target="search,campusFilter,courseFilter,academicYearFilter,statusFilter" 
                 class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-10 rounded-lg">
                <div class="flex items-center space-x-2">
                    <x-icon name="spinner" style="fas" class="w-5 h-5 text-primary animate-spin" />
                    <span class="text-sm text-gray-600">Filtering...</span>
                </div>
            </div>

            <x-tables.table :is-empty="$students->isEmpty()" empty-message="No students found matching your filters">
                <thead class="bg-gray-50">
                    <tr>
                        <x-tables.table-header>#</x-tables.table-header>
                        <x-tables.table-header>Student Info</x-tables.table-header>
                        <x-tables.table-header>Student ID</x-tables.table-header>
                        <x-tables.table-header>ULI</x-tables.table-header>
                        <x-tables.table-header>Course</x-tables.table-header>
                        <x-tables.table-header>Campus</x-tables.table-header>
                        <x-tables.table-header>Academic Year</x-tables.table-header>
                        <x-tables.table-header>Actions</x-tables.table-header>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($students as $student)
                        <x-tables.table-row wire:key="student-{{ $student->id }}">
                            <x-tables.table-cell class="w-16">
                                {{ $loop->iteration + ($students->currentPage() - 1) * $students->perPage() }}
                            </x-tables.table-cell>
                            <x-tables.table-cell>
                                
                                        <div class="flex items-center space-x-2">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $student->user ? $student->full_name : 'No user data' }}
                                            </div>
                                            @if($student->user && $student->user->status === 'dropped')
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    <x-icon name="user-times" style="fas" class=" mr-1" />
                                                    Dropped
                                                </span>
                                            @elseif($student->user && $student->user->status === 'inactive')
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    <x-icon name="user-clock" style="fas" class="w-3 h-3 mr-1" />
                                                    Inactive
                                                </span>
                                            @endif
                                        </div>
                                        @if($student->user && $student->user->email)
                                            <div class="text-xs text-gray-500">{{ $student->user->email }}</div>
                                        @endif
                                    
                            </x-tables.table-cell>
                            <x-tables.table-cell>
                                <div class="text-sm font-medium text-gray-900">{{ $student->student_id ?: 'Not assigned' }}</div>
                            </x-tables.table-cell>
                            <x-tables.table-cell>
                                <div class="text-xs text-gray-900">{{ $student->uli ?: 'Not assigned' }}</div>
                            </x-tables.table-cell>
                            <x-tables.table-cell>
                                @if($student->course)
                                    <div class="flex flex-col">
                                        <div title="{{ $student->course->name }}" class="text-sm font-medium text-gray-900">{{ $student->course->code }}</div>
                                        
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">No course assigned</span>
                                @endif
                            </x-tables.table-cell>
                            <x-tables.table-cell>
                                @if($student->user->campus)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                                          style="background-color: {{ $student->user->campus->color }}20; color: {{ $student->user->campus->color }}; border: 1px solid {{ $student->user->campus->color }}40;">
                                        <div class="w-2 h-2 rounded-full mr-1" style="background-color: {{ $student->user->campus->color }}"></div>
                                        {{ $student->user->campus->name }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">Not assigned</span>
                                @endif
                            </x-tables.table-cell>
                            <x-tables.table-cell>
                                @if($student->academicYear)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        {{ $student->academicYear->formatted_description }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">Not assigned</span>
                                @endif
                            </x-tables.table-cell>
                            <x-tables.table-cell>
                                <div class="flex items-center space-x-2">
                                    <button wire:click="$dispatch('openModal', { component: 'modals.admin.view-student-details', arguments: { studentId: {{ $student->id }} } })"
                                            class="text-green-600 hover:text-green-900 text-sm font-medium">
                                        <x-icon name="eye" style="fas" class="w-4 h-4 mr-1" />
                                        View
                                    </button>
                                    <button wire:click="$dispatch('openModal', { component: 'modals.admin.edit-student', arguments: { studentId: {{ $student->id }} } })"
                                            class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                        <x-icon name="edit" style="fas" class="w-4 h-4 mr-1" />
                                        Edit
                                    </button>
                                    @if($student->user)
                                        <button wire:click="toggleStatus({{ $student->user->id }})"
                                                class="{{ $student->user->status === 'active' ? 'text-orange-600 hover:text-orange-900' : 'text-green-600 hover:text-green-900' }} text-sm font-medium"
                                                title="{{ $student->user->status === 'active' ? 'Deactivate' : 'Activate' }}">
                                            <x-icon name="{{ $student->user->status === 'active' ? 'user-slash' : 'user-check' }}" style="fas" class="w-4 h-4 mr-1" />
                                            {{ $student->user->status === 'active' ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    @endif
                                    <button wire:click="confirmDelete({{ $student->id }})"
                                            class="text-red-600 hover:text-red-900 text-sm font-medium">
                                        <x-icon name="trash" style="fas" class="w-4 h-4 mr-1" />
                                        Delete
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
