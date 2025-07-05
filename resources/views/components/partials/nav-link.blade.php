@props(['active' => false])

@php
$isActive = is_bool($active) ? $active : false;
$classes = $isActive
            ? 'flex items-center px-4 py-2 font-medium text-white bg-secondary/80'
            : 'flex items-center px-4 py-2 font-medium text-white hover:bg-secondary/60 transition-colors';
@endphp

<a wire:navigate.hover {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>