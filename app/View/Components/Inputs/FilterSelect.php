<?php

namespace App\View\Components\Inputs;

use Illuminate\View\Component;
use Illuminate\Support\Collection;

class FilterSelect extends Component
{
    public function __construct(
        public bool $disabled = false,
        public ?string $label = null,
        public ?string $placeholder = 'All',
        public ?string $error = null,
        public array|Collection $options = [],
        public string $valueField = 'id',
        public string $textField = 'name',
        public bool $showIcon = true,
        public string $icon = 'filter',
        public bool $showClearButton = true
    ) {}

    public function render()
    {
        return view('components.inputs.filter-select');
    }
}