<?php

namespace App\View\Components\Inputs;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TextInput extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public ?string $label = null,
        public ?string $type = 'text',
        public ?string $name = null,
        public ?string $value = null,
        public ?string $placeholder = null,
        public bool $required = false,
        public bool $disabled = false,
        public bool $autofocus = false
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.inputs.text-input');
    }
}
