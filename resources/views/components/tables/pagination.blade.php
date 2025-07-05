@props([
    'paginator',
    'showInfo' => true,
    'onEachSide' => 3
])

@if($paginator->hasPages())
    <div class="flex flex-col sm:flex-row items-center justify-between space-y-3 sm:space-y-0 py-3">
        
        @if($showInfo)
            <!-- Pagination Info -->
            <div class="text-sm text-gray-700">
                Showing 
                <span class="font-medium">{{ $paginator->firstItem() }}</span>
                to 
                <span class="font-medium">{{ $paginator->lastItem() }}</span>
                of 
                <span class="font-medium">{{ $paginator->total() }}</span>
                results
            </div>
        @endif

        <!-- Pagination Links -->
        <div class="flex items-center space-x-1">
            {{-- Previous Page Link --}}
            @if($paginator->onFirstPage())
                <span class="rounded-md inline-flex items-center justify-center px-3 py-2 text-xs font-semibold text-gray-400 bg-gray-100 border border-gray-200 cursor-not-allowed">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Previous
                </span>
            @else
                <button wire:click="gotoPage({{ $paginator->currentPage() - 1 }})"
                        class="rounded-md inline-flex items-center justify-center px-3 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition ease-in-out duration-150 cursor-pointer">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Previous
                </button>
            @endif

            {{-- Pagination Elements --}}
            @php
                $start = max(1, $paginator->currentPage() - $onEachSide);
                $end = min($paginator->lastPage(), $paginator->currentPage() + $onEachSide);
            @endphp

            {{-- First Page --}}
            @if($start > 1)
                <button wire:click="gotoPage(1)"
                        class="rounded-md inline-flex items-center justify-center px-3 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition ease-in-out duration-150 cursor-pointer">
                    1
                </button>
                
                @if($start > 2)
                    <span class="px-2 py-2 text-xs text-gray-500">...</span>
                @endif
            @endif

            {{-- Page Numbers --}}
            @for($page = $start; $page <= $end; $page++)
                @if($page == $paginator->currentPage())
                    <span class="rounded-md inline-flex items-center justify-center px-3 py-2 bg-primary border border-transparent font-semibold text-xs text-white">
                        {{ $page }}
                    </span>
                @else
                    <button wire:click="gotoPage({{ $page }})"
                            class="rounded-md inline-flex items-center justify-center px-3 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition ease-in-out duration-150 cursor-pointer">
                        {{ $page }}
                    </button>
                @endif
            @endfor

            {{-- Last Page --}}
            @if($end < $paginator->lastPage())
                @if($end < $paginator->lastPage() - 1)
                    <span class="px-2 py-2 text-xs text-gray-500">...</span>
                @endif
                
                <button wire:click="gotoPage({{ $paginator->lastPage() }})"
                        class="rounded-md inline-flex items-center justify-center px-3 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition ease-in-out duration-150 cursor-pointer">
                    {{ $paginator->lastPage() }}
                </button>
            @endif

            {{-- Next Page Link --}}
            @if($paginator->hasMorePages())
                <button wire:click="gotoPage({{ $paginator->currentPage() + 1 }})"
                        class="rounded-md inline-flex items-center justify-center px-3 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition ease-in-out duration-150 cursor-pointer">
                    Next
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            @else
                <span class="rounded-md inline-flex items-center justify-center px-3 py-2 text-xs font-semibold text-gray-400 bg-gray-100 border border-gray-200 cursor-not-allowed">
                    Next
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </span>
            @endif
        </div>
    </div>
@endif