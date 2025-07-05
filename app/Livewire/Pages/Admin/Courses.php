<?php

namespace App\Livewire\Pages\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Models\Course;
use App\Models\Campus;

class Courses extends Component
{
    use WithPagination;
    
    public $search = '';
    public $campusFilter = '';
    
    #[Layout('layouts.app')]
    #[Title('Course Management')]

    #[On('course-created')]
    #[On('course-updated')]
    #[On('course-deleted')]
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

    public function confirmDelete($id)
    {
        $course = Course::find($id);
        
        $this->dispatch('swal:delete', [
            'title' => 'Delete Course',
            'text' => "Are you sure you want to delete {$course->name}?",
            'method' => 'deleteCourse',
            'params' => $id
        ]);
    }

    #[On('deleteCourse')]
    public function deleteCourse($id)
    {
        try {
            $course = Course::findOrFail($id);
            
            // Check if course has students
            if ($course->students()->count() > 0) {
                $this->dispatch('swal:error', [
                    'title' => 'Cannot Delete!',
                    'text' => 'This course has enrolled students and cannot be deleted.',
                ]);
                return;
            }
            
            $course->delete();
            
            $this->dispatch('swal:success', [
                'title' => 'Deleted!',
                'text' => 'Course has been deleted successfully.',
            ]);
            
            $this->refreshTable();
            
        } catch (\Exception $e) {
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Failed to delete course.',
            ]);
        }
    }

    public function render()
    {
        $courses = Course::with(['campus', 'students'])
                        ->when($this->search, function($query) {
                            $query->where(function($q) {
                                $q->where('code', 'like', '%' . $this->search . '%')
                                  ->orWhere('name', 'like', '%' . $this->search . '%');
                            });
                        })
                        ->when($this->campusFilter, function($query) {
                            if ($this->campusFilter === 'unassigned') {
                                $query->whereNull('campus_id');
                            } else {
                                $query->where('campus_id', $this->campusFilter);
                            }
                        })
                        ->orderBy('code', 'asc')
                        ->paginate(10);

        $campuses = Campus::orderBy('name', 'asc')->get();

        return view('livewire.pages.admin.courses', [
            'courses' => $courses,
            'campuses' => $campuses
        ]);
    }
}