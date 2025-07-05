@props([
    'clickable' => false,
    'href' => null
])

@php
$classes = collect([
    $clickable || $href ? 'cursor-pointer hover:bg-gray-50' : '',
])->filter()->implode(' ');
@endphp

<tr {{ $attributes->merge(['class' => $classes]) }}
    @if($href)
        onclick="window.location.href='{{ $href }}'"
    @endif
>
    {{ $slot }}
</tr>