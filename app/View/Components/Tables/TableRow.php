<?php

namespace App\View\Components\Tables;

use Illuminate\View\Component;

class TableRow extends Component
{
    public function __construct(
        public bool $clickable = false,
        public ?string $href = null
    ) {}

    public function render()
    {
        return view('components.tables.table-row');
    }
}