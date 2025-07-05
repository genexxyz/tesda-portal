<div>
    <x-modals.modal-header 
        title="Add New Campus"
        subtitle="Create a new campus for the system." />

    <form wire:submit="save">
        <x-modals.modal-body>
            <div class="space-y-4">
                <!-- Campus Code Field -->
                <x-inputs.text-input 
                    id="code"
                    wire:model="code"
                    label="Campus Code"
                    placeholder="e.g., MA, BG"
                    required />

                <!-- Campus Name Field -->
                <x-inputs.text-input 
                    id="name"
                    wire:model="name"
                    label="Campus Name"
                    placeholder="e.g., MALOLOS, BULACAN"
                    required />

                <!-- Campus Number Field -->
                <x-inputs.text-input 
                    id="number"
                    wire:model="number"
                    label="Campus Number"
                    type="number"
                    placeholder="e.g., 1, 2, 3"
                    required />

                <!-- Color Field -->
                <div>
                    <label for="color" class="block pl-1 text-sm font-semibold text-gray-700 mb-1">
                        Campus Color <span class="text-red-500 ml-1">*</span>
                    </label>
                    <div class="flex items-center space-x-3">
                        <input type="color" 
                               id="color"
                               wire:model="color"
                               class="h-10 w-20 rounded border border-gray-300 cursor-pointer">
                        <x-inputs.text-input 
                            wire:model="color"
                            placeholder="#FF5733"
                            class="flex-1" />
                    </div>
                    @error('color')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">
                        Choose a color to represent this campus in the system.
                    </p>
                </div>

                <!-- Preview -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Preview:</h4>
                    <div class="flex items-center space-x-3">
                        <div class="w-6 h-6 rounded-full border border-gray-300" 
                             style="background-color: {{ $color }}"></div>
                        <span class="font-medium">{{ $code ?: 'CODE' }}</span>
                        <span>-</span>
                        <span>{{ $name ?: 'Campus Name' }}</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            #{{ $number ?: '0' }}
                        </span>
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
                Create Campus
            </x-buttons.primary-button>
        </x-modals.modal-footer>
    </form>
</div>