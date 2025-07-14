<div>
    <x-modals.modal-header 
        title="Add New Qualification Type"
        subtitle="Create a new qualification type for the system." />

    <form wire:submit="save">
        <x-modals.modal-body>
            <div class="space-y-4">
                <!-- Qualification Code Field -->
                <div>
                    <x-inputs.text-input 
                        id="code"
                        wire:model="code"
                        label="Qualification Code"
                        placeholder="e.g., BKP, SMAW, FBS"
                        required />
                    <x-error for="code" />
                </div>

                <!-- Qualification Name Field -->
                <div>
                    <x-inputs.text-input 
                        id="name"
                        wire:model="name"
                        label="Qualification Name"
                        placeholder="e.g., Food and Beverage Services"
                        required />
                    <x-error for="name" />
                </div>

                <!-- Level Field -->
                <div>
                    <label for="level" class="block pl-1 text-sm font-semibold text-gray-700 mb-1">
                        Level <span class="text-red-500 ml-1">*</span>
                    </label>
                    <select 
                        id="level"
                        wire:model="level"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        required>
                        <option value="">Select Level</option>
                        <option value="NC I">NC I</option>
                        <option value="NC II">NC II</option>
                        <option value="NC III">NC III</option>
                        <option value="NC IV">NC IV</option>
                    </select>
                    <x-error for="level" />
                </div>

                <!-- Description Field -->
                <div>
                    <label for="description" class="block pl-1 text-sm font-semibold text-gray-700 mb-1">
                        Description
                    </label>
                    <textarea 
                        id="description"
                        wire:model="description"
                        rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Brief description of the qualification type..."></textarea>
                    <x-error for="description" />
                </div>

                <!-- Course Assignment Field -->
                <div>
                    <label for="selectedCourses" class="block pl-1 text-sm font-semibold text-gray-700 mb-1">
                        Course Assignment <span class="text-gray-500 text-xs">(Optional)</span>
                    </label>
                    <div class="space-y-2 max-h-60 overflow-y-auto border border-gray-200 rounded-md p-3 bg-gray-50">
                        @forelse($courses as $course)
                            <div class="flex items-center">
                                <input 
                                    type="checkbox"
                                    id="course_{{ $course->id }}"
                                    wire:model="selectedCourses"
                                    value="{{ $course->id }}"
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="course_{{ $course->id }}" class="ml-2 flex items-center">
                                    <span class="text-sm font-medium text-gray-700">{{ $course->code }}</span>
                                    <span class="ml-2 text-xs text-gray-500">{{ $course->name }}</span>
                                </label>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No courses available</p>
                        @endforelse
                    </div>
                    <x-error for="selectedCourses" />
                    <p class="text-xs text-gray-500 mt-1">
                        Select which courses this qualification type applies to.
                    </p>
                </div>
            </div>
        </x-modals.modal-body>

        <x-modals.modal-footer>
            <x-buttons.secondary-button wire:click="closeModal">
                Cancel
            </x-buttons.secondary-button>
            
            <x-buttons.primary-button
                type="submit"
                wire:loading.attr="disabled">
                <span wire:loading.remove>Create Qualification Type</span>
                <span wire:loading>Creating...</span>
            </x-buttons.primary-button>
        </x-modals.modal-footer>
    </form>
</div>