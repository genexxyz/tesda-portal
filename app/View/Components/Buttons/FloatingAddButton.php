<?php

namespace App\View\Components\Buttons;

use Illuminate\View\Component;

class FloatingAddButton extends Component
{
    public function __construct(
        public ?string $href = null,
        public ?string $wireClick = null,
        public string $tooltip = 'Add new item',
        public string $icon = 'plus'
    ) {}

    public function render()
    {
        return view('components.buttons.floating-add-button');
    }
}