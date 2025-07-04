<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Str;

class Icon extends Component
{
    public string $class;

    public function __construct(
        public string $name,
        public string $style = 'fas', // fas, far, fab, etc
        public ?string $size = null,  // sm, lg, xl, 2x, etc
        public ?string $color = null,
        public bool $spin = false,
        public bool $pulse = false,
    ) {
        $this->class = $this->generateClass();
    }

    protected function generateClass(): string
    {
        $classes = [$this->style, "fa-{$this->name}"];

        if ($this->size) {
            $classes[] = "fa-{$this->size}";
        }

        if ($this->spin) {
            $classes[] = "fa-spin";
        }

        if ($this->pulse) {
            $classes[] = "fa-pulse";
        }

        if ($this->color) {
            $classes[] = "text-{$this->color}";
        }

        return implode(' ', $classes);
    }

    public function render()
    {
        return view('components.icon');
    }
}