@props([
    'type' => 'button',
    'disabled' => false
])

<button type="{{ $type }}"
    {{ $attributes->merge([
        'class' => 'rounded-md inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition ease-in-out duration-150 cursor-pointer' . ($disabled ? ' opacity-50 cursor-not-allowed' : ''),
    ]) }}
    {{ $disabled ? 'disabled' : '' }}>
    {{ $slot }}
</button>