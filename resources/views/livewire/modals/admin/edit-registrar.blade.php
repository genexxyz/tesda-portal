<div>
    <x-modals.modal-header 
        title="Edit Registrar"
        subtitle="Update registrar information." />

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
                <x-inputs.select-input 
                    id="campus_id"
                    wire:model="campus_id"
                    label="Campus Assignment"
                    placeholder="Select campus"
                    :options="$campuses"
                    value-field="id"
                    text-field="name"
                    required />

                <!-- Status -->
                <x-inputs.select-input 
                    id="status"
                    wire:model="status"
                    label="Status"
                    placeholder="Select status"
                    :options="[
                        ['value' => 'active', 'text' => 'Active'],
                        ['value' => 'inactive', 'text' => 'Inactive']
                    ]"
                    value-field="value"
                    text-field="text"
                    required />
            </div>

            <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <x-icon name="info-circle" style="fas" class="h-5 w-5 text-blue-400" />
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            <strong>Note:</strong> The registrar's password will remain unchanged. 
                            If password reset is needed, please contact the system administrator.
                        </p>
                    </div>
                </div>
            </div>
        </x-modals.modal-body>

        <x-modals.modal-footer>
            <x-buttons.secondary-button wire:click="closeModal">
                Cancel
            </x-buttons.secondary-button>

            <x-buttons.primary-button type="submit" wire:loading.attr="disabled">
                Update Registrar
            </x-buttons.primary-button>
        </x-modals.modal-footer>
    </form>
</div>
