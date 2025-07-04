<?php

namespace App\Livewire\Pages\Admin;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class AdminDashboard extends Component
{

    #[Layout('layouts.app')]
    #[Title('Dashboard')]
    public function render()
    {
        return view('livewire.pages.admin.admin-dashboard');
    }
}
