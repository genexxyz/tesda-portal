<div>
    <x-modals.modal-header 
        title="Edit Assessor"
        subtitle="Update assessor information and center assignments." />

    <form wire:submit="save">
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
                    <p class="mt-1 text-xs text-gray-500">Enter the complete name of the assessor</p>
                </div>

                <!-- Assessment Centers Assignment -->
                <div>
                    <label class="block pl-1 text-sm font-semibold text-gray-700 mb-2">
                        Assessment Center Assignment
                    </label>
                    <div class="space-y-2 max-h-64 overflow-y-auto border border-gray-200 rounded-lg p-3">
                        @forelse($assessmentCenters as $center)
                            <div class="flex items-start">
                                <input 
                                    type="checkbox"
                                    id="center_{{ $center->id }}"
                                    wire:model="selectedAssessmentCenters"
                                    value="{{ $center->id }}"
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded mt-1">
                                <label for="center_{{ $center->id }}" class="ml-3 flex-1">
                                    <div class="text-sm font-medium text-gray-900">{{ $center->name }}</div>
                                    <div class="text-xs text-gray-500 mt-1">{{ $center->address }}</div>
                                </label>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <x-icon name="building" style="fas" class="mx-auto h-12 w-12 text-gray-400" />
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No assessment centers</h3>
                                <p class="mt-1 text-sm text-gray-500">Create assessment centers first to assign to assessors.</p>
                            </div>
                        @endforelse
                    </div>
                    @error('selectedAssessmentCenters')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">
                        Select one or more assessment centers where this assessor will be assigned.
                    </p>
                </div>

                <!-- Info Box -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <x-icon name="info-circle" style="fas" class="w-5 h-5 text-blue-400" />
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Assignment Information</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <p>Updating assessor assignments will:</p>
                                <ul class="list-disc list-inside mt-1 space-y-1">
                                    <li>Sync the assessor with selected assessment centers</li>
                                    <li>Remove assignments from unselected centers</li>
                                    <li>Update existing assessment records accordingly</li>
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

            <x-buttons.primary-button type="submit" wire:loading.attr="disabled">
                Update Assessor
            </x-buttons.primary-button>
        </x-modals.modal-footer>
    </form>
</div>
