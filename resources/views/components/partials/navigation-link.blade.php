<x-partials.nav-link
    :href="route($route)"
    :active="$isActive()"
    wire:navigate
    class="flex items-center px-4 py-3 text-white hover:bg-primary-700 hover:bg-opacity-80 smooth-transition whitespace-nowrap overflow-hidden group">
    
    <x-icon :name="$icon"
            :style="$iconStyle"
            class="w-5 h-5 text-lg flex-shrink-0"
            x-bind:class="(isHovered || isMobile) ? 'mr-3' : ''"/>
    
    <span x-show="isHovered || isMobile"
          x-transition:enter="transition-opacity duration-300"
          x-transition:enter-start="opacity-0"
          x-transition:enter-end="opacity-100"
          class="text-sm font-medium">
        {{ $label }}
    </span>
</x-partials.nav-link>