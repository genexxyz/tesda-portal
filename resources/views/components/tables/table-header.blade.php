@props([
    'sortable' => false,
    'sortDirection' => null,
    'sortField' => null
])

@php
$classes = collect([
    'px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider',
    $sortable ? 'cursor-pointer select-none hover:bg-gray-100' : ''
])->filter()->implode(' ');
@endphp

<th {{ $attributes->merge(['class' => $classes]) }}>
    <div class="flex items-center justify-between">
        <span>{{ $slot }}</span>
        
        @if($sortable)
            <div class="flex flex-col ml-2">
                <x-icon name="caret-up" 
                        style="fas" 
                        class="w-3 h-3 {{ $sortDirection === 'asc' && $sortField ? 'text-primary' : 'text-gray-400' }}" />
                <x-icon name="caret-down" 
                        style="fas" 
                        class="w-3 h-3 -mt-1 {{ $sortDirection === 'desc' && $sortField ? 'text-primary' : 'text-gray-400' }}" />
            </div>
        @endif
    </div>
</th>