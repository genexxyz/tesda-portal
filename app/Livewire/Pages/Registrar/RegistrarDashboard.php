<?php

namespace App\Livewire\Pages\Registrar;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class RegistrarDashboard extends Component
{

    #[Layout('layouts.app')]
    #[Title('Dashboard')]
    public function render()
    {
        return view('livewire.pages.registrar.registrar-dashboard');
    }
}
