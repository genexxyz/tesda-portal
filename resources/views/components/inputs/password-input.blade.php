@props([
    'disabled' => false,
    'label' => null,
    'placeholder' => null,
    'error' => null
])

<div x-data="{ show: false }" class="relative mb-3">
    @if($label)
        <label for="{{ $attributes->get('id') }}" class="block pl-1 text-sm font-semibold text-gray-700 mb-1">
            {{ $label }}
        </label>
    @endif

    <div class="relative">
        <input placeholder="{{ $placeholder }}"
            :type="show ? 'text' : 'password'"
            {{ $disabled ? 'disabled' : '' }}
            {!! $attributes->merge([
                'class' => 'w-full text-sm pl-5 pr-10 font-medium border-1 border-gray-700 focus:border-primary h-10' . 
                          ($error ? ' border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500' : '')
            ]) !!}>
            
        <button 
            type="button"
            @click="show = !show"
            class="absolute inset-y-0 right-0 px-3 flex items-center"
        >
            <x-icon 
                name="eye" 
                style="far"
                size="sm"
                color="gray-500"
                x-show="!show"
            />
            <x-icon 
                name="eye-slash" 
                style="far"
                size="sm"
                color="gray-500"
                x-show="show"
            />
        </button>
    </div>

    @error($attributes->wire('model')->value())
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

