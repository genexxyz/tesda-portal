<?php

namespace App\Livewire\Modals\Admin;

use App\Models\Academic;
use LivewireUI\Modal\ModalComponent;
use Livewire\Attributes\Rule;

class EditAcademic extends ModalComponent
{
    public $academicId;
    public $academic;

    #[Rule('required|integer|min:2025')]
    public $start_year = '';

    #[Rule('required|integer|gte:start_year')]
    public $end_year = '';

    #[Rule('required|string|max:255')]
    public $semester = '';

    public $is_active = false;

    public function mount($academicId)
    {
        $this->academicId = $academicId;
        $this->academic = Academic::findOrFail($academicId);
        
        // Load academic data
        $this->start_year = $this->academic->start_year;
        $this->end_year = $this->academic->end_year;
        $this->semester = $this->academic->semester;
        $this->is_active = $this->academic->is_active;
    }

    public function rules()
    {
        return [
            'start_year' => 'required|integer|min:2025',
            'end_year' => 'required|integer|gte:start_year',
            'semester' => 'required|string|max:255',
            'is_active' => 'boolean',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function save()
    {
        $this->validate();

        try {
            // Check if another academic year with same details exists (excluding current)
            $existingAcademic = Academic::where('start_year', $this->start_year)
                ->where('end_year', $this->end_year)
                ->where('semester', $this->semester)
                ->where('id', '!=', $this->academicId)
                ->first();

            if ($existingAcademic) {
                $this->dispatch('swal:error', [
                    'title' => 'Error!',
                    'text' => 'Academic year with same details already exists!',
                ]);
                return;
            }

            // If setting as active, deactivate all others first
            if ($this->is_active) {
                Academic::where('is_active', true)
                    ->where('id', '!=', $this->academicId)
                    ->update(['is_active' => false]);
            }

            $this->academic->update([
                'start_year' => $this->start_year,
                'end_year' => $this->end_year,
                'semester' => $this->semester,
                'is_active' => $this->is_active,
            ]);

            $this->dispatch('swal:success', [
                'title' => 'Success!',
                'text' => 'Academic year updated successfully!',
            ]);

            $this->dispatch('academic-year-updated');
            $this->closeModal();

        } catch (\Exception $e) {
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Something went wrong. Please try again.',
            ]);
        }
    }

    public function render()
    {
        return view('livewire.modals.admin.edit-academic');
    }

    public static function modalMaxWidth(): string
    {
        return 'md';
    }
}
