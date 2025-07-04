<?php

namespace App\View\Components\Inputs;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PasswordInput extends Component
{
    public function __construct(
        public ?string $label = null,
        public ?string $name = null,
        public ?string $value = null,
        public ?string $placeholder = null,
        public bool $required = false,
        public bool $disabled = false,
        public bool $autofocus = false
    ) {}

    public function render(): View|Closure|string
    {
        return view('components.inputs.password-input');
    }
}