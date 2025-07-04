@props(['active'])

@php
$classes = ($active ?? false)
            ? 'flex items-center px-4 py-2 text-sm font-medium text-white bg-primary'
            : 'flex items-center px-4 py-2 text-sm font-medium text-white hover:bg-secondary transition-colors';
@endphp

<a wire:navigate {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>