@props([
    'align' => 'left',
    'nowrap' => false,
    'width' => null
])

@php
$classes = collect([
    'px-6 py-4 text-sm text-gray-900',
    'text-' . $align,
    $nowrap ? 'whitespace-nowrap' : 'whitespace-normal'
])->filter()->implode(' ');

$styles = $width ? "width: {$width};" : '';
@endphp

<td {{ $attributes->merge(['class' => $classes, 'style' => $styles]) }}>
    {{ $slot }}
</td>