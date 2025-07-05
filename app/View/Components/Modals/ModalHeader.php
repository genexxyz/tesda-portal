<?php

namespace App\View\Components\Modals;

use Illuminate\View\Component;

class ModalHeader extends Component
{
    public function __construct(
        public ?string $title = null,
        public ?string $subtitle = null,
        public bool $showClose = true
    ) {}

    public function render()
    {
        return view('components.modals.modal-header');
    }
}