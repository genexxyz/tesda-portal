Primary Button

<x-buttons.primary-button type="submit">Save</x-buttons.primary-button>
<x-buttons.primary-button wire:click="doSomething">Click Me</x-buttons.primary-button>
<x-buttons.primary-button :disabled="true">Disabled</x-buttons.primary>


    Import Buttons
<x-import-export-buttons 
    import-modal="modals.admin.import-users"
    :show-export="false" />

<!-- Only export -->
<x-import-export-buttons 
    export-method="exportData"
    :show-import="false" />

<!-- Both with custom tooltips -->
<x-import-export-buttons 
    import-modal="modals.admin.import-courses"
    export-method="exportCourses"
    import-tooltip="Upload course data from Excel"
    export-tooltip="Download course list as CSV" />


Basic link
<x-link href="/dashboard">
    Go to Dashboard
</x-link>

{{-- External link with icon --}}
<x-link href="https://example.com" external target="_blank">
    Visit Example Site
</x-link>

{{-- Different colors --}}
<x-link href="#" color="secondary">Secondary Link</x-link>
<x-link href="#" color="success">Success Link</x-link>
<x-link href="#" color="danger">Danger Link</x-link>

{{-- Disabled state --}}
<x-link href="#" disabled>
    Disabled Link
</x-link>

{{-- With custom classes --}}
<x-link href="#" class="text-lg underline">
    Custom Styled Link
</x-link>




Text Input
<x-text-input wire:model="email" label="Email" type="email" id="email" placeholder="Enter your email" required
    autofocus />



Password Input
<x-inputs.password-input wire:model="password" label="Password" id="password" placeholder="Enter your password"
    required />

Select Input
<x-inputs.select-input 
    id="semester"
    wire:model="semester"
    label="Semester"
    placeholder="Select Semester"
    :options="['1st Semester', '2nd Semester', 'Summer']"
    required />
<!-- In your Livewire component -->
{{-- $roles = Role::all(); --}}

<x-inputs.select-input 
    id="role_id"
    wire:model="role_id"
    label="User Role"
    placeholder="Select a role"
    :options="$roles"
    required />

    <!-- For a model with different field names -->
{{-- $courses = Course::all(); // has 'course_id' and 'course_name' fields --}}

<x-inputs.select-input 
    id="course_id"
    wire:model="course_id"
    label="Course"
    placeholder="Select a course"
    :options="$courses"
    value-field="course_id"
    text-field="course_name"
    required />
    
    <x-inputs.select-input 
    id="status"
    wire:model="status"
    label="Status"
    placeholder="Select status"
    :options="[
        ['id' => 'active', 'name' => 'Active'],
        ['id' => 'inactive', 'name' => 'Inactive'],
        ['id' => 'pending', 'name' => 'Pending']
    ]"
    required />




Icon
<x-icon name="circle-notch" size="lg" color="blue-600" spin class="mr-2" />



Header
<!-- Simple title -->
<x-partials.header title="Admin Dashboard" />

<!-- With breadcrumb -->
<x-partials.header 
    title="User Management" 
    breadcrumb="Users" 
/>

<!-- Title only (no breadcrumb) -->
<x-partials.header title="Settings" />




Table
<!-- Basic Table -->
<x-tables.table :is-empty="$academicYear->isEmpty()" empty-message="No academic years found">
    <thead class="bg-gray-50">
        <tr>
            <x-tables.table-header>Academic Year</x-tables.table-header>
            <x-tables.table-header>Semester</x-tables.table-header>
            <x-tables.table-header>Actions</x-tables.table-header>
        </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">
        @foreach($academicYear as $year)
            <x-tables.table-row>
                <x-tables.table-cell>{{ $year->academic_year }}</x-tables.table-cell>
                <x-tables.table-cell>{{ $year->semester }}</x-tables.table-cell>
                <x-tables.table-cell>
                    <button class="text-blue-600 hover:text-blue-900">Edit</button>
                </x-tables.table-cell>
            </x-tables.table-row>
        @endforeach
    </tbody>
</x-tables.table>

<!-- Table with Sorting -->
<x-tables.table>
    <thead class="bg-gray-50">
        <tr>
            <x-tables.table-header 
                sortable 
                wire:click="sortBy('name')"
                :sort-direction="$sortField === 'name' ? $sortDirection : null"
                :sort-field="$sortField === 'name'">
                Name
            </x-tables.table-header>
            <x-tables.table-header 
                sortable 
                wire:click="sortBy('created_at')"
                :sort-direction="$sortField === 'created_at' ? $sortDirection : null"
                :sort-field="$sortField === 'created_at'">
                Created At
            </x-tables.table-header>
        </tr>
    </thead>
</x-tables.table>

<!-- Clickable Rows -->
<x-tables.table>
    <tbody>
        <x-tables.table-row clickable href="{{ route('users.show', $user) }}">
            <x-tables.table-cell>{{ $user->name }}</x-tables.table-cell>
        </x-tables.table-row>
    </tbody>
</x-tables.table>

<!-- Custom Styling -->
<x-tables.table :striped="false" :hover="false" bordered>
    <tbody>
        <x-tables.table-row>
            <x-tables.table-cell align="center" nowrap width="100px">
                Center Aligned
            </x-tables.table-cell>
            <x-tables.table-cell align="right">
                Right Aligned
            </x-tables.table-cell>
        </x-tables.table-row>
    </tbody>
</x-tables.table>

<x-tables.table :show-numbers="false">
    <!-- table content -->
</x-tables.table>



Floating Add Button
<x-buttons.floating-add-button 
    href="{{ route('users.create') }}" 
    tooltip="Add New User" 
/>

<!-- Livewire action -->
<x-buttons.floating-add-button 
    wire-click="openCreateModal" 
    tooltip="Create Assessment" 
    icon="clipboard-list"
/>

<!-- Different icon -->
<x-buttons.floating-add-button 
    href="{{ route('courses.create') }}" 
    tooltip="Add New Course" 
    icon="graduation-cap"
/>

<x-buttons.floating-add-button
    tooltip="Add Student" 
    @click="showModal = true"
/>



Modal Usage
<div>
    <x-modals.modal-header 
        title="Delete User"
        subtitle="This action cannot be undone." />

    <x-modals.modal-body>
        <div class="flex items-center space-x-3">
            <div class="flex-shrink-0">
                <x-icon name="exclamation-triangle" style="fas" class="w-8 h-8 text-red-500" />
            </div>
            <div>
                <p class="text-sm text-gray-700">
                    Are you sure you want to delete <strong>{{ $user->name }}</strong>? 
                    All of their data will be permanently removed from our servers forever.
                </p>
            </div>
        </div>
    </x-modals.modal-body>

    <x-modals.modal-footer alignment="between">
        <p class="text-xs text-gray-500">
            <x-icon name="warning" style="fas" class="w-3 h-3 mr-1" />
            This action is irreversible
        </p>
        <div class="flex space-x-3">
            <button type="button" 
                    wire:click="closeModal"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                Cancel
            </button>
            <button type="button"
                    wire:click="deleteUser"
                    class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700">
                Delete User
            </button>
        </div>
    </x-modals.modal-footer>
</div>