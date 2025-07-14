<div>
    <x-modals.modal-header 
        title="Edit Student"
        subtitle="Update student information and details." />

    <form wire:submit="save">
        <x-modals.modal-body>
            <div class="space-y-6">
                <!-- Personal Information Section -->
                <div>
                    <h4 class="text-lg font-medium text-gray-900 mb-4">Personal Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- First Name -->
                        <x-inputs.text-input 
                            id="first_name"
                            wire:model="first_name"
                            label="First Name"
                            placeholder="Enter first name"
                            required />

                        <!-- Middle Name -->
                        <x-inputs.text-input 
                            id="middle_name"
                            wire:model="middle_name"
                            label="Middle Name"
                            placeholder="Enter middle name (optional)" />

                        <!-- Last Name -->
                        <x-inputs.text-input 
                            id="last_name"
                            wire:model="last_name"
                            label="Last Name"
                            placeholder="Enter last name"
                            required />

                        <!-- Email -->
                        <x-inputs.text-input 
                            id="email"
                            wire:model="email"
                            label="Email Address"
                            type="email"
                            placeholder="Enter email address"
                            required />
                    </div>
                </div>

                <!-- Campus Assignment Section -->
                <div class="border-t border-gray-200 pt-6">
                    <h4 class="text-lg font-medium text-gray-900 mb-4">Campus Assignment</h4>
                    <div class="grid grid-cols-1 gap-4">
                        <!-- Campus -->
                        <x-inputs.select-input 
                            id="campus_id"
                            wire:model="campus_id"
                            label="Campus"
                            placeholder="Select campus"
                            :options="$campuses"
                            value-field="id"
                            text-field="name"
                            required />
                    </div>
                </div>

                <!-- Academic Information Section -->
                <div class="border-t border-gray-200 pt-6">
                    <h4 class="text-lg font-medium text-gray-900 mb-4">Academic Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Student ID -->
                        <x-inputs.text-input 
                            id="student_id"
                            wire:model="student_id"
                            label="Student ID"
                            placeholder="Enter student ID" />

                        <!-- ULI -->
                        <x-inputs.text-input 
                            id="uli"
                            wire:model="uli"
                            label="ULI (Unique Learner Identifier)"
                            placeholder="Enter ULI" />

                        <!-- Course -->
                        <x-inputs.select-input 
                            id="course_id"
                            wire:model="course_id"
                            label="Course"
                            placeholder="Select course"
                            :options="$courses"
                            value-field="id"
                            text-field="code"
                            required />

                        <!-- Academic Year -->
                        <x-inputs.select-input 
                            id="academic_year_id"
                            wire:model="academic_year_id"
                            label="Academic Year"
                            placeholder="Select academic year"
                            :options="$academicYears"
                            value-field="id"
                            text-field="formatted_description"
                            required />
                    </div>
                </div>
            </div>
        </x-modals.modal-body>

        <x-modals.modal-footer>
            <div class="flex justify-between w-full">
                <!-- Left side - Mark as Dropped button -->
                <div class="flex">
                    @if($student->user && $student->user->status !== 'dropped')
                        <x-buttons.danger-button wire:confirm="Are you sure you want to mark this student as dropped? This will update their user status and mark all pending assessments as dropped." 
                                                wire:click="markAsDropped"
                                                type="button">
                            <x-icon name="user-times" style="fas" class="w-4 h-4 mr-2" />
                            Mark as Dropped
                        </x-buttons.danger-button>
                    @endif
                </div>

                <!-- Right side - Action buttons -->
                <div class="flex space-x-3">
                    <x-buttons.secondary-button wire:click="$dispatch('closeModal')" type="button">
                        Cancel
                    </x-buttons.secondary-button>
                    <x-buttons.primary-button type="submit">
                        <x-icon name="save" style="fas" class="w-4 h-4 mr-2" />
                        Update Student
                    </x-buttons.primary-button>
                </div>
            </div>
        </x-modals.modal-footer>
    </form>
</div>
