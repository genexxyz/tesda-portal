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

                <!-- Academic Information Section -->
                <div class="border-t border-gray-200 pt-6">
                    <h4 class="text-lg font-medium text-gray-900 mb-4">Academic Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Student ID -->
                        <x-inputs.text-input 
                            id="student_id"
                            wire:model="student_id"
                            label="Student ID"
                            placeholder="Enter student ID (optional)" />

                        <!-- ULI -->
                        <x-inputs.text-input 
                            id="uli"
                            wire:model="uli"
                            label="ULI (Unique Learner Identifier)"
                            placeholder="Enter ULI (optional)" />

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
                            text-field="description"
                            required />
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="border-t border-gray-200 pt-4">
                    <div class="bg-blue-50 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <x-icon name="info-circle" style="fas" class="h-5 w-5 text-blue-400" />
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">
                                    <strong>Note:</strong> Student ID and ULI are optional but recommended for proper identification. 
                                    The course selection is limited to courses available at your campus.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </x-modals.modal-body>

        <x-modals.modal-footer>
            <x-buttons.secondary-button wire:click="closeModal">
                Cancel
            </x-buttons.secondary-button>

            <x-buttons.primary-button type="submit" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="save">Update Student</span>
                <span wire:loading wire:target="save" class="flex items-center">
                    <x-icon name="spinner" style="fas" class="w-4 h-4 mr-2 animate-spin" />
                    Updating...
                </span>
            </x-buttons.primary-button>
        </x-modals.modal-footer>
    </form>
</div>
