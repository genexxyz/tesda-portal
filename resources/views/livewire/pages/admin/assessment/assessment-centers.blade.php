<div>
    <x-buttons.floating-add-button 
        wire-click="$dispatch('openModal', { component: 'modals.admin.add-new-assessment-center' })"
        tooltip="Add new assessment center" 
        icon="building"
    />
    
    <!-- Search Bar -->
    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Search Input -->
        <div>
            <x-inputs.search-input 
                id="search"
                wire:model.live="search"
                placeholder="Search centers by name or address..." />
        </div>
    </div>

    <!-- Active Filters Display -->
    @if($search)
        <div class="mb-4">
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-sm font-medium text-gray-700">Active filters:</span>
                
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    <x-icon name="search" style="fas" class="w-3 h-3 mr-1" />
                    Search: "{{ $search }}"
                    <button wire:click="$set('search', '')" class="ml-2 text-blue-600 hover:text-blue-800">
                        <x-icon name="times" style="fas" class="w-3 h-3" />
                    </button>
                </span>

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
        <div wire:loading.delay wire:target="search" 
             class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-10 rounded-lg">
            <div class="flex items-center space-x-2">
                <x-icon name="spinner" style="fas" class="w-5 h-5 text-primary animate-spin" />
                <span class="text-sm text-gray-600">Searching...</span>
            </div>
        </div>

        <x-tables.table :is-empty="$assessmentCenters->isEmpty()" empty-message="No assessment centers found matching your filters">
            <thead class="bg-gray-50">
                <tr>
                    <x-tables.table-header>#</x-tables.table-header>
                    <x-tables.table-header>Center Name</x-tables.table-header>
                    <x-tables.table-header>Address</x-tables.table-header>
                    <x-tables.table-header>Assigned Assessors</x-tables.table-header>
                    <x-tables.table-header>Created</x-tables.table-header>
                    <x-tables.table-header>Actions</x-tables.table-header>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($assessmentCenters as $center)
                    <x-tables.table-row wire:key="center-{{ $center->id }}">
                        <x-tables.table-cell class="w-16">
                            {{ $loop->iteration }}
                        </x-tables.table-cell>
                        <x-tables.table-cell>
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                        <x-icon name="building" style="fas" class="w-5 h-5 text-green-700" />
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $center->name }}
                                    </div>
                                </div>
                            </div>
                        </x-tables.table-cell>
                        <x-tables.table-cell>
                            <div class="text-sm text-gray-900">
                                {{ $center->address }}
                            </div>
                        </x-tables.table-cell>
                        <x-tables.table-cell>
                            @if($center->assessors_count > 0)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <x-icon name="user-tie" style="fas" class="w-3 h-3 mr-1" />
                                    {{ $center->assessors_count }} assessor{{ $center->assessors_count > 1 ? 's' : '' }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                    <x-icon name="minus-circle" style="fas" class="w-3 h-3 mr-1" />
                                    No assessors assigned
                                </span>
                            @endif
                        </x-tables.table-cell>
                        <x-tables.table-cell>
                            <div class="text-sm text-gray-500">
                                {{ $center->created_at->format('M d, Y') }}
                            </div>
                        </x-tables.table-cell>
                        <x-tables.table-cell>
                            <div class="flex items-center space-x-2">
                                <button wire:click="$dispatch('openModal', { component: 'modals.admin.edit-assessment-center', arguments: { assessmentCenterId: {{ $center->id }} } })"
                                        class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                    <x-icon name="edit" style="fas" class="w-4 h-4 mr-1" />
                                    Edit
                                </button>
                                <button wire:click="confirmDelete({{ $center->id }})"
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
    
    <x-tables.pagination :paginator="$assessmentCenters" />
</div>