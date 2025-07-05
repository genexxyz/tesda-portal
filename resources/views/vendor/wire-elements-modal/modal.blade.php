<div>
    @isset($jsPath)
        <script>{!! file_get_contents($jsPath) !!}</script>
    @endisset
    @isset($cssPath)
        <style>{!! file_get_contents($cssPath) !!}</style>
    @endisset

    <div
            x-data="LivewireUIModal()"
            x-on:close.stop="setShowPropertyTo(false)"
            x-on:keydown.escape.window="show && closeModalOnEscape()"
            x-show="show"
            class="fixed inset-0 overflow-y-auto"
            style="z-index: 10000; display: none;"
    >
        <style>
            /* Modal width classes */
            .modal-sm { max-width: 20rem !important; width: 20rem; }
            .modal-md { max-width: 28rem !important; width: 28rem; }
            .modal-lg { max-width: 32rem !important; width: 32rem; }
            .modal-xl { max-width: 36rem !important; width: 36rem; }
            .modal-2xl { max-width: 42rem !important; width: 42rem; }
            .modal-3xl { max-width: 48rem !important; width: 48rem; }
            .modal-4xl { max-width: 56rem !important; width: 56rem; }
            .modal-5xl { max-width: 64rem !important; width: 64rem; }
            .modal-6xl { max-width: 72rem !important; width: 72rem; }
            .modal-7xl { max-width: 80rem !important; width: 80rem; }

            @media (max-width: 640px) {
                .modal-sm, .modal-md, .modal-lg, .modal-xl, 
                .modal-2xl, .modal-3xl, .modal-4xl, 
                .modal-5xl, .modal-6xl, .modal-7xl {
                    max-width: 95vw !important;
                    width: 95vw !important;
                }
            }
        </style>

        <div class="flex items-end justify-center min-h-dvh px-4 pt-4 pb-10 text-center sm:block sm:p-0">
            <div
                    x-show="show"
                    x-on:click="closeModalOnClickAway()"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 transition-all transform"
                    style="z-index: 9999;"
            >
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                    x-show="show && showActiveComponent"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-bind:class="modalWidth"
                    class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle"
                    style="z-index: 10001; max-width: 90vw;"
                    id="modal-container"
                    x-trap.noscroll.inert="show && showActiveComponent"
                    aria-modal="true"
            >
                @forelse($components as $id => $component)
                    <div x-show.immediate="activeComponent == '{{ $id }}'" x-ref="{{ $id }}" wire:key="{{ $id }}">
                        @livewire($component['name'], $component['arguments'], key($id))
                    </div>
                @empty
                @endforelse
            </div>
        </div>
    </div>
</div>