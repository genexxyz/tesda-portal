<div>
    <x-modals.modal-header 
        title="Add New Assessor"
        subtitle="Create a new assessor profile" />

    <x-modals.modal-body>
        <div class="space-y-6">
            <!-- Name Field -->
            <div>
                <x-inputs.text-input 
                    id="name"
                    wire:model="name"
                    label="Full Name"
                    placeholder="Enter assessor's full name"
                    required />
                <x-error for="name" />
                <p class="mt-1 text-xs text-gray-500">Enter the complete name of the assessor</p>
            </div>

            <!-- Info Box -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <x-icon name="info-circle" style="fas" class="w-5 h-5 text-blue-400" />
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Additional Information</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <p>After creating the assessor, you can:</p>
                            <ul class="list-disc list-inside mt-1 space-y-1">
                                <li>Assign them to assessment centers</li>
                                <li>Manage their assessment assignments</li>
                                <li>Edit their profile information</li>
                            </ul>
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
        
        <x-buttons.primary-button
            wire:click="save"
            wire:loading.attr="disabled">
            <span wire:loading.remove>Create Assessor</span>
            <span wire:loading class="flex items-center">
                <x-icon name="spinner" style="fas" class="w-4 h-4 mr-2 animate-spin" />
                Creating...
            </span>
        </x-buttons.primary-button>
    </x-modals.modal-footer>
</div>