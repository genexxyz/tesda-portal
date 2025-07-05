<div>
    <x-modals.modal-header 
        title="Add New Program Head"
        subtitle="Create a new program head account and assign courses." />

    <form wire:submit="save">
        <x-modals.modal-body>
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

                <!-- Campus -->
                <div class="md:col-span-2">
                    <x-inputs.select-input 
                        id="campus_id"
                        wire:model.live="campus_id"
                        label="Campus Assignment"
                        placeholder="Select campus"
                        :options="$campuses"
                        value-field="id"
                        text-field="name"
                        required />
                </div>
            </div>

            <!-- Course Assignment Section -->
            <div class="mt-6 border-t pt-6">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-medium text-gray-900">Course Assignment</h4>
                    @if(!empty($availableCourses) && count($availableCourses) > 0)
                        <div class="text-sm text-gray-500">
                            {{ count($course_ids) }} of {{ count($availableCourses) }} selected
                        </div>
                    @endif
                </div>
                
                @if(empty($availableCourses) || count($availableCourses) == 0)
                    <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <x-icon name="exclamation-triangle" style="fas" class="w-5 h-5 text-yellow-400" />
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    @if($campus_id)
                                        No unassigned courses available for the selected campus. All courses are already assigned to other program heads.
                                    @else
                                        Please select a campus first to see available courses.
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Select All / Deselect All -->
                    <div class="mb-3 flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <button type="button" 
                                    wire:click="$set('course_ids', {{ collect($availableCourses)->pluck('id')->toJson() }})"
                                    class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                Select All
                            </button>
                            <button type="button" 
                                    wire:click="$set('course_ids', [])"
                                    class="text-sm text-gray-600 hover:text-gray-800 font-medium">
                                Clear All
                            </button>
                        </div>
                    </div>

                    <!-- Simple Course List -->
                    <div class="space-y-2 max-h-60 overflow-y-auto border border-gray-200 rounded-md p-3 bg-gray-50">
                        @foreach($availableCourses as $course)
                            <label class="flex items-center space-x-3 p-2 hover:bg-white rounded-md cursor-pointer transition-colors">
                                <input type="checkbox" 
                                       wire:model.live="course_ids" 
                                       value="{{ is_array($course) ? $course['id'] : $course->id }}"
                                       class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ is_array($course) ? $course['code'] : $course->code }}
                                    </div>
                                    <div class="text-xs text-gray-600">
                                        {{ is_array($course) ? $course['name'] : $course->name }}
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    @error('course_ids')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                @endif
            </div>

            <!-- Info Box -->
            <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <x-icon name="info-circle" style="fas" class="w-5 h-5 text-blue-400" />
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            <strong>Program Head Role:</strong> This user will manage and oversee the selected courses within their assigned campus.
                        </p>
                    </div>
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
                Create Program Head
            </x-buttons.primary-button>
        </x-modals.modal-footer>
    </form>
</div>