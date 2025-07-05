<?php

namespace App\View\Components\Modals;

use Illuminate\View\Component;

class ModalBody extends Component
{
    public function __construct(
        public ?string $padding = 'default'
    ) {}

    public function render()
    {
        return view('components.modals.modal-body');
    }
}