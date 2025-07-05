<?php

namespace App\Livewire\Pages\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Models\Campus;

class Campuses extends Component
{
    use WithPagination;
    
    #[Layout('layouts.app')]
    #[Title('Campus Management')]

    #[On('campus-created')]
    #[On('campus-updated')]
    #[On('campus-deleted')]
    public function refreshTable()
    {
        $this->resetPage();
    }

    public function confirmDelete($id)
    {
        $campus = Campus::find($id);
        
        $this->dispatch('swal:delete', [
            'title' => 'Delete Campus',
            'text' => "Are you sure you want to delete {$campus->name} campus?",
            'method' => 'deleteCampus',
            'params' => $id
        ]);
    }

    #[On('deleteCampus')]
    public function deleteCampus($id)
    {
        try {
            Campus::findOrFail($id)->delete();
            
            $this->dispatch('swal:success', [
                'title' => 'Deleted!',
                'text' => 'Campus has been deleted successfully.',
            ]);
            
            $this->refreshTable();
            
        } catch (\Exception $e) {
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Failed to delete campus.',
            ]);
        }
    }

    public function render()
    {
        $campuses = Campus::orderBy('name', 'asc')
                         ->paginate(10);

        return view('livewire.pages.admin.campuses', [
            'campuses' => $campuses
        ]);
    }
}