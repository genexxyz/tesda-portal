@props([
    'type' => 'button',
    'href' => null,
    'disabled' => false
])

@if($href)
    <a href="{{ $href }}"
        {{ $attributes->merge([
            'class' => 'inline-flex items-center justify-center px-4 py-2 bg-primary border border-transparent font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary/80 focus:bg-primary/80 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150' . ($disabled ? ' opacity-50 cursor-not-allowed' : ''),
        ]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}"
        {{ $attributes->merge([
            'class' => 'w-full inline-flex items-center justify-center cursor-pointer px-4 py-2 bg-primary border border-transparent font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary/80 focus:bg-primary/80 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150' . ($disabled ? ' opacity-50 cursor-not-allowed' : ''),
        ]) }}
        {{ $disabled ? 'disabled' : '' }}>
        {{ $slot }}
    </button>
@endif
