<div>
    <x-modals.modal-header 
        title="Add New Course"
        subtitle="Create a new course for the system." />

    <form wire:submit="save">
        <x-modals.modal-body>
            <div class="space-y-4">
                <!-- Course Code Field -->
                <x-inputs.text-input 
                    id="code"
                    wire:model="code"
                    label="Course Code"
                    placeholder="e.g., SMAW NC I, CSS NC II"
                    required />

                <!-- Course Name Field -->
                <x-inputs.text-input 
                    id="name"
                    wire:model="name"
                    label="Course Name"
                    placeholder="e.g., SHIELDED METAL ARC WELDING"
                    required />

                <!-- Campus Assignment Field -->
                <x-inputs.select-input 
                    id="campus_id"
                    wire:model="campus_id"
                    label="Campus Assignment"
                    placeholder="Select Campus"
                    :options="$campuses"
                    value-field="id"
                    text-field="name"
                    required />

                <!-- Preview -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Preview:</h4>
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0 h-10 w-10">
                            <div class="h-10 w-10 rounded-lg bg-blue-100 flex items-center justify-center">
                                <x-icon name="graduation-cap" style="fas" class="w-5 h-5 text-blue-600" />
                            </div>
                        </div>
                        <div>
                            <div class="font-medium text-gray-900">
                                {{ $code ?: 'COURSE CODE' }}
                            </div>
                            <div class="text-sm text-gray-600">
                                {{ $name ?: 'Course Name' }}
                            </div>
                        </div>
                        @if($campus_id)
                            @php $selectedCampus = $campuses->find($campus_id); @endphp
                            @if($selectedCampus)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                                      style="background-color: {{ $selectedCampus->color }}20; color: {{ $selectedCampus->color }}; border: 1px solid {{ $selectedCampus->color }}40;">
                                    <div class="w-2 h-2 rounded-full mr-1" style="background-color: {{ $selectedCampus->color }}"></div>
                                    {{ $selectedCampus->name }}
                                </span>
                            @endif
                        @endif
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
                Create Course
            </x-buttons.primary-button>
        </x-modals.modal-footer>
    </form>
</div>