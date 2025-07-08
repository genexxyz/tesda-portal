@php
$classes = collect([
    'min-w-full divide-y divide-gray-200',
    $striped ? 'odd:bg-gray-50' : '',
    $hover ? 'hover:bg-gray-100' : '',
])->filter()->implode(' ');
@endphp

<div class="overflow-x-auto shadow ring-1 ring-gray-300 ring-opacity-5 md:rounded-lg">
    <table {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </table>
    
    @if($isEmpty)
        <div class="px-6 py-12 text-center text-gray-500 bg-white border-t border-gray-200">
            {{ $emptyMessage }}
        </div>
    @endif
</div>