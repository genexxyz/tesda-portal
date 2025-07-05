<?php
namespace App\Livewire;

use Livewire\Component;

class BaseComponent extends Component
{
    public function layout()
    {
        return 'layouts.app';
    }
}