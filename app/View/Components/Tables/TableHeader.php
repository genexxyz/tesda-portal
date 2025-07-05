<?php

namespace App\View\Components\Tables;

use Illuminate\View\Component;

class TableHeader extends Component
{
    public function __construct(
        public bool $sortable = false,
        public ?string $sortDirection = null,
        public ?string $sortField = null
    ) {}

    public function render()
    {
        return view('components.tables.table-header');
    }
}