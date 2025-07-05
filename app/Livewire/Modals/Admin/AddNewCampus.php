<?php

namespace App\Livewire\Modals\Admin;

use App\Models\Campus;
use LivewireUI\Modal\ModalComponent;
use Livewire\Attributes\Rule;

class AddNewCampus extends ModalComponent
{
    #[Rule('required|string|max:10|unique:campuses,code')]
    public $code = '';

    #[Rule('required|string|max:255|unique:campuses,name')]
    public $name = '';

    #[Rule('required|integer|min:1|unique:campuses,number')]
    public $number = '';

    #[Rule('required|string')]
    public $color = '#FF5733';

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function save()
    {
        $this->validate();

        try {
            Campus::create([
                'code' => strtoupper($this->code),
                'name' => strtoupper($this->name),
                'number' => $this->number,
                'color' => $this->color,
            ]);

            $this->dispatch('swal:success', [
                'title' => 'Success!',
                'text' => 'Campus created successfully!',
            ]);

            $this->dispatch('campus-created');
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
        return view('livewire.modals.admin.add-new-campus');
    }

    public static function modalMaxWidth(): string
    {
        return 'lg';
    }
}