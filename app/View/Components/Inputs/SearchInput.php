<?php

namespace App\View\Components\Inputs;

use Illuminate\View\Component;

class SearchInput extends Component
{
    public function __construct(
        public bool $disabled = false,
        public ?string $label = null,
        public ?string $placeholder = 'Search...',
        public ?string $error = null,
        public bool $showIcon = true,
        public bool $showClearButton = true
    ) {}

    public function render()
    {
        return view('components.inputs.search-input');
    }
}