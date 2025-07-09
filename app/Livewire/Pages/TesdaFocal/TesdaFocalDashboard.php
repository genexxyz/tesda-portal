<?php

namespace App\Livewire\Pages\TesdaFocal;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class TesdaFocalDashboard extends Component
{
    #[Layout('layouts.app')]
    #[Title('Dashboard')]
    public function render()
    {
        return view('livewire.pages.tesda-focal.tesda-focal-dashboard');
    }
}
