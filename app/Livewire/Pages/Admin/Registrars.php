<?php

namespace App\Livewire\Pages\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Campus;

class Registrars extends Component
{
    use WithPagination;
    
    public $search = '';
    public $campusFilter = '';
    // Remove: public $statusFilter = '';
    
    #[Layout('layouts.app')]
    #[Title('Registrar Management')]

    #[On('registrar-created')]
    #[On('registrar-updated')]
    #[On('registrar-deleted')]
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

    // Remove: public function updatedStatusFilter()

    public function clearFilters()
    {
        $this->search = '';
        $this->campusFilter = '';
        // Remove: $this->statusFilter = '';
        $this->resetPage();
    }

    public function toggleStatus($id)
    {
        try {
            $registrar = User::findOrFail($id);
            $newStatus = $registrar->status === 'active' ? 'inactive' : 'active';
            
            $registrar->update(['status' => $newStatus]);
            
            $this->dispatch('swal:success', [
                'title' => 'Success!',
                'text' => 'Registrar status updated successfully!',
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
        $registrar = User::find($id);
        
        $this->dispatch('swal:delete', [
            'title' => 'Delete Registrar',
            'text' => "Are you sure you want to delete {$registrar->first_name} {$registrar->last_name}?",
            'method' => 'deleteRegistrar',
            'params' => $id
        ]);
    }

    #[On('deleteRegistrar')]
    public function deleteRegistrar($id)
    {
        try {
            User::findOrFail($id)->delete();
            
            $this->dispatch('swal:success', [
                'title' => 'Deleted!',
                'text' => 'Registrar has been deleted successfully.',
            ]);
            
            $this->refreshTable();
            
        } catch (\Exception $e) {
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Failed to delete registrar.',
            ]);
        }
    }

    public function render()
    {
        $registrars = User::where('role_id', 2)
                         ->when($this->search, function($query) {
                             $query->where(function($q) {
                                 $q->where('first_name', 'like', '%' . $this->search . '%')
                                   ->orWhere('last_name', 'like', '%' . $this->search . '%')
                                   ->orWhere('email', 'like', '%' . $this->search . '%');
                             });
                         })
                         ->when($this->campusFilter, function($query) {
                             if ($this->campusFilter === 'unassigned') {
                                 $query->whereNull('campus_id');
                             } else {
                                 $query->where('campus_id', $this->campusFilter);
                             }
                         })
                         // Remove: ->when($this->statusFilter, function($query) { ... })
                         ->with(['role', 'campus'])
                         ->orderBy('first_name', 'asc')
                         ->paginate(10);

        $campuses = Campus::orderBy('name', 'asc')->get();

        return view('livewire.pages.admin.registrars', [
            'registrars' => $registrars,
            'campuses' => $campuses
        ]);
    }
}