<?php

namespace App\Livewire\Modals\Admin;

use App\Models\Academic;
use LivewireUI\Modal\ModalComponent;

class AddNewSemester extends ModalComponent
{
    public  $start_year = '';
    public  $end_year = '';
    public $semester = '';

    public $is_active = false;
protected function rules()
{
    return [
        'start_year' => 'required|integer|min:2025',
        'end_year' => 'required|integer|gte:start_year',
        'semester' => 'required|string|max:255',
        'is_active' => 'boolean',
    ];
}
    protected $listeners = ['add-new-semester' => 'closeModal'];
    public function save()
    {
        $this->validate();

        

        //Check if the semester already exists
        $existingSemester = Academic::where('start_year', $this->start_year)
            ->where('end_year', $this->end_year)
            ->where('semester', $this->semester)
            ->first();

        if ($existingSemester) {
            $this->closeModal();
            $this->dispatch('swal:alert', type: 'error', text: 'Academic year already existing!');
            return;
        }

        // If setting as active, deactivate all others first
        if ($this->is_active) {
            Academic::where('is_active', true)->update(['is_active' => false]);
        }

        Academic::create([
            'start_year' => $this->start_year,
            'end_year' => $this->end_year,
            'semester' => $this->semester,
            'is_active' => $this->is_active,
        ]);

        
        
        $this->dispatch('academic-year-created');
        $this->dispatch('swal:alert', type: 'success', text: 'Academic year created successfully!');
        $this->closeModal();
    }

    public function render()
    {
        return view('livewire.modals.admin.add-new-semester');
    }

    public static function modalMaxWidth(): string
    {
        return 'lg';
    }
}