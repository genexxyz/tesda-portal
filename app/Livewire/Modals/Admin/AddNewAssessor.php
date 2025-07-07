<?php

namespace App\Livewire\Modals\Admin;

use App\Models\Assessor;
use LivewireUI\Modal\ModalComponent;
use Livewire\Attributes\Rule;

class AddNewAssessor extends ModalComponent
{
    #[Rule('required|string|max:255')]
    public $name = '';

    public function save()
    {
        $this->validate();

        try {
            Assessor::create([
                'name' => $this->name,
            ]);

            $this->dispatch('swal:success', [
                'title' => 'Success!',
                'text' => 'Assessor has been created successfully.',
            ]);

            $this->dispatch('assessor-created');
            $this->closeModal();

        } catch (\Exception $e) {
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Failed to create assessor.',
            ]);
        }
    }

    public function render()
    {
        return view('livewire.modals.admin.add-new-assessor');
    }

    public static function modalMaxWidth(): string
    {
        return 'md';
    }
}