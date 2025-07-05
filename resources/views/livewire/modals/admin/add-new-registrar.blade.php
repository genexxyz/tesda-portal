<div>
    <x-modals.modal-header 
        title="Add New Registrar"
        subtitle="Create a new registrar account for the system." />

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

                
            </div>

            <!-- Info Box -->
            <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <x-icon name="info-circle" style="fas" class="w-5 h-5 text-blue-400" />
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            <strong>Registrar Role:</strong> This user will have access to student management, enrollment, and academic records.
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
                Create Registrar
            </x-buttons.primary-button>
        </x-modals.modal-footer>
    </form>
</div>