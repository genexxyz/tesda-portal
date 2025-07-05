<div>
    <x-partials.header title="Academic Year Management" breadcrumb="Academic Year" />
<x-buttons.floating-add-button 
    wire-click="$dispatch('openModal', { component: 'modals.admin.add-new-semester' })"
    tooltip="Add new semester" 
    icon="plus"
/>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <x-tables.table :is-empty="$academicYear->isEmpty()" empty-message="No academic years found">
            <thead class="bg-gray-50">
                <tr>
                    <x-tables.table-header>#</x-tables.table-header>
                    <x-tables.table-header>Academic Year</x-tables.table-header>
                    <x-tables.table-header>Semester</x-tables.table-header>
                    <x-tables.table-header>Active</x-tables.table-header>
                    <x-tables.table-header>Actions</x-tables.table-header>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($academicYear as $year)
                    <x-tables.table-row>
                        <x-tables.table-cell class="w-2"></x-tables.table-cell>
                        <x-tables.table-cell>{{ $year->start_year }} - {{ $year->end_year }}</x-tables.table-cell>
                        <x-tables.table-cell>{{ $year->semester }}</x-tables.table-cell>
                        <x-tables.table-cell>@if (
                                $year->is_active
                            )
                                <span class="text-green-600">Yes</span>
                        @else
                                <span class="text-red-600">No</span>

                            @endif</x-tables.table-cell>
                        <x-tables.table-cell>
                            <button class="text-blue-600 hover:text-blue-900">Edit</button>
                        </x-tables.table-cell>
                    </x-tables.table-row>
                @endforeach
            </tbody>
        </x-tables.table>
        <x-tables.pagination :paginator="$academicYear" />
    </div>
</div>