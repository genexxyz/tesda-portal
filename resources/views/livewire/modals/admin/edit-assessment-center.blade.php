<div>
    <x-modals.modal-header 
        title="Edit Assessment Center"
        subtitle="Update assessment center information and assessor assignments." />

    <form wire:submit="save">
        <x-modals.modal-body>
            <div class="space-y-6">
                <!-- Name Field -->
                <div>
                    <x-inputs.text-input 
                        id="name"
                        wire:model="name"
                        label="Center Name"
                        placeholder="Enter assessment center name"
                        required />
                </div>

                <!-- Address Field -->
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                        Address <span class="text-red-500">*</span>
                    </label>
                    <textarea 
                        id="address"
                        wire:model="address"
                        rows="4"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Enter complete address of the assessment center"
                        required></textarea>
                    @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Include street, city, province, and postal code if applicable</p>
                </div>
                
                <!-- Assessors Assignment -->
                <div x-data="{ 
                    selectedAssessors: @entangle('selectedAssessors'),
                    availableAssessors: @js($availableAssessors->toArray()),
                    search: '',
                    isOpen: false,
                    
                    get selectedItems() {
                        return this.availableAssessors.filter(assessor => 
                            this.selectedAssessors.includes(assessor.id)
                        );
                    },
                    
                    get filteredAssessors() {
                        if (!this.search) return this.availableAssessors;
                        return this.availableAssessors.filter(assessor =>
                            assessor.name.toLowerCase().includes(this.search.toLowerCase())
                        );
                    },
                    
                    selectAssessor(assessor) {
                        if (!this.selectedAssessors.includes(assessor.id)) {
                            this.selectedAssessors.push(assessor.id);
                        } else {
                            this.removeAssessor(assessor.id);
                        }
                        this.search = '';
                    },
                    
                    removeAssessor(assessorId) {
                        this.selectedAssessors = this.selectedAssessors.filter(id => id !== assessorId);
                    },
                    
                    isSelected(assessorId) {
                        return this.selectedAssessors.includes(assessorId);
                    }
                }" class="relative">
                    
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Assign Assessors
                    </label>
                    
                    <!-- Selected Items Display -->
                    <div class="flex flex-wrap gap-2 mb-2" x-show="selectedItems.length > 0">
                        <template x-for="item in selectedItems" :key="item.id">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                <span x-text="item.name"></span>
                                <button 
                                    type="button"
                                    @click="removeAssessor(item.id)"
                                    class="ml-2 inline-flex items-center justify-center w-4 h-4 text-blue-400 hover:text-blue-600">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </span>
                        </template>
                    </div>
                    
                    <!-- Search Input -->
                    <div class="relative">
                        <input
                            type="text"
                            x-model="search"
                            @focus="isOpen = true"
                            @click.away="isOpen = false"
                            :placeholder="selectedItems.length > 0 ? 'Add more assessors...' : 'Search and select assessors...'"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            autocomplete="off">
                        
                        <!-- Dropdown Arrow -->
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                    
                    <!-- Dropdown Options -->
                    <div 
                        x-show="isOpen && filteredAssessors.length > 0"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto">
                        
                        <template x-for="assessor in filteredAssessors" :key="assessor.id">
                            <div 
                                @click="selectAssessor(assessor)"
                                class="px-3 py-2 cursor-pointer hover:bg-gray-100 flex items-center justify-between"
                                :class="{ 'bg-blue-50 text-blue-600': isSelected(assessor.id) }">
                                <span x-text="assessor.name"></span>
                                <svg x-show="isSelected(assessor.id)" class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </template>
                        
                        <!-- No results -->
                        <div x-show="filteredAssessors.length === 0 && search.length > 0" class="px-3 py-2 text-gray-500 text-sm">
                            No results found for "<span x-text="search"></span>"
                        </div>
                    </div>
                </div>
                
                @error('selectedAssessors')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Search and select multiple assessors to assign to this center.</p>

                <!-- Summary -->
                <div x-show="selectedItems.length > 0" class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <x-icon name="info-circle" style="fas" class="w-5 h-5 text-blue-400" />
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Assignment Summary</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <p><span x-text="selectedItems.length"></span> assessor<span x-show="selectedItems.length > 1">s</span> will be assigned to this center.</p>
                                <p class="mt-1 text-xs">Changes will update existing assessment assignments accordingly.</p>
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
                <span wire:loading.remove>
                    Update Center
                </span>
                <span wire:loading class="flex items-center">
                    <x-icon name="spinner" style="fas" class="w-4 h-4 mr-2 animate-spin" />
                    Updating...
                </span>
            </x-buttons.primary-button>
        </x-modals.modal-footer>
    </form>
</div>
