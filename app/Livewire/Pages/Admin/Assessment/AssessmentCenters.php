<?php

namespace App\Livewire\Pages\Admin\Assessment;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use App\Models\AssessmentCenter;

class AssessmentCenters extends Component
{
    use WithPagination;
    
    public $search = '';
    
    #[On('assessment-center-created')]
    #[On('assessment-center-updated')]
    #[On('assessment-center-deleted')]
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
        $center = AssessmentCenter::find($id);
        
        $this->dispatch('swal:delete', [
            'title' => 'Delete Assessment Center',
            'text' => "Are you sure you want to delete {$center->name}?",
            'method' => 'deleteAssessmentCenter',
            'params' => $id
        ]);
    }

    #[On('deleteAssessmentCenter')]
    public function deleteAssessmentCenter($id)
    {
        try {
            $center = AssessmentCenter::findOrFail($id);
            $center->delete();
            
            $this->dispatch('swal:success', [
                'title' => 'Deleted!',
                'text' => 'Assessment center has been deleted successfully.',
            ]);
            
            $this->refreshTable();
            
        } catch (\Exception $e) {
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Failed to delete assessment center.',
            ]);
        }
    }

    public function render()
    {
        $assessmentCenters = AssessmentCenter::withCount('assessors')
                                           ->when($this->search, function($query) {
                                               $query->where('name', 'like', '%' . $this->search . '%')
                                                    ->orWhere('address', 'like', '%' . $this->search . '%');
                                           })
                                           ->orderBy('name', 'asc')
                                           ->paginate(15);

        return view('livewire.pages.admin.assessment.assessment-centers', [
            'assessmentCenters' => $assessmentCenters
        ]);
    }
}