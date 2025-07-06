@props([
    'importModal' => '',
    'exportMethod' => '',
    'importTooltip' => 'Import data',
    'exportTooltip' => 'Export data',
    'showImport' => true,
    'showExport' => true
])
<div class="flex items-center justify-between mb-3">
    
<div {{ $attributes->merge(['class' => 'flex items-end space-x-2']) }}>
    @if($showImport && $importModal)
        <button wire:click="$dispatch('openModal', { component: '{{ $importModal }}' })"
                title="{{ $importTooltip }}"
                class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
            <x-icon name="download" style="fas" class="w-4 h-4 mr-2" />
            Import
        </button>
    @endif

    @if($showExport && $exportMethod)
        <button wire:click="{{ $exportMethod }}"
                title="{{ $exportTooltip }}"
                class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
            <x-icon name="upload" style="fas" class="w-4 h-4 mr-2" />
            Export
        </button>
    @endif
</div>
</div>