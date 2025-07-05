<?php

namespace App\View\Components\Modals;

use Illuminate\View\Component;

class ModalFooter extends Component
{
    public function __construct(
        public string $alignment = 'right',
        public bool $showBorder = true,
        public string $background = 'gray'
    ) {}

    public function render()
    {
        return view('components.modals.modal-footer');
    }
}