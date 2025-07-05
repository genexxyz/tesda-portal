<?php

namespace App\View\Components\Partials;

use Illuminate\View\Component;

class Header extends Component
{
    public function __construct(
        public ?string $title = null,
        public ?string $breadcrumb = null,
        
    ) {}

    public function render()
    {
        return view('components.partials.header');
    }
}