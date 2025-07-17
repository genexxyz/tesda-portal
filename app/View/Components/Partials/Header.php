<?php

namespace App\View\Components\Partials;

use App\Models\Academic;
use Illuminate\View\Component;

class Header extends Component
{
    public function __construct(
        public ?string $title = null,
        public ?string $breadcrumb = null,
        
    ) {}

    public function render()
    {
        $academicYear = Academic::where('is_active', true)->first();
        return view('components.partials.header', [
            'academicYear' => $academicYear,
        ]);
    }
}