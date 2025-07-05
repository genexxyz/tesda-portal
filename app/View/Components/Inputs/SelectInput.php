<?php

namespace App\View\Components\Inputs;

use Illuminate\View\Component;

class SelectInput extends Component
{
    public function __construct(
        public bool $disabled = false,
        public ?string $label = null,
        public ?string $placeholder = null,
        public ?string $error = null,
        public bool $required = false,
        public $options = [],
        public string $valueField = 'id',
        public string $textField = 'name'
    ) {}

    public function render()
    {
        return view('components.inputs.select-input');
    }
}