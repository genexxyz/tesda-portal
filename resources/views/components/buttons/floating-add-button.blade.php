@props([
    'href' => null,
    'wireClick' => null,
    'tooltip' => 'Add new item',
    'icon' => 'plus'
])

@php
$buttonClasses = "fixed bottom-6 right-6 w-16 h-16 bg-primary hover:bg-primary/70 text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-300 ease-in-out transform z-50 flex items-center justify-center cursor-pointer";
@endphp

<div class="relative">
    @if($href)
        <a href="{{ $href }}" 
           wire:navigate
           class="{{ $buttonClasses }}"
           x-data="{ showTooltip: false }"
           @mouseenter="showTooltip = true"
           @mouseleave="showTooltip = false">
            <x-icon :name="$icon" style="fas" class="w-6 h-6 mt-1.5" />
            
            <!-- Tooltip -->
            <div x-show="showTooltip"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-x-2"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-x-0"
                 x-transition:leave-end="opacity-0 translate-x-2"
                 class="absolute right-full mr-3 top-1/2 transform -translate-y-1/2 bg-gray-900 text-white text-sm px-3 py-2 rounded-lg whitespace-nowrap">
                {{ $tooltip }}
                <!-- Tooltip arrow -->
                <div class="absolute left-full top-1/2 transform -translate-y-1/2 w-0 h-0 border-l-4 border-l-gray-900 border-t-4 border-t-transparent border-b-4 border-b-transparent"></div>
            </div>
        </a>
    @elseif($wireClick)
        <button wire:click="{{ $wireClick }}"
                class="{{ $buttonClasses }}"
                x-data="{ showTooltip: false }"
                @mouseenter="showTooltip = true"
                @mouseleave="showTooltip = false">
            <x-icon :name="$icon" style="fas" class="w-6 h-6 mt-1.5" />
            
            <!-- Tooltip -->
            <div x-show="showTooltip"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-x-2"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-x-0"
                 x-transition:leave-end="opacity-0 translate-x-2"
                 class="absolute right-full mr-3 top-1/2 transform -translate-y-1/2 bg-gray-900 text-white text-sm px-3 py-2 rounded-lg whitespace-nowrap">
                {{ $tooltip }}
                <!-- Tooltip arrow -->
                <div class="absolute left-full top-1/2 transform -translate-y-1/2 w-0 h-0 border-l-4 border-l-gray-900 border-t-4 border-t-transparent border-b-4 border-b-transparent"></div>
            </div>
        </button>
    @else
        <button {{ $attributes->merge(['class' => $buttonClasses]) }}
                x-data="{ showTooltip: false }"
                @mouseenter="showTooltip = true"
                @mouseleave="showTooltip = false">
            <x-icon :name="$icon" style="fas" class="w-6 h-6 -mt-0.5" />
            
            <!-- Tooltip -->
            <div x-show="showTooltip"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-x-2"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-x-0"
                 x-transition:leave-end="opacity-0 translate-x-2"
                 class="absolute right-full mr-3 top-1/2 transform -translate-y-1/2 bg-gray-900 text-white text-sm px-3 py-2 rounded-lg whitespace-nowrap">
                {{ $tooltip }}
                <!-- Tooltip arrow -->
                <div class="absolute left-full top-1/2 transform -translate-y-1/2 w-0 h-0 border-l-4 border-l-gray-900 border-t-4 border-t-transparent border-b-4 border-b-transparent"></div>
            </div>
        </button>
    @endif
</div>