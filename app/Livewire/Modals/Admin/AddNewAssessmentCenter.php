<?php

namespace App\Livewire\Modals\Admin;

use App\Models\AssessmentCenter;
use App\Models\Assessor;
use LivewireUI\Modal\ModalComponent;
use Livewire\Attributes\Rule;
use Livewire\Attributes\On;

class AddNewAssessmentCenter extends ModalComponent
{
    #[Rule('required|string|max:255')]
    public $name = '';

    #[Rule('required|string|max:500')]
    public $address = '';

    #[Rule('nullable|array')]
    public $selectedAssessors = [];

    public function mount()
    {
        $this->selectedAssessors = [];
    }

    #[On('update-livewire-model')]
    public function updateModel($property, $value)
    {
        if ($property === 'selectedAssessors') {
            $this->selectedAssessors = $value;
            logger('Updated selectedAssessors via event:', $this->selectedAssessors);
        }
    }

    public function updatedSelectedAssessors()
    {
        // Debug: Log the updated values
        logger('Selected Assessors Updated:', $this->selectedAssessors);
    }

    public function save()
    {
        $this->validate();

        try {
            // Debug: Log before saving
            logger('Saving Assessment Center with selected assessors:', [
                'name' => $this->name,
                'address' => $this->address,
                'selectedAssessors' => $this->selectedAssessors
            ]);

            // Create the assessment center
            $assessmentCenter = AssessmentCenter::create([
                'name' => $this->name,
                'address' => $this->address,
            ]);

            // Attach selected assessors if any
            if (!empty($this->selectedAssessors)) {
                // Make sure we have integers
                $assessorIds = array_map('intval', $this->selectedAssessors);
                $assessmentCenter->assessors()->attach($assessorIds);
                
                logger('Attached assessors:', $assessorIds);
            }

            $this->dispatch('swal:success', [
                'title' => 'Success!',
                'text' => 'Assessment center has been created successfully' . 
                         (!empty($this->selectedAssessors) ? ' with ' . count($this->selectedAssessors) . ' assessor(s) assigned.' : '.'),
            ]);

            $this->dispatch('assessment-center-created');
            $this->closeModal();

        } catch (\Exception $e) {
            logger('Error creating assessment center:', ['error' => $e->getMessage()]);
            
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Failed to create assessment center: ' . $e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.modals.admin.add-new-assessment-center', [
            'availableAssessors' => Assessor::orderBy('name')->get(),
        ]);
    }

    public static function modalMaxWidth(): string
    {
        return 'lg';
    }
}