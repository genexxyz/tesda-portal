<div>
    @if($userCampus)
    <x-partials.header title="{{ 'Student Managment (' . $userCampus->name . ')'}}" breadcrumb="Students" />
    @endif
    <x-buttons.floating-add-button 
        wire-click="$dispatch('openModal', { component: 'modals.registrar.add-new-student' })"
        tooltip="Add new student" 
        icon="user-graduate"
    />
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        

        <!-- Search and Filter Bar -->
        <div class=" grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Search Input -->
            <div>
                <x-inputs.search-input 
                    id="search"
                    wire:model.live="search"
                    placeholder="Search by name, student ID, or ULI..." />
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

            <!-- Quick Actions -->
            <x-buttons.import-export-buttons 
    import-modal="modals.registrar.bulk-import-students"
    export-method="exportStudents"
    import-tooltip="Import student data"
    export-tooltip="Export student list" />
        </div>

        <!-- Active Filters Display -->
        @if($search || $courseFilter)
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
                            Course: {{ $courses->find($courseFilter)?->code }}
                            <button wire:click="$set('courseFilter', '')" class="ml-2 text-green-600 hover:text-green-800">
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
                    <x-icon name="graduation-cap" style="fas" class="w-4 h-4 mr-1 text-green-500" />
                    <span>{{ $courses->count() }} Courses</span>
                </div>
            </div>
        </div>

        <!-- Table with Loading State -->
        <div class="relative">
            <!-- Loading Overlay -->
            <div wire:loading.delay wire:target="search,courseFilter" 
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
                        <x-tables.table-header>Actions</x-tables.table-header>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($students as $student)
                        <x-tables.table-row wire:key="student-{{ $student->id }}">
                            <x-tables.table-cell class="w-16">
                                
                            </x-tables.table-cell>
                            <x-tables.table-cell>
    <div class="flex items-center">
        <div class="flex-shrink-0 h-10 w-10">
            <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                <span class="text-sm font-medium text-indigo-700">
                    {{ strtoupper(substr($student->user->first_name ?? 'N', 0, 1) . substr($student->user->last_name ?? 'A', 0, 1)) }}
                </span>
            </div>
        </div>
        <div class="ml-4">
            <div class="text-sm font-medium text-gray-900">
                {{ $student->user ? $student->user->last_name . ', ' . $student->user->first_name . ' ' . $student->user->middle_name : 'No user data' }}
            </div>
            @if($student->user && $student->user->email)
                <div class="text-sm text-gray-500">{{ $student->user->email }}</div>
            @endif
        </div>
    </div>
</x-tables.table-cell>
                            <x-tables.table-cell>
                                <div class="text-sm font-medium text-gray-900">{{ $student->student_id ?: 'Not assigned' }}</div>
                            </x-tables.table-cell>
                            <x-tables.table-cell>
                                <div class="text-sm text-gray-900">{{ $student->uli ?: 'Not assigned' }}</div>
                            </x-tables.table-cell>
                            <x-tables.table-cell>
                                @if($student->course)
                                    <div class="flex flex-col">
                                        
                                            {{ $student->course->code }}
                                        
                                @else
                                    <span class="text-xs text-gray-400">No course assigned</span>
                                @endif
                            </x-tables.table-cell>
                            <x-tables.table-cell>
                                <div class="flex items-center space-x-2">
                                    <button wire:click="$dispatch('openModal', { component: 'modals.registrar.view-student', arguments: { studentId: {{ $student->id }} } })"
                                            class="text-green-600 hover:text-green-900 text-sm font-medium">
                                        <x-icon name="eye" style="fas" class="w-4 h-4 mr-1" />
                                        View
                                    </button>
                                    <button wire:click="$dispatch('openModal', { component: 'modals.registrar.edit-student', arguments: { studentId: {{ $student->id }} } })"
                                            class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                        <x-icon name="edit" style="fas" class="w-4 h-4 mr-1" />
                                        Edit
                                    </button>
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