<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Link extends Component
{
    public function __construct(
        public ?string $href = '#',
        public ?string $target = null,
        public bool $external = false,
        public ?string $color = 'primary',
        public bool $disabled = false
    ) {}

    public function render()
    {
        return view('components.link');
    }
}