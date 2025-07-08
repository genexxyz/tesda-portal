<div>
    <x-modals.modal-header 
        title="Edit Course"
        subtitle="Update course information." />

    <form wire:submit="save">
        <x-modals.modal-body>
            <div class="space-y-4">
                <!-- Course Code Field -->
                <x-inputs.text-input 
                    id="code"
                    wire:model="code"
                    label="Course Code"
                    placeholder="e.g., SMAW, HRS,..."
                    required />

                <!-- Course Name Field -->
                <x-inputs.text-input 
                    id="name"
                    wire:model="name"
                    label="Course Name"
                    placeholder="e.g., SHIELDED METAL ARC WELDING"
                    required />

                <!-- Campus Assignment Field -->
                <div>
                    <label for="selectedCampuses" class="block pl-1 text-sm font-semibold text-gray-700 mb-1">
                        Campus Assignment
                    </label>
                    <div class="space-y-2">
                        @foreach($campuses as $campus)
                            <div class="flex items-center">
                                <input 
                                    type="checkbox"
                                    id="campus_{{ $campus->id }}"
                                    wire:model="selectedCampuses"
                                    value="{{ $campus->id }}"
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="campus_{{ $campus->id }}" class="ml-2 flex items-center">
                                    <div class="w-3 h-3 rounded-full mr-2" style="background-color: {{ $campus->color }}"></div>
                                    <span class="text-sm text-gray-700">{{ $campus->name }}</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                    @error('selectedCampuses')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">
                        Select one or more campuses where this course will be available.
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
                Update Course
            </x-buttons.primary-button>
        </x-modals.modal-footer>
    </form>
</div>
