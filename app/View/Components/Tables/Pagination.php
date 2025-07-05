<?php

namespace App\View\Components\Tables;

use Illuminate\View\Component;
use Illuminate\Pagination\LengthAwarePaginator;

class Pagination extends Component
{
    public function __construct(
        public LengthAwarePaginator $paginator,
        public bool $showInfo = true,
        public int $onEachSide = 3
    ) {}

    public function render()
    {
        return view('components.tables.pagination');
    }
}