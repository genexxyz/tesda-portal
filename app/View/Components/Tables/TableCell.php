<?php

namespace App\View\Components\Tables;

use Illuminate\View\Component;

class TableCell extends Component
{
    public function __construct(
        public ?string $align = 'left',
        public bool $nowrap = false,
        public ?string $width = null
    ) {}

    public function render()
    {
        return view('components.tables.table-cell');
    }
}