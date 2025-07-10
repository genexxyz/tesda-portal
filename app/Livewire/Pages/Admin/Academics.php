<?php

namespace App\Livewire\Pages\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Models\Academic;

class Academics extends Component
{
    use WithPagination;
    #[Layout('layouts.app')]
    #[Title('Academic Year')]

    #[On('academic-year-created')]
    #[On('academic-year-updated')]
    public function refreshTable()
    {
        // Just force a re-render
        $this->render();
    }


    public function render()
    {
        $academicYear = Academic::orderBy('start_year', 'desc')
                              ->orderBy('semester', 'asc')
                              ->paginate(3);

        return view('livewire.pages.admin.academics', [
            'academicYear' => $academicYear
        ]);
    }
}