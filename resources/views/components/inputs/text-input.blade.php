@props([
    'disabled' => false,
    'label' => null,
    'type' => 'text',
    'placeholder' => null,
    'error' => null
])

<div class="relative mb-3">
    @if($label)
        <label for="{{ $attributes->get('id') }}" class="block pl-1 text-sm font-semibold text-gray-700 mb-1">
            {{ $label }}
        </label>
    @endif

    <input placeholder="{{ $placeholder }}"
        type="{{ $type }}"
        {{ $disabled ? 'disabled' : '' }}
        {!! $attributes->merge([
            'class' => 'w-full text-sm pl-5 font-medium border-1 border-gray-700 focus:border-primary h-10' . 
                      ($error ? ' border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500' : '')
        ]) !!}>

    @error($attributes->wire('model')->value())
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
