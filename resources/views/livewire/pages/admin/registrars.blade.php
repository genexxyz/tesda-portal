<div>
    <x-partials.header title="Registrar Management" breadcrumb="Registrars" />
    
    <x-buttons.floating-add-button 
        wire-click="$dispatch('openModal', { component: 'modals.admin.add-new-registrar' })"
        tooltip="Add new registrar" 
        icon="user-plus"
    />
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Search and Filter Bar -->
        <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Search Input -->
            <div>
                <x-inputs.search-input 
                    id="search"
                    wire:model.live="search"
                    placeholder="Search registrars by name or email..." />
            </div>
            
            <!-- Campus Filter -->
            <div>
                <x-inputs.filter-select 
                    id="campusFilter"
                    wire:model.live="campusFilter"
                    placeholder="All Campuses"
                    icon="building"
                    :options="collect($campuses)" />
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
                            Campus: {{ $campusFilter === 'unassigned' ? 'Unassigned' : $campuses->find($campusFilter)?->name }}
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

            <x-tables.table :is-empty="$registrars->isEmpty()" empty-message="No registrars found matching your filters">
                <thead class="bg-gray-50">
                    <tr>
                        <x-tables.table-header>#</x-tables.table-header>
                        <x-tables.table-header>Name</x-tables.table-header>
                        <x-tables.table-header>Email</x-tables.table-header>
                        <x-tables.table-header>Campus</x-tables.table-header>
                        <x-tables.table-header>Status</x-tables.table-header>
                        <x-tables.table-header>Actions</x-tables.table-header>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($registrars as $registrar)
                        <x-tables.table-row wire:key="registrar-{{ $registrar->id }}">
                            <x-tables.table-cell class="w-16">
                                {{ $loop->iteration }}
                            </x-tables.table-cell>
                            <x-tables.table-cell>
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                            <span class="text-sm font-medium text-gray-700">
                                                {{ strtoupper(substr($registrar->first_name, 0, 1) . substr($registrar->last_name, 0, 1)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $registrar->first_name }} {{ $registrar->middle_name }} {{ $registrar->last_name }}
                                        </div>
                                    </div>
                                </div>
                            </x-tables.table-cell>
                            <x-tables.table-cell>
                                <div class="text-sm text-gray-900">{{ $registrar->email }}</div>
                            </x-tables.table-cell>
                            <x-tables.table-cell>
                                @if($registrar->campus_id && $registrar->campus)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                                          style="background-color: {{ $registrar->campus->color }}20; color: {{ $registrar->campus->color }}; border: 1px solid {{ $registrar->campus->color }}40;">
                                        <div class="w-2 h-2 rounded-full mr-1" style="background-color: {{ $registrar->campus->color }}"></div>
                                        {{ $registrar->campus->name }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                        <x-icon name="minus-circle" style="fas" class="w-3 h-3 mr-1" />
                                        Not assigned
                                    </span>
                                @endif
                            </x-tables.table-cell>
                            <x-tables.table-cell>
                                @if($registrar->status === 'active')
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
                                    <button wire:click="$dispatch('openModal', { component: 'modals.admin.edit-registrar', arguments: { registrarId: {{ $registrar->id }} } })"
                                            class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                        <x-icon name="edit" style="fas" class="w-4 h-4 mr-1" />
                                        Edit
                                    </button>
                                    <button wire:click="toggleStatus({{ $registrar->id }})"
                                            class="text-green-600 hover:text-green-900 text-sm font-medium">
                                        {{ $registrar->status === 'active' ? 'Deactivate' : 'Activate' }}
                                    </button>
                                    <button wire:click="confirmDelete({{ $registrar->id }})"
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
        
        <x-tables.pagination :paginator="$registrars" />
    </div>
</div>