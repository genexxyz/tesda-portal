<?php

namespace App\Livewire\Pages\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Campus;
use App\Models\ProgramHead;

class ProgramHeads extends Component
{
    use WithPagination;
    
    public $search = '';
    public $campusFilter = '';
    
    #[Layout('layouts.app')]
    #[Title('Program Head Management')]

    #[On('program-head-created')]
    #[On('program-head-updated')]
    #[On('program-head-deleted')]
    public function refreshTable()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedCampusFilter()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->campusFilter = '';
        $this->resetPage();
    }

    public function toggleStatus($id)
    {
        try {
            $user = User::findOrFail($id);
            $newStatus = $user->status === 'active' ? 'inactive' : 'active';
            
            $user->update(['status' => $newStatus]);
            
            $this->dispatch('swal:success', [
                'title' => 'Success!',
                'text' => 'Program Head status updated successfully!',
            ]);
            
            $this->refreshTable();
            
        } catch (\Exception $e) {
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Failed to update status.',
            ]);
        }
    }

    public function confirmDelete($id)
    {
        $user = User::with('programHead.course')->find($id);
        
        $this->dispatch('swal:delete', [
            'title' => 'Delete Program Head',
            'text' => "Are you sure you want to delete {$user->first_name} {$user->last_name}?",
            'method' => 'deleteProgramHead',
            'params' => $id
        ]);
    }

    #[On('deleteProgramHead')]
    public function deleteProgramHead($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Delete associated program head records first
            ProgramHead::where('user_id', $id)->delete();
            
            // Then delete the user
            $user->delete();
            
            $this->dispatch('swal:success', [
                'title' => 'Deleted!',
                'text' => 'Program Head has been deleted successfully.',
            ]);
            
            $this->refreshTable();
            
        } catch (\Exception $e) {
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Failed to delete program head.',
            ]);
        }
    }

    public function render()
    {
        $programHeads = User::where('role_id', 3) // Assuming role_id 3 is for program heads
                           ->with(['campus', 'programHead.course'])
                           ->when($this->search, function($query) {
                               $query->where(function($q) {
                                   $q->where('first_name', 'like', '%' . $this->search . '%')
                                     ->orWhere('last_name', 'like', '%' . $this->search . '%')
                                     ->orWhere('email', 'like', '%' . $this->search . '%');
                               });
                           })
                           ->when($this->campusFilter, function($query) {
                               if ($this->campusFilter === 'unassigned') {
                                   // Filter for program heads without any course assignments
                                   $query->whereDoesntHave('programHead');
                               } else {
                                   $query->where('campus_id', $this->campusFilter);
                               }
                           })
                           ->orderBy('first_name', 'asc')
                           ->paginate(10);

        // Load course assignments for each program head to avoid N+1 queries
        $programHeads->getCollection()->transform(function ($programHead) {
            $programHead->courseAssignments = ProgramHead::where('user_id', $programHead->id)
                                                        ->with('course')
                                                        ->get();
            return $programHead;
        });

        $campuses = Campus::orderBy('name', 'asc')->get();

        return view('livewire.pages.admin.program-heads', [
            'programHeads' => $programHeads,
            'campuses' => $campuses
        ]);
    }
}