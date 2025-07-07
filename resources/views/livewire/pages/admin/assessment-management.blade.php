<div>
    <x-partials.header title="Assessment Management" breadcrumb="Assessment" />
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Tab Navigation -->
        <div class="mb-8">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button wire:click="setActiveTab('assessors')"
                            class="{{ $activeTab === 'assessors' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer">
                        <div class="flex items-center">
                            <x-icon name="user-tie" style="fas" class="w-5 h-5 mr-2" />
                            Assessors
                        </div>
                    </button>
                    
                    <button wire:click="setActiveTab('assessment-centers')"
                            class="{{ $activeTab === 'assessment-centers' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer">
                        <div class="flex items-center">
                            <x-icon name="building" style="fas" class="w-5 h-5 mr-2" />
                            Assessment Centers
                        </div>
                    </button>
                </nav>
            </div>
        </div>

        <!-- Tab Content -->
        <div class="mt-6">
            @if($activeTab === 'assessors')
                <livewire:pages.admin.assessment.assessors />
            @elseif($activeTab === 'assessment-centers')
                <livewire:pages.admin.assessment.assessment-centers />
            @endif
        </div>
    </div>
</div>