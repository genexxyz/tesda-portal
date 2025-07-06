<div>
    <x-partials.header title="Program Head Management" breadcrumb="Program Heads" />
    
    <x-buttons.floating-add-button 
        wire-click="$dispatch('openModal', { component: 'modals.admin.add-new-program-head' })"
        tooltip="Add new program head" 
        icon="user-tie"
    />
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Search and Filter Bar -->
        <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Search Input -->
            <div>
                <x-inputs.search-input 
                    id="search"
                    wire:model.live="search"
                    placeholder="Search program heads by name or email..." />
            </div>
            
            <!-- Campus Filter -->
            <div>
                <x-inputs.filter-select 
                    id="campusFilter"
                    wire:model.live="campusFilter"
                    placeholder="All Campuses"
                    icon="building"
                    :options="collect($campuses)->prepend(['id' => 'unassigned', 'name' => 'No Courses Assigned'])" />
            </div>
        </div>

        <!-- Active Filters Display -->
        @if($search || $campusFilter)
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
                            {{ $campusFilter === 'unassigned' ? 'No Courses Assigned' : 'Campus: ' . $campuses->find($campusFilter)?->name }}
                            <button wire:click="$set('campusFilter', '')" class="ml-2 text-green-600 hover:text-green-800">
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
            <div wire:loading.delay wire:target="search,campusFilter" 
                 class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-10 rounded-lg">
                <div class="flex items-center space-x-2">
                    <x-icon name="spinner" style="fas" class="w-5 h-5 text-primary animate-spin" />
                    <span class="text-sm text-gray-600">Filtering...</span>
                </div>
            </div>

            <x-tables.table :is-empty="$programHeads->isEmpty()" empty-message="No program heads found matching your filters">
                <thead class="bg-gray-50">
                    <tr>
                        <x-tables.table-header>#</x-tables.table-header>
                        <x-tables.table-header>Name</x-tables.table-header>
                        <x-tables.table-header>Email</x-tables.table-header>
                        <x-tables.table-header>Campus</x-tables.table-header>
                        <x-tables.table-header>Assigned Courses</x-tables.table-header>
                        <x-tables.table-header>Status</x-tables.table-header>
                        <x-tables.table-header>Actions</x-tables.table-header>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($programHeads as $programHead)
                        <x-tables.table-row wire:key="program-head-{{ $programHead->id }}">
                            <x-tables.table-cell class="w-16"></x-tables.table-cell>
                            <x-tables.table-cell>
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                                            <span class="text-sm font-medium text-purple-700">
                                                {{ strtoupper(substr($programHead->first_name, 0, 1) . substr($programHead->last_name, 0, 1)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $programHead->first_name }} {{ $programHead->middle_name }} {{ $programHead->last_name }}
                                        </div>
                                    </div>
                                </div>
                            </x-tables.table-cell>
                            <x-tables.table-cell>
                                <div class="text-sm text-gray-900">{{ $programHead->email }}</div>
                            </x-tables.table-cell>
                            <x-tables.table-cell>
                                @if($programHead->campus_id && $programHead->campus)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                                          style="background-color: {{ $programHead->campus->color }}20; color: {{ $programHead->campus->color }}; border: 1px solid {{ $programHead->campus->color }}40;">
                                        <div class="w-2 h-2 rounded-full mr-1" style="background-color: {{ $programHead->campus->color }}"></div>
                                        {{ $programHead->campus->name }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                        <x-icon name="minus-circle" style="fas" class="w-3 h-3 mr-1" />
                                        Not assigned
                                    </span>
                                @endif
                            </x-tables.table-cell>
                            <x-tables.table-cell>
                                @php
                                    $courses = \App\Models\ProgramHead::where('user_id', $programHead->id)->with('course')->get();
                                @endphp
                                @if($courses->count() > 0)
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($courses->take(2) as $programHeadCourse)
                                            @if($programHeadCourse->course)
                                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-indigo-100 text-indigo-800">
                                                    {{ $programHeadCourse->course->code }}
                                                </span>
                                            @endif
                                        @endforeach
                                        @if($courses->count() > 2)
                                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-600">
                                                +{{ $courses->count() - 2 }} more
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">No courses assigned</span>
                                @endif
                            </x-tables.table-cell>
                            <x-tables.table-cell>
                                @if($programHead->status === 'active')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <x-icon name="check-circle" style="fas" class="w-3 h-3 mr-1" />
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <x-icon name="times-circle" style="fas" class="w-3 h-3 mr-1" />
                                        Inactive
                                    </span>
                                @endif
                            </x-tables.table-cell>
                            <x-tables.table-cell>
                                <div class="flex items-center space-x-2">
                                    <button wire:click="$dispatch('openModal', { component: 'modals.admin.edit-program-head', arguments: { programHeadId: {{ $programHead->id }} } })"
                                            class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                        <x-icon name="edit" style="fas" class="w-4 h-4 mr-1" />
                                        Edit
                                    </button>
                                    <button wire:click="$dispatch('openModal', { component: 'modals.admin.manage-program-head-courses', arguments: { programHeadId: {{ $programHead->id }} } })"
                                            class="text-green-600 hover:text-green-900 text-sm font-medium">
                                        <x-icon name="graduation-cap" style="fas" class="w-4 h-4 mr-1" />
                                        Courses
                                    </button>
                                    <button wire:click="toggleStatus({{ $programHead->id }})"
                                            class="text-yellow-600 hover:text-yellow-900 text-sm font-medium">
                                        {{ $programHead->status === 'active' ? 'Deactivate' : 'Activate' }}
                                    </button>
                                    <button wire:click="confirmDelete({{ $programHead->id }})"
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
        
        <x-tables.pagination :paginator="$programHeads" />
    </div>
</div>