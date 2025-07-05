@php
$classes = collect([
    'min-w-full divide-y divide-gray-200',
    $striped ? 'odd:bg-gray-50' : '',
    $hover ? 'hover:bg-gray-100' : '',
    $showNumbers ? 'table-with-numbers' : ''
])->filter()->implode(' ');
@endphp

<div class="overflow-x-auto shadow ring-1 ring-gray-300 ring-opacity-5 md:rounded-lg">
    <table {{ $attributes->merge(['class' => $classes]) }}
           @if($showNumbers) style="counter-reset: row-number {{ $startNumber - 1 }};" @endif>
        {{ $slot }}
    </table>
    
    @if($isEmpty)
        <div class="px-6 py-12 text-center text-gray-500 bg-white border-t border-gray-200">
            {{ $emptyMessage }}
        </div>
    @endif
</div>

@if($showNumbers)
    <style>
        .table-with-numbers tbody tr {
            counter-increment: row-number;
        }
        
        .table-with-numbers tbody tr td:first-child::before {
            content: counter(row-number, decimal);
            font-weight: bold;
            color: #6b7280;
            margin-right: 0.5rem;
        }
    </style>
@endif