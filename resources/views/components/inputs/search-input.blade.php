@props([
    'disabled' => false,
    'label' => null,
    'placeholder' => 'Search...',
    'error' => null,
    'showIcon' => true,
    'showClearButton' => true
])

<div class="relative mb-3">
    @if($label)
        <label for="{{ $attributes->get('id') }}" class="block pl-1 text-sm font-semibold text-gray-700 mb-1">
            {{ $label }}
        </label>
    @endif

    <div class="relative">
        @if($showIcon)
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <x-icon name="search" style="fas" class="w-4 h-4 text-gray-400" />
            </div>
        @endif

        <input placeholder="{{ $placeholder }}"
            type="text"
            {{ $disabled ? 'disabled' : '' }}
            {!! $attributes->merge([
                'class' => 'w-full text-sm font-medium border-2 border-gray-400 rounded-md h-10 focus:border-primary focus:ring-primary' . 
                          ($showIcon ? ' pl-10' : ' pl-5') . 
                          ($showClearButton ? ' pr-10' : ' pr-5') .
                          ($error ? ' ring-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500' : '')
            ]) !!}>

        @if($showClearButton)
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                <button type="button" 
                        wire:click="$set('{{ $attributes->wire('model')->value() }}', '')"
                        class="text-gray-400 hover:text-gray-600 focus:outline-none"
                        x-show="$wire.{{ $attributes->wire('model')->value() }}"
                        x-cloak>
                    <x-icon name="times" style="fas" class="w-4 h-4" />
                </button>
            </div>
        @endif
    </div>

    @error($attributes->wire('model')->value())
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>