<?php

namespace App\Livewire\Modals\Admin;

use App\Models\Campus;
use LivewireUI\Modal\ModalComponent;
use Livewire\Attributes\Rule;

class EditCampus extends ModalComponent
{
    public $campusId;
    public $campus;

    #[Rule('required|string|max:10')]
    public $code = '';

    #[Rule('required|string|max:255')]
    public $name = '';

    #[Rule('required|integer|min:1')]
    public $number = '';

    #[Rule('required|string')]
    public $color = '#FF5733';

    public function mount($campusId)
    {
        $this->campusId = $campusId;
        $this->campus = Campus::findOrFail($campusId);
        
        // Load campus data
        $this->code = $this->campus->code;
        $this->name = $this->campus->name;
        $this->number = $this->campus->number;
        $this->color = $this->campus->color;
    }

    public function rules()
    {
        return [
            'code' => 'required|string|max:10|unique:campuses,code,' . $this->campusId,
            'name' => 'required|string|max:255|unique:campuses,name,' . $this->campusId,
            'number' => 'required|integer|min:1|unique:campuses,number,' . $this->campusId,
            'color' => 'required|string',
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
            $this->campus->update([
                'code' => strtoupper($this->code),
                'name' => strtoupper($this->name),
                'number' => $this->number,
                'color' => $this->color,
            ]);

            $this->dispatch('swal:success', [
                'title' => 'Success!',
                'text' => 'Campus updated successfully!',
            ]);

            $this->dispatch('campus-updated');
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
        return view('livewire.modals.admin.edit-campus');
    }

    public static function modalMaxWidth(): string
    {
        return 'md';
    }
}
