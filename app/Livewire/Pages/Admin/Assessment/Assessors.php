<?php

namespace App\Livewire\Pages\Admin\Assessment;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use App\Models\Assessor;

class Assessors extends Component
{
    use WithPagination;
    
    public $search = '';
    
    #[On('assessor-created')]
    #[On('assessor-updated')]
    #[On('assessor-deleted')]
    public function refreshTable()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->resetPage();
    }

    public function confirmDelete($id)
    {
        $assessor = Assessor::find($id);
        
        $this->dispatch('swal:delete', [
            'title' => 'Delete Assessor',
            'text' => "Are you sure you want to delete {$assessor->name}?",
            'method' => 'deleteAssessor',
            'params' => $id
        ]);
    }

    #[On('deleteAssessor')]
    public function deleteAssessor($id)
    {
        try {
            $assessor = Assessor::findOrFail($id);
            $assessor->delete();
            
            $this->dispatch('swal:success', [
                'title' => 'Deleted!',
                'text' => 'Assessor has been deleted successfully.',
            ]);
            
            $this->refreshTable();
            
        } catch (\Exception $e) {
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Failed to delete assessor.',
            ]);
        }
    }

    public function render()
    {
        $assessors = Assessor::withCount('assessmentCenters')
                            ->when($this->search, function($query) {
                                $query->where('name', 'like', '%' . $this->search . '%');
                            })
                            ->orderBy('name', 'asc')
                            ->paginate(15);

        return view('livewire.pages.admin.assessment.assessors', [
            'assessors' => $assessors
        ]);
    }
}