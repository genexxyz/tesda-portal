<?php

namespace App\Livewire\Pages\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;

class AssessmentManagement extends Component
{
    #[Url]
    public $activeTab = 'assessors';

    #[Layout('layouts.app')]
    #[Title('Assessment Management')]

    public function mount()
    {
        // Set default tab if none specified
        if (!in_array($this->activeTab, ['assessors', 'assessment-centers'])) {
            $this->activeTab = 'assessors';
        }
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.pages.admin.assessment-management');
    }
}