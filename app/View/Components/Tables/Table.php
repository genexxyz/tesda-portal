<?php

namespace App\View\Components\Tables;

use Illuminate\View\Component;

class Table extends Component
{
    public function __construct(
        public bool $striped = true,
        public bool $hover = true,
        public string $emptyMessage = 'No data available',
        public bool $isEmpty = false,
        public bool $showNumbers = true,
        public int $startNumber = 1
    ) {}

    public function render()
    {
        return view('components.tables.table');
    }
}