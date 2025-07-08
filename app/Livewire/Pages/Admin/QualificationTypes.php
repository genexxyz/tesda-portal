<?php

namespace App\Livewire\Pages\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Models\QualificationType;

class QualificationTypes extends Component
{
    use WithPagination;
    
    public $search = '';
    public $levelFilter = '';
    
    #[Layout('layouts.app')]
    #[Title('Qualification Type Management')]

    #[On('qualification-type-created')]
    #[On('qualification-type-updated')]
    #[On('qualification-type-deleted')]
    public function refreshTable()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedLevelFilter()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->levelFilter = '';
        $this->resetPage();
    }

    public function confirmDelete($id)
    {
        $qualificationType = QualificationType::find($id);
        
        $this->dispatch('swal:delete', [
            'title' => 'Delete Qualification Type',
            'text' => "Are you sure you want to delete {$qualificationType->name}?",
            'method' => 'deleteQualificationType',
            'params' => $id
        ]);
    }

    #[On('deleteQualificationType')]
    public function deleteQualificationType($id)
    {
        try {
            $qualificationType = QualificationType::findOrFail($id);
            
            // Check if qualification type has courses
            if ($qualificationType->courses()->count() > 0) {
                $this->dispatch('swal:error', [
                    'title' => 'Cannot Delete!',
                    'text' => 'This qualification type has associated courses and cannot be deleted.',
                ]);
                return;
            }
            
            // Check if qualification type has assessments
            if ($qualificationType->results()->count() > 0) {
                $this->dispatch('swal:error', [
                    'title' => 'Cannot Delete!',
                    'text' => 'This qualification type has assessment results and cannot be deleted.',
                ]);
                return;
            }
            
            // Detach all course relationships before deleting
            $qualificationType->courses()->detach();
            
            $qualificationType->delete();
            
            $this->dispatch('swal:success', [
                'title' => 'Deleted!',
                'text' => 'Qualification type has been deleted successfully.',
            ]);
            
            $this->refreshTable();
            
        } catch (\Exception $e) {
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Failed to delete qualification type.',
            ]);
        }
    }

    public function render()
    {
        $qualificationTypes = QualificationType::with(['courses'])
                                              ->when($this->search, function($query) {
                                                  $query->where(function($q) {
                                                      $q->where('code', 'like', '%' . $this->search . '%')
                                                        ->orWhere('name', 'like', '%' . $this->search . '%')
                                                        ->orWhere('level', 'like', '%' . $this->search . '%');
                                                  });
                                              })
                                              ->when($this->levelFilter, function($query) {
                                                  $query->where('level', $this->levelFilter);
                                              })
                                              ->orderBy('code', 'asc')
                                              ->paginate(10);

        return view('livewire.pages.admin.qualification-types', [
            'qualificationTypes' => $qualificationTypes
        ]);
    }
}
