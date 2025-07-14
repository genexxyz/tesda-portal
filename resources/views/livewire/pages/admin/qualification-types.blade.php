<div>
    <x-partials.header title="Qualification Management" breadcrumb="Qualifications" />
    
    <x-buttons.floating-add-button 
        wire-click="$dispatch('openModal', { component: 'modals.admin.add-new-qualification-type' })"
        tooltip="Add new qualification" 
        icon="award"
    />
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Search and Filter Bar -->
        <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Search Input -->
            <div class="md:col-span-2">
                <x-inputs.search-input 
                    id="search"
                    wire:model.live="search"
                    placeholder="Search qualification by code, name, or level..." />
            </div>
            
            <!-- Level Filter -->
            <div>
                <x-inputs.filter-select 
                    id="levelFilter"
                    wire:model.live="levelFilter"
                    placeholder="All Levels"
                    icon="layer-group"
                    :options="[
                        ['id' => 'NC I', 'name' => 'NC I'],
                        ['id' => 'NC II', 'name' => 'NC II'],
                        ['id' => 'NC III', 'name' => 'NC III'],
                        ['id' => 'NC IV', 'name' => 'NC IV']
                    ]" />
            </div>
        </div>

        <!-- Active Filters Display -->
        @if($search || $levelFilter)
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

                    @if($levelFilter)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <x-icon name="layer-group" style="fas" class="w-3 h-3 mr-1" />
                            Level: {{ $levelFilter }}
                            <button wire:click="$set('levelFilter', '')" class="ml-2 text-green-600 hover:text-green-800">
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
            <div wire:loading.delay wire:target="search,levelFilter" 
                 class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-10 rounded-lg">
                <div class="flex items-center space-x-2">
                    <x-icon name="spinner" style="fas" class="w-5 h-5 text-primary animate-spin" />
                    <span class="text-sm text-gray-600">Filtering...</span>
                </div>
            </div>

            <x-tables.table :is-empty="$qualificationTypes->isEmpty()" empty-message="No qualifications found matching your filters">
                <thead class="bg-gray-50">
                    <tr>
                        <x-tables.table-header>#</x-tables.table-header>
                        <x-tables.table-header>Code & Name</x-tables.table-header>
                        <x-tables.table-header>Level</x-tables.table-header>
                        <x-tables.table-header>Description</x-tables.table-header>
                        <x-tables.table-header>Associated Courses</x-tables.table-header>
                        <x-tables.table-header>Actions</x-tables.table-header>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($qualificationTypes as $qualificationType)
                        <x-tables.table-row wire:key="qualification-type-{{ $qualificationType->id }}">
                            <x-tables.table-cell class="w-16">
                                {{ $loop->iteration }}
                            </x-tables.table-cell>
                            <x-tables.table-cell>
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-lg bg-green-100 flex items-center justify-center">
                                            <x-icon name="award" style="fas" class="text-green-600" />
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $qualificationType->code }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $qualificationType->name }}
                                        </div>
                                    </div>
                                </div>
                            </x-tables.table-cell>
                            <x-tables.table-cell>
                                @php
                                    $levelColors = [
                                        'NC I' => 'bg-blue-100 text-blue-800',
                                        'NC II' => 'bg-green-100 text-green-800',
                                        'NC III' => 'bg-yellow-100 text-yellow-800',
                                        'NC IV' => 'bg-purple-100 text-purple-800'
                                    ];
                                    $colorClass = $levelColors[$qualificationType->level] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colorClass }}">
                                    {{ $qualificationType->level }}
                                </span>
                            </x-tables.table-cell>
                            <x-tables.table-cell>
                                <div class="text-sm text-gray-600 max-w-xs truncate">
                                    {{ $qualificationType->description ?? 'No description' }}
                                </div>
                            </x-tables.table-cell>
                            <x-tables.table-cell>
                                @if($qualificationType->courses && $qualificationType->courses->isNotEmpty())
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($qualificationType->courses->take(2) as $course)
                                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-indigo-100 text-indigo-800">
                                                {{ $course->code }}
                                            </span>
                                        @endforeach
                                        @if($qualificationType->courses->count() > 2)
                                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-600">
                                                +{{ $qualificationType->courses->count() - 2 }} more
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">No courses assigned</span>
                                @endif
                            </x-tables.table-cell>
                            <x-tables.table-cell>
                                <div class="flex items-center space-x-2">
                                    <button wire:click="$dispatch('openModal', { component: 'modals.admin.edit-qualification-type', arguments: { qualificationTypeId: {{ $qualificationType->id }} } })"
                                            class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                        <x-icon name="edit" style="fas" class="w-4 h-4 mr-1" />
                                        Edit
                                    </button>
                                    
                                    <button wire:click="confirmDelete({{ $qualificationType->id }})"
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
        
        <x-tables.pagination :paginator="$qualificationTypes" />
    </div>
</div>
