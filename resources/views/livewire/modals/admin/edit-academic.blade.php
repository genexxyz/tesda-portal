<div>
    <x-modals.modal-header 
        title="Edit Academic Year"
        subtitle="Update academic year information." />

    <form wire:submit="save">
        <x-modals.modal-body>
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4 max-w-lg">
                    <!-- Start Year Field -->
                    <x-inputs.select-input 
                        wire:model="start_year" 
                        label="Start Year" 
                        id="start_year"
                        placeholder="Select Start Year" 
                        :options="range(2025, 2035)" 
                        required />
                    
                    <!-- End Year Field -->
                    <x-inputs.select-input 
                        wire:model="end_year" 
                        id="end_year" 
                        placeholder="Select End Year" 
                        label="End Year"
                        :options="range(2025, 2035)" 
                        required />
                </div>

                <!-- Semester Field -->
                <x-inputs.select-input 
                    id="semester" 
                    wire:model="semester" 
                    label="Semester"
                    placeholder="Select Semester" 
                    :options="['1st Semester', '2nd Semester', 'Summer']" 
                    required />

                <!-- Active Field -->
                <div class="flex items-center">
                    <input 
                        type="checkbox" 
                        id="is_active" 
                        wire:model="is_active"
                        class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                    <label for="is_active" class="ml-2 block text-sm text-gray-700">
                        Set as active academic year
                    </label>
                </div>
                <p class="text-xs text-gray-500 ml-6">
                    Setting this as active will deactivate all other academic years.
                </p>
            </div>
        </x-modals.modal-body>

        <x-modals.modal-footer>
            <x-buttons.secondary-button wire:click="closeModal">
                Cancel
            </x-buttons.secondary-button>

            <x-buttons.primary-button type="submit" wire:loading.attr="disabled">
                Update Academic Year
            </x-buttons.primary-button>
        </x-modals.modal-footer>
    </form>
</div>
