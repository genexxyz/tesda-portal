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
                <x-inputs.select-input 
                    id="campus_id"
                    wire:model="campus_id"
                    label="Campus Assignment"
                    placeholder="Select Campus"
                    :options="$campuses"
                    value-field="id"
                    text-field="name"
                    required />

                
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