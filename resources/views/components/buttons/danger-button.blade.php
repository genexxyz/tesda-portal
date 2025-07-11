@props([
    'type' => 'button',
    'disabled' => false
])

<button type="{{ $type }}"
    {{ $attributes->merge([
        'class' => 'rounded-md inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150 cursor-pointer' . ($disabled ? ' opacity-50 cursor-not-allowed' : ''),
    ]) }}
    {{ $disabled ? 'disabled' : '' }}>
    {{ $slot }}
</button>
