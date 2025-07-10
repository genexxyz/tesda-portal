<div>
    <x-partials.header title="Campus Management" breadcrumb="Campuses" />
    
    <x-buttons.floating-add-button 
        wire-click="$dispatch('openModal', { component: 'modals.admin.add-new-campus' })"
        tooltip="Add new campus" 
        icon="plus"
    />
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <x-tables.table :is-empty="$campuses->isEmpty()" empty-message="No campuses found">
            <thead class="bg-gray-50">
                <tr>
                    <x-tables.table-header>#</x-tables.table-header>
                    <x-tables.table-header>Code</x-tables.table-header>
                    <x-tables.table-header>Campus Name</x-tables.table-header>
                    <x-tables.table-header>Number</x-tables.table-header>
                    <x-tables.table-header>Color</x-tables.table-header>
                    <x-tables.table-header>Actions</x-tables.table-header>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($campuses as $campus)
                    <x-tables.table-row wire:key="campus-{{ $campus->id }}">
                        <x-tables.table-cell class="w-2">
                            {{ $loop->iteration }}
                        </x-tables.table-cell>
                        <x-tables.table-cell>
                            <span class="font-medium text-gray-900">{{ $campus->code }}</span>
                        </x-tables.table-cell>
                        <x-tables.table-cell>{{ $campus->name }}</x-tables.table-cell>
                        <x-tables.table-cell>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $campus->number }}
                            </span>
                        </x-tables.table-cell>
                        <x-tables.table-cell>
                            <div class="flex items-center space-x-2">
                                <div class="w-4 h-4 rounded-full border border-gray-300" 
                                     style="background-color: {{ $campus->color }}"></div>
                                <span class="text-sm text-gray-600">{{ $campus->color }}</span>
                            </div>
                        </x-tables.table-cell>
                        <x-tables.table-cell>
                            <div class="flex items-center space-x-2">
                                <button wire:click="$dispatch('openModal', { component: 'modals.admin.edit-campus', arguments: { campusId: {{ $campus->id }} } })"
                                        class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                    <x-icon name="edit" style="fas" class="w-4 h-4 mr-1" />
                                    Edit
                                </button>
                                <button wire:click="confirmDelete({{ $campus->id }})"
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
        
        <x-tables.pagination :paginator="$campuses" />
    </div>
</div>