@props([
    'padding' => 'default'
])

@php
$paddingClasses = match($padding) {
    'none' => '',
    'sm' => 'px-4 py-3',
    'lg' => 'px-8 py-6',
    'xl' => 'px-10 py-8',
    default => 'px-6 py-4'
};
@endphp

<div {{ $attributes->merge(['class' => $paddingClasses]) }}>
    {{ $slot }}
</div>