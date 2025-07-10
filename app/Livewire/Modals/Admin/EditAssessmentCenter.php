<?php

namespace App\Livewire\Modals\Admin;

use App\Models\AssessmentCenter;
use App\Models\Assessor;
use LivewireUI\Modal\ModalComponent;
use Livewire\Attributes\Rule;

class EditAssessmentCenter extends ModalComponent
{
    public $assessmentCenterId;
    public $assessmentCenter;

    #[Rule('required|string|max:255')]
    public $name = '';

    #[Rule('required|string|max:500')]
    public $address = '';

    #[Rule('nullable|array')]
    public $selectedAssessors = [];

    public function mount($assessmentCenterId)
    {
        $this->assessmentCenterId = $assessmentCenterId;
        $this->assessmentCenter = AssessmentCenter::with('assessors')->findOrFail($assessmentCenterId);
        
        // Load assessment center data
        $this->name = $this->assessmentCenter->name;
        $this->address = $this->assessmentCenter->address;
        $this->selectedAssessors = $this->assessmentCenter->assessors->pluck('id')->toArray();
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:assessment_centers,name,' . $this->assessmentCenterId,
            'address' => 'required|string|max:500',
            'selectedAssessors' => 'nullable|array',
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
            $this->assessmentCenter->update([
                'name' => $this->name,
                'address' => $this->address,
            ]);

            // Sync selected assessors
            $this->assessmentCenter->assessors()->sync($this->selectedAssessors);

            $this->dispatch('swal:success', [
                'title' => 'Success!',
                'text' => 'Assessment center updated successfully!',
            ]);

            $this->dispatch('assessment-center-updated');
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
        return view('livewire.modals.admin.edit-assessment-center', [
            'availableAssessors' => Assessor::orderBy('name', 'asc')->get()
        ]);
    }

    public static function modalMaxWidth(): string
    {
        return 'lg';
    }
}
