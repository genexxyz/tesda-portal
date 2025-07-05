@props([
    'disabled' => false,
    'label' => null,
    'type' => 'text',
    'placeholder' => null,
    'error' => null,
    'required' => false
])

<div class="relative mb-3">
    @if($label)
        <label for="{{ $attributes->get('id') }}" class="block pl-1 text-sm font-semibold text-gray-700 mb-1">
            {{ $label }}
            @if($required)
                <span class="text-red-500 ml-1">*</span>
            @endif
        </label>
    @endif

    <input placeholder="{{ $placeholder }}"
        type="{{ $type }}"
        {{ $disabled ? 'disabled' : '' }}
        {{ $required ? 'required' : '' }}
        {!! $attributes->merge([
            'class' => 'w-full text-sm pl-5 font-medium border-2 border-gray-400 rounded-md h-10' . 
                      ($error ? ' ring-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500' : '')
        ]) !!}>

    @error($attributes->wire('model')->value())
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>