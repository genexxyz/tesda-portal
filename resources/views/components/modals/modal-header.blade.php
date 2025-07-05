@props([
    'title' => null,
    'subtitle' => null,
    'showClose' => true
])

<div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
    <div class="flex-1">
        @if($title)
            <h3 class="text-lg font-medium text-gray-900">
                {{ $title }}
            </h3>
        @endif
        
        @if($subtitle)
            <p class="mt-1 text-sm text-gray-600">
                {{ $subtitle }}
            </p>
        @endif
        
        <!-- Custom content slot -->
        {{ $slot }}
    </div>
    
    @if($showClose)
        <button type="button" 
                wire:click="closeModal"
                class="cursor-pointer ml-4 bg-white rounded-md text-gray-400 hover:text-gray-600 transition-colors duration-200">
            <span class="sr-only">Close</span>
            <x-icon name="times" style="fas" class="w-5 h-5" />
        </button>
    @endif
</div>