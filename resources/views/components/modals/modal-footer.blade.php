@props([
    'alignment' => 'right',
    'showBorder' => true,
    'background' => 'gray'
])

@php
$alignmentClasses = match($alignment) {
    'left' => 'justify-start',
    'center' => 'justify-center',
    'between' => 'justify-between',
    'around' => 'justify-around',
    default => 'justify-end'
};

$backgroundClasses = match($background) {
    'white' => 'bg-white',
    'none' => '',
    default => 'bg-gray-50'
};

$borderClasses = $showBorder ? 'border-t border-gray-200' : '';

$classes = collect([
    'px-6 py-4 flex items-center space-x-3',
    $alignmentClasses,
    $backgroundClasses,
    $borderClasses
])->filter()->implode(' ');
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</div>