@props([
    'href' => '#',
    'target' => null,
    'external' => false,
    'color' => 'primary',
    'disabled' => false
])

<a href="{{ $href }}"
    {{ $target ? "target={$target}" : '' }}
    {{ $external ? 'rel="noopener noreferrer"' : '' }}
    {{ $disabled ? 'tabindex="-1"' : '' }}
    {{ $attributes->merge([
        'class' => 'inline-flex items-center gap-2 font-medium transition-colors ' . 
        match($color) {
            'primary' => 'text-primary hover:text-primary/80',
            'secondary' => 'text-secondary hover:text-secondary/80',
            'success' => 'text-green-600 hover:text-green-700',
            'danger' => 'text-red-600 hover:text-red-700',
            'gray' => 'text-gray-600 hover:text-gray-700',
            default => $color
        } . ($disabled ? ' opacity-50 cursor-not-allowed pointer-events-none' : '')
    ]) }}>
    {{ $slot }}
    @if($external)
        <x-icon name="external-link-alt" style="fas" size="sm" />
    @endif
</a>