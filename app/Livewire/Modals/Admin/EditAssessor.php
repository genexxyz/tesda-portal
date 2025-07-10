<?php

namespace App\Livewire\Modals\Admin;

use App\Models\Assessor;
use App\Models\AssessmentCenter;
use LivewireUI\Modal\ModalComponent;
use Livewire\Attributes\Rule;

class EditAssessor extends ModalComponent
{
    public $assessorId;
    public $assessor;

    #[Rule('required|string|max:255')]
    public $name = '';

    #[Rule('nullable|array')]
    public $selectedAssessmentCenters = [];

    public function mount($assessorId)
    {
        $this->assessorId = $assessorId;
        $this->assessor = Assessor::with('assessmentCenters')->findOrFail($assessorId);
        
        // Load assessor data
        $this->name = $this->assessor->name;
        $this->selectedAssessmentCenters = $this->assessor->assessmentCenters->pluck('id')->toArray();
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:assessors,name,' . $this->assessorId,
            'selectedAssessmentCenters' => 'nullable|array',
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
            $this->assessor->update([
                'name' => $this->name,
            ]);

            // Sync selected assessment centers
            $this->assessor->assessmentCenters()->sync($this->selectedAssessmentCenters);

            $this->dispatch('swal:success', [
                'title' => 'Success!',
                'text' => 'Assessor updated successfully!',
            ]);

            $this->dispatch('assessor-updated');
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
        return view('livewire.modals.admin.edit-assessor', [
            'assessmentCenters' => AssessmentCenter::orderBy('name', 'asc')->get()
        ]);
    }

    public static function modalMaxWidth(): string
    {
        return 'lg';
    }
}
