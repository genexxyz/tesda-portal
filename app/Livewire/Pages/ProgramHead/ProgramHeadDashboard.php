<?php

namespace App\Livewire\Pages\ProgramHead;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class ProgramHeadDashboard extends Component
{
    #[Layout('layouts.app')]
    #[Title('Dashboard')]

    public function render()
    {
        return view('livewire.pages.program-head.program-head-dashboard');
    }
}
